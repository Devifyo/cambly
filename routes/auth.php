<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthController;
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.email.otp');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
