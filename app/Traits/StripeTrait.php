<?php

namespace App\Traits;

use Stripe\Stripe;
use Illuminate\Support\Str;

trait StripeTrait
{
    /**
     * Initialize Stripe globally using Cashier's configured key.
     */
    protected function initStripe(): void
    {
        $secret = config('cashier.secret');

        if (empty($secret)) {
            throw new \Exception('Stripe API key not found in config(cashier.secret)');
        }

        Stripe::setApiKey($secret);
    }

    /**
     * Generate an idempotency key for safe API calls.
     */
    protected function idempotencyKey(?string $seed = null): string
    {
        $seedPart = $seed ? Str::slug($seed) : (string) now()->timestamp;

        return 'app-' . $seedPart . '-' . md5((string) microtime(true));
    }
}
