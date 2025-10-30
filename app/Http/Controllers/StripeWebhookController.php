<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Plan;
use App\Models\CreditTransaction; // optional - see notes
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Stripe\Webhook;
use Stripe\Checkout\Session;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('cashier.webhook.secret'); // set in .env

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle important events
        switch ($event->type) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event->data->object);
                break;

            case 'invoice.payment_succeeded':
                $this->handleInvoicePaymentSucceeded($event->data->object);
                break;

            case 'customer.subscription.deleted':
                // handle cancellations if you want
                break;

            // add more events as needed
            default:
                Log::info('Unhandled stripe event: '.$event->type);
        }

        return response()->json(['status' => 'ok']);
    }

    protected function handleCheckoutSessionCompleted($session)
    {
        // $session is a Stripe Checkout Session object
        // For one-time payments, session.mode === 'payment'
        // For subscriptions, session.mode === 'subscription'
        try {
            $metadata = $session->metadata ?? [];
            $localUserId = $metadata['local_user_id'] ?? null;
            $localPlanSlug = $metadata['local_plan_slug'] ?? null;

            if (! $localUserId) {
                Log::warning('Checkout session completed with no local_user_id metadata.');
                return;
            }

            $user = User::find($localUserId);
            if (! $user) return;

            // If one-time trial: grant one credit and set user trial status if you want
            if (($session->mode ?? '') === 'payment') {
                // For trial or single payments: grant credits immediately
                $plan = Plan::where('slug', $localPlanSlug)->first();
                $credits = $plan ? (int)$plan->credits_per_cycle : 1;

                $this->grantCreditsToUser($user, $credits, 'trial', $session->id);
                // mark user's subscription state if applicable (e.g., trial_active)
                $user->update(['subscription_status' => 'trial']);
            } else {
                // mode subscription: a subscription was created; wait for invoice.payment_succeeded to issue credits
                // Optionally, you can attach subscription id in your DB for reference
                // $session->subscription contains stripe subscription id
                // Save mapping if you want
                // e.g., $user->update(['stripe_subscription_id' => $session->subscription]);
            }
        } catch (\Exception $e) {
            Log::error('Error handling checkout.session.completed: '.$e->getMessage());
        }
    }

    protected function handleInvoicePaymentSucceeded($invoice)
    {
        // Called whenever a subscription invoice is paid (including initial payment for subscription)
        // invoice contains customer, subscription, lines, etc.
        try {
            $customerId = $invoice->customer;
            // We stored user stripe id as stripe_id on user model via Cashier
            $user = \App\Models\User::where('stripe_id', $customerId)->first();
            if (! $user) {
                Log::warning('invoice.payment_succeeded: unknown customer '.$customerId);
                return;
            }

            // Determine plan: try to read invoice lines -> price -> product metadata
            $lines = $invoice->lines->data;
            if (count($lines) > 0) {
                $firstLine = $lines[0];
                $price = $firstLine->price ?? null;
                $productId = $price->product ?? null;

                // Try mapping using local Plan: we saved stripe_product_id earlier
                $plan = Plan::where('stripe_product_id', $productId)->first();

                // Credits to issue for this cycle:
                $credits = $plan ? (int)$plan->credits_per_cycle : 0;

                // Safety: if multiple contracts per user allowed (multi-subscriptions), calculate accordingly.
                // For MVP: assume one subscription per plan per user.
                if ($credits > 0) {
                    $this->grantCreditsToUser($user, $credits, 'billing_cycle', $invoice->id);
                    // set user's subscription state to active
                    $user->update(['subscription_status' => 'active']);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error in invoice.payment_succeeded: '.$e->getMessage());
        }
    }

    protected function grantCreditsToUser($user, int $credits, string $reason = 'manual', $reference = null)
    {
        // Implement according to your DB model; below is a simple example.
        // You likely want to track by cycle, e.g. credits_available and credits_used per cycle.

        DB::transaction(function () use ($user, $credits, $reason, $reference) {
            // Example: increment credits_available column
            if (! isset($user->credits_available)) {
                // ensure users table has credits_available int column; otherwise adapt accordingly
                $user->credits_available = 0;
            }
            $user->credits_available += $credits;
            $user->save();

            // record transaction (optional)
            if (class_exists(\App\Models\CreditTransaction::class)) {
                \App\Models\CreditTransaction::create([
                    'user_id' => $user->id,
                    'credits' => $credits,
                    'reason' => $reason,
                    'reference' => $reference,
                ]);
            }
        });
    }
}
