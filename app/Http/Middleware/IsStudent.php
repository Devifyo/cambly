<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsStudent
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login');
        }

        if (!Auth::user()->hasRole('student')) {
            // Optional: You can redirect them by role
            if (Auth::user()->hasRole('teacher')) {
                return redirect()->route('teacher.dashboard');
            } elseif (Auth::user()->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }

            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
