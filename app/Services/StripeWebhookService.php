<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Plan;
use App\Models\WebhookEvent;
use Stripe\Webhook;
use App\Exceptions\WebhookException;

class StripeWebhookService
{
    public function validateWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = $this->getWebhookSecret();

        if (empty($endpointSecret)) {
            throw new WebhookException('Webhook secret not configured', 500);
        }

        try {
            return Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            Log::warning('Stripe webhook invalid payload', ['error' => $e->getMessage()]);
            throw new WebhookException('Invalid payload', 400, $e);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::warning('Stripe webhook invalid signature', ['error' => $e->getMessage()]);
            throw new WebhookException('Invalid signature', 400, $e);
        }
    }

    public function isEventProcessed(string $eventId): bool
    {
        // Cache for faster idempotency checks
        if (Cache::has("webhook_event_{$eventId}")) {
            return true;
        }

        return WebhookEvent::where('event_id', $eventId)->exists();
    }

    public function recordEvent($event): void
    {
        try {
            WebhookEvent::create([
                'event_id' => $event->id,
                'type' => $event->type,
                'payload' => json_encode($event),
                'processed_at' => now(),
            ]);

            // Cache for 24 hours for fast idempotency checks
            Cache::put("webhook_event_{$event->id}", true, 86400);
            
        } catch (\Throwable $e) {
            Log::error('Failed to record webhook event', [
                'event_id' => $event->id,
                'error' => $e->getMessage()
            ]);
            // Don't throw - we can still process the event
        }
    }

    public function findUser($userId): User
    {
        $user = User::find($userId);
        if (!$user) {
            throw new \InvalidArgumentException("User not found: {$userId}");
        }
        return $user;
    }

    public function findUserByStripeId(string $stripeId): User
    {
        $user = User::where('stripe_id', $stripeId)->first();
        if (!$user) {
            throw new \InvalidArgumentException("User not found for Stripe ID: {$stripeId}");
        }
        return $user;
    }

    public function findPlanBySlug(string $slug): ?Plan
    {
        return Plan::active()->where('slug', $slug)->first();
    }

    public function resolvePlanFromInvoice($invoice): ?Plan
    {
        // Try to get plan from invoice lines
        $lines = $invoice->lines->data ?? [];
        if (count($lines) > 0) {
            $firstLine = $lines[0];
            $price = $firstLine->price ?? null;
            $productId = $price->product ?? null;

            if ($productId) {
                $plan = Plan::active()->where('stripe_product_id', $productId)->first();
                if ($plan) {
                    return $plan;
                }
            }
        }

        // Fallback to metadata
        if (!empty($invoice->metadata->local_plan_slug)) {
            return $this->findPlanBySlug($invoice->metadata->local_plan_slug);
        }

        return null;
    }

    private function getWebhookSecret(): string
    {
        return config('cashier.webhook.secret') 
            ?? config('services.stripe.webhook_secret') 
            ?? env('STRIPE_WEBHOOK_SECRET');
    }
}