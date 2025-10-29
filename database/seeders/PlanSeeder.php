<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use App\Services\StripeServiceForSeeder;
use Illuminate\Support\Str;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $stripe = new StripeServiceForSeeder();

        $plans = [
            [
                'name' => 'Trial',
                'subtitle' => 'Try your first lesson for only ¥100',
                'description' => '1 credit for ¥100 — one-time trial plan.',
                'price' => 100,
                'credits_per_cycle' => 1,
                'interval' => 'one_time',
                'slug' => 'trial_one_time',
                'features' => [
                    '1 credit (one 25-minute lesson)',
                    'Book from teacher calendar',
                    'Email confirmation & reminder',
                    'Discord call with your teacher',
                ],
                'is_popular' => false,
                'icon_path' => null,
            ],
            [
                'name' => 'Basic',
                'subtitle' => 'Perfect for casual learners',
                'description' => '4 credits per month ',
                'price' => 4500,
                'credits_per_cycle' => 4,
                'interval' => 'monthly',
                'slug' => 'basic_monthly',

                'features' => [
                    '4 Credits per month',
                    'Book directly from teacher calendar',
                    'Email confirmations & reminders',
                    'Discord call with your teacher',
                ],
                'is_popular' => false,
                'icon_path' => 'assets/img/icons/price-icon1.svg',
            ],
            [
                'name' => 'Premium',
                'subtitle' => 'Ideal for regular learners',
                'description' => '8 credits per month',
                'price' => 8000,
                'credits_per_cycle' => 8,
                'interval' => 'monthly',
                'slug' => 'premium_monthly',
                'features' => [
                    '8 Credits per month',
                    'Priority booking',
                    'Lesson history & reminders',
                    'Priority support',
                ],
                'is_popular' => true,
                'icon_path' => 'assets/img/icons/price-icon2.svg',
            ],
            [
                'name' => 'Enterprise',
                'subtitle' => 'For dedicated and serious learners',
                'description' => '12 credits per month',
                'price' => 9000,
                'credits_per_cycle' => 12,
                'interval' => 'monthly',
                'slug' => 'enterprise_monthly',
                'features' => [
                    '12 Credits per month',
                    'Highest booking limits',
                    'Full lesson history',
                    'Dedicated support',
                ],
                'is_popular' => false,
                'icon_path' => 'assets/img/icons/price-icon3.svg',
            ],
        ];

        foreach ($plans as $plan) {
            // Build the display name that includes interval (for Stripe product name)
            $nameWithInterval = $plan['interval'] === 'one_time'
                ? "{$plan['name']} (One-Time)"
                : "{$plan['name']} {$plan['interval']}";

            // Normalize slug
            $slug = Str::slug($plan['slug']);

            // Find existing local plan (if any) so we can reuse stripe ids when needed
            $existingPlan = Plan::where('slug', $slug)->first();

            // Prepare payload for StripeServiceForSeeder::createPlan (array form)
            $payload = [
                'name' => $nameWithInterval,
                'description' => $plan['description'],
                'amount' => (int) $plan['price'], // JPY full yen
                'currency' => 'jpy',
                'interval' => $plan['interval'],
                'slug' => $slug,
                'metadata' => [
                    'subtitle' => $plan['subtitle'],
                    'local_plan_slug' => $slug,
                    'features' => json_encode($plan['features']),
                ],
            ];

            // Create or reuse product & price in Stripe
            $stripeResult = $stripe->createPlan($payload);

            // Determine stripe ids: prefer returned ids, else keep existing ones
            $stripeProductId = $stripeResult['product_id'] ?? $existingPlan?->stripe_product_id ?? null;
            $stripePriceId   = $stripeResult['price_id']   ?? $existingPlan?->stripe_price_id   ?? null;

            // create or update local DB record by slug
            Plan::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $plan['name'],
                    'slug' => $slug,
                    'interval' => $plan['interval'],
                    'subtitle' => $plan['subtitle'],
                    'description' => $plan['description'],
                    'features' => $plan['features'],
                    'is_popular' => $plan['is_popular'],
                    'icon_path' => $plan['icon_path'],
                    'price' => $plan['price'],
                    'status' => 'active',
                    'credits_per_cycle' => $plan['credits_per_cycle'],
                    'stripe_product_id' => $stripeProductId,
                    'stripe_price_id' => $stripePriceId,
                ]
            );

            // optional: log output so you can inspect results during seeding
            if (isset($stripeResult['error'])) {
                dump("Plan {$slug} seeded locally but Stripe returned error:", $stripeResult['error']);
            } else {
                dump("Plan {$slug} synced with Stripe:", $stripeProductId, $stripePriceId);
            }
        }
    }
}
