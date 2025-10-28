<?php

use App\Http\Controllers\Student\StudentDashboardController;
use Illuminate\Support\Facades\Route;

// Simple routes
Route::get('/students', function () {
    return 'List of students';
});

Route::middleware(['auth.custom','isStudent'])->group(function () {
// Controller route
Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
});
