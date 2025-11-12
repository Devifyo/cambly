<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Plan;
use App\Models\TicketLedger;
use App\Models\CreditTransaction;

class CreditService
{
    private SubscriptionService $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function isInvoiceProcessed(?string $invoiceId, int $userId): bool
    {
        if (!$invoiceId) {
            return false;
        }

        // Check credit transactions table
        if ($this->creditTransactionsTableExists()) {
            $exists = CreditTransaction::where('reference', $invoiceId)->exists();
            if ($exists) {
                return true;
            }
        }

        // Check ticket ledger
        return TicketLedger::where('stripe_invoice_id', $invoiceId)
            ->where('student_id', $userId)
            ->exists();
    }

    public function issueCredits(
        User $user,
        int $credits,
        string $reason,
        ?string $reference = null,
        ?Plan $plan = null,
        ?int $cycleNumber = null,
        ?string $stripeSubscriptionId = null
    ): bool {
        // Skip if no credits need to be issued
        if ($credits <= 0) {
            Log::debug('Skipping credit issuance: zero credits', [
                'user_id' => $user->id,
            ]);

            return false;
        }

        // Prevent duplicate issuance using reference idempotency
        if ($reference && $this->isInvoiceProcessed($reference, $user->id)) {
            Log::warning('Credit issuance skipped: reference already processed', [
                'user_id'   => $user->id,
                'reference' => $reference,
            ]);

            return false;
        }

        return DB::transaction(function () use (
            $user,
            $credits,
            $reason,
            $reference,
            $plan,
            $cycleNumber,
            $stripeSubscriptionId,
        ) {
            // Resolve current billing cycle
            $cycle = $cycleNumber ?? $this->resolveCycleNumber($user);

            // Find or create user ledger for this cycle
            $ledger = $this->findOrCreateLedger(
                $user->id,
                $cycle,
                $stripeSubscriptionId,
                $reference
            );

            // Double-check within transaction for safety
            if (
                $reference &&
                $ledger->stripe_invoice_id === $reference &&
                $ledger->issued_credits > 0
            ) {
                Log::warning('Credits already issued for this invoice in ledger', [
                    'user_id'   => $user->id,
                    'ledger_id' => $ledger->id,
                    'reference' => $reference,
                ]);

                return false;
            }

            // Update issued credits
            $ledger->issued_credits += $credits;

            // Settle any hold credits from previous cycles
            $settlementResult = $this->settleHoldCredits($user->id, $ledger);
            $holdCreditsSettled = $settlementResult['total_settled'];
            $settlements = $settlementResult['settlements'];

            // Update used credits with settled amount
            $ledger->used_credits += $holdCreditsSettled;
            $ledger->save();

            // Record issuance transaction
            $this->recordCreditTransaction(
                $user->id,
                $cycle,
                $credits,
                'issued',
                'issueCredit_function',
                $reason,
                $reference,
                $plan,
                $ledger->id,
                $ledger->id,
                'issueCredits'
            );

            // Record each hold credit settlement as separate transaction
            foreach ($settlements as $settlement) {
                $this->recordCreditTransaction(
                    $user->id,
                    $cycle,
                    -$settlement['settled'],
                    'hold',
                    'hold_credits_settled',
                    "Settlement from cycle {$settlement['previous_cycle']}",
                    "previous_ledger_{$settlement['previous_ledger_id']}",
                    $plan,
                    $ledger->id,
                    $ledger->id, // action_id
                   'issueCredits'
                );
            }

            // Final success log
            Log::info('Credits issued successfully', [
                'user_id'              => $user->id,
                'credits'              => $credits,
                'ledger_id'            => $ledger->id,
                'hold_credits_settled' => $holdCreditsSettled,
                'settlements_count'    => count($settlements),
                'available_credits'    => $ledger->issued_credits - $ledger->used_credits,
            ]);

            return true;
        });
    }



    /**
     * Settle hold credits from ALL previous ledgers with outstanding holds (FIFO)
     * 
     * @param int $userId
     * @param TicketLedger $currentLedger
     * @return array ['total_settled' => int, 'settlements' => array]
     */
    private function settleHoldCredits(int $userId, $currentLedger): array
    {
        // Find ALL previous ledgers with hold_credits (oldest first for FIFO)
        $previousLedgers = DB::table('ticket_ledgers')
            ->select(['id', 'cycle_number', 'hold_credits'])
            ->where('student_id', $userId)
            ->where('id', '<', $currentLedger->id)
            ->where('hold_credits', '>', 0)
            ->orderBy('id') // FIFO: oldest debts first
            ->lockForUpdate()
            ->get();

        if ($previousLedgers->isEmpty()) {
            return ['total_settled' => 0, 'settlements' => []];
        }

        $availableCredits = $currentLedger->issued_credits;
        $totalSettled = 0;
        $settlements = [];

        // Process each ledger with hold credits (FIFO)
        foreach ($previousLedgers as $ledger) {
            if ($availableCredits <= 0) {
                break; // No more credits to settle
            }

            $holdCredits = (int) $ledger->hold_credits;
            $creditsToSettle = min($holdCredits, $availableCredits);
            $remainingHoldCredits = $holdCredits - $creditsToSettle;

            // Update the previous ledger (atomic operation)
            DB::table('ticket_ledgers')
                ->where('id', $ledger->id)
                ->update(['hold_credits' => $remainingHoldCredits]);

            $availableCredits -= $creditsToSettle;
            $totalSettled += $creditsToSettle;

            $settlements[] = [
                'previous_ledger_id' => $ledger->id,
                'previous_cycle' => $ledger->cycle_number,
                'hold_credits' => $holdCredits,
                'settled' => $creditsToSettle,
                'remaining' => $remainingHoldCredits,
            ];
        }

        // Single comprehensive log entry
        Log::info('Hold credits settlement completed', [
            'user_id' => $userId,
            'current_ledger_id' => $currentLedger->id,
            'current_cycle' => $currentLedger->cycle_number,
            'ledgers_processed' => count($settlements),
            'total_settled' => $totalSettled,
            'details' => $settlements,
        ]);

        return [
            'total_settled' => $totalSettled,
            'settlements' => $settlements
        ];
    }

    private function findOrCreateLedger(
        int $userId,
        int $cycleNumber,
        ?string $stripeSubscriptionId,
        ?string $stripeInvoiceId
    ): TicketLedger {
        $criteria = [
            'student_id' => $userId,
            'cycle_number' => $cycleNumber,
        ];

        if ($stripeInvoiceId) {
            $criteria['stripe_invoice_id'] = $stripeInvoiceId;
        }

        if ($stripeSubscriptionId) {
            $criteria['stripe_subscription_id'] = $stripeSubscriptionId;
        }

        return TicketLedger::firstOrCreate($criteria, [
            'issued_credits' => 0,
            'used_credits' => 0,
            'hold_credits' => 0,
        ]);
    }

    /**
     * Record credit transaction with full audit trail
     * 
     * @param int $userId
     * @param int $cycleNumber
     * @param int $credits (positive for credit, negative for debit)
     * @param string $type ('credit' or 'debit')
     * @param string $reason
     * @param string|null $description
     * @param string|null $reference
     * @param Plan|null $plan
     * @param int|null $ticketLedgerId
     * @param int|null $actionId (ID of the action that used credits)
     * @param string|null $actionType (e.g., 'exam_attempt', 'resource_download', 'ai_query')
     */
    public function recordCreditTransaction(
        int $userId,
        int $cycleNumber,
        int $credits,
        string $type,
        string $reason,
        ?string $description = null,
        ?string $reference = null,
        ?Plan $plan = null,
        ?int $ticketLedgerId = null,
        ?int $actionId = null,
        ?string $actionType = null
    ): void {
        // if (!$this->creditTransactionsTableExists()) {
        //     return;
        // }

        try {
            $transactionData = [
                'ticket_ledger_id' => $ticketLedgerId,
                'student_id' => $userId,
                'cycle_number' => $cycleNumber,
                'credits' => $credits,
                'type' => $type, // 'credit' or 'debit'
                'reason' => $reason,
                'reference' => $reference,
                'description' => $description ?? ($plan ? "Credits issued for plan {$plan->name}" : "Credits transaction"),
            ];
            // Add action tracking if provided
            if ($actionId && $actionType) {
                $transactionData['action_id'] = $actionId;
                $transactionData['action_type'] = $actionType;
            }
            
            $cred  = CreditTransaction::create($transactionData);
        } catch (\Throwable $e) {
            Log::warning('Failed to create credit transaction', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function resolveCycleNumber(User $user): int
    {
        $subscription =  $user->activeSubscription;
        if ($subscription) {
            return $subscription->cycle_number;
        }

        $maxCycle = TicketLedger::where('student_id', $user->id)->max('cycle_number');
        return $maxCycle ? $maxCycle + 1 : 1;
    }

    private function creditTransactionsTableExists(): bool
    {
        return \Illuminate\Support\Facades\Schema::hasTable('credit_transactions');
    }
}
