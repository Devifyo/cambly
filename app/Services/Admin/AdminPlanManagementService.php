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

    /**
     * public function updatePlan(Plan $plan, array $data)
     * {
     * // Logic to update an existing plan
     * }
     */

    /**
     * public function deletePlan(Plan $plan)
     * {
     * // Logic to delete a plan
     * }
     */
}