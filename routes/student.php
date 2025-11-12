<?php

use App\Http\Controllers\Student\{StudentDashboardController,SubscriptionController, TeacherController, BookingController, LessonController, StudentAccountController, TicketHistoryController};
use App\Http\Controllers\{StripeWebhookController };
use Illuminate\Support\Facades\Route;

// Simple routes
Route::get('/students', function () {
    return 'List of students';
});

Route::middleware(['auth.custom','isStudent'])->group(function () {
    /************** AccountSetting Routes ********/
    // GET - Show the settings page
        Route::get('/account/settings', [StudentAccountController::class, 'show'])
             ->name('account.show');

        // PATCH - Handle the profile (name, email, avatar) update
        Route::patch('/account/profile', [StudentAccountController::class, 'updateProfile'])
             ->name('profile.update');

        // PUT - Handle the password update
        Route::put('/account/password', [StudentAccountController::class, 'updatePassword'])
             ->name('password.update');
        
    /********* END ACCOUNT SETTING ROUTES *******/
    // Controller route
    Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/calendar-events', 
            [StudentDashboardController::class, 'getCalendarEvents'])
            ->name('dashboard.calendar.events');

    Route::get('/account/subscription', [SubscriptionController::class, 'index'])->name('account.subscription');
    Route::get('subscription/checkout/{slug}', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
    Route::get('subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');
    Route::any('subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
    /****   route for student *****/

    Route::get('search/tutors', [TeacherController::class, 'searchTeachers'])->name('tutors.search');
    Route::get('tutors/profile/{id}', [TeacherController::class, 'showProfile'])->name('tutors.profile');
    // show the date/time selection UI for a teacher (encrypted id expected)
    Route::get('booking/{teacherId}/datetime', [BookingController::class, 'showDateTime'])->name('tutors.booking.datetime');
    // fetch availabilities for a teacher on a date (AJAX)
    Route::get('booking/{teacherId}/slots', [BookingController::class, 'slots'])->name('booking.slots');
    // confirm (create) a reservation
    Route::post('booking/confirm', [BookingController::class, 'confirm'])->name('booking.confirm');
    // cancel a reservation
    Route::any('booking/{reservation}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');

    // Meeting routes
    //    Route::get('/lessons', [LessonController::class, 'view'])->name('lessons.view');
    Route::get('/lessons/list', [LessonController::class, 'index'])->name('lessons.list');    
    Route::get('lessons/upcoming',[LessonController::class,'index'])->name('lessons.upcoming');
    Route::get('lessons/completed',[LessonController::class,'index'])->name('lessons.completed');
    Route::get('lessons/details/{id}',[LessonController::class,'lessonDetails'])->name('lessons.details');
    /**************** Ticket History *********************/
    Route::get('ticket-history',[TicketHistoryController::class, 'index'])->name('account.ticket-history');


});


Route::post('stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');