<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\{TeacherDashboardController, TeacherAccountController};

Route::middleware(['auth.custom','isTeacher'])->group(function () {
    
        Route::get('/dashboard', [TeacherDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/calendar-events', [TeacherDashboardController::class, 'getCalendarEvents'])->name('dashboard.calendar.events');

        /******Account******/
            // GET - Show the settings page
        Route::get('/account/settings', [TeacherAccountController::class, 'show'])->name('account.show');

        // PATCH - Handle the profile (name, email, avatar) update
        Route::patch('/account/profile', [TeacherAccountController::class, 'updateProfile'])->name('profile.update');

        // PUT - Handle the password update
        Route::put('/account/password', [TeacherAccountController::class, 'updatePassword'])->name('password.update');
});