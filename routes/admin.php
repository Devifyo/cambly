<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{AdminDashboardController, AdminManagePlansController};
Route::middleware(['auth.custom','isAdmin'])->group(function () {

    Route::get('/dashboard',[AdminDashboardController::class, 'index'])->name('dashboard');
    Route::prefix('subscription')->name('subscription.plan.')->controller(AdminManagePlansController::class)->group(function(){
        
        Route::get('index','index')->name('index');
        Route::post('store', 'store')->name('store');
        Route::patch('update/{id}', 'update')->name('update');
        Route::delete('destroy/{id}', 'destroy')->name('destroy');
    });

});