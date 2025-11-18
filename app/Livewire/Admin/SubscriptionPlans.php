<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Plan;
use App\Services\Admin\AdminPlanManagementService;

class SubscriptionPlans extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    // Form Properties
    public $plan_id;
    public $name, $price, $credits_per_cycle, $subtitle, $description, $features;
    public $status = 'active'; // Default
    public $is_popular = 0;    // Default
    public $icon_link;
    public $icon_path;         // For the new upload (TemporaryUploadedFile)
    public $existing_icon_url; // To show current image in edit mode

    // Validation Rules
    protected function rules()
    {
        return [
            'name' => 'required|min:3|max:255',
            'price' => 'required|numeric|min:0',
            'credits_per_cycle' => 'required|integer|min:0',
            'is_popular' => 'required|boolean',
            'status' => 'required|in:active,inactive',
            'subtitle' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'features' => 'nullable|string', // We pass string to service, service converts to array
            'icon_link' => 'nullable|url',
            'icon_path' => 'nullable|image|max:2048', 
        ];
    }

    public function resetInputFields()
    {
        $this->reset([
            'plan_id', 'name', 'price', 'credits_per_cycle', 'subtitle', 
            'description', 'features', 'status', 'is_popular', 
            'icon_link', 'icon_path', 'existing_icon_url'
        ]);
        $this->resetErrorBag();
    }

    // --- CREATE ---
    public function create()
    {
        $this->resetInputFields();
        $this->dispatch('open-modal', name: 'add_subscription_plan');
    }

    public function store(AdminPlanManagementService $service)
    {
        // 1. Validate inputs
        $validatedData = $this->validate();

        // 2. Pass to Service (Service handles file upload & array conversion)
        $service->store($validatedData);

        // 3. UI Feedback
        $this->dispatch('close-modal', name: 'add_subscription_plan');
        $this->dispatch('alert', type: 'success', message: 'Plan created successfully!');
        $this->resetInputFields();
    }

    // --- EDIT ---
    public function edit($id)
    {
        $this->resetInputFields();
        $plan = Plan::findOrFail($id);

        $this->plan_id = $plan->id;
        $this->name = $plan->name;
        $this->price = $plan->price;
        $this->credits_per_cycle = $plan->credits_per_cycle;
        $this->subtitle = $plan->subtitle;
        $this->description = $plan->description;
        $this->status = $plan->status;
        $this->is_popular = $plan->is_popular;
        $this->icon_link = $plan->icon_link;
        
        // Convert array back to string for textarea display
        $this->features = is_array($plan->features) ? implode("\n", $plan->features) : $plan->features;
        
        // Handle Image Preview
        $this->existing_icon_url = $plan->icon_link;

        $this->dispatch('open-modal', name: 'edit_subscription_plan');
    }

    public function update(AdminPlanManagementService $service)
    {
        // 1. Validate inputs
        $validatedData = $this->validate();

        // 2. Get Plan
        $plan = Plan::findOrFail($this->plan_id);

        // 3. Pass to Service (Service handles file upload logic & updates)
        // Note: We pass $this->icon_path. If it's null, the service checks that. 
        // If it's a file, the service uploads it.
        $service->update($plan, $validatedData);

        // 4. UI Feedback
        $this->dispatch('close-modal', name: 'edit_subscription_plan');
        $this->dispatch('alert', type: 'success', message: 'Plan updated successfully!');
        $this->resetInputFields();
    }

    // --- DELETE ---
    public function deleteConfirmation($id)
    {
        $this->plan_id = $id;
        $this->dispatch('open-modal', name: 'delete_modal');
    }

    public function destroy(AdminPlanManagementService $service)
    {
        $plan = Plan::findOrFail($this->plan_id);
        
        // Service handles deleting the image from storage
        $service->destroy($plan);

        $this->dispatch('close-modal', name: 'delete_modal');
        $this->dispatch('alert', type: 'success', message: 'Plan deleted successfully!');
    }

    public function render(AdminPlanManagementService $service)
    {
        // Use service to get list (pagination works automatically)
        return view('livewire.admin.subscription-plans', [
            'plans' => $service->list(10)
        ]);
    }
}