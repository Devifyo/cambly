<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\Subscription;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

/**
 * Class UserSubscriptionService
 *
 * Responsibilities:
 *  - Provide safe, human-readable subscription details for a user
 *  - Offer both "active subscription" details and full subscription history
 *
 * Notes:
 *  - This service expects Subscription -> plan relationship to exist (optional)
 *  - Dates are returned as ISO8601 and a readable format
 *  - Amount is returned as number (in smallest unit assumed) and formatted string (human)
 */
class UserSubscriptionService
{
    /**
     * Get details for the user's latest active subscription.
     *
     * @param int|User $user
     * @return array|null  Returns associative array of details or null if no active subscription
     */
    public function getActiveSubscriptionDetails($user): ?array
    {
        $userModel = $this->resolveUser($user);
        if (! $userModel) {
            return null;
        }

        // Use the existing relationship on User model named `activeSubscription` (returns relation)
        $subscription = $userModel->activeSubscription()->with('plan')->first();

        if (! $subscription instanceof Subscription) {
            return null;
        }

        return $this->formatSubscription($subscription);
    }

    /**
     * Get all subscription records for the user.
     *
     * @param int|User $user
     * @param int|null $perPage If provided, returns LengthAwarePaginator, otherwise Collection
     * @return Collection|LengthAwarePaginator
     */
    public function getAllSubscriptions($user, ?int $perPage = null)
    {
        $userModel = $this->resolveUser($user);
        if (! $userModel) {
            return collect();
        }

        $query = Subscription::where('user_id', $userModel->id)
            ->with('plan')
            ->orderByDesc('created_at');

        if ($perPage && $perPage > 0) {
            $paginator = $query->paginate($perPage);
            // map before returning so front-end gets consistent formatted structure
            $paginator->getCollection()->transform(function ($s) {
                return $this->formatSubscription($s);
            });
            return $paginator;
        }

        $subs = $query->get();
        return $subs->map(function ($s) {
            return $this->formatSubscription($s);
        });
    }

    /**
     * Format an individual Subscription model into a simple array for consumption.
     *
     * @param Subscription $sub
     * @return array
     */
    protected function formatSubscription(Subscription $sub): array
    {
        $plan = $sub->plan ?? null;

        $periodStart = $this->toCarbonSafe($sub->current_period_start);
        $periodEnd = $this->toCarbonSafe($sub->current_period_end);
        $startsAt = $this->toCarbonSafe($sub->created_at);
        $endsAt = $this->toCarbonSafe($sub->ends_at);

        $isActiveStatus = (string) ($sub->status ?? '') === 'active';
        $cancelAtPeriodEnd = $sub->cancel_at_period_end ?? false; // stripe field if synced
        $isCanceled = ((string) ($sub->status ?? '') === 'cancelled' || ! is_null($sub->ends_at));

        // If subscription is marked active and within period window, it's renewing unless cancel flag set or ends_at reached.
        $now = Carbon::now();

        $inCurrentPeriod = ($periodStart ? $periodStart->lte($now) : true)
                       && ($periodEnd ? $periodEnd->gte($now) : true);

        $isRenewing = $isActiveStatus && $inCurrentPeriod && ! $cancelAtPeriodEnd && ! $isCanceled;

        // amount: prefer Plan.price, else fallback to stripe price if stored on subscription
        $amountRaw = null;
        $amountHuman = null;
        if ($plan) {
            $amountRaw = $plan->price ?? null;
            $currency = $plan->currency ?? Config::get('app.currency', 'INR'); // optional currency on Plan
            $amountHuman = $this->formatMoney($amountRaw, $currency);
        } else {
            // Try to infer from subscription (if you have stripe_price or amount field)
            $amountRaw = $sub->amount ?? null;
            $amountHuman = $amountRaw ? $this->formatMoney($amountRaw, Config::get('app.currency', 'INR')) : null;
        }

        // Build the response array with important details only
        return [
            'id' => $sub->id,
            'stripe_subscription_id' => $sub->stripe_subscription_id ?? null,
            'stripe_customer_id' => $sub->stripe_customer_id ?? null,
            'plan' => $plan ? [
                'id' => $plan->id,
                'name' => $plan->name,
                'slug' => $plan->slug ?? null,
                'credits_per_cycle' => (int) ($plan->credits_per_cycle ?? 0),
                'interval' => $plan->interval ?? null,
            ] : null,
            'status' => $sub->status ?? null,
            'is_active' => $isActiveStatus && $inCurrentPeriod,
            'is_renewing' => (bool) $isRenewing,
            'is_cancelled' => (bool) $isCanceled || (bool) $cancelAtPeriodEnd,
            'cancel_at_period_end' => (bool) $cancelAtPeriodEnd,
            'cycle_number' => $sub->cycle_number ?? null,
            'start_date' => $startsAt ? $startsAt->toIso8601String() : null,
            'current_period_start' => $periodStart ? $periodStart->toIso8601String() : null,
            'current_period_end' => $periodEnd ? $periodEnd->toIso8601String() : null,
            'ends_at' => $endsAt ? $endsAt->toIso8601String() : null,
            'amount_raw' => $amountRaw,
            'amount' => $amountHuman,
            'metadata' => $sub->metadata ?? null,
            'created_at' => $sub->created_at ? $sub->created_at->toIso8601String() : null,
            'updated_at' => $sub->updated_at ? $sub->updated_at->toIso8601String() : null,
        ];
    }

    /**
     * Try to resolve user model if ID passed.
     *
     * @param int|User $user
     * @return User|null
     */
    protected function resolveUser($user): ?User
    {
        if ($user instanceof User) {
            return $user;
        }
        if (is_int($user) || ctype_digit((string) $user)) {
            return User::find((int) $user);
        }
        return null;
    }

    /**
     * Convert value to Carbon instance safely or null.
     *
     * @param mixed $value
     * @return Carbon|null
     */
    protected function toCarbonSafe($value): ?Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }
        if (is_null($value)) {
            return null;
        }
        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Simple money formatter. Expects price in major units (e.g. rupees/dollars).
     * If your Plan.price is in cents, adjust accordingly.
     *
     * @param float|int|null $amount
     * @param string|null $currency
     * @return string|null
     */
    protected function formatMoney($amount, ?string $currency = null): ?string
    {
        if (is_null($amount)) {
            return null;
        }

        $currency = strtoupper($currency ?? Config::get('app.currency', 'INR'));
        // Present with two decimals and thousands separators
        $formatted = number_format((float) $amount, 2, '.', ',');

        return "{$currency} {$formatted}";
    }
}
