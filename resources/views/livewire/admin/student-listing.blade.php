<div>
    {{-- Alert Handler Component (Assumed to exist) --}}
    <livewire:admin.components.alert-handler />

    <div class="content container-fluid">
        
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12 d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="page-title">Student Listing</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Students</li>
                        </ul>
                    </div>
                    <div>
                        {{-- CREATE BUTTON LOADER --}}
                        <button wire:click="create" wire:loading.attr="disabled" class="btn btn-primary">
                            <i class="fe fe-plus" wire:loading.remove wire:target="create"></i>
                            <span wire:loading wire:target="create" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            <span wire:loading.remove wire:target="create">Add New Student</span>
                            <span wire:loading wire:target="create">Loading...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search by name or email...">
                    </div>
                    <div class="col-md-2">
                        <select wire:model.live="statusFilter" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select wire:model.live="subscriptionFilter" class="form-control">
                            <option value="">All Students</option>
                            <option value="yes">With Subscription</option>
                            <option value="no">Without Subscription</option>
                        </select>
                    </div>

                    <div class="col-md-3 text-end">
                        <button class="btn btn-secondary" wire:click="resetFilters">Clear Filters</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-center mb-0">
                        <thead>
                            <tr>
                                <th>Profile</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Current Tickets</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($students as $student)
                                <tr>
                                    <td class="text-center">
                                        <img src="{{ $student->profile_link }}" alt="Profile" width="30" class="rounded-circle">
                                    </td>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>
                                        @php
                                            // The helper function is called here
                                            $currentCredits = get_current_month_credits($student, 'show_all');
                                        @endphp
                                        <span class="badge bg-primary">
                                            {{ $currentCredits }}
                                        </span>
                                    </td>
                                    <td>
                                        @if (Str::lower($student->status) == '1')
                                            <span class="badge rounded-pill bg-success">Active</span>
                                        @else
                                            <span class="badge rounded-pill bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="actions">
                                            {{-- 1. CREDIT ADJUSTMENT BUTTON (FIXED TARGETING) --}}
                                            <button wire:click="adjustCreditsModal({{ $student->id }})" 
                                                    wire:loading.attr="disabled" 
                                                    class="btn btn-sm bg-info-light me-2"
                                                    data-bs-toggle="tooltip" title="Adjust Tickets">
                                                <i class="fa-solid fa-dollar-sign" 
                                                   wire:loading.remove 
                                                   wire:target="adjustCreditsModal({{ $student->id }})">
                                                </i>
                                                <span wire:loading 
                                                      wire:target="adjustCreditsModal({{ $student->id }})" 
                                                      class="spinner-border spinner-border-sm" 
                                                      role="status" 
                                                      aria-hidden="true">
                                                </span>
                                            </button>

                                            {{-- 1.5. BOOK CLASS BUTTON (NEW BUTTON) --}}
                                            @if($student->id !== auth()->id() && $student->canBeImpersonated())
                                                <a href="{{ role_route('admin.impersonate', ['id' => encryptId($student->id)]) }}"
                                                    
                                                        class="btn btn-sm bg-primary-light me-2"
                                                        data-bs-toggle="tooltip" title="Book Lesson">
                                                    <i class="fa-solid fa-calendar-plus"> </i>
                                                </a>
                                            @endif

                                            {{-- 2. EDIT BUTTON LOADER (FIXED TARGETING) --}}
                                            <button wire:click="edit({{ $student->id }})" 
                                                    wire:loading.attr="disabled" 
                                                    class="btn btn-sm bg-success-light me-2">
                                                <i class="fe fe-pencil" 
                                                   wire:loading.remove 
                                                   wire:target="edit({{ $student->id }})">
                                                </i>
                                                <span wire:loading 
                                                      wire:target="edit({{ $student->id }})" 
                                                      class="spinner-border spinner-border-sm" 
                                                      role="status" 
                                                      aria-hidden="true">
                                                </span>
                                            </button>

                                            {{-- 3. DELETE BUTTON LOADER (FIXED TARGETING) --}}
                                            <button wire:click="deleteConfirmation({{ $student->id }})" 
                                                    wire:loading.attr="disabled" 
                                                    class="btn btn-sm bg-danger-light">
                                                <i class="fe fe-trash" 
                                                   wire:loading.remove 
                                                   wire:target="deleteConfirmation({{ $student->id }})">
                                                </i>
                                                <span wire:loading 
                                                      wire:target="deleteConfirmation({{ $student->id }})" 
                                                      class="spinner-border spinner-border-sm" 
                                                      role="status" 
                                                      aria-hidden="true">
                                                </span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No students found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $students->links('components.admin-pagination') }}
                </div>
            </div>
        </div>

    </div>

    {{-- ADD MODAL (Create) --}}
    <div wire:ignore.self class="modal fade" id="add_student_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetForm"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="store">
                        @include('livewire.admin.partials.student-form')
                        
                        {{-- SAVE BUTTON LOADER --}}
                        <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled" wire:target="store">
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

    {{-- EDIT MODAL (Update) --}}
    <div wire:ignore.self class="modal fade" id="edit_student_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Student</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetForm"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="update">
                        @include('livewire.admin.partials.student-form')
                        
                        {{-- UPDATE BUTTON LOADER --}}
                        <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled" wire:target="update">
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
                        <h4 class="modal-title">Delete Student</h4>
                        <p class="mb-4">Are you sure you want to delete this student?</p>
                        
                        {{-- DELETE CONFIRMATION LOADER --}}
                        <button type="button" wire:click="destroy" wire:loading.attr="disabled" wire:target="destroy" class="btn btn-primary">
                            <span wire:loading.remove wire:target="destroy">Delete</span>
                            <span wire:loading wire:target="destroy">
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                Deleting...
                            </span>
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" wire:click="resetForm">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ADJUST CREDITS MODAL --}}
    <div wire:ignore.self class="modal fade" id="adjust_credits_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manually Adjust Student Tickets</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetForm"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="adjustCredits">
                        <p class="text-muted">Enter a **positive number** to add tickets, or a **negative number** to remove tickets.</p>
                        <div class="mb-3">
                            <label class="form-label">Tickets to Adjust (e.g., 10 or -5)</label>
                            <input type="number" wire:model.blur="creditsToAdjust" class="form-control @error('creditsToAdjust') is-invalid @enderror">
                            @error('creditsToAdjust') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Reason for Adjustment</label>
                            <textarea wire:model.blur="creditsAdjustmentReason" class="form-control @error('creditsAdjustmentReason') is-invalid @enderror" rows="3"></textarea>
                            @error('creditsAdjustmentReason') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled" wire:target="adjustCredits">
                            <span wire:loading.remove wire:target="adjustCredits">Apply Adjustment</span>
                            <span wire:loading wire:target="adjustCredits">
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                Applying...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</div>

@push('js')
    <script>
        // Use DOMContentLoaded to ensure Bootstrap and Livewire are available
        document.addEventListener('DOMContentLoaded', function () {
            // Modal instances should be created once the elements are guaranteed to exist
            const addModal = new bootstrap.Modal(document.getElementById('add_student_modal'));
            const editModal = new bootstrap.Modal(document.getElementById('edit_student_modal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('delete_modal'));
            const creditsModal = new bootstrap.Modal(document.getElementById('adjust_credits_modal'));
            
            // Livewire 3 event listeners (listen for events dispatched from PHP)
            Livewire.on('showAddModal', () => { addModal.show(); });
            Livewire.on('hideAddModal', () => { addModal.hide(); });
            
            Livewire.on('showEditModal', () => { editModal.show(); });
            Livewire.on('hideEditModal', () => { editModal.hide(); });

            Livewire.on('showDeleteModal', () => { deleteModal.show(); });
            Livewire.on('hideDeleteModal', () => { deleteModal.hide(); });

            Livewire.on('showCreditsModal', () => { creditsModal.show(); });
            Livewire.on('hideCreditsModal', () => { creditsModal.hide(); });
        });
    </script>
@endpush