@extends('layouts.auth.app')

@section('title', 'Sign In | ' . config('app.name'))

@section('content')
<div class="row g-0 min-vh-100">
    <x-auth.auth-sidebar/>
    <div class="col-lg-6 d-flex align-items-center justify-content-center bg-white">
        <div class="login-form-container p-4 p-md-5 w-100" style="max-width: 550px;">
            
            <div class="mb-5 text-center">
                <h3 class="fw-bold text-primary">{{ config('app.name') }}</h3>
                <h2 class="fw-bold text-dark mt-2">Welcome Back! 👋</h2>
                <p class="text-muted">Enter your credentials to access your account.</p>
            </div>


            @if (session('success'))
                <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0 ps-3 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('auth.login.request') }}">
                @csrf
                <input name="tz" value="{{ getTimeZone() }}" hidden />

                <div class="form-floating mb-4">
                    <input type="email" 
                           class="form-control custom-input @error('email') is-invalid @enderror" 
                           id="email" 
                           name="email" 
                           placeholder="name@example.com" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus>
                    <label for="email">Email Address</label>
                    <i class="feather-mail input-icon"></i>
                </div>

                <div class="form-floating mb-2 position-relative">
                    <input type="password" 
                           class="form-control custom-input @error('password') is-invalid @enderror" 
                           id="password" 
                           name="password" 
                           placeholder="Password" 
                           required>
                    <label for="password">Password</label>
                    <i class="feather-lock input-icon"></i>
                    
                    <span class="toggle-password feather-eye-off" style="cursor: pointer; position: absolute; right: 20px; top: 20px; z-index: 10;"></span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label text-muted small" for="remember">
                            Remember me
                        </label>
                    </div>
                    <a href="{{ route('auth.password.request') }}" class="text-primary fw-semibold small text-decoration-none">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-pill fw-bold shadow-sm btn-hover-effect">
                    Sign In
                </button>

                <div class="text-center mt-5">
                    <p class="text-muted">Don't have an account? 
                        <a href="{{ route('auth.register') }}" class="text-primary fw-bold text-decoration-none">Create Account</a>
                    </p>
                </div>
                
                <div class="text-center mt-4">
                    <a href="{{ route('cms.about') }}" class="text-muted small text-decoration-none me-3">About Us</a>
                    <a href="{{ route('cms.contact') }}" class="text-muted small text-decoration-none me-3">Contact Us</a>
                    <a href="{{ route('cms.terms') }}" class="text-muted small text-decoration-none me-3">Terms & Conditions</a>
                    <a href="{{ route('cms.privacy') }}" class="text-muted small text-decoration-none">Privacy Policy</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
$(document).ready(function () {
    
    // Validation
    $("form").validate({
        errorElement: 'div',
        errorClass: 'text-danger small mt-1',
        highlight: function (element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
        },
        rules: {
            email: { required: true, email: true },
            password: { required: true}
        },
        messages: {
            email: {
                required: "Please enter your email address.",
                email: "Enter a valid email."
            },
            password: {
                required: "Please enter your password.",
                }
        }
    });
});
</script>
@endpush