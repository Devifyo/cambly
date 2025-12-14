@extends('layouts.auth.app')

@section('title', 'Forgot Password | ' . config('app.name'))

@section('content')
<div class="row g-0 min-vh-100">
    
<x-auth.auth-sidebar 
        heading="Recover Access" 
        description="Don't worry, it happens to the best of us. We'll help you reset your password and get back to learning." 
    />

    <div class="col-lg-6 d-flex align-items-center justify-content-center bg-white">
        <div class="login-form-container p-4 p-md-5 w-100" style="max-width: 550px;">
            
            <div class="mb-4 text-center">
                <h3 class="fw-bold text-primary">{{ config('app.name') }}</h3>
                <h2 class="fw-bold text-dark">Forgot Password? 🔒</h2>
                <p class="text-muted">Enter your registered email and we'll send you a link to reset your password.</p>
            </div>

            @if (session('status'))
                <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
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

            <form id="forgotPasswordForm" method="POST" action="{{ route('auth.password.email') }}">
                @csrf

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

                <button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-pill fw-bold shadow-sm btn-hover-effect">
                    Send Reset Link
                </button>

                <div class="text-center mt-5">
                    <p class="text-muted">Remembered your password? 
                        <a href="{{ route('auth.login') }}" class="text-primary fw-bold text-decoration-none">
                            <i class="feather-arrow-left me-1"></i> Back to Login
                        </a>
                    </p>
                </div>
                {{-- footer links  --}}
                @include('auth.partials.footer_links')
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function () {
    // Validation
    $("#forgotPasswordForm").validate({
        errorElement: 'div',
        errorClass: 'text-danger small mt-1',
        highlight: function (element) {
            $(element).addClass('is-invalid');
        },
        unhighlight: function (element) {
            $(element).removeClass('is-invalid');
        },
        rules: {
            email: { required: true, email: true }
        },
        messages: {
            email: {
                required: "Please enter your registered email address.",
                email: "Please enter a valid email address."
            }
        },
        errorPlacement: function (error, element) {
            // Place error message nicely after the form-floating parent
            error.insertAfter(element.closest('.form-floating') || element);
        }
    });
});
</script>
@endpush