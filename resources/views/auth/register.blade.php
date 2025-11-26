@extends('layouts.auth.app')

@section('title', 'Create Account | ' . config('app.name'))

@section('content')
<div class="row g-0 min-vh-100">
    
    <x-auth.auth-sidebar 
        heading="Join the Community" 
        description="Start your journey today. Whether you want to learn new skills or share your knowledge, we have a place for you." 
    />

    <div class="col-lg-6 d-flex align-items-center justify-content-center bg-white">
        <div class="login-form-container p-4 p-md-5 w-100" style="max-width: 550px;">
            
            <div class="mb-4">
                <h2 class="fw-bold text-dark">Create Account 🚀</h2>
                <p class="text-muted">Join <strong>{{ config('app.name') }}</strong> today!</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0 ps-3 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form id="registerForm" method="POST" action="{{ route('auth.register') }}">
                @csrf

                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold text-uppercase ls-1">I am a...</label>
                    <div class="d-flex gap-3">
                        <div class="form-check custom-radio-box">
                            <input class="form-check-input" type="radio" name="role" id="role_student" value="student" {{ old('role') === 'student' ? 'checked' : '' }} required>
                            <label class="form-check-label fw-semibold" for="role_student">👨‍🎓 Student</label>
                        </div>
                        <div class="form-check custom-radio-box">
                            <input class="form-check-input" type="radio" name="role" id="role_teacher" value="teacher" {{ old('role') === 'teacher' ? 'checked' : '' }} required>
                            <label class="form-check-label fw-semibold" for="role_teacher">👨‍🏫 Teacher</label>
                        </div>
                    </div>
                    @error('role')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="name" name="name" placeholder="John Doe" value="{{ old('name') }}" required>
                        <label for="name">Full Name</label>
                        <i class="feather-user input-icon"></i>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" value="{{ old('email') }}" required>
                        <label for="email">Email Address</label>
                        <i class="feather-mail input-icon"></i>
                    </diV>
                </div>

                <div class="mb-3">
                    <div class="form-floating position-relative">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <label for="password">Password</label>
                        <i class="feather-lock input-icon"></i>
                        <span class="feather-eye-off toggle-password" style="position: absolute; right: 20px; top: 20px; cursor: pointer; z-index: 10;"></span>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-floating position-relative">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
                        <label for="password_confirmation">Confirm Password</label>
                        <i class="feather-lock input-icon"></i>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                    <label class="form-check-label small text-muted" for="terms">
                        I agree to the <a href="{{route('cms.terms')}}" class="text-primary text-decoration-none fw-bold">Terms & Conditions</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-pill fw-bold shadow-sm btn-hover-effect">
                    Create Account
                </button>

                <div class="text-center mt-4">
                    <p class="text-muted mb-0">
                        Already have an account?
                        <a href="{{ route('auth.login') }}" class="text-primary fw-bold text-decoration-none">Sign in</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    
    // jQuery Validation
    $("#registerForm").validate({
        errorElement: 'div',
        errorClass: 'text-danger small mt-1',
        highlight: function (element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
        },
        rules: {
            role: { required: true },
            name: { required: true, minlength: 3 },
            email: { required: true, email: true },
            password: { required: true, minlength: 8 },
            password_confirmation: { required: true, equalTo: "#password" },
            terms: { required: true }
        },
        messages: {
            role: "Please select a role.",
            name: "Please enter your full name.",
            email: "Please enter a valid email.",
            password: {
                required: "Please provide a password.",
                minlength: "Password must be at least 8 characters."
            },
            password_confirmation: {
                equalTo: "Passwords do not match."
            },
            terms: "You must agree to the terms."
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") === "role") {
                error.insertAfter(element.closest('.d-flex'));
            } 
            else if (element.attr("name") === "terms") {
                // OLD CODE (Causing the gap):
                // error.insertAfter(element.closest('.form-check'));
                
                // ✅ NEW CODE (Fixes the gap):
                // Place the error inside the container, right after the label
                error.appendTo(element.closest('.form-check'));
            } 
            else {
                // For floating labels
                error.insertAfter(element.closest('.form-floating') || element);
            }
        }
    });
});
</script>
@endpush