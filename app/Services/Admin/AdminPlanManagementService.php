<?php

namespace App\Services\Admin;

use App\Models\{Plan, Subscription};
use Illuminate\Database\Eloquent\Collection;
use App\Services\StripeService;
use Illuminate\Support\Facades\{Log};
use Illuminate\Support\{Str};
/**
 * Service class for managing subscription plans in the admin panel.
 */
class AdminPlanManagementService
{   

    protected StripeService $stripe;


    public function __construct(StripeService $stripe)
    {
        $this->stripe = $stripe;
    }



    public function list()
    {
        // Fetch all plans, ordered by the most recently created
        // You can change this to any order you prefer (e.g., orderBy('price', 'asc'))
        return Plan::latest()->paginate(10);
    }

    // You can add other methods here in the future, like:
    
    public function store(array $data): Plan
    {
        // 1. Process Data
        $data = $this->prepareData($data);

        // 2. Generate Unique Slug
        $data['slug'] = $this->generateUniqueSlug($data['name']);
        $slug = $data['slug'];

        // 3. Prepare Payload for Stripe
        $payload = [
            'name'        => $data['name'],
            'description' => $data['description'] ?? '',
            'amount'      => (int) $data['price'], 
            'currency'    => 'jpy',
            'interval'    => $data['interval'] ?? 'monthly',
            'slug'        => $slug,
            'metadata'    => [
                'subtitle'        => $data['subtitle'] ?? '',
                'local_plan_slug' => $slug,
                'features'        => json_encode($data['features']),
            ],
        ];
        // 4. Call Stripe API (assuming $this->stripe is set up)
            $stripeResponse = $this->stripe->createPlan($payload);

            if ($stripeResponse) {
                $data['stripe_product_id'] = $stripeResponse['product_id'] ?? null; 
                $data['stripe_price_id']   = $stripeResponse['price_id'] ?? null;      
            }

        // 5. Handle Icon Upload
        if (isset($data['icon_path']) && $data['icon_path'] instanceof UploadedFile) {
            $data['icon_path'] = uploadFile($data['icon_path'], 'plan_icons');
        }

        // 6. Create Local Record
        return Plan::create($data);
    }

    
    public function update(Plan $plan, array $data): Plan
    {
        // 1. Process Data (Convert features string to array)
        $data = $this->prepareData($data);
        $slug = $plan->slug;
        $payload = [
                'name' => $data['name'],
                'description' => $data['description'],
                'amount' => (int) $data['price'], // JPY full yen
                'currency' => 'jpy',
                'interval' => $plan['interval'] ?? 'monthly',
                'slug' => $slug,
                'metadata' => [
                    'subtitle' => $data['subtitle'],
                    'local_plan_slug' => $slug,
                    'features' => json_encode($data['features']),
                ],
            ];
        
        // 2. Handle Icon Upload
        if (isset($data['icon_path']) && $data['icon_path'] instanceof UploadedFile) {
            // Pass the new file, the folder, and the OLD path from the model
            $data['icon_path'] = uploadFile(
                $data['icon_path'], 
                'plan_icons', 
                $plan->icon_path
            );
        }

         $stripeResponse = $this->stripe->createPlan($payload);

        if ($stripeResponse) {
            $data['stripe_product_id'] = $stripeResponse['product_id'] ?? null; 
            $data['stripe_price_id']   = $stripeResponse['price_id'] ?? null;      
        }
        // 3. Update Plan
        $plan->update($data);
        $this->syncSubscriptionPrice($plan->id);
        return $plan;
    }

    public function destroy(Plan $plan): bool
    {
        if ($plan->icon_path && Storage::disk('public')->exists($plan->icon_path)) {
            Storage::disk('public')->delete($plan->icon_path);
        }
        return $plan->delete();
    }


     private function prepareData(array $data): array
    {
        // Convert new-line separated features string into an array
        if (isset($data['features']) && is_string($data['features'])) {
            // Split by any type of newline
            $featuresArray = preg_split('/\r\n|\r|\n/', $data['features']);
            // Trim whitespace and remove empty lines
            $data['features'] = array_values(array_filter(array_map('trim', $featuresArray)));
        }

        return $data;
    }

    /**
     * Generate a unique slug based on the plan name.
     */
    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        
        // Fallback if slug is empty (e.g. name was only symbols)
        if (empty($slug)) {
            $slug = 'plan-' . time();
        }

        $originalSlug = $slug;
        $count = 1;

        // Loop until a unique slug is found
        while (Plan::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        return $slug;
    }

    private function syncSubscriptionPrice($planId){
        $subscriptions = Subscription::query()
        ->active() // Use your scopeActive
        ->where('plan_id', $planId)
        ->with('plan') // Eager load the plan
        ->cursor();

        foreach ($subscriptions as $sub) {
            try {
                $this->stripe->syncSubscriptionPrice($sub);
            } catch (\Exception $e) {
            Log::error("Failed to update Sub ID {$sub->id}: " . $e->getMessage());
            }
            
        }
        return true;
    }

}