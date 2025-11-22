<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{AdminDashboardController, AdminManagePlansController, AdminStudentController, AdminTeacherController, AccountSettingController, ImpersonationController, OpsController, PermssionController, SubadminController};
Route::middleware(['auth.custom','isOps'])->group(function () {

    Route::get('/dashboard',[AdminDashboardController::class, 'index'])->name('dashboard');
    Route::prefix('subscription')->name('subscription.plan.')->controller(AdminManagePlansController::class)->group(function(){
        
        Route::get('index','index')->name('index')->middleware('permission:view_subscriptions');
        Route::post('store', 'store')->name('store')->middleware('permission:create_subscriptions');
        Route::patch('update/{id}', 'update')->name('update')->middleware('permission:edit_subscriptions');
        Route::delete('destroy/{id}', 'destroy')->name('destroy')->middleware('permission:delete_subscriptions');
    });

    Route::prefix('students')->name('students.')->controller(AdminStudentController::class)->group(function(){
        Route::get('index','index')->name('index')->middleware('permission:view_students');
    });

    Route::prefix('teachers')->name('teachers.')->controller(AdminTeacherController::class)->group(function(){
        Route::get('index','index')->name('index')->middleware('permission:view_teachers');
    });

    Route::prefix('')->name('subadmins.')->controller(SubadminController::class)->group(function(){
        Route::get('index','index')->name('index')->middleware('permission:view_admins');
    });

    Route::prefix('ops')->name('ops.')->controller(OpsController::class)->group(function(){
        Route::get('index','index')->name('index')->middleware('permission:view_ops');
    });

    Route::prefix('settings')->name('settings.')->group(function(){
        Route::get('permissions',[PermssionController::class, 'index'])->name('roles')->middleware('permission:manage_permissions');
    });

    Route::get('/account-settings',[ AccountSettingController::class, 'index'])->name('account.settings');

    Route::get('/impersonate/{id}', [ImpersonationController::class, 'impersonate'])->name('impersonate');


});