<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IsTeacher
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login');
        }

        if (!Auth::user()->hasRole('teacher')) {
            if (Auth::user()->hasRole('student')) {
                return redirect()->route('student.dashboard');
            } elseif (Auth::user()->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            }

            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
