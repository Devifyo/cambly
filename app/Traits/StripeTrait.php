<?php

namespace App\Traits;

use Stripe\Stripe;
use Stripe\StripeClient;
use Laravel\Cashier\Cashier;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

trait StripeTrait
{
    /**
     * Initialize Stripe globally using Cashier's configured key.
     *
     * Keeps backward compatibility with code that calls Stripe::setApiKey(...)
     */
    protected function initStripe(): void
    {
        $secret = config('cashier.secret') ?? config('services.stripe.secret') ?? env('STRIPE_SECRET');

        if (empty($secret)) {
            Log::error('Stripe API key not found in config(cashier.secret) or services.stripe.secret or STRIPE_SECRET.');
            throw new \Exception('Stripe API key not found in config(cashier.secret)');
        }

        // set global key for static calls (Product::create(), Price::create(), etc.)
        Stripe::setApiKey($secret);
    }

    /**
     * Return a StripeClient instance.
     *
     * Uses Cashier::stripe() when available; otherwise constructs a StripeClient from config.
     *
     * @return \Stripe\StripeClient
     */
    protected function stripe(): StripeClient
    {
        // If Cashier is installed, prefer its client
        try {
            if (class_exists(Cashier::class)) {
                $client = Cashier::stripe();
                if ($client instanceof StripeClient) {
                    return $client;
                }
            }
        } catch (\Throwable $e) {
            // Log and fallback
            Log::debug('Cashier::stripe() not usable, falling back to StripeClient: '.$e->getMessage());
        }

        // Fallback: create a new client from config
        $secret = config('cashier.secret') ?? config('services.stripe.secret') ?? env('STRIPE_SECRET');

        if (empty($secret)) {
            Log::error('Stripe API key not found for constructing StripeClient.');
            throw new \RuntimeException('Stripe secret key not configured.');
        }

        return new StripeClient($secret);
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
