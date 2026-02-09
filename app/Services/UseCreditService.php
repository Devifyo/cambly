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
        $subscription = $user->activeSubscription;
        if (!$subscription && $rule != 'show_all') {
            return null;
        }

        $now = Carbon::now();

        // 1️⃣ Database-level filter: Pull only records from the last 32 days 
        $ledgers = TicketLedger::where('student_id', $user->id)
            ->where('created_at', '>=', $now->copy()->subDays(32))
            ->get()
            ->filter(function ($ledger) {
                if ($ledger->issued_credits > 0) {
                    return $ledger->created_at->copy()->addMonth()->isFuture();
                }
                return true; 
            });

        // 2️⃣ Calculate lifetime totals for Usage/Hold 
        $totalUsed = (int) TicketLedger::where('student_id', $user->id)->sum('used_credits');
        $totalHold = (int) TicketLedger::where('student_id', $user->id)->sum('hold_credits');

        // Admin Tickets
        $tickets_added_by_admin = $ledgers->filter(function($l) {
            return is_null($l->stripe_subscription_id) && is_null($l->stripe_invoice_id) && $l->issued_credits > 0;
        })->map(fn($l) => $this->formatTicketBatch($l, $now, $user))->values()->toArray(); 

        // Subscription Tickets
        $tickets_added_by_subscription = $ledgers->filter(function($l) {
            return (!is_null($l->stripe_subscription_id) || !is_null($l->stripe_invoice_id)) && $l->issued_credits > 0;
        })->map(fn($l) => $this->formatTicketBatch($l, $now, $user))->values()->toArray();
        // 4️⃣ Sum the totals
        $issued = (int) $ledgers->sum('issued_credits');
        
        // Pick the most recent valid issuance for the ledger_id
        $latestLedger = $ledgers->where('issued_credits', '>', 0)->last();
        return [
            'ledger_id'                     => $latestLedger->id ?? null,
            'subscription_id'               => $subscription->id ?? null,
            'cycle_number'                  => $subscription->cycle_number ?? 0,
            'issued'                        => $issued,
            'used'                          => $totalUsed,
            'hold'                          => $totalHold,
            'available'                     => max(0, $issued - $totalUsed - $totalHold),
            'tickets_added_by_admin'        => $tickets_added_by_admin,
            'tickets_added_by_subscription' => $tickets_added_by_subscription,
            'ledgers'                       => $ledgers->values(),
        ];
    }
    // public function getCurrentMonthCredits($user)
    // {
    //     $now = Carbon::now();

    //     // 1 Database-level filtering (Performance)
    //     // We only pull records from the last 32 days. 
    //     // This is safe because no "1 month" period is longer than 31 days.
    //     $activeIssuances = TicketLedger::where('student_id', $user->id)
    //         ->where('issued_credits', '>', 0)
    //         ->where('created_at', '>=', $now->copy()->subDays(32)) 
    //         ->get()
    //         ->filter(function ($ledger) {
    //             // Precise Carbon calendar month check
    //             return $ledger->created_at->addMonth()->isFuture();
    //         });
    //     // 2️ Optimized Aggregate Queries
    //     // Don't pull all records, just get the sum directly from SQL
    //     $totalUsed = (int) TicketLedger::where('student_id', $user->id)->sum('used_credits');
    //     $totalHold = (int) TicketLedger::where('student_id', $user->id)->sum('hold_credits');

    //     $validIssuedSum = (int) $activeIssuances->sum('issued_credits');

    //     // 3️ Calculation
    //     $available = $validIssuedSum - $totalUsed - $totalHold;

    //     return [
    //         'available'        => max(0, $available),
    //         'issued_this_month'=> $validIssuedSum,
    //         'lifetime_used'    => $totalUsed,
    //         'details'          => $activeIssuances->map(fn($item) => [
    //             'amount'     => $item->issued_credits,
    //             'expires_at' => $item->created_at->addMonth()->toDateTimeString(),
    //             'days_left'  => $now->diffInDays($item->created_at->addMonth(), false),
    //         ])->values(),
    //     ];
    // }

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
            // 1. Late Cancellation Policy
            // Students cannot get a refund if cancelling within 12 hours.
            // However, if an Admin is impersonating, we override this rule to allow the refund.
            if ($hoursUntilStart < 12 && ! $isImpersonating) {
                return ['ok' => true, 'skipped' => 'within_12_hours'];
            }

            // 2. Booking Origin Check
            // If the student didn't book this lesson themselves (e.g., it was created by an Admin 
            // or was a free lesson), they didn't spend a ticket, so no refund is needed.
            if ($reservation->created_by !== $reservation->student_id) {
                return ['ok' => true, 'skipped' => 'free_lesson_no_refund'];
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

    /**
    * Helper to format ticket batch with Timezone support
    */
    private function formatTicketBatch($ledger, $now, $user)
    {
        // 1. Calculate Expiry (Always do math on UTC first for accuracy)
        $expiryDate = $ledger->created_at->copy()->addMonth();
        
        $issued = (int) $ledger->issued_credits;
        $used   = (int) $ledger->used_credits;
        $hold   = (int) $ledger->hold_credits;

        return [
            'id'                => $ledger->id,
            'total_tickets'     => $issued,
            'used_tickets'      => $used,
            'hold_tickets'      => $hold,
            'available_tickets' => max(0, $issued - $used - $hold),
            
            // 2. Use the User model helper to convert to their Timezone
            'expiring_on'       => $user->asUserTime($expiryDate)->toDateTimeString(),
            'created_at'        => $user->asUserTime($ledger->created_at)->toDateTimeString(),
            
            // Days left is just a number, so timezone doesn't matter
            'days_left'         => (int) $now->diffInDays($expiryDate, false),
            'reason'            => $ledger->reason,
        ];
    }
}
