<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
Route::middleware(['auth.custom','isAdmin'])->group(function () {

Route::get('/dashboard',[AdminDashboardController::class, 'index'])->name('dashboard');

});