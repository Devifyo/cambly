<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\{ForgotPasswordController,ResetPasswordController };
use App\Http\Controllers\Auth\AuthController;
Route::middleware('guest.custom')->group(function () {
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.request');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    // Handle Register Request
Route::post('/register', [AuthController::class, 'register'])->name('register.submit');

// Forgot password routes
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');