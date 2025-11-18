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
        dd($request->all());
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
        $id = decryptId($id);

        $plan = Plan::findOrFail($id);

        if( !$plan){

             return back()->with('error', 'Plan not found!');

        }

        // Validate the request...

        $validatedData = $request->validate([

            'name'              => 'required|max:255',
            'price'             => 'required|min:0',
            'credits_per_cycle' => 'required|min:0',
            'subtitle'          => 'nullable|max:255',
            'description'       => 'nullable',
            'features'          => 'nullable', // Accepts the string from textarea
            'is_popular'        => 'required',
            'icon_path'         => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
            // 'interval'       => 'required|string|in:month,year', 
            'status'            => 'required|string|in:active,inactive',

        ]);

        $this->planService->update($plan, $validatedData);
        return back()->with('success', 'Plan updated successfully!');

    }


    public function destroy($id)
    {
        $plan = Plan::findOrFail($id);
        $plan->delete();
        return redirect()->back()->with('success', 'Plan moved to trash.');
    }


}
