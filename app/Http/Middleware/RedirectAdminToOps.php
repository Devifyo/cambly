<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectAdminToOps
{
    public function handle(Request $request, Closure $next)
    {
        // If not logged in → skip
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Only apply for OPS role
        if (!$user->hasRole('ops')) {
            return $next($request);
        }

        // Get the current route name
        $routeName = $request->route()->getName();

        // If route starts with "admin."
        if ($routeName && str_starts_with($routeName, 'admin.')) {

            // Replace admin. → ops.
            $newRoute = str_replace('admin.', 'ops.', $routeName);

            // If the new route exists, redirect
            if (route($newRoute, [], false)) {
                return redirect()->route($newRoute);
            }
        }

        return $next($request);
    }
}
