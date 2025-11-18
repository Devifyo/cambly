<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Admin\AdminPlanManagementService; // Import the new service
use App\Models\Plan; // Import the model
class AdminManagePlansController extends Controller
{   
    protected  $view_path = 'admin.subscription-plan.';

   /**
     * @var AdminPlanManagementService
     */
    protected $planService;

    /**
     * Inject the service into the controller.
     *
     * @param AdminPlanManagementService $planService
     */
    public function __construct(AdminPlanManagementService $planService)
    {
        $this->planService = $planService;
    }

    public function index()
    {
        $plans = $this->planService->list();    
        return view($this->view_path.'index', compact('plans'));
    }


    public function store(Request $request)
    {
        // Validate the request... (add your validation rules)
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|string|in:Monthly,Yearly',
            'status' => 'required|string|in:Active,Inactive',
            'features' => 'nullable|string',
        ]);

        // Use the Plan model to create
        Plan::create($validatedData);

        return back()->with('success', 'Plan created successfully!');
    }

    public function update(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);

        // Validate the request...
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|string|in:Monthly,Yearly',
            'status' => 'required|string|in:Active,Inactive',
            'features' => 'nullable|string',
        ]);

        $plan->update($validatedData);

        return back()->with('success', 'Plan updated successfully!');
    }


    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();
        return redirect()->back()->with('success', 'Plan moved to trash.');
    }


}
