<?php

use App\Http\Controllers\Student\{StudentDashboardController,SubscriptionController };
use Illuminate\Support\Facades\Route;

// Simple routes
Route::get('/students', function () {
    return 'List of students';
});

Route::middleware(['auth.custom','isStudent'])->group(function () {
// Controller route
Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
Route::get('/account/subscription', [SubscriptionController::class, 'index'])->name('account.subscription');
});
