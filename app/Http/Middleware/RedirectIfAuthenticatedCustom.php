<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedCustom
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Redirect based on role
            if ($user->hasRole('student')) {
                return redirect()->route('student.dashboard');
            }

            // You can add more role-based redirects here if needed
            if ($user->hasRole('teacher')) {
                return redirect()->route('teacher.dashboard');
            }

            // Default fallback for authenticated users
            return redirect()->route('home');
        }

        return $next($request);
    }
}
