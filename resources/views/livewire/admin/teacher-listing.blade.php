<div class="content container-fluid">
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
                        <span wire:loading wire:target="create" class="spinner-border spinner-border-sm me-1"></span>
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
                    {{-- EXPORT BUTTON --}}
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
                            <th wire:click="sortBy('name')" role="button">
                                Name @if ($sortField === 'name') <i class="fe fe-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @else <i class="fe fe-arrow-up" style="opacity: 0.3;"></i> @endif
                            </th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th wire:click="sortBy('completed_lessons_count')" role="button">
                                Completed Lessons @if ($sortField === 'completed_lessons_count') <i class="fe fe-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @else <i class="fe fe-arrow-up" style="opacity: 0.3;"></i> @endif
                            </th>
                            <th wire:click="sortBy('status')" role="button">
                                Status @if ($sortField === 'status') <i class="fe fe-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i> @else <i class="fe fe-arrow-up" style="opacity: 0.3;"></i> @endif
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
                                <td><span class="badge bg-info">{{ $teacher->reservations_as_teacher_count }}</span></td>
                                <td>
                                    @if ((int) $teacher->status === 1)
                                        <span class="badge rounded-pill bg-success">Active</span>
                                    @else
                                        <span class="badge rounded-pill bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="actions">
                                        @if($teacher->id !== auth()->id() && $teacher->canBeImpersonated())
                                            <a href="{{ role_route('admin.impersonate', ['id' => encryptId($teacher->id)]) }}" class="btn btn-sm bg-primary-light me-2" data-bs-toggle="tooltip" title="Impersonate Teacher">
                                                <i class="fa-solid fa-user-secret"></i>
                                            </a>
                                        @endif
                                        <button wire:click="edit({{ $teacher->id }})" wire:loading.attr="disabled" class="btn btn-sm bg-success-light me-2">
                                            <i class="fe fe-pencil"></i>
                                        </button>
                                        <button wire:click="deleteConfirmation({{ $teacher->id }})" wire:loading.attr="disabled" class="btn btn-sm bg-danger-light">
                                            <i class="fe fe-trash"></i>
                                        </button>
                                        <button wire:click="openExportModalForTeacher({{ $teacher->id }})" wire:loading.attr="disabled" class="btn btn-sm bg-warning-light me-2" title="Download Report">
                                            <i class="fas fa-file-download"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">No teachers found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $teachers->links('components.admin-pagination') }}</div>
        </div>
    </div>

    {{-- ADD MODAL --}}
    <div wire:ignore.self class="modal fade" id="add_teacher_modal" tabindex="-1">
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
                            <span wire:loading wire:target="store">Saving...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- EDIT MODAL --}}
    <div wire:ignore.self class="modal fade" id="edit_teacher_modal" tabindex="-1">
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
                            <span wire:loading wire:target="update">Updating...</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- DELETE MODAL --}}
    <div wire:ignore.self class="modal fade" id="delete_modal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-content p-2">
                        <h4 class="modal-title">Delete Teacher</h4>
                        <p class="mb-4">Are you sure you want to delete this teacher?</p>
                        <button type="button" wire:click="destroy" class="btn btn-primary">Delete</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal" wire:click="resetForm">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- EXPORT LESSONS MODAL --}}
    <div wire:ignore.self class="modal fade" id="export_modal" tabindex="-1">
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
                            <select wire:model.live="exportPeriod" class="form-control">
                                <option value="last_month">Last Month</option>
                                <option value="last_6_months">Last 6 Months</option>
                                <option value="custom">Custom Date Range</option>
                            </select>
                        </div>

                        {{-- CUSTOM DATE RANGE INPUTS --}}
                        @if($exportPeriod === 'custom')
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label class="form-label small">From</label>
                                    <input type="date" wire:model="exportCustomStart" class="form-control" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">To</label>
                                    <input type="date" wire:model="exportCustomEnd" class="form-control" required>
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Filter by Teacher</label>
                            <select wire:model="exportTeacherId" class="form-control">
                                <option value="all">All Teachers</option>
                                @foreach ($allTeachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            Generate & Download CSV
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
<script>
    // --- Initialize Standard HTML Select for Country ---
    function initCountrySelect(modalId) {
        // 1. Find elements specific to the opened modal
        const $modal = document.querySelector(modalId);
        if (!$modal) return;

        const selectEl = $modal.querySelector('.country-select');
        const hiddenEl = $modal.querySelector('.country-hidden');
        
        if (!selectEl || !hiddenEl) return;

        // 2. Fetch Data (Only if empty)
        if (selectEl.options.length <= 1) {
            fetch('https://restcountries.com/v3.1/all?fields=name,cca2')
                .then(res => res.json())
                .then(data => {
                    // Sort Alphabetically
                    data.sort((a, b) => a.name.common.localeCompare(b.name.common));
                    
                    // Create Options
                    data.forEach(c => {
                        const option = document.createElement('option');
                        option.value = c.name.common;
                        option.textContent = c.name.common;
                        selectEl.appendChild(option);
                    });

                    // Set Value (If Editing) - Check hidden input from Livewire
                    if (hiddenEl.value) {
                        selectEl.value = hiddenEl.value;
                    }
                })
                .catch(err => console.error('Country API Error:', err));
        } else {
            // Data already loaded, simply set the value based on Livewire data
            if (hiddenEl.value) {
                selectEl.value = hiddenEl.value;
            } else {
                selectEl.value = ""; // Reset if add mode
            }
        }

        // 3. Listen for changes -> Update hidden input -> Notify Livewire
        selectEl.onchange = function() {
            hiddenEl.value = this.value;
            hiddenEl.dispatchEvent(new Event('input'));
        };
    }

    // --- Modal Toggle Helper (Safe Version) ---
    function toggleModal(modalId, action) {
        // Small delay ensures Livewire HTML updates are done
        setTimeout(() => {
            const el = document.getElementById(modalId);
            if (el) {
                const modal = bootstrap.Modal.getOrCreateInstance(el);
                if (action === 'show') modal.show();
                else modal.hide();
            }
        }, 50);
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Livewire Handlers
        Livewire.on('showAddModal', () => toggleModal('add_teacher_modal', 'show'));
        Livewire.on('hideAddModal', () => toggleModal('add_teacher_modal', 'hide'));
        Livewire.on('showEditModal', () => toggleModal('edit_teacher_modal', 'show'));
        Livewire.on('hideEditModal', () => toggleModal('edit_teacher_modal', 'hide'));
        Livewire.on('showDeleteModal', () => toggleModal('delete_modal', 'show'));
        Livewire.on('hideDeleteModal', () => toggleModal('delete_modal', 'hide'));
        Livewire.on('showExportModal', () => toggleModal('export_modal', 'show'));

        // Init Country Select when modal is fully visible
        document.body.addEventListener('shown.bs.modal', function (event) {
            if (event.target.id === 'add_teacher_modal') {
                initCountrySelect('#add_teacher_modal');
            }
            if (event.target.id === 'edit_teacher_modal') {
                // Wait slightly for Livewire hydration in Edit mode
                setTimeout(() => initCountrySelect('#edit_teacher_modal'), 100);
            }
        });

        // Focus fixes
        Livewire.on('hideAddModal', () => document.body.focus());
        Livewire.on('hideEditModal', () => document.body.focus());
        Livewire.on('hideDeleteModal', () => document.body.focus());
    });
</script>
@endpush