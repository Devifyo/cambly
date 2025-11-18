@extends('layouts.admin.app')

{{-- Set the title for this page --}}
@section('title', 'Subscription Plans')

{{-- Push page-specific CSS --}}
@push('css')
    <link rel="stylesheet" href="{{ asset('admin/assets/plugins/datatables/datatables.min.css') }}">
    <style>
        textarea {
        background-color: transparent;
        border: 3px solid rgba(0, 0, 0, 0.3);
        border-radius: 6px;
        margin-bottom: 20px;
        height: 100px;
        resize: none;
        font-family: monospace;
        }

        textarea::placeholder { color: rgba(0, 0, 0, 0.4); }
        textarea:focus { outline: none; }
        textarea:focus::placeholder { color: transparent; }
    </style>
@endpush

{{-- This is the content that will be injected into the layout's @yield('content') --}}
@section('content')

<div class="content container-fluid">
     <x-alert/>               
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
                                    <th>Popular</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($plans as $plan)
                                    <tr>
                                        <td>
                                            @if($plan->icon_path || $plan->icon_link)
                                                <img src="{{ $plan->icon_link }}" alt="icon" width="30" class="ms-2">
                                            @endif
                                            {{ $plan->name }}
                                        </td>
                                        <td>${{ number_format($plan->price, 2) }}</td>
                                        <td>{{ $plan->credits_per_cycle }}</td>
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
                                                   data-status="{{ $plan->status }}"
                                                   data-is-popular="{{ $plan->is_popular ? 1 : 0 }}"
                                                   data-features="{{ $featuresString }}"
                                                   data-icon-path="{{ $plan->icon_path ?? '' }}"
                                                   data-icon-link="{{ $plan->icon_link ?? '' }}"
                                                   data-update-url="{{ route('admin.subscription.plan.update', encryptId($plan->id)) }}">
                                                    <i class="fe fe-pencil"></i> Edit
                                                </a>
                                                <a href="#" class="btn btn-sm bg-danger-light delete-btn" 
                                                   data-bs-toggle="modal" 
                                                   data-bs-target="#delete_modal"
                                                   data-destroy-url="{{ route('admin.subscription.plan.destroy', encryptId($plan->id)) }}">
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
                    {{-- Pagination --}}
                    <div class="mt-3">
                        <x-admin-pagination :paginator="$plans" />
                    </div>
                </div>
            </div>
        </div>          
    </div>
    
</div>

{{-- Include the separated modal files --}}
@include('admin.subscription-plan.modals.add')
@include('admin.subscription-plan.modals.edit')
@include('admin.subscription-plan.modals.delete')

@endsection

@push('js')
<script>
        $(document).ready(function () {
            
            // Handle Edit Button Click
            $('.table tbody').on('click', '.edit-btn', function () {
                var button = $(this);
                
                // Get data from button data-attributes
                var updateUrl = button.data('update-url');
                var name = button.data('name');
                var price = button.data('price');
                var credits = button.data('credits');
                var subtitle = button.data('subtitle');
                var description = button.data('description');
                var status = button.data('status');
                var isPopular = button.data('is-popular');
                var features = button.data('features');
                var iconUrl = button.data('icon-link');
                var iconPath = button.data('icon-path'); 
                var iconLink = button.data('icon-link');

                // Populate the modal form
                var modal = $('#edit_subscription_plan');
                modal.find('#editPlanForm').attr('action', updateUrl);
                modal.find('#edit_name').val(name);
                modal.find('#edit_price').val(price);
                modal.find('#edit_credits_per_cycle').val(credits);
                modal.find('#edit_subtitle').val(subtitle);
                modal.find('#edit_description').val(description);
                modal.find('#edit_status').val(status);
                modal.find('#edit_is_popular').val(isPopular);
                modal.find('#edit_features').val(features);
                modal.find('#edit_icon_link').val(iconLink);

                // Handle icon preview
                var preview = modal.find('#current_icon_preview');
                console.log(iconUrl);
                preview.empty(); 
                if (iconPath || iconLink) {
                    preview.html('<img src="' + iconUrl + '" alt="Current Icon" width="50">');
                } else {
                    preview.html('<small>No icon uploaded.</small>');
                }
            });

            // Handle Delete Button Click
            $('.table tbody').on('click', '.delete-btn', function () {
                var button = $(this);
                var destroyUrl = button.data('destroy-url');
                
                // Set the form action
                var modal = $('#delete_modal');
                modal.find('#deletePlanForm').attr('action', destroyUrl);
            });
        });

        $(document).ready(function() {
        
            // 1. Shared Validation Rules (Don't repeat code!)
            var validationRules = {
                name: { required: true, minlength: 3 },
                price: { required: true, number: true, min: 0 },
                credits_per_cycle: { required: true, digits: true, min: 1 },
                is_popular: { required: true },
                status: { required: true },
                icon_link: { url: true }
            };

            var validationMessages = {
                name: "Please enter a valid plan name",
                price: "Please enter valid price",
                credits_per_cycle: "Must be a whole number",
                icon_link: "Please enter a valid URL (starting with http:// or https://)"
            };

            // Shared Styling Settings for Bootstrap 5
            var validationSettings = {
                errorElement: 'span',
                errorClass: 'invalid-feedback',
                errorPlacement: function (error, element) {
                    error.insertAfter(element);
                },
                highlight: function (element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element) {
                    $(element).removeClass('is-invalid');
                }
            };

            // 2. Initialize Validation for ADD Form
            $("#addPlanForm").validate({
                rules: validationRules,
                messages: validationMessages,
                ...validationSettings
            });

            // 3. Initialize Validation for EDIT Form
            $("#editPlanForm").validate({
                rules: validationRules,
                messages: validationMessages,
                ...validationSettings
            });


            // --- Your Existing Data-Population Logic Below ---
            
            // Handle Edit Button Click
            $('.table tbody').on('click', '.edit-btn', function () {
                // Reset validation errors when opening the modal
                var validator = $("#editPlanForm").validate();
                validator.resetForm();
                $("#editPlanForm").find('.is-invalid').removeClass('is-invalid');

                var button = $(this);
                // ... (Rest of your population logic) ...
                var updateUrl = button.data('update-url');
                // Assign values...
                var modal = $('#edit_subscription_plan');
                modal.find('#editPlanForm').attr('action', updateUrl);
                // ... etc
            });
        });
    </script>
@endpush