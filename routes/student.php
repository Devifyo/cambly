<?php

use App\Http\Controllers\Student\{StudentDashboardController,SubscriptionController, TeacherController };
use App\Http\Controllers\{StripeWebhookController };
use Illuminate\Support\Facades\Route;

// Simple routes
Route::get('/students', function () {
    return 'List of students';
});

Route::middleware(['auth.custom','isStudent'])->group(function () {
// Controller route
Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');


Route::get('/account/subscription', [SubscriptionController::class, 'index'])->name('account.subscription');
Route::get('subscription/checkout/{slug}', [SubscriptionController::class, 'checkout'])->name('subscription.checkout');
Route::get('subscription/success', [SubscriptionController::class, 'success'])->name('subscription.success');
Route::any('subscription/cancel', [SubscriptionController::class, 'cancel'])->name('subscription.cancel');
// doctor route for student

Route::get('search/tutors', [TeacherController::class, 'searchTeachers'])->name('tutors.search');

});


Route::post('stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');