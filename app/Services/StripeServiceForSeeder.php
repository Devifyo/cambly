<?php

namespace App\Services;

use Stripe\Exception\ApiErrorException;
use Stripe\Price;
use Stripe\Product;
use Stripe\StripeClient;
use App\Traits\StripeTrait;

class StripeServiceForSeeder
{
    use StripeTrait;

    /**
     * Create or reuse a Stripe product + price.
     *
     * Accepts either an array payload:
     * [
     *   'name' => '', 'description' => '', 'amount' => 4500,
     *   'currency' => 'jpy', 'interval' => 'monthly'|'yearly'|'one_time',
     *   'slug' => 'basic-monthly', 'metadata' => []
     * ]
     *
     * Or the legacy signature: createPlan($name, $description, $amount, $currency = 'jpy', $interval = 'month', $slug = null)
     *
     * @param  array|string  $params
     * @param  mixed|null    $maybeDescription
     * @param  mixed|null    $maybeAmount
     * @return array
     */
    public function createPlan($params, $maybeDescription = null, $maybeAmount = null): array
    {
        // normalize input
        if (is_array($params)) {
            $data = $params;
        } else {
            $data = [
                'name' => (string) $params,
                'description' => $maybeDescription ?? '',
                'amount' => (int) ($maybeAmount ?? 0),
                'currency' => 'jpy',
                'interval' => 'monthly',
                'slug' => null,
                'metadata' => [],
            ];
        }

        // normalize fields
        $name = trim($data['name'] ?? '');
        $description = $data['description'] ?? '';
        $amount = (int) ($data['amount'] ?? 0); // JPY full yen
        $currency = $data['currency'] ?? 'jpy';
        $interval = $data['interval'] ?? 'monthly';
        $slug = $data['slug'] ?? null;
        $metadata = $data['metadata'] ?? [];

        // map app interval to stripe recurring interval (or null for one-time)
        $stripeInterval = null;
        if ($interval && $interval !== 'one_time') {
            $it = strtolower($interval);
            $stripeInterval = str_starts_with($it, 'month') ? 'month' : (str_starts_with($it, 'year') ? 'year' : $it);
        }

        // initialize global Stripe API key (via trait)
        $this->initStripe();

        // stripe client for listing/searching (uses same secret)
        $client = new StripeClient(config('cashier.secret'));

        try {
            // 1) find or create product
            $product = $this->findProductBySlugOrNameUsingClient($client, $slug, $name);

            if (! $product) {
                $productPayload = [
                    'name' => $name,
                    'description' => $description,
                    'metadata' => $metadata,
                ];

                if ($slug) {
                    $productPayload['metadata']['slug'] = $slug;
                }

                // use static create (relies on Stripe::setApiKey)
                $product = Product::create(
                    $productPayload,
                    ['idempotency_key' => $this->idempotencyKey(($slug ?: $name) . '-product')]
                );
                $createdProduct = true;
            } else {
                $createdProduct = false;
            }

            // 2) find existing matching price for product
            $existingPrice = $this->findPriceForProductUsingClient($client, $product->id, $amount, $currency, $interval);

            if ($existingPrice) {
                return [
                    'product_id' => $product->id,
                    'price_id' => $existingPrice->id,
                    'created' => false,
                ];
            }

            // 3) prepare price payload
            $pricePayload = [
                'unit_amount' => $amount,
                'currency' => $currency,
                'product' => $product->id,
                'metadata' => array_merge(['created_by' => 'app'], (array) $metadata),
            ];

            if ($stripeInterval !== null) {
                $pricePayload['recurring'] = ['interval' => $stripeInterval];
            }

            // use static Price::create (relies on Stripe::setApiKey)
            $price = Price::create($pricePayload, ['idempotency_key' => $this->idempotencyKey(($slug ?: $name) . '-price')]);

            return [
                'product_id' => $product->id,
                'price_id' => $price->id,
                'created' => true,
            ];
        } catch (ApiErrorException $e) {
            logger()->error('Stripe createPlan ApiError: ' . $e->getMessage(), ['params' => $data]);
            return ['error' => $e->getMessage()];
        } catch (\Exception $e) {
            logger()->error('Stripe createPlan error: ' . $e->getMessage(), ['params' => $data]);
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Find product by slug (metadata) or name using provided StripeClient.
     *
     * @param \Stripe\StripeClient $client
     * @param string|null $slug
     * @param string|null $name
     * @return \Stripe\Product|null
     */
    protected function findProductBySlugOrNameUsingClient(StripeClient $client, ?string $slug, ?string $name)
    {
        if ($slug) {
            $products = $client->products->all(['limit' => 100]);
            foreach ($products->autoPagingIterator() as $p) {
                if (! empty($p->metadata) && isset($p->metadata['slug']) && $p->metadata['slug'] === $slug) {
                    return $p;
                }
            }
        }

        if ($name) {
            $products = $client->products->all(['limit' => 100]);
            foreach ($products->autoPagingIterator() as $p) {
                if (isset($p->name) && trim($p->name) === trim($name)) {
                    return $p;
                }
            }
        }

        return null;
    }

    /**
     * Find an existing Price for a product matching amount/currency/interval using provided StripeClient.
     *
     * @param \Stripe\StripeClient $client
     * @param string $productId
     * @param int $unitAmount
     * @param string $currency
     * @param string|null $interval
     * @return \Stripe\Price|null
     */
    protected function findPriceForProductUsingClient(StripeClient $client, string $productId, int $unitAmount, string $currency, ?string $interval)
    {
        $prices = $client->prices->all(['product' => $productId, 'limit' => 100]);

        foreach ($prices->autoPagingIterator() as $pr) {
            if (((int) $pr->unit_amount) === ((int) $unitAmount) && strtolower($pr->currency) === strtolower($currency)) {
                if ($interval === null || $interval === 'one_time') {
                    if (empty($pr->recurring)) {
                        return $pr;
                    }
                } else {
                    $wanted = (strtolower($interval) === 'monthly' ? 'month' : (strtolower($interval) === 'yearly' ? 'year' : strtolower($interval)));
                    if (! empty($pr->recurring) && isset($pr->recurring->interval) && $pr->recurring->interval === $wanted) {
                        return $pr;
                    }
                }
            }
        }

        return null;
    }
    
}
