<div class="content container-fluid">
    {{-- Alert Handler Component --}}
    <livewire:admin.components.alert-handler />

    <div class="page-header">
        <div class="row">
            <div class="col-sm-12 d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="page-title">Admin Listing</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Subadmins</li>
                    </ul>
                </div>
                <div>
                    {{-- CREATE BUTTON --}}
                    <button wire:click="create" wire:loading.attr="disabled" class="btn btn-primary">
                        <i class="fe fe-plus" wire:loading.remove wire:target="create"></i>
                        <span wire:loading wire:target="create" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        <span wire:loading.remove wire:target="create">Add Subadmin</span>
                        <span wire:loading wire:target="create">Loading...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                {{-- Search --}}
                <div class="col-md-4">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Search by name or email...">
                </div>
                {{-- Status Filter --}}
                <div class="col-md-3">
                    <select wire:model.live="statusFilter" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div class="col-md-5 text-end d-flex justify-content-end align-items-center">
                    <button class="btn btn-outline-secondary" wire:click="resetFilters">Clear Filters</button>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-center mb-0">
                    <thead>
                        <tr>
                            <th>Profile</th>
                            
                            {{-- Name Column --}}
                            <th wire:click="sortBy('name')" role="button">
                                Name
                                @if ($sortField === 'name')
                                    <i class="fe fe-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                    <i class="fe fe-arrow-up" style="opacity: 0.3;"></i> 
                                @endif
                            </th>
                            
                            <th>Email</th>
                            
                            {{-- Status Column --}}
                            <th wire:click="sortBy('status')" role="button">
                                Status
                                @if ($sortField === 'status')
                                    <i class="fe fe-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                     <i class="fe fe-arrow-up" style="opacity: 0.3;"></i>  
                                @endif
                            </th>
                            
                            <th wire:click="sortBy('created_at')" role="button">
                                Created At
                                @if ($sortField === 'created_at')
                                    <i class="fe fe-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                     <i class="fe fe-arrow-up" style="opacity: 0.3;"></i>  
                                @endif
                            </th>

                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($subadmins as $subadmin)
                            <tr>
                                <td class="text-center">
                                    <img src="{{ $subadmin->profile_link }}" alt="Profile" width="30" class="rounded-circle table-user-img">
                                </td>
                                <td>{{ $subadmin->name }}</td>
                                <td>{{ $subadmin->email }}</td>
                                <td>
                                    @if ((int) $subadmin->status === 1)
                                        <span class="badge rounded-pill bg-success">Active</span>
                                    @else
                                        <span class="badge rounded-pill bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $subadmin->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="actions">
                                        {{-- EDIT BUTTON LOADER --}}
                                        <button wire:click="edit({{ $subadmin->id }})" 
                                                wire:loading.attr="disabled" 
                                                class="btn btn-sm bg-success-light me-2">
                                            <i class="fe fe-pencil" 
                                               wire:loading.remove.delay 
                                               wire:target="edit({{ $subadmin->id }})">
                                            </i>
                                            <span wire:loading 
                                                  wire:target="edit({{ $subadmin->id }})" 
                                                  class="spinner-border spinner-border-sm" 
                                                  role="status" 
                                                  aria-hidden="true">
                                            </span>
                                        </button>

                                        {{-- DELETE BUTTON LOADER --}}
                                        <button wire:click="deleteConfirmation({{ $subadmin->id }})" 
                                                wire:loading.attr="disabled" 
                                                class="btn btn-sm bg-danger-light">
                                            <i class="fe fe-trash" 
                                               wire:loading.remove.delay 
                                               wire:target="deleteConfirmation({{ $subadmin->id }})">
                                            </i>
                                            <span wire:loading 
                                                  wire:target="deleteConfirmation({{ $subadmin->id }})" 
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
                                <td colspan="6" class="text-center">No subadmins found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $subadmins->links('components.admin-pagination') }}
            </div>
        </div>
    </div>

    {{-- ADD MODAL (Create) --}}
    <div wire:ignore.self class="modal fade" id="add_subadmin_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Subadmin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetForm"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="store">
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror">
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Password <span class="text-danger">*</span></label>
                                    <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror">
                                    @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                                    <input type="password" wire:model="password_confirmation" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select wire:model="status" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

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
    <div wire:ignore.self class="modal fade" id="edit_subadmin_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Subadmin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetForm"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="update">
                        <div class="mb-3">
                            <label class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror">
                            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" wire:model="email" class="form-control @error('email') is-invalid @enderror">
                            @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Password <small class="text-muted">(Optional)</small></label>
                                    <input type="password" wire:model="password" class="form-control @error('password') is-invalid @enderror">
                                    @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" wire:model="password_confirmation" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select wire:model="status" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

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
                        <h4 class="modal-title">Delete Subadmin</h4>
                        <p class="mb-4">Are you sure you want to delete this subadmin?</p>
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
</div>

@push('js')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addModal = new bootstrap.Modal(document.getElementById('add_subadmin_modal'));
            const editModal = new bootstrap.Modal(document.getElementById('edit_subadmin_modal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('delete_modal'));
            
            Livewire.on('showAddModal', () => { addModal.show(); });
            Livewire.on('hideAddModal', () => { addModal.hide(); document.body.focus(); });
            
            Livewire.on('showEditModal', () => { editModal.show(); });
            Livewire.on('hideEditModal', () => { editModal.hide(); document.body.focus(); });

            Livewire.on('showDeleteModal', () => { deleteModal.show(); });
            Livewire.on('hideDeleteModal', () => { deleteModal.hide(); document.body.focus(); });
        });
    </script>
@endpush