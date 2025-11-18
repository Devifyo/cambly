@extends('layouts.admin.app')

{{-- Set the title for this page --}}
@section('title', 'Subscription Plans')

{{-- Push page-specific CSS --}}
@push('css')
    <style>
        textarea {
        background-color: transparent;
        border: 3px solid rgba(0, 0, 0, 0.3);
        border-radius: 6px;
        margin-bottom: 20px;
        height: 100px;
        /* width: 300px; */ /* Let bootstrap grid handle width */
        resize: none;
        font-family: monospace;
        }

        textarea::placeholder {
        color: rgba(0, 0, 0, 0.4);
        }

        textarea:focus {
        outline: none;
        }

        textarea:focus::placeholder {
        color: transparent;
        }
    </style>
@endpush

{{-- This is the content that will be injected into the layout's @yield('content') --}}
@section('content')

<div class="content container-fluid">
                
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12 d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="page-title">Subscription Plans</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{-- route('admin.dashboard') --}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Subscription Plans</li>
                    </ul>
                </div>
                <div>
                    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#add_subscription_plan">
                        <i class="fe fe-plus"></i> Add New Plan
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="datatable table table-hover table-center mb-0">
                            <thead>
                                <tr>
                                    <th>Plan Name</th>
                                    <th>Price</th>
                                    <th>Credits</th>
                                    {{-- <th>Interval</th> --}}
                                    <th>Popular</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- This will be populated by DataTables AJAX --}}
                                @forelse ($plans as $plan)
                                    <tr>
                                        <td>
                                            @if($plan->icon_path)
                                                <img src="{{ $plan->icon_link }}" alt="icon" width="30" class="ms-2">
                                            @endif
                                            {{ $plan->name }}
                                        </td>
                                        <td>{{ format_currency($plan->price) }} </td>
                                        <td>{{ $plan->credits_per_cycle }}</td>
                                        {{-- <td>{{ $plan->interval }}</td> --}}
                                        <td>
                                            @if ($plan->is_popular)
                                                <span class="badge rounded-pill bg-info inv-badge">Yes</span>
                                            @else
                                                <span class="badge rounded-pill bg-secondary inv-badge">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (Str::lower($plan->status) == 'active')
                                                <span class="badge rounded-pill bg-success inv-badge">Active</span>
                                            @else
                                                <span class="badge rounded-pill bg-danger inv-badge">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="actions">
                                                @php
                                                    // Convert features array (from JSON) to a newline-separated string for the textarea
                                                    $featuresString = (is_array($plan->features) ? implode("\n", $plan->features) : $plan->features ?? '');
                                                @endphp
                                                <a href="#" class="btn btn-sm bg-success-light me-2 edit-btn"
                                                   data-bs-toggle="modal"
                                                   data-bs-target="#edit_subscription_plan"
                                                   data-name="{{ $plan->name }}"
                                                   data-price="{{ $plan->price }}"
                                                   data-credits="{{ $plan->credits_per_cycle }}"
                                                   data-subtitle="{{ $plan->subtitle }}"
                                                   data-description="{{ $plan->description }}"
                                                   {{-- data-interval="{{ $plan->interval }}" --}}
                                                   data-status="{{ $plan->status }}"
                                                   data-is-popular="{{ $plan->is_popular ? 1 : 0 }}"
                                                   data-features="{{ $featuresString }}"
                                                   data-icon-url="{{ $plan->icon_link ? asset($plan->icon_link) : '' }}"
                                                   data-update-url="{{ route('admin.subscription.plan.update', $plan->id) }}">
                                                    <i class="fe fe-pencil"></i> Edit
                                                </a>
                                                <a href="#" class="btn btn-sm bg-danger-light delete-btn" 
                                                   data-bs-toggle="modal" 
                                                   data-bs-target="#delete_modal"
                                                   data-destroy-url="{{ route('admin.subscription.plan.destroy', $plan->id) }}">
                                                    <i class="fe fe-trash"></i> Delete
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No subscription plans found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{-- pagaination --}}
                    <div class="mt-3">
                        <x-admin-pagination :paginator="$plans" />
                    </div>
                    {{-- end pagination --}}
                </div>
            </div>
        </div>          
    </div>
    
</div>

<!-- Add Modal -->
<div class="modal fade" id="add_subscription_plan" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Subscription Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.subscription.plan.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Plan Name</label>
                                <input type="text" name="name" class="form-control" placeholder="e.g., Pro Plan" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Price</label>
                                <input type="text" name="price" class="form-control" placeholder="e.g., 29.99" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Credits per Cycle</label>
                                <input type="number" name="credits_per_cycle" class="form-control" placeholder="e.g., 100" required>
                            </div>
                        </div>
                         {{-- <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Interval</label>
                                <select class="form-select" name="interval">
                                    <option value="month">Monthly</option>
                                    <option value="year">Yearly</option>
                                </select>
                            </div>
                        </div> --}}
                         <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Is Popular?</label>
                                <select class="form-select" name="is_popular">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Status</label>
                                <select class="form-select" name="status">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Subtitle</label>
                                <input type="text" name="subtitle" class="form-control" placeholder="e.g., Best for professionals">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Description</label>
                                <textarea class="form-control" name="description" rows="3" placeholder="Full plan description..."></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Features</label>
                                <textarea class="form-control" name="features" rows="3" placeholder="Enter features, one per line..."></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Icon (Optional)</label>
                                <input type="file" name="icon_path" class="form-control">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Modal -->

<!-- Edit Modal -->
<div class="modal fade" id="edit_subscription_plan" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Subscription Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editPlanForm" method="POST" action="" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')
                    <div class="row">
                         <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Plan Name</label>
                                <input type="text" id="edit_name" name="name" class="form-control" value="" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Price</label>
                                <input type="text" id="edit_price" name="price" class="form-control" value="" required>
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Credits per Cycle</label>
                                <input type="number" id="edit_credits_per_cycle" name="credits_per_cycle" class="form-control" required>
                            </div>
                        </div>
                         {{-- <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Interval</label>
                                <select id="edit_interval" name="interval" class="form-select">
                                    <option value="month">Monthly</option>
                                    <option value="year">Yearly</option>
                                </select>
                            </div>
                        </div> --}}
                         <div class="col-12 col-sm-6">
                            <div class="mb-3">
                                <label class="mb-2">Is Popular?</label>
                                <select id="edit_is_popular" name="is_popular" class="form-select">
                                    <option value="0">No</option>
                                    <option value="1">Yes</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Status</label>
                                <select id="edit_status" name="status" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Subtitle</label>
                                <input type="text" id="edit_subtitle" name="subtitle" class="form-control">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Description</label>
                                <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Features</label>
                                <textarea id="edit_features" name="features" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="mb-2">Icon (Optional)</label>
                                <input type="file" name="icon_path" class="form-control">
                                <small class="form-text text-muted">Upload a new icon to replace the current one.</small>
                                <div id="current_icon_preview" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Edit Modal -->

<!-- Delete Modal -->
<div class="modal fade" id="delete_modal" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document" >
        <div class="modal-content">
            <div class="modal-body">
                 <form id="deletePlanForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="form-content p-2">
                        <h4 class="modal-title">Delete</h4>
                        <p class="mb-4">Are you sure you want to delete this plan?</p>
                        <button type="submit" class="btn btn-primary">Delete</button>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Modal -->
@endsection

@push('js')
    {{-- This script now loads data via AJAX and handles the modals --}}
    <script>
        $(document).ready(function () {
            
            // 1. Initialize DataTables on the pre-rendered HTML table
            // This enables sorting, searching, and pagination
            // var table = $('.datatable').DataTable();

            // 2. Handle Edit Button Click (using event delegation)
            // We use .datatable tbody as the static parent for the dynamic .edit-btn
            $('.datatable tbody').on('click', '.edit-btn', function () {
                var button = $(this);
                
                // Get data from button
                var updateUrl = button.data('update-url');
                var name = button.data('name');
                var price = button.data('price');
                var credits = button.data('credits');
                var subtitle = button.data('subtitle');
                var description = button.data('description');
                var interval = button.data('interval');
                var status = button.data('status');
                var isPopular = button.data('is-popular');
                var features = button.data('features');
                var iconUrl = button.data('icon-url');

                // Populate the modal form
                var modal = $('#edit_subscription_plan');
                modal.find('#editPlanForm').attr('action', updateUrl);
                modal.find('#edit_name').val(name);
                modal.find('#edit_price').val(price);
                modal.find('#edit_credits_per_cycle').val(credits);
                modal.find('#edit_subtitle').val(subtitle);
                modal.find('#edit_description').val(description);
                modal.find('#edit_interval').val(interval);
                modal.find('#edit_status').val(status);
                modal.find('#edit_is_popular').val(isPopular);
                modal.find('#edit_features').val(features);

                // Handle icon preview
                var preview = modal.find('#current_icon_preview');
                preview.empty(); // Clear old preview
                if (iconUrl) {
                    preview.html('<img src="' + iconUrl + '" alt="Current Icon" width="50">');
                } else {
                    preview.html('<small>No icon uploaded.</small>');
                }
            });

            // 3. Handle Delete Button Click (using event delegation)
            $('.datatable tbody').on('click', '.delete-btn', function () {
                var button = $(this);
                var destroyUrl = button.data('destroy-url');
                
                // Set the form action
                var modal = $('#delete_modal');
                modal.find('#deletePlanForm').attr('action', destroyUrl);
            });
        });
    </script>
@endpush