<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('auth.login');
        }

        if (!Auth::user()->hasRole('admin')) {
            // Optional: You can redirect them by role
            if (Auth::user()->hasRole('teacher')) {
                return redirect()->route('teacher.dashboard');
            } elseif (Auth::user()->hasRole('student')) {
                return redirect()->route('student.dashboard');
            }

            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
