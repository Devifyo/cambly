@extends('layouts.auth.app')

@section('title', 'Register | Doccure')

@section('content')
<div class="login-content-info py-5">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-5 col-md-7">
                <div class="account-content shadow-sm rounded bg-white p-4 p-md-5">
                    <div class="account-info text-center mb-4">
                        <h3 class="fw-bold mb-2">Create Your Account</h3>
                        <p class="text-muted mb-0">
                            Join <strong>{{ config('app.name') }}</strong> as a Student or Teacher and start your journey today!
                        </p>
                    </div>

                    <!-- Alerts -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                            <ul class="mb-0 ps-3 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Registration Form -->
                    <form id="registerForm" method="POST" action="{{ route('auth.register') }}">
                        @csrf

                        <!-- Role Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold d-block mb-2">Register As</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="role"
                                        id="role_student"
                                        value="student"
                                        {{ old('role') === 'student' ? 'checked' : '' }}
                                        required>
                                    <label class="form-check-label" for="role_student">Student</label>
                                </div>
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="role"
                                        id="role_teacher"
                                        value="teacher"
                                        {{ old('role') === 'teacher' ? 'checked' : '' }}
                                        required>
                                    <label class="form-check-label" for="role_teacher">Teacher</label>
                                </div>
                            </div>
                            @error('role')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Full Name -->
                        <div class="mb-4">
                            <label for="name" class="form-label fw-semibold">Full Name</label>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="John Doe"
                                required>
                            @error('name')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="you@example.com"
                                required>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group">
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    class="form-control pass-input @error('password') is-invalid @enderror"
                                    placeholder="Enter your password"
                                    required>
                                <span class="feather-eye-off toggle-password"></span>
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                class="form-control"
                                placeholder="Re-enter your password"
                                required>
                        </div>

                        <!-- Terms -->
                        <div class="form-check mb-4">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                id="terms"
                                name="terms"
                                required>
                            <label class="form-check-label small" for="terms">
                                I agree to the <a href="#" class="text-primary text-decoration-none">Terms & Conditions</a>
                            </label>
                        </div>

                        <!-- Submit -->
                        <div class="d-grid mb-3">
                            <button class="btn btn-primary-gradient py-2 fw-semibold" type="submit">
                                Create Account
                            </button>
                        </div>

                        <!-- Login Link -->
                        <div class="text-center mt-4">
                            <p class="text-muted mb-0">
                                Already have an account?
                                <a href="{{ route('auth.login') }}" class="fw-semibold text-primary text-decoration-none">Sign in</a>
                            </p>
                        </div>
                    </form>
                    <!-- /Registration Form -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.login-content-info {
    min-height: 100vh;
    background: linear-gradient(135deg, #f5f8ff 0%, #e6f0ff 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.account-content {
    background-color: #fff;
    border-radius: 12px;
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
}

.account-content:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
}

.btn-primary-gradient {
    background: linear-gradient(90deg, #3a7bd5 0%, #00d2ff 100%);
    border: none;
    color: #fff;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-primary-gradient:hover {
    transform: scale(1.02);
    background: linear-gradient(90deg, #2f6bcf 0%, #00b8e6 100%);
}

</style>
@endpush

@push('scripts')
<!-- jQuery Validation Plugin -->

<script>
$(document).ready(function () {
    // jQuery Validation setup
    $("#registerForm").validate({
        errorElement: 'div',
        errorClass: 'error text-danger small',
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
            role: "Please select whether you are registering as a Student or Teacher.",
            name: {
                required: "Please enter your full name.",
                minlength: "Your name must be at least 3 characters long."
            },
            email: {
                required: "Please enter your email address.",
                email: "Please enter a valid email address."
            },
            password: {
                required: "Please provide a password.",
                minlength: "Your password must be at least 8 characters long."
            },
            password_confirmation: {
                required: "Please confirm your password.",
                equalTo: "Passwords do not match."
            },
            terms: "You must agree to the terms & conditions."
        },
        errorPlacement: function (error, element) {
            // ✅ For radio buttons
            if (element.attr("name") === "role") {
                error.insertAfter(element.closest('.d-flex'));
            }
            // ✅ For checkbox (terms)
            else if (element.attr("name") === "terms") {
                error.insertAfter(element.closest('.form-check'));
            }
            // ✅ For input groups (passwords, etc.)
            else if (element.closest('.input-group').length) {
                error.insertAfter(element.closest('.input-group'));
            }
            // ✅ Default
            else {
                error.insertAfter(element);
            }
        }
    });
});
</script>
@endpush
