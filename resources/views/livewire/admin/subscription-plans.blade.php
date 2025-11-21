<div class="content container-fluid">
    
    {{-- Page Header --}}
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12 d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="page-title">Subscription Plans</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Subscription Plans</li>
                    </ul>
                </div>
                <div>
                    {{-- 1. CREATE BUTTON LOADER --}}
                    <button wire:click="create" wire:loading.attr="disabled" class="btn btn-primary">
                        {{-- Show Plus Icon when NOT loading --}}
                        <i class="fe fe-plus" wire:loading.remove wire:target="create"></i>
                        
                        {{-- Show Spinner when LOADING --}}
                        <span wire:loading wire:target="create" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        
                        {{-- Dynamic Text --}}
                        <span wire:loading.remove wire:target="create">Add New Plan</span>
                        <span wire:loading wire:target="create">Loading...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Plan Name</th>
                                    <th>Price</th>
                                    <th>Tickets</th>
                                    <th>Popular</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($plans as $plan)
                                {{-- @if($loop->iteration == 1)
                                @php
                                $plan->icon_path = null;
                                $plan->save();
                                @endphp
                                @endif --}}
                                    {{-- Debugging output for the 4th plan --}}
                                    <tr>
                                        <td>
                                            @if($plan->icon_path || $plan->icon_link)
                                                <img src="{{ $plan->icon_link }}" alt="icon" width="30" class="ms-2">
                                            @endif
                                            {{ $plan->name }}
                                        </td>
                                        <td>{{ format_currency($plan->price) }}</td>
                                        <td>{{ $plan->credits_per_cycle }}</td>
                                        <td>
                                            @if ($plan->is_popular)
                                                <span class="badge rounded-pill bg-info">Yes</span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (Str::lower($plan->status) == 'active')
                                                <span class="badge rounded-pill bg-success">Active</span>
                                            @else
                                                <span class="badge rounded-pill bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="actions">
                                                {{-- 2. EDIT BUTTON LOADER (Scoped by ID) --}}
                                                <button wire:click="edit({{ $plan->id }})" 
                                                        wire:loading.attr="disabled" 
                                                        class="btn btn-sm bg-success-light me-2">
                                                    
                                                    {{-- Icon visible when NOT loading this specific ID --}}
                                                    <i class="fe fe-pencil" wire:loading.remove wire:target="edit({{ $plan->id }})"></i>
                                                    
                                                    {{-- Spinner visible ONLY when loading this specific ID --}}
                                                    <span wire:loading wire:target="edit({{ $plan->id }})" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                    
                                                    Edit
                                                </button>

                                                {{-- 3. DELETE BUTTON LOADER (Scoped by ID) --}}
                                                <button wire:click="deleteConfirmation({{ $plan->id }})" 
                                                        wire:loading.attr="disabled" 
                                                        class="btn btn-sm bg-danger-light">
                                                    
                                                    <i class="fe fe-trash" wire:loading.remove wire:target="deleteConfirmation({{ $plan->id }})"></i>
                                                    
                                                    <span wire:loading wire:target="deleteConfirmation({{ $plan->id }})" class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                    
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No plans found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $plans->links('components.admin-pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ADD MODAL --}}
    <div wire:ignore.self class="modal fade" id="add_subscription_plan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="store">
                        @include('livewire.admin.partials.plan-form')
                        
                        {{-- 4. SAVE BUTTON LOADER --}}
                        <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled" wire:target="store, icon_path">
                            <span wire:loading.remove wire:target="store">Save</span>
                            <span wire:loading wire:target="store">
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                Saving...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div wire:ignore.self class="modal fade" id="edit_subscription_plan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="update">
                        @include('livewire.admin.partials.plan-form')
                        
                        {{-- 5. UPDATE BUTTON LOADER --}}
                        <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled" wire:target="update, icon_path">
                            <span wire:loading.remove wire:target="update">Update Changes</span>
                            <span wire:loading wire:target="update">
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                Updating...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- DELETE MODAL --}}
    <div wire:ignore.self class="modal fade" id="delete_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-content p-2">
                        <h4 class="modal-title">Delete</h4>
                        <p class="mb-4">Are you sure you want to delete this plan?</p>
                        
                        {{-- 6. DELETE CONFIRMATION LOADER --}}
                        <button type="button" wire:click="destroy" wire:loading.attr="disabled" wire:target="destroy" class="btn btn-primary">
                            <span wire:loading.remove wire:target="destroy">Delete</span>
                            <span wire:loading wire:target="destroy">
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                Deleting...
                            </span>
                        </button>
                        
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>