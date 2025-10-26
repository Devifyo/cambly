<?php

use App\Http\Controllers\Student\StudentDashboardController;
use Illuminate\Support\Facades\Route;

// Simple routes
Route::get('/students', function () {
    return 'List of students';
});

// Controller route
Route::get('/dashboard', [StudentDashboardController::class, 'index']);
