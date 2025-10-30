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
        if ($credits <= 0) {
            Log::debug('Skipping credit issuance: zero credits', ['user_id' => $user->id]);
            return false;
        }

        // Idempotency check
        if ($reference && $this->isInvoiceProcessed($reference, $user->id)) {
            Log::warning('Credit issuance skipped: reference already processed', [
                'user_id' => $user->id,
                'reference' => $reference
            ]);
            return false;
        }

        return DB::transaction(function () use (
            $user, $credits, $reason, $reference, $plan, $cycleNumber, $stripeSubscriptionId
        ) {
            $cycle = $cycleNumber ?? $this->resolveCycleNumber($user);
            
            $ledger = $this->findOrCreateLedger(
                $user->id,
                $cycle,
                $stripeSubscriptionId,
                $reference
            );

            // Additional idempotency check within transaction
            if ($reference && $ledger->stripe_invoice_id === $reference && $ledger->issued_credits > 0) {
                Log::warning('Credits already issued for this invoice in ledger', [
                    'user_id' => $user->id,
                    'ledger_id' => $ledger->id,
                    'reference' => $reference
                ]);
                return false;
            }

            // Update ledger
            $ledger->issued_credits += $credits;
            $ledger->save();

            // Record transaction if table exists
            $this->recordCreditTransaction(
                $user->id,
                $cycle,
                $credits,
                $reason,
                $reference,
                $plan
            );

            Log::info('Credits issued successfully', [
                'user_id' => $user->id,
                'credits' => $credits,
                'ledger_id' => $ledger->id,
                'reference' => $reference,
                'reason' => $reason
            ]);

            return true;
        });
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

    private function recordCreditTransaction(
        int $userId,
        int $cycleNumber,
        int $credits,
        string $reason,
        ?string $reference,
        ?Plan $plan
    ): void {
        if (!$this->creditTransactionsTableExists()) {
            return;
        }

        try {
            CreditTransaction::create([
                'student_id' => $userId,
                'cycle_number' => $cycleNumber,
                'credits' => $credits,
                'type' => 'issued',
                'reason' => $reason,
                'reference' => $reference,
                'description' => $plan ? "Credits issued for plan {$plan->name}" : "Credits issued",
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to create credit transaction', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function resolveCycleNumber(User $user): int
    {
        $subscription = $this->subscriptionService->getActiveSubscription($user->id);
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