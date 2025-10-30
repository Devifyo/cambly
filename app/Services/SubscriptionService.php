<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Plan;
use Carbon\Carbon;

class SubscriptionService
{
    public function createOrUpdateSubscription(
        int $userId,
        string $stripeSubscriptionId,
        string $stripeCustomerId,
        ?int $planId,
        string $status
    ): Subscription {
        return Subscription::updateOrCreate(
            [
                'user_id' => $userId,
                'stripe_subscription_id' => $stripeSubscriptionId,
            ],
            [
                'plan_id' => $planId,
                'stripe_customer_id' => $stripeCustomerId,
                'status' => $status,
                'cycle_number' => 1,
            ]
        );
    }

    public function syncSubscriptionFromStripe($stripeSubscription): void
    {
        DB::transaction(function () use ($stripeSubscription) {
            $customerId = $stripeSubscription->customer ?? null;
            $subscriptionId = $stripeSubscription->id ?? null;

            if (!$customerId || !$subscriptionId) {
                throw new \InvalidArgumentException('Missing customer or subscription ID');
            }

            $user = User::where('stripe_id', $customerId)->first();
            if (!$user) {
                throw new \InvalidArgumentException("User not found for Stripe customer: {$customerId}");
            }

            $plan = $this->resolvePlanFromStripeSubscription($stripeSubscription);
            $status = $stripeSubscription->status ?? 'pending';

            $periodStart = isset($stripeSubscription->current_period_start)
                ? Carbon::createFromTimestamp($stripeSubscription->current_period_start)
                : null;
            $periodEnd = isset($stripeSubscription->current_period_end)
                ? Carbon::createFromTimestamp($stripeSubscription->current_period_end)
                : null;

            $updateData = [
                'plan_id' => $plan?->id,
                'status' => $status,
                'current_period_start' => $periodStart,
                'current_period_end' => $periodEnd,
            ];

            // Handle subscription cancellation
            if (($stripeSubscription->cancel_at_period_end ?? false) && $periodEnd) {
                $updateData['ends_at'] = $periodEnd;
            }

            Subscription::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'stripe_subscription_id' => $subscriptionId,
                ],
                $updateData
            );
        });
    }

    public function updateSubscriptionPeriod(
        string $stripeSubscriptionId,
        ?int $planId,
        string $status,
        ?Carbon $periodStart,
        ?Carbon $periodEnd
    ): Subscription {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();

        if (!$subscription) {
            throw new \InvalidArgumentException("Subscription not found: {$stripeSubscriptionId}");
        }

        $subscription->update([
            'plan_id' => $planId ?? $subscription->plan_id,
            'status' => $status,
            'current_period_start' => $periodStart ?? $subscription->current_period_start,
            'current_period_end' => $periodEnd ?? $subscription->current_period_end,
        ]);

        return $subscription;
    }

    public function updateSubscriptionStatus(string $stripeSubscriptionId, string $status): void
    {
        Subscription::where('stripe_subscription_id', $stripeSubscriptionId)
            ->update(['status' => $status]);
    }

    public function cancelSubscription(string $stripeSubscriptionId): void
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscriptionId)->first();
        
        if ($subscription) {
            $subscription->update([
                'status' => 'cancelled',
                'ends_at' => now(),
            ]);
        }
    }

    public function markSubscriptionsPastDue(string $stripeCustomerId): void
    {
        Subscription::where('stripe_customer_id', $stripeCustomerId)
            ->where('status', 'active')
            ->update(['status' => 'past_due']);
    }

    public function incrementCycle(int $subscriptionId): void
    {
        try {
            Subscription::where('id', $subscriptionId)->increment('cycle_number');
        } catch (\Throwable $e) {
            Log::warning('Failed to increment subscription cycle', [
                'subscription_id' => $subscriptionId,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function getActiveSubscription(int $userId): ?Subscription
    {
        return Subscription::where('user_id', $userId)
            ->whereIn('status', ['active', 'trialing', 'pending'])
            ->latest()
            ->first();
    }

    private function resolvePlanFromStripeSubscription($stripeSubscription): ?Plan
    {
        $items = $stripeSubscription->items->data ?? [];
        
        if (empty($items)) {
            return null;
        }

        $firstItem = $items[0];
        $price = $firstItem->price ?? null;
        $productId = $price->product ?? null;

        if ($productId) {
            return Plan::active()->where('stripe_product_id', $productId)->first();
        }

        return null;
    }
}