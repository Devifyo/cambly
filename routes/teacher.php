<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Teacher\{TeacherDashboardController, TeacherAccountController, BookingController, LessonController};

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
        Route::any('booking/{reservation}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
        /********Lesson Routes*********/
        Route::get('/lessons/list', [LessonController::class, 'index'])->name('lessons.list');    
        Route::get('lessons/upcoming',[LessonController::class,'index'])->name('lessons.upcoming');
        Route::get('lessons/completed',[LessonController::class,'index'])->name('lessons.completed');
        Route::get('lessons/details/{id}',[LessonController::class,'lessonDetails'])->name('lessons.details');
        Route::post('lessons/update/link/{id}',[LessonController::class,'updateLessonLink'])->name('lessons.update-link');


});