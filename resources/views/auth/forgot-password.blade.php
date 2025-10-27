@extends('layouts.auth.app')

@section('title', 'Forgot Password | Doccure')

@section('content')
<div class="auth-page d-flex align-items-center justify-content-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="auth-card shadow-sm rounded-4 bg-white p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold text-dark mb-2">Forgot Your Password?</h2>
                        <p class="text-muted mb-0">
                            Enter your registered email below and we’ll send you a link to reset your password.
                        </p>
                    </div>

                    <!-- Alerts -->
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                            <ul class="mb-0 ps-3 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Forgot Password Form -->
                    <form id="forgotPasswordForm" method="POST" action="{{ route('auth.password.email') }}">
                        @csrf

                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold text-dark">Email Address</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control form-control-lg rounded-3 @error('email') is-invalid @enderror"
                                placeholder="you@example.com"
                                required
                            >
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary-gradient py-3 fw-semibold rounded-3">
                                Send Reset Link
                            </button>
                        </div>

                        <!-- Back to Login -->
                        <div class="text-center mt-4">
                            <p class="text-muted mb-0">
                                Remembered your password?
                                <a href="{{ route('auth.login') }}" class="fw-semibold text-primary text-decoration-none">
                                    Back to Login
                                </a>
                            </p>
                        </div>
                    </form>
                    <!-- /Forgot Password Form -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Page background */
.auth-page {
    background: linear-gradient(135deg, #f9fbff 0%, #edf3ff 100%);
    font-family: 'Inter', sans-serif;
}

/* Card */
.auth-card {
    background-color: #ffffff;
    border-radius: 16px;
    border: 1px solid rgba(0, 0, 0, 0.03);
    transition: all 0.3s ease;
}
.auth-card:hover {
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    transform: translateY(-3px);
}

/* Typography */
h2 {
    font-size: 1.75rem;
    letter-spacing: -0.3px;
}
p {
    font-size: 0.95rem;
}

/* Input Fields */
.form-control {
    padding: 0.75rem 1rem;
    font-size: 0.95rem;
    border: 1px solid #d9e1ec;
    transition: all 0.2s ease-in-out;
}
.form-control:focus {
    border-color: #3a7bd5;
    box-shadow: 0 0 0 0.15rem rgba(58, 123, 213, 0.15);
}

/* Button */
.btn-primary-gradient {
    background: linear-gradient(90deg, #3a7bd5 0%, #00d2ff 100%);
    border: none;
    color: #fff;
    font-size: 1rem;
    letter-spacing: 0.3px;
    transition: all 0.3s ease;
}
.btn-primary-gradient:hover {
    background: linear-gradient(90deg, #2f6bcf 0%, #00b8e6 100%);
    transform: translateY(-1px);
}

/* Alerts */
.alert {
    border-radius: 10px;
    font-size: 0.9rem;
}

/* Validation error style (global-safe) */
.error {
    color: #dc3545;
    font-size: 0.85rem;
    margin-top: 4px;
    display: block;
}

input.error {
    border-color: #dc3545 !important;
}

/* Link */
a.text-primary {
    color: #3a7bd5 !important;
}
a.text-primary:hover {
    text-decoration: underline;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function () {
    // jQuery Validation for Forgot Password form
    $("#forgotPasswordForm").validate({
        errorElement: 'div',
        errorClass: 'error text-danger small',
        highlight: function (element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
        },
        rules: {
            email: {
                required: true,
                email: true
            }
        },
        messages: {
            email: {
                required: "Please enter your registered email address.",
                email: "Please enter a valid email address."
            }
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element); // ensure message stays neatly below input
        }
    });
});
</script>
@endpush
