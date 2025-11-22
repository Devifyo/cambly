<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedCustom
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Student redirect
        if ($user->hasRole('student')) {
            if(is_impersonating()){
                return redirect()->route('student.tutors.search');
            }
            return redirect()->route('student.dashboard');
        }

        // Teacher redirect
        if ($user->hasRole('teacher')) {
            return redirect()->route('teacher.dashboard');
        }

        // Admin or Sub-admin redirect
        if ($user->hasAnyRole(['admin', 'subadmin'])) {
            return redirect()->route('admin.dashboard');
        }

        // Ops redirect
        if ($user->hasRole('ops')) {
            return redirect()->route('ops.dashboard');
        }

        // Default fallback (if some unknown role)
        return redirect()->route('home');
    }
}
