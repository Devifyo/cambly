<?php

namespace App\Services;

use App\Models\TicketLedger;
use App\Models\{Subscription, Reservation};
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;


class UseCreditService
{
    /**
     * Get user's current month credit details
     * based on their active subscription cycle.
     *
     * @param int $userId
     * @return array|null
     */
    public function getCurrentMonthCredits($user, $rule = 'only_has_subscription')
    {
        // 1️⃣ Find active subscription
 
        $subscription = $user->activeSubscription;
        if (!$subscription && $rule != 'show_all') {
            return null;
        }

        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth()->startOfDay();
        $endOfMonth   = $now->copy()->endOfMonth()->endOfDay();
        // 2️⃣ Get cycle number from subscription
        $cycleNumber = $subscription->cycle_number ?? null;
        // dd($cycleNumber);
        // fetch current ledger
            $CurrentCycleledger = TicketLedger::where('student_id', $user->id)
            ->when($rule == 'only_has_subscription' || !is_null($cycleNumber) , function ($query) use ($cycleNumber) {
                    $query->where('cycle_number', $cycleNumber);
                })
            ->latest()
            ->first();
            // if(!$CurrentCycleledger){
            //     dd($CurrentCycleledger,$user);
            //     return null;
            // }
        // 3️⃣ Fetch matching ledger entry
        $ledgers = TicketLedger::where('student_id', $user->id)
        ->where(function ($query) use ($cycleNumber, $startOfMonth, $endOfMonth) {

            // OR logic grouped properly
            $query->where('cycle_number', $cycleNumber)
                  ->orWhereBetween('created_at', [$startOfMonth, $endOfMonth]);

        })
        ->orderBy('created_at', 'asc')
        ->get();

        if (!$ledgers) {
            return null;
        }

        $issued = (int) $ledgers->sum('issued_credits');
        $used   = (int) $ledgers->sum('used_credits');
        $hold   = (int) $ledgers->sum('hold_credits');
        
        return [
            'ledger_id' => $CurrentCycleledger->id ?? null,
            'subscription_id' => $subscription->id ?? null,
            'cycle_number'    => $cycleNumber,
            'issued'          => $issued,
            'used'            => $used,
            'hold'            => $hold,
            'available'       => $issued - $used - $hold,
            'ledgers'         => $ledgers,
        ];
    }

    /**
     * Get user's previous credit history (by all cycles).
     *
     * @param int $userId
     * @param int|null $limit
     * @return \Illuminate\Support\Collection
     */
    public function getCreditHistory($userId, $limit = null)
    {
        $query = TicketLedger::where('student_id', $userId)
            ->orderByDesc('cycle_number');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get()->map(function ($ledger) {
            return [
                'cycle_number' => $ledger->cycle_number,
                'issued' => $ledger->issued_credits,
                'used' => $ledger->used_credits,
                'hold' => $ledger->hold_credits,
                'available' => $ledger->issued_credits - $ledger->used_credits - $ledger->hold_credits,
                'created_at' => $ledger->created_at->format('Y-m-d'),
            ];
        });
    }

    public function getCurrentTicketLedger($user, $rule = 'only_has_subscription')
    {
        $subscription = $user->activeSubscription;
        if (!$subscription && $rule != 'show_all') {
            return null;
        }
                // 2️⃣ Get cycle number from subscription
        $cycleNumber = $subscription?->cycle_number ?? null;

        // 3️⃣ Fetch matching ledger entry
        $ledger = TicketLedger::where('student_id', $user->id)
            ->when($rule == 'only_has_subscription' || !is_null($cycleNumber) , function ($query) use ($cycleNumber) {
                $query->where('cycle_number', $cycleNumber);
            })
            ->latest()
            ->first();
        if (!$ledger) {
            return null;
        }

        return $ledger;
    }


    public function refundCreditsOnCancel(Reservation $reservation): array
    {
        try {

            $isImpersonating = is_impersonating();

            $refundCount = max(1, (int) config('app.ticket_per_meeting', 1));

            $availability = $reservation->availability;
            if (! $availability) {
                return ['ok' => true, 'skipped' => 'no_availability'];
            }

            $rawStart = $availability->start_utc ?? $reservation->created_at;

            $startUtc = Carbon::parse($rawStart, 'UTC')->setTimezone('UTC');

            // Refund only if >= 12 hours until start
            $hoursUntilStart = Carbon::now('UTC')->diffInHours($startUtc, false);
            if ($hoursUntilStart < 12 && !$isImpersonating) {
                return ['ok' => true, 'skipped' => 'within_12_hours'];
            }

            $studentId = $reservation->student_id;
            if (! $studentId) {
                return ['error' => 'no_student_id', 'status' => 400];
            }

            // Obtain ledger (prefer UseCreditService, fallback to TicketLedger)
            $ledger = null;
            if (class_exists(\App\Services\UseCreditService::class)) {
                try {
                    $service = app(\App\Services\UseCreditService::class);
                    if (method_exists($service, 'getCurrentTicketLedger')) {
                        $ledger = $service->getCurrentTicketLedger($studentId, 'show_all');
                    }
                } catch (\Throwable $e) {
                    Log::warning('UseCreditService::getCurrentTicketLedger failed: '.$e->getMessage());
                }
            }

            if (! $ledger) {
                $ledger = \App\Models\TicketLedger::where('student_id', $studentId)
                    ->latest('id')
                    ->first();
                if (! $ledger) {
                    return ['ok' => true, 'skipped' => 'no_ledger'];
                }
            }

            // Update ledger safely in DB transaction
            $result = DB::transaction(function () use ($ledger, $refundCount) {
                $hold = max(0, (int) ($ledger->hold_credits ?? 0));
                $used = max(0, (int) ($ledger->used_credits ?? 0));

                if ($hold > 0) {
                    $decrement = min($refundCount, $hold);
                    $ledger->hold_credits = $hold - $decrement;
                    $action = 'hold_decremented';
                    $refunded = $decrement;
                } elseif ($used > 0) {
                    $decrement = min($refundCount, $used);
                    $ledger->used_credits = $used - $decrement;
                    $action = 'used_decremented';
                    $refunded = $decrement;
                } else {
                    return ['ok' => true, 'skipped' => 'no_credits_to_refund'];
                }

                // Normalize and persist
                $ledger->hold_credits = max(0, (int) ($ledger->hold_credits ?? 0));
                $ledger->used_credits = max(0, (int) ($ledger->used_credits ?? 0));
                $ledger->save();

                // Optionally add a ledger history/audit entry here

                return ['ok' => true, 'refunded' => $refunded, 'action' => $action];
            });

            Log::info('refundCreditsOnCancel', [
                'reservation' => $reservation->id ?? null,
                'student_id' => $reservation->student_id ?? null,
                'result' => $result,
            ]);
            return $result;
        } catch (\Throwable $e) {
            Log::error('refundCreditsOnCancel error: '.$e->getMessage(), [
                'reservation' => $reservation->id ?? null,
            ]);
            return ['error' => 'Failed to refund credits', 'status' => 500];
        }
    }
}
