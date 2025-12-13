@extends('layouts.auth.app')

@section('title', 'Create Account | ' . config('app.name'))

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    :root {
        --primary-color: #2563eb; /* Royal Blue */
        --primary-light: #eff6ff; /* Very light blue for tags */
        --primary-dark: #1e40af;  /* Dark blue for text */
        --hover-bg: #f8FAFC;
    }

    /* --- 1. Custom Role Selector Cards --- */
    .role-card-input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }
    .role-card {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        background: white;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .role-card:hover {
        background-color: var(--hover-bg);
        border-color: #cbd5e1;
    }
    .role-card-input:checked + .role-card {
        border-color: var(--primary-color);
        background-color: rgba(37, 99, 235, 0.05);
        color: var(--primary-color);
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.1);
    }
    .role-icon {
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    /* --- 2. Floating Labels & Standard Icons --- */
    .form-floating {
        position: relative;
    }
    .form-floating > .form-control,
    .form-floating > .form-select {
        padding-left: 3rem !important; /* Space for icon */
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        height: 58px;
    }
    .form-floating > .form-control:focus,
    .form-floating > .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }
    
    .input-group-text-icon {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 3rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
        z-index: 5;
        pointer-events: none;
    }
    .form-floating > .form-control:focus ~ .input-group-text-icon,
    .form-floating > .form-select:focus ~ .input-group-text-icon {
        color: var(--primary-color);
    }

    /* --- 3. Select2 Wrapper Logic --- */
    .select2-wrapper {
        position: relative; 
        width: 100%;
    }
    .select2-wrapper .input-group-text-icon {
        height: 58px; 
        z-index: 10;
    }
    .select2-container--bootstrap-5 .select2-selection--multiple {
        min-height: 58px;
        padding-left: 3rem !important; 
        padding-top: 15px !important; 
        border: 1px solid #e2e8f0;
        border-radius: 8px;
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        border-color: #e2e8f0;
    }

    /* --- 4. BEAUTIFUL TAGS (FIXED - NO CLIPPING) --- */
    
    /* The Pill Container */
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
        background-color: var(--primary-light) !important;
        border: 1px solid rgba(37, 99, 235, 0.2) !important;
        color: var(--primary-dark) !important;
        border-radius: 50px !important;
        padding: 4px 12px 4px 8px !important; /* Right padding for text, Left for icon */
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 0 !important;
        margin-bottom: 4px !important;
        
        /* Flexbox Fix - This aligns X and Text perfectly */
        display: inline-flex !important;
        align-items: center !important;
        flex-direction: row !important; 
    }

    /* The 'X' Button */
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice .select2-selection__choice__remove {
        border: none !important;
        background: transparent !important;
        color: var(--primary-dark) !important;
        padding: 0 !important;
        margin-right: 8px !important; /* Push text away slightly */
        font-size: 0 !important; /* Hide default 'x' char if present */
        width: 16px !important;
        height: 16px !important;
        display: flex !important;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        border-radius: 50%;
        opacity: 0.6;
        
        /* Reset absolute positioning that caused clipping */
        position: static !important; 
    }

    /* Create the FontAwesome Icon */
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice .select2-selection__choice__remove::before {
        content: "\f00d" !important; /* FontAwesome Times Icon */
        font-family: "Font Awesome 6 Free" !important;
        font-weight: 900 !important;
        font-size: 0.75rem !important;
    }

    /* Hover State */
    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice .select2-selection__choice__remove:hover {
        background-color: rgba(37, 99, 235, 0.2) !important;
        opacity: 1;
    }

    
element.style {
}
.select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__rendered .select2-selection__choice .select2-selection__choice__remove{
    text-indent: 0 !important
}
</style>
@endpush

@section('content')
<div class="row g-0 min-vh-100">
    
    <x-auth.auth-sidebar 
        heading="Join the Community" 
        description="Start your journey today. Whether you want to learn new skills or share your knowledge, we have a place for you." 
    />

    <div class="col-lg-6 d-flex align-items-center justify-content-center bg-white">
        <div class="login-form-container p-4 p-md-5 w-100" style="max-width: 700px;">
            
            <div class="mb-5 text-center">
                <h3 class="fw-bold text-primary mb-1">{{ config('app.name') }}</h3>
                <h2 class="fw-bolder text-dark">Create Account</h2>
                <p class="text-muted">Fill in your details to get started</p>
            </div>

            <form id="registerForm" method="POST" action="{{ route('auth.register') }}">
                @csrf

                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted text-uppercase ls-1">I am joining as a...</label>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="position-relative w-100">
                                <input type="radio" class="role-card-input" name="role" id="role_student" value="student" checked>
                                <div class="role-card">
                                    <i class="fa-solid fa-graduation-cap role-icon"></i>
                                    <span class="fw-bold">Student</span>
                                </div>
                            </label>
                        </div>
                        <div class="col-6">
                            <label class="position-relative w-100">
                                <input type="radio" class="role-card-input" name="role" id="role_teacher" value="teacher">
                                <div class="role-card">
                                    <i class="fa-solid fa-chalkboard-user role-icon"></i>
                                    <span class="fw-bold">Teacher</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
                            <label for="name">Full Name</label>
                            <span class="input-group-text-icon"><i class="fa-regular fa-user"></i></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="discord_id" name="discord_id" placeholder="Discord" required>
                            <label for="discord_id">Discord Username</label>
                            <span class="input-group-text-icon"><i class="fa-brands fa-discord"></i></span>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                        <label for="email">Email Address</label>
                        <span class="input-group-text-icon"><i class="fa-regular fa-envelope"></i></span>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12" id="student-specific-fields">
                        <div class="form-floating">
                            <input type="date" class="form-control" id="dob" name="dob">
                            <label for="dob">Date of Birth</label>
                            <span class="input-group-text-icon"><i class="fa-regular fa-calendar"></i></span>
                        </div>
                    </div>

                    <div class="col-12" id="teacher-specific-fields" style="display: none;">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="headline" name="headline" placeholder="Headline">
                                    <label for="headline">Headline / Title</label>
                                    <span class="input-group-text-icon"><i class="fa-solid fa-heading"></i></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control" id="teaching_experience" name="teaching_experience" placeholder="Years">
                                    <label for="teaching_experience">Exp (Yrs)</label>
                                    <span class="input-group-text-icon"><i class="fa-solid fa-briefcase"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="country_residence" name="country" required>
                                <option value="">Select Country</option>
                            </select>
                            <label for="country_residence">Country</label>
                            <span class="input-group-text-icon"><i class="fa-solid fa-location-dot"></i></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <select class="form-select" id="japanese_level" name="japanese_level" required>
                                <option value="" selected disabled>Select Level</option>
                                <option value="native-like">Native-like</option>
                                <option value="fluent">Fluent</option>
                                <option value="conversational">Conversational</option>
                                <option value="basic">Basic</option>
                                <option value="none">None</option>
                            </select>
                            <label for="japanese_level">Japanese Level</label>
                            <span class="input-group-text-icon"><i class="fa-solid fa-language"></i></span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-muted ms-1">Native Language (Max 3)</label>
                    <div class="select2-wrapper">
                        <select class="form-select" id="native_languages" name="native_languages[]" multiple="multiple" style="width: 100%" required>
                        </select>
                        <span class="input-group-text-icon">
                            <i class="fa-regular fa-comments"></i>
                        </span>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                            <label for="password">Password</label>
                            <span class="input-group-text-icon"><i class="fa-solid fa-lock"></i></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm" required>
                            <label for="password_confirmation">Confirm</label>
                            <span class="input-group-text-icon"><i class="fa-solid fa-lock"></i></span>
                        </div>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                    <label class="form-check-label small text-muted" for="terms">
                        I agree to the <a href="#" class="text-primary fw-bold text-decoration-none">Terms & Conditions</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-pill fw-bold shadow-sm">
                    Create Account <i class="fa-solid fa-arrow-right ms-2"></i>
                </button>
            </form>

            <div class="text-center mt-4">
                <p class="text-muted mb-0">Already have an account? 
                    <a href="{{ route('auth.login') }}" class="fw-bold text-primary text-decoration-none">Login here</a>
                </p>
            </div>
            
            <div class="text-center mt-4 pt-3 border-top">
                <a href="{{ route('cms.about') }}" class="text-muted small text-decoration-none me-3">About Us</a>
                <a href="{{ route('cms.contact') }}" target="_blank" rel="noopener noreferrer" class="text-muted small text-decoration-none me-3">Contact Us</a>
                <a href="{{ route('cms.terms') }}" class="text-muted small text-decoration-none me-3">Terms & Conditions</a>
                <a href="{{ route('cms.privacy') }}" class="text-muted small text-decoration-none">Privacy Policy</a>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/iso-639-1@2.1.15/build/index.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {

    // 1. Role Toggle Logic
    function toggleRoleFields() {
        const role = $('input[name="role"]:checked').val();
        
        if (role === 'teacher') {
            $('#teacher-specific-fields').slideDown();
            $('#student-specific-fields').slideUp();
            $('#dob').prop('disabled', true); 
        } else {
            $('#teacher-specific-fields').slideUp();
            $('#student-specific-fields').slideDown();
            $('#dob').prop('disabled', false);
        }
    }
    
    $('input[name="role"]').change(toggleRoleFields);
    toggleRoleFields(); 

    // 2. Load Countries
    fetch('https://restcountries.com/v3.1/all?fields=name,cca2')
        .then(res => res.json())
        .then(data => {
            data.sort((a, b) => a.name.common.localeCompare(b.name.common));
            data.forEach(c => $('#country_residence').append(new Option(c.name.common, c.name.common)));
        })
        .catch(err => console.error("API Error", err));

    // 3. Load Languages
    if (typeof ISO6391 !== 'undefined') {
        const languages = ISO6391.getAllNames();
        const select = $('#native_languages');
        languages.sort().forEach(lang => select.append(new Option(lang, lang)));
        
        select.select2({
            theme: "bootstrap-5",
            placeholder: "Select languages",
            maximumSelectionLength: 3,
            allowClear: true
        });
    }

    // 4. Validation
    // 4. Validation
    $("#registerForm").validate({
        errorElement: 'div',
        errorClass: 'text-danger small mt-1 fw-bold',
        ignore: ":hidden", 
        rules: {
            role: "required",
            name: "required",
            email: { required: true, email: true },
            discord_id: "required",
            dob: "required",
            country: "required",
            "native_languages[]": { required: true, maxlength: 3 },
            japanese_level: "required",
            headline: { required: function() { return $('#role_teacher').is(':checked'); } },
            teaching_experience: { required: function() { return $('#role_teacher').is(':checked'); } },
            password: { required: true, minlength: 8 },
            password_confirmation: { required: true, equalTo: "#password" },
            terms: "required"
        },
        messages: {
            role: "Please select whether you are a Student or a Teacher.",
            name: "Please enter your full name.",
            email: {
                required: "Please enter your email address.",
                email: "Please enter a valid email address."
            },
            discord_id: "Your Discord username is required.",
            dob: "Please select your date of birth.",
            country: "Please select your country of residence.",
            "native_languages[]": {
                required: "Please select at least one native language.",
                maxlength: "You can select a maximum of 3 native languages."
            },
            japanese_level: "Please select your current Japanese proficiency level.",
            headline: "Please enter a professional headline (e.g., 'Senior English Teacher').",
            teaching_experience: "Please enter your years of teaching experience.",
            password: {
                required: "Please create a password.",
                minlength: "Password must be at least 8 characters long."
            },
            password_confirmation: {
                required: "Please confirm your password.",
                equalTo: "Passwords do not match."
            },
            terms: "You must accept the Terms & Conditions to proceed."
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                error.insertAfter(element.closest('.select2-wrapper'));
            } else if (element.attr("name") == "role") {
                error.insertAfter(element.closest('.mb-4').find('.row'));
            } else if (element.attr("name") == "terms") {
                error.appendTo(element.closest('.form-check'));
            } else {
                error.insertAfter(element.closest('.form-floating') || element);
            }
        }
    });
});
</script>
@endpush