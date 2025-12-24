<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CmsController;
use App\Http\Controllers\Admin\ImpersonationController;

Route::get('/', function () {
    return redirect()->route('auth.login');
    return view('welcome');
});


Route::controller(CmsController::class)->group(function () {
    
    // The "About Us" page
    Route::get('about-us', 'about')->name('cms.about');
    
    // The "Contact Us" page (I've added this for you)
    Route::get('contact-us', 'contact')->name('cms.contact');
    Route::post('contact-us/send', 'storeContact')->name('cms.contact.store');

    // The "Terms" page (I've added this for you)
    Route::get('regulation', 'terms')->name('cms.terms');
    
    // The "Privacy" page (I've added this for you)
    Route::get('privacypolicy', 'privacy')->name('cms.privacy');
    // how-it-works
    Route::get('how-it-works','howItWorks')->name('cms.how.works');
});

Route::get('/impersonate/leave', [ImpersonationController::class, 'stopImpersonating'])->name('impersonate.leave');