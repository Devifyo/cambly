@extends('layouts.student.app')
@section('title', 'Account Settings')

@push('styles')
    {{-- SweetAlert styles (for confirmations) --}}
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    {{-- NEW: Styles for Select2 and DateTimePicker (from your new design) --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    
    <style>
        /* Base styles */
        .settings-container { 
            max-width: 1200px; 
            margin: 0 auto;
            margin-top: 2rem; 
        }
        .text-danger { color: #dc2626; }

        /* --- NEW: Tabbed Layout Styles --- */
        .settings-tabs {
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 2rem;
        }
        .settings-tabs .nav-link {
            border: none;
            border-bottom: 3px solid transparent;
            color: #6b7280;
            font-weight: 500;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .settings-tabs .nav-link.active {
            border-bottom-color: #0E82FD;
            color: #111827;
            font-weight: 600;
        }
        .settings-tabs .nav-link:hover {
            color: #111827;
            border-bottom-color: #d1d5db;
        }
        .tab-content > .tab-pane { display: none; }
        .tab-content > .tab-pane.active { display: block; }

        /* --- NEW: Card Styles (from your new design) --- */
        .settings-card-base {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }
        .settings-card-base .card-body {
            padding: 1.25rem;
        }
        .setting-card { 
            margin-bottom: 1.5rem; 
        }
        .setting-title { 
            margin-bottom: 1rem; 
        }
        .setting-title h6 { 
            font-weight: 600; 
            font-size: 1rem; 
            color: #374151; 
        }
        .modal-btn { 
            padding: 0.75rem 1.25rem; 
            background: #f9fafb; 
            border-top: 1px solid #e5e7eb; 
            display: flex; 
            justify-content: flex-end; 
            gap: 0.5rem; 
            border-radius: 0 0 12px 12px;
        }

        /* Form styles */
        .form-group { margin-bottom: 1.25rem; }
        .form-label {
            display: block;
            font-size: 0.9rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            border-radius: 8px;
            border: 1px solid #d1d5db;
        }
        .form-control:focus {
            border-color: #0E82FD;
            box-shadow: 0 0 0 0.25rem rgba(14,130,253,0.12);
            outline: none;
        }
        .form-icon { position: relative; }
        .form-icon .icon { 
            position: absolute; 
            right: 15px; 
            top: 50%; 
            transform: translateY(-50%); 
            color: #6b7280; 
        }

        /* --- NEW: Avatar Styles (from your new design) --- */
        .change-avatar { 
            display: flex; 
            align-items: center; 
            gap: 1.5rem; 
        }
        .profile-img { 
            width: 100px; 
            height: 100px; 
            border-radius: 50%; 
            background: #f3f4f6; 
            border: 1px solid #e5e7eb; 
            display: flex; 
            align-items: center; 
            justify-content: center;
            overflow: hidden; /* Ensures img stays round */
        }
        .profile-img i { 
            font-size: 2.5rem; 
            color: #d1d5db; 
        }
        .profile-img img { 
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
        }
        .imgs-load { 
            display: flex; 
            align-items: center; 
            gap: 1rem; 
            margin-bottom: 0.5rem; 
        }
        .change-photo { 
            position: relative; 
            /* Using your new theme's primary button style */
            background-image: linear-gradient(to right, #0E82FD 0%, #06AED4 51%, #0E82FD 100%);
            background-size: 200% auto;
            color: #fff !important; 
            padding: 0.5rem 1rem; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .change-photo:hover {
            background-position: right center;
        }
        .change-photo .upload { 
            position: absolute; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            opacity: 0; 
            cursor: pointer; 
        }
        .upload-remove { 
            color: #dc2626; 
            font-weight: 500; 
            cursor: pointer; 
            background: #f3f4f6;
            border: 1px solid #e5e7eb;
            padding: 0.5rem 1rem;
            border-radius: 8px;
        }
        .upload-remove:hover {
            background: #fee2e2;
            border-color: #fca5a5;
        }
        .upload-img p { 
            font-size: 0.875rem; 
            color: #6b7280; 
            margin: 0; 
        }

        /* Button Styles */
        .btn-md {
            padding: 0.6rem 1.15rem;
            font-size: 0.9rem;
            font-weight: 500;
        }
        .rounded-pill {
            border-radius: 50px !important;
        }


        /* Validation Error Styles */
        .form-group .error { color: #dc2626; font-size: 0.875rem; font-weight: 500; margin-top: 0.25rem; }
        .form-control.error { border-color: #dc2626; }
        .form-control.error:focus { box-shadow: 0 0 0 0.25rem rgba(220, 38, 38, 0.12); }
    </style>
@endpush

@section('content')
    <div class="breadcrumb-bar overflow-visible">
        <div class="container">
            <div class="row align-items-center inner-banner">
                <div class="col-md-12 col-12 text-center">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                {{-- This route name 'student.dashboard' is an assumption, update if yours is different --}}
                                <a href="{{ route('student.dashboard') }}"><i class="isax isax-home-15"></i></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Account Settings</li>
                        </ol>
                        <h2 class="breadcrumb-title">Account Settings</h2>
                    </nav>
                </div>
            </div>
        </div>
        <div class="breadcrumb-bg">
            <img src="{{ asset('assets/img/bg/breadcrumb-bg-01.png') }}" alt="img" class="breadcrumb-bg-01">
            <img src="{{ asset('assets/img/bg/breadcrumb-bg-02.png') }}" alt="img" class="breadcrumb-bg-02">
        </div>
    </div>

    <div class="settings-container">
        <x-alert />

        <ul class="nav settings-tabs" id="settingsTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-pane" type="button" role="tab" aria-selected="true">
                    <i data-feather="user"></i>
                    Profile
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password-pane" type="button" role="tab" aria-selected="false">
                    <i data-feather="lock"></i>
                    Password
                </button>
            </li>
        </ul>
        
        <div class="tab-content" id="settingsTabContent">
            
            <div class="tab-pane fade show active" id="profile-pane" role="tabpanel" aria-labelledby="profile-tab">
                <x-student.settings.update-profile-form />
            </div>

            <div class="tab-pane fade" id="password-pane" role="tabpanel" aria-labelledby="password-tab">
                <x-student.settings.update-password-form />
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Load JS for plugins --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Render all feather icons
        feather.replace();


        // Handle tab switching for server-side errors
        $(function() {
            // Check for profile errors
            if ($('#profile-pane .form-control.error, #profile-pane .is-invalid').length > 0) {
                $('#password-tab').removeClass('active');
                $('#password-pane').removeClass('show active');
                $('#profile-tab').addClass('active');
                $('#profile-pane').addClass('show active');
            }
            // Check for password errors
            else if ($('#password-pane .form-control.error, #password-pane .is-invalid').length > 0) {
                $('#profile-tab').removeClass('active');
                $('#profile-pane').removeClass('show active');
                $('#password-tab').addClass('active');
                $('#password-pane').addClass('show active');
            }
        });
    </script>
@endpush