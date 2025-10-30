<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Services\StripeWebhookService;
use App\Services\CreditService;
use App\Services\SubscriptionService;
use App\Exceptions\WebhookException;

class StripeWebhookController extends Controller
{
    private StripeWebhookService $webhookService;
    private CreditService $creditService;
    private SubscriptionService $subscriptionService;

    public function __construct(
        StripeWebhookService $webhookService,
        CreditService $creditService,
        SubscriptionService $subscriptionService
    ) {
        $this->webhookService = $webhookService;
        $this->creditService = $creditService;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Main webhook entry point with comprehensive error handling and idempotency
     */
    public function handle(Request $request)
    {
        $startTime = microtime(true);
        
        try {
            // Validate webhook signature and payload
            $event = $this->webhookService->validateWebhook($request);
            
            // Idempotency check - skip if already processed
            if ($this->webhookService->isEventProcessed($event->id)) {
                Log::info('Webhook event already processed', [
                    'event_id' => $event->id,
                    'type' => $event->type,
                    'processing_time_ms' => round((microtime(true) - $startTime) * 1000, 2)
                ]);
                
                return response()->json(['status' => 'ignored_already_processed']);
            }

            // Record event for idempotency before processing
            $this->webhookService->recordEvent($event);

            // Process the event
            $this->routeEventToHandler($event);

            Log::info('Webhook processed successfully', [
                'event_id' => $event->id,
                'type' => $event->type,
                'processing_time_ms' => round((microtime(true) - $startTime) * 1000, 2)
            ]);

            return response()->json(['status' => 'success']);

        } catch (WebhookException $e) {
            Log::warning('Webhook validation failed', [
                'error' => $e->getMessage(),
                'event_type' => $e->getEventType() ?? 'unknown',
                'processing_time_ms' => round((microtime(true) - $startTime) * 1000, 2)
            ]);

            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());

        } catch (\Throwable $e) {
            Log::error('Webhook processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processing_time_ms' => round((microtime(true) - $startTime) * 1000, 2)
            ]);

            // Don't expose internal errors to Stripe
            return response()->json(['error' => 'processing_failed'], 500);
        }
    }

    /**
     * Route events to appropriate handlers with circuit breaker pattern
     */
    private function routeEventToHandler($event): void
    {
        $handlerMap = [
            'invoice.paid' => 'handleInvoicePaymentSucceeded',
            'invoice.payment_succeeded' => 'handleInvoicePaymentSucceeded',
            'invoice.payment_failed' => 'handleInvoicePaymentFailed',
            'checkout.session.completed' => 'handleCheckoutSessionCompleted',
            'customer.subscription.created' => 'handleSubscriptionCreated',
            'customer.subscription.updated' => 'handleSubscriptionUpdated',
            'customer.subscription.deleted' => 'handleSubscriptionDeleted',
            'payment_intent.succeeded' => 'handlePaymentIntentSucceeded',
            'payment_intent.payment_failed' => 'handlePaymentIntentFailed',
        ];

        $eventType = $event->type;
        $handlerMethod = $handlerMap[$eventType] ?? null;

        if (!$handlerMethod) {
            Log::debug('Unhandled webhook event type', ['type' => $eventType]);
            return;
        }

        // Circuit breaker - prevent repeated failures from overwhelming the system
        $circuitBreakerKey = "webhook_handler_{$handlerMethod}";
        if (Cache::get($circuitBreakerKey)) {
            Log::warning('Circuit breaker active for handler', [
                'handler' => $handlerMethod,
                'event_type' => $eventType
            ]);
            throw new \Exception("Handler temporarily disabled");
        }

        try {
            $this->{$handlerMethod}($event->data->object);
        } catch (\Throwable $e) {
            // Track failures for circuit breaker
            $this->trackHandlerFailure($circuitBreakerKey);
            throw $e;
        }
    }

    /**
     * Circuit breaker failure tracking
     */
    private function trackHandlerFailure(string $circuitBreakerKey): void
    {
        $failureCount = Cache::get("{$circuitBreakerKey}_failures", 0) + 1;
        Cache::put("{$circuitBreakerKey}_failures", $failureCount, 300); // 5 minutes

        // Open circuit after 5 failures in 5 minutes
        if ($failureCount >= 5) {
            Cache::put($circuitBreakerKey, true, 60); // Open circuit for 1 minute
            Log::alert('Circuit breaker opened for handler', ['handler' => $circuitBreakerKey]);
        }
    }

    /* -------------------------
     * Event Handlers
     * ------------------------- */

    private function handleCheckoutSessionCompleted($session): void
    {
        DB::transaction(function () use ($session) {
            $metadata = $session->metadata ?? [];
            $userId = $metadata['local_user_id'] ?? null;
            $planSlug = $metadata['local_plan_slug'] ?? null;

            if (!$userId) {
                throw new \InvalidArgumentException('Missing local_user_id in session metadata');
            }

            $user = $this->webhookService->findUser($userId);
            $plan = $planSlug ? $this->webhookService->findPlanBySlug($planSlug) : null;

            // One-time payment
            if (($session->mode ?? '') === 'payment') {
                $credits = $plan ? (int)$plan->credits_per_cycle : 1;
                $this->creditService->issueCredits(
                    $user, 
                    $credits, 
                    'checkout_payment', 
                    $session->id,
                    $plan
                );
                return;
            }

            // Subscription - create pending record
            if (!empty($session->subscription)) {
                $this->subscriptionService->createOrUpdateSubscription(
                    $user->id,
                    $session->subscription,
                    $session->customer ?? $user->stripe_id,
                    $plan?->id,
                    'pending'
                );
            }
        });
    }

    private function handleInvoicePaymentSucceeded($invoice): void
    {
        DB::transaction(function () use ($invoice) {
            $customerId = $invoice->customer ?? null;
            $subscriptionId = $invoice->subscription ?? null;
            $invoiceId = $invoice->id ?? null;

            if (!$customerId) {
                throw new \InvalidArgumentException('Missing customer in invoice');
            }

            $user = $this->webhookService->findUserByStripeId($customerId);
            
            // Idempotency checks
            if ($this->creditService->isInvoiceProcessed($invoiceId, $user->id)) {
                Log::info('Invoice already processed', [
                    'invoice_id' => $invoiceId,
                    'user_id' => $user->id
                ]);
                return;
            }

            // Determine plan and credits
            $plan = $this->webhookService->resolvePlanFromInvoice($invoice);
            $credits = $plan ? (int)$plan->credits_per_cycle : 0;

            if ($credits > 0 && $subscriptionId) {
                $this->processSubscriptionInvoice($user, $plan, $credits, $invoice, $subscriptionId);
            }
        });
    }

    private function processSubscriptionInvoice($user, $plan, $credits, $invoice, $subscriptionId): void
    {
        $periodStart = isset($invoice->period_start) 
            ? \Carbon\Carbon::createFromTimestamp($invoice->period_start) 
            : null;
        $periodEnd = isset($invoice->period_end) 
            ? \Carbon\Carbon::createFromTimestamp($invoice->period_end) 
            : null;

        // Update subscription
        $subscription = $this->subscriptionService->updateSubscriptionPeriod(
            $subscriptionId,
            $plan?->id,
            'active',
            $periodStart,
            $periodEnd
        );

        // Issue credits
        $applied = $this->creditService->issueCredits(
            $user,
            $credits,
            'invoice_paid',
            $invoice->id,
            $plan,
            $subscription->cycle_number,
            $subscriptionId
        );

        // Increment cycle if credits applied
        if ($applied) {
            $this->subscriptionService->incrementCycle($subscription->id);
        }
    }

    private function handleInvoicePaymentFailed($invoice): void
    {
        $customerId = $invoice->customer ?? null;
        $subscriptionId = $invoice->subscription ?? null;

        if (!$customerId) {
            throw new \InvalidArgumentException('Missing customer in invoice');
        }

        $user = $this->webhookService->findUserByStripeId($customerId);

        if ($subscriptionId) {
            $this->subscriptionService->updateSubscriptionStatus($subscriptionId, 'past_due');
        }

        Log::info('Invoice payment failed processed', [
            'user_id' => $user->id,
            'invoice_id' => $invoice->id ?? null,
            'subscription_id' => $subscriptionId
        ]);
    }

    private function handleSubscriptionCreated($subscription): void
    {
        $this->subscriptionService->syncSubscriptionFromStripe($subscription);
    }

    private function handleSubscriptionUpdated($subscription): void
    {
        $this->subscriptionService->syncSubscriptionFromStripe($subscription);
    }

    private function handleSubscriptionDeleted($subscription): void
    {
        $customerId = $subscription->customer ?? null;
        $subscriptionId = $subscription->id ?? null;

        if (!$customerId) {
            throw new \InvalidArgumentException('Missing customer in subscription');
        }

        $user = $this->webhookService->findUserByStripeId($customerId);
        $this->subscriptionService->cancelSubscription($subscriptionId);
    }

    private function handlePaymentIntentSucceeded($intent): void
    {
        $customerId = $intent->customer ?? null;
        if (!$customerId) return;

        $user = $this->webhookService->findUserByStripeId($customerId);
        $metadata = $intent->metadata ?? [];
        $planSlug = $metadata['local_plan_slug'] ?? null;

        if ($planSlug) {
            $plan = $this->webhookService->findPlanBySlug($planSlug);
            $credits = $plan ? (int)$plan->credits_per_cycle : 0;

            if ($credits > 0) {
                $this->creditService->issueCredits(
                    $user,
                    $credits,
                    'payment_intent',
                    $intent->id,
                    $plan
                );
            }
        }
    }

    private function handlePaymentIntentFailed($intent): void
    {
        $customerId = $intent->customer ?? null;
        if (!$customerId) return;

        $user = $this->webhookService->findUserByStripeId($customerId);

        if (isset($intent->invoice)) {
            $this->subscriptionService->markSubscriptionsPastDue($customerId);
        }

        Log::info('Payment intent failed', [
            'user_id' => $user->id,
            'intent_id' => $intent->id ?? null
        ]);
    }
}