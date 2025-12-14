@extends('layouts.auth.app')

@section('title', 'Reset Password')

@section('content')
<div class="auth-page d-flex align-items-center justify-content-center min-vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="auth-card shadow-sm rounded-4 bg-white p-4 p-md-5">
                    
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-primary">{{ config('app.name') }}</h3>
                        <h2 class="fw-bold text-dark mb-2">Reset Password</h2>
                        <p class="text-muted mb-0">
                            Create a new password for your account.
                        </p>
                    </div>

                    <!-- Error Alerts -->
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

                    <!-- Reset Password Form -->
                    <!-- Note the action: auth.password.update -->
                    <form id="resetPasswordForm" method="POST" action="{{ route('auth.password.update') }}">
                        @csrf

                        <!-- REQUIRED: Token from the URL -->
                        <input type="hidden" name="token" value="{{ $token }}">

                        <!-- Email Address (Read Only) -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold text-dark">Email Address</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control form-control-lg rounded-3"
                                value="{{ $email ?? old('email') }}"
                                required
                                readonly
                            >
                        </div>

                        <!-- New Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold text-dark">New Password</label>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control form-control-lg rounded-3 @error('password') is-invalid @enderror"
                                placeholder="New password"
                                required
                                autofocus
                            >
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold text-dark">Confirm Password</label>
                            <input
                                type="password"
                                name="password_confirmation"
                                id="password_confirmation"
                                class="form-control form-control-lg rounded-3"
                                placeholder="Confirm new password"
                                required
                            >
                        </div>

                        <!-- Submit -->
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary-gradient py-3 fw-semibold rounded-3">
                                Reset Password
                            </button>
                        </div>
                        {{-- footer links  --}}
                        @include('auth.partials.footer_links')
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.auth-page { background: linear-gradient(135deg, #f9fbff 0%, #edf3ff 100%); font-family: 'Inter', sans-serif; }
.auth-card { background-color: #ffffff; border-radius: 16px; border: 1px solid rgba(0, 0, 0, 0.03); }
.btn-primary-gradient { background: linear-gradient(90deg, #3a7bd5 0%, #00d2ff 100%); color: #fff; border: none; }
.btn-primary-gradient:hover { background: linear-gradient(90deg, #2f6bcf 0%, #00b8e6 100%); transform: translateY(-1px); }
</style>
@endpush