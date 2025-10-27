@extends('layouts.auth.app')

@section('title', 'Sign In | Doccure')

@section('content')
<div class="login-content-info py-5">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-5 col-md-7">
                <div class="account-content shadow-sm rounded bg-white p-4 p-md-5">
                    <div class="account-info text-center mb-4">
                        <h3 class="fw-bold mb-2">Welcome Back 👋</h3>
                        <p class="text-muted mb-0">
                            Sign in to continue to <strong>{{ config('app.name') }}</strong>
                        </p>
                    </div>

                    <!-- Alerts -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
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

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('auth.login.request') }}">
                        @csrf

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fa fa-envelope text-muted"></i>
                                </span>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="form-control border-start-0 @error('email') is-invalid @enderror"
                                    placeholder="you@example.com"
                                    required
                                    autofocus>
                            </div>
                            @error('email')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label for="password" class="form-label fw-semibold mb-0">Password</label>
                                <a href="{{ route('auth.password.request') }}" class="small text-primary text-decoration-none">Forgot password?</a>
                            </div>
                            <div class="input-group pass-group">
                                <span class="input-group-text bg-light border-end-0">
                                    <i class="fa fa-lock text-muted"></i>
                                </span>
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    class="form-control pass-input border-start-0 @error('password') is-invalid @enderror"
                                    placeholder="Enter your password"
                                    required>
                                     <span class="feather-eye-off toggle-password"></span>
                                {{-- <button type="button" class="input-group-text bg-light border-0 toggle-password">
                                    <i class="fa fa-eye-slash text-muted"></i>
                                </button> --}}
                            </div>
                            @error('password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Options -->
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="remember"
                                    name="remember"
                                    {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="remember">Remember Me</label>
                            </div>
                            {{-- <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="login_otp"
                                    name="login_otp"
                                    {{ old('login_otp') ? 'checked' : '' }}>
                                <label class="form-check-label small" for="login_otp">Login with OTP</label>
                            </div> --}}
                        </div>

                        <!-- Submit -->
                        <div class="d-grid mb-3">
                            <button class="btn btn-primary-gradient py-2 fw-semibold" type="submit">
                                <i class="fa fa-sign-in-alt me-2"></i> Sign In
                            </button>
                        </div>

                        <!-- Signup -->
                        <div class="text-center mt-4">
                            <p class="text-muted mb-0">
                                Don’t have an account?
                                <a href="{{ route('auth.register') }}" class="fw-semibold text-primary text-decoration-none">Sign up</a>
                            </p>
                        </div>
                    </form>
                    <!-- /Login Form -->
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

.pass-group {
    position: relative;
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
<script>
$(document).ready(function () {
    // Attach validation to login form
    $("form").validate({
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
            },
            password: {
                required: true,
                minlength: 6
            }
        },
        messages: {
            email: {
                required: "Please enter your email address.",
                email: "Please enter a valid email address."
            },
            password: {
                required: "Please enter your password.",
                minlength: "Password must be at least 6 characters long."
            }
        },
        errorPlacement: function (error, element) {
            // 👇 ensures messages appear neatly under each input group
            if (element.closest('.input-group').length) {
                error.insertAfter(element.closest('.input-group'));
            } else {
                error.insertAfter(element);
            }
        }
    });
});
</script>
@endpush
