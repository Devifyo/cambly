<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Notifications\ResetPassword;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
         Schema::defaultStringLength(191);
          View::composer('*', function ($view) {
            $view->with('authUser', Auth::user());
        });

        ResetPassword::createUrlUsing(function ($user, string $token) {
            return route('auth.password.reset', ['token' => $token, 'email' => $user->email]);
        });
    }
}
