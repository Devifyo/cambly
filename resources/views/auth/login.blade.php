@extends('layouts.auth.app')

@section('title', 'Sign In | Doccure')

@section('content')
<div class="login-content-info">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6">
                <div class="account-content">
                    <div class="account-info">

                        <!-- Title -->
                        <div class="login-title">
                            <h3>Sign in</h3>
                            <p>We'll send a confirmation code to your email.</p>
                            <span>Sign in with
                                <a href="{{ route('login.phone') }}">Phone Number</a>
                            </span>
                        </div>

                        <!-- Alert Messages -->
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('status'))
                            <div class="alert alert-info">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Login Form -->
                        <form method="POST" action="{{ route('login.email.otp') }}">
                            @csrf
                            <!-- Email -->
                            <div class="mb-3">
                                <label for="email" class="form-label">E-mail</label>
                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    class="form-control @error('email') is-invalid @enderror"
                                    required
                                    autofocus>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <div class="form-group-flex">
                                    <label for="password" class="form-label">Password</label>
                                    <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                                </div>
                                <div class="pass-group">
                                    <input
                                        id="password"
                                        type="password"
                                        name="password"
                                        class="form-control pass-input @error('password') is-invalid @enderror"
                                        required>
                                    <span class="feather-eye-off toggle-password"></span>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Options -->
                            <div class="mb-3 form-check-box">
                                <div class="form-group-flex">
                                    <div class="form-check mb-0">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="remember"
                                            name="remember"
                                            {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">Remember Me</label>
                                    </div>
                                    <div class="form-check mb-0">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="login_otp"
                                            name="login_otp"
                                            {{ old('login_otp') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="login_otp">Login with OTP</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="mb-3">
                                <button class="btn btn-primary-gradient w-100" type="submit">Sign in</button>
                            </div>

                            <!-- OR Divider -->
                            <div class="login-or">
                                <span class="or-line"></span>
                                <span class="span-or">or</span>
                            </div>

                            <!-- Social Logins -->
                            <div class="social-login-btn">
                                <a href="{{ route('social.login', 'google') }}" class="btn w-100 mb-2">
                                    <img src="{{ asset('assets/img/icons/google-icon.svg') }}" alt="google-icon">
                                    Sign in With Google
                                </a>
                                <a href="{{ route('social.login', 'facebook') }}" class="btn w-100">
                                    <img src="{{ asset('assets/img/icons/facebook-icon.svg') }}" alt="fb-icon">
                                    Sign in With Facebook
                                </a>
                            </div>

                            <!-- Signup Link -->
                            <div class="account-signup mt-3">
                                <p>
                                    Don't have an account?
                                    <a href="{{ route('register') }}">Sign up</a>
                                </p>
                            </div>
                        </form>
                        <!-- /Login Form -->

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
