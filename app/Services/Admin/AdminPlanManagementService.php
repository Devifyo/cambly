<?php

namespace App\Services\Admin;

use App\Models\Plan;
use Illuminate\Database\Eloquent\Collection;

/**
 * Service class for managing subscription plans in the admin panel.
 */
class AdminPlanManagementService
{
    /**
     * Fetch a list of all subscription plans.
     *
     * This method is used to get the plans for the admin index page.
     * We order by the newest first by default.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function list()
    {
        // Fetch all plans, ordered by the most recently created
        // You can change this to any order you prefer (e.g., orderBy('price', 'asc'))
        return Plan::latest()->paginate(10);
    }

    // You can add other methods here in the future, like:
    
    /**
     * public function createPlan(array $data)
     * {
     * // Logic to create a new plan
     * }
     */

    
    public function update(Plan $plan, array $data): Plan
    {
        // 1. Process Data (Convert features string to array)
        $data = $this->prepareData($data);

        // 2. Handle Icon Upload
        if (isset($data['icon_path']) && $data['icon_path'] instanceof UploadedFile) {
            // Pass the new file, the folder, and the OLD path from the model
            $data['icon_path'] = uploadFile(
                $data['icon_path'], 
                'plan_icons', 
                $plan->icon_path
            );
        }
        // 3. Update Plan
        $plan->update($data);

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
}