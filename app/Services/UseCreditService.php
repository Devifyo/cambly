<?php

namespace App\Services;

use App\Models\TicketLedger;
use App\Models\Subscription;
use Carbon\Carbon;

class UseCreditService
{
    /**
     * Get user's current month credit details
     * based on their active subscription cycle.
     *
     * @param int $userId
     * @return array|null
     */
    public function getCurrentMonthCredits($user)
    {
        // 1️⃣ Find active subscription
        $subscription = $user->activeSubscription;
        if (!$subscription) {
            return null;
        }

        // 2️⃣ Get cycle number from subscription
        $cycleNumber = $subscription->current_cycle_number ?? 1;

        // 3️⃣ Fetch matching ledger entry
        $ledger = TicketLedger::where('student_id', $user->id)
            ->where('cycle_number', $cycleNumber)
            ->latest()
            ->first();
        if (!$ledger) {
            return null;
        }

        return [
            'cycle_number' => $ledger->cycle_number,
            'issued' => $ledger->issued_credits,
            'used' => $ledger->used_credits,
            'hold' => $ledger->hold_credits,
            'available' => $ledger->issued_credits - $ledger->used_credits - $ledger->hold_credits,
            'subscription_id' => $subscription->id,
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
}
