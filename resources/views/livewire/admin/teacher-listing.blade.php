<div class="content container-fluid">
    {{-- Alert Handler Component (Assumed to exist) --}}
    <livewire:admin.components.alert-handler />

    <div class="page-header">
        <div class="row">
            <div class="col-sm-12 d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="page-title">Teacher Listing</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Teachers</li>
                    </ul>
                </div>
                <div>
                    {{-- CREATE BUTTON --}}
                    <button wire:click="create" wire:loading.attr="disabled" class="btn btn-primary">
                        <i class="fe fe-plus" wire:loading.remove wire:target="create"></i>
                        <span wire:loading wire:target="create" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        <span wire:loading.remove wire:target="create">Add New Teacher</span>
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
                <div class="col-md-2">
                    <select wire:model.live="statusFilter" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                {{-- Gender Filter --}}
                <div class="col-md-2">
                    <select wire:model.live="genderFilter" class="form-control">
                        <option value="">All Genders</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="col-md-4 text-end d-flex justify-content-end align-items-center">
                    <button class="btn btn-outline-secondary me-2" wire:click="resetFilters">Clear Filters</button>
                    
                    {{-- EXPORT BUTTON (Opens Modal for All Teachers) --}}
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#export_modal" wire:click="$set('exportTeacherId', 'all')">
                        <i class="fe fe-download"></i> Export Lessons
                    </button>
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
                            <th>Gender</th>
                            
                            {{-- Sortable Completed Lessons Column --}}
                            <th wire:click="sortBy('completed_lessons_count')" role="button">
                                Completed Lessons
                                @if ($sortField === 'completed_lessons_count')
                                    <i class="fe fe-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                     <i class="fe fe-arrow-up" style="opacity: 0.3;"></i> 
                                @endif
                            </th>
                            
                            {{-- Status Column --}}
                            <th wire:click="sortBy('status')" role="button">
                                Status
                                @if ($sortField === 'status')
                                    <i class="fe fe-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @else
                                     <i class="fe fe-arrow-up" style="opacity: 0.3;"></i>  
                                @endif
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($teachers as $teacher)
                            <tr>
                                <td class="text-center">
                                    <img src="{{ $teacher->profile_link }}" alt="Profile" width="30" class="rounded-circle">
                                </td>
                                <td>{{ $teacher->name }}</td>
                                <td>{{ $teacher->email }}</td>
                                <td>{{ ucfirst($teacher->gender ?? 'N/A') }}</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ $teacher->reservations_as_teacher_count }}
                                    </span>
                                </td>
                                <td>
                                    @if ((int) $teacher->status === 1)
                                        <span class="badge rounded-pill bg-success">Active</span>
                                    @else
                                        <span class="badge rounded-pill bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="actions">
                                        
                                        {{-- PER-TEACHER DOWNLOAD BUTTON --}}
                                        <button wire:click="openExportModalForTeacher({{ $teacher->id }})" 
                                                wire:loading.attr="disabled" 
                                                class="btn btn-sm bg-warning-light me-2"
                                                title="Download Report">
                                            
                                            <i class="fas fa-file-download" 
                                               wire:loading.remove.delay 
                                               wire:target="openExportModalForTeacher({{ $teacher->id }})">
                                            </i>
                                            <span wire:loading 
                                                  wire:target="openExportModalForTeacher({{ $teacher->id }})" 
                                                  class="spinner-border spinner-border-sm" 
                                                  role="status" 
                                                  aria-hidden="true">
                                            </span>
                                        </button>
                                        
                                        {{-- EDIT BUTTON LOADER (FIXED TARGETING) --}}
                                        <button wire:click="edit({{ $teacher->id }})" 
                                                wire:loading.attr="disabled" 
                                                class="btn btn-sm bg-success-light me-2">
                                            <i class="fe fe-pencil" 
                                               wire:loading.remove.delay 
                                               wire:target="edit({{ $teacher->id }})">
                                            </i>
                                            <span wire:loading 
                                                  wire:target="edit({{ $teacher->id }})" 
                                                  class="spinner-border spinner-border-sm" 
                                                  role="status" 
                                                  aria-hidden="true">
                                            </span>
                                        </button>

                                        {{-- DELETE BUTTON LOADER (FIXED TARGETING) --}}
                                        <button wire:click="deleteConfirmation({{ $teacher->id }})" 
                                                wire:loading.attr="disabled" 
                                                class="btn btn-sm bg-danger-light">
                                            <i class="fe fe-trash" 
                                               wire:loading.remove.delay 
                                               wire:target="deleteConfirmation({{ $teacher->id }})">
                                            </i>
                                            <span wire:loading 
                                                  wire:target="deleteConfirmation({{ $teacher->id }})" 
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
                                <td colspan="7" class="text-center">No teachers found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $teachers->links('components.admin-pagination') }}
            </div>
        </div>
    </div>

    {{-- ADD MODAL (Create) --}}
    <div wire:ignore.self class="modal fade" id="add_teacher_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetForm"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="store">
                        @include('livewire.admin.partials.teacher-form')
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
    <div wire:ignore.self class="modal fade" id="edit_teacher_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Teacher</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="resetForm"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="update">
                        @include('livewire.admin.partials.teacher-form')
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
                        <h4 class="modal-title">Delete Teacher</h4>
                        <p class="mb-4">Are you sure you want to delete this teacher?</p>
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
    
    {{-- EXPORT LESSONS MODAL --}}
    <div wire:ignore.self class="modal fade" id="export_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Export Completed Lessons CSV</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="exportCompletedLessons">
                        <div class="mb-3">
                            <label class="form-label">Time Period</label>
                            <select wire:model="exportPeriod" class="form-control">
                                <option value="last_month">Last Month</option>
                                <option value="last_6_months">Last 6 Months</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Filter by Teacher</label>
                            <select wire:model="exportTeacherId" class="form-control">
                                <option value="all">All Teachers</option>
                                @foreach ($allTeachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="btn btn-success w-100" wire:loading.attr="disabled" wire:target="exportCompletedLessons">
                            <i class="fe fe-download" wire:loading.remove wire:target="exportCompletedLessons"></i>
                            <span wire:loading.remove wire:target="exportCompletedLessons">Generate & Download CSV</span>
                            <span wire:loading wire:target="exportCompletedLessons">
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                Generating...
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
        document.addEventListener('DOMContentLoaded', function () {
            const addModal = new bootstrap.Modal(document.getElementById('add_teacher_modal'));
            const editModal = new bootstrap.Modal(document.getElementById('edit_teacher_modal'));
            const deleteModal = new bootstrap.Modal(document.getElementById('delete_modal'));
            const exportModal = new bootstrap.Modal(document.getElementById('export_modal')); 
            
            Livewire.on('showAddModal', () => { addModal.show(); });
            Livewire.on('hideAddModal', () => { addModal.hide(); });
            
            Livewire.on('showEditModal', () => { editModal.show(); });
            Livewire.on('hideEditModal', () => { editModal.hide(); });

            Livewire.on('showDeleteModal', () => { deleteModal.show(); });
            Livewire.on('hideDeleteModal', () => { deleteModal.hide(); });
            
            Livewire.on('showExportModal', () => { exportModal.show(); });
            
            // Focus Fixes 
            Livewire.on('hideEditModal', () => { editModal.hide(); document.body.focus(); });
            Livewire.on('hideDeleteModal', () => { deleteModal.hide(); document.body.focus(); });
            Livewire.on('hideAddModal', () => { addModal.hide(); document.body.focus(); });
        });
    </script>
@endpush