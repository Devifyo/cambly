<?php
use App\Models\User;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Auth;

if (! function_exists('is_impersonating')) {
    /**
     * Check if the current session is an impersonation.
     *
     * @return bool
     */
    function is_impersonating()
    {
        return app('impersonate')->isImpersonating();
    }
}

if (! function_exists('impersonator_id')) {
    /**
     * Get the ID of the admin who is impersonating.
     *
     * @return int|null
     */
    function impersonator_id()
    {
        return app('impersonate')->getImpersonatorId();
    }
}

if (! function_exists('impersonator')) {
    /**
     * Get the User model of the admin who is impersonating.
     *
     * @return \App\Models\User|null
     */
    function impersonator()
    {
        if (is_impersonating()) {
            return User::find(impersonator_id());
        }
        return null;
    }
}

if (!function_exists('role_route')) {

    function role_route($routeName, $params = [], $absolute = true)
    {
        $user = Auth::user();

        if (!$user) {
            return route($routeName, $params, $absolute);
        }

        // 1. Admin/subadmin → use admin route as-is
        if ($user->hasAnyRole(['admin', 'subadmin'])) {
            return route($routeName, $params, $absolute);
        }

        // 2. OPS → auto replace "admin." → "ops."
        if ($user->hasRole('ops')) {

            if (str_starts_with($routeName, 'admin.')) {
                $new = str_replace('admin.', 'ops.', $routeName);

                // Check if new route exists
                if (Route::has($new)) {
                    return route($new, $params, $absolute);
                }
            }
        }

        // 3. Teacher or student fallback
        if ($user->hasRole('teacher') && Route::has("teacher.dashboard")) {
            return route("teacher.dashboard");
        }

        if ($user->hasRole('student') && Route::has("student.dashboard")) {
            return route("student.dashboard");
        }

        // 4. If nothing matched → original
        if (Route::has($routeName)) {
            return route($routeName, $params, $absolute);
        }

        return url('/'); // final fallback
    }
}

if (!function_exists('role_route_active')) {

    function role_route_active($routeName)
    {
        $user = Auth::user();

        if (!$user) {
            return '';
        }

        $current = request()->route()?->getName();

        // Admin / Subadmin use admin.* routes
        if ($user->hasAnyRole(['admin', 'subadmin'])) {
            return $current === $routeName ? 'active' : '';
        }

        // OPS: convert admin.* to ops.*
        if ($user->hasRole('ops')) {
            $opsRoute = str_replace('admin.', 'ops.', $routeName);
            return $current === $opsRoute ? 'active' : '';
        }

        // Teacher fallback
        if ($user->hasRole('teacher')) {
            return $current === 'teacher.dashboard' ? 'active' : '';
        }

        // Student fallback
        if ($user->hasRole('student')) {
            return $current === 'student.dashboard' ? 'active' : '';
        }

        return '';
    }
}

