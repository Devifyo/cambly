<?php
use App\Models\{User, Language};
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


if (!function_exists('format_user_languages')) {
    
    /**
     * Get comma-separated language names based on the relation and type.
     *
     * @param  mixed  $user  The user model
     * @param  string $type  The type of language (e.g. 'native', 'mother_tongue')
     * @return string
     */
    function format_user_languages($user, $type)
    {
        if (!$user) {
            return 'English';
        }

        // Check if the relation is already loaded to save DB queries
        if ($user->relationLoaded('languages')) {
            $langs = $user->languages->filter(function ($language) use ($type) {
                // Adjust 'pivot' access depending on your exact relationship name
                return $language->pivot->type === $type;
            });
        } else {
            // Query the relationship directly
            // Assumes you have defined: return $this->belongsToMany(Language::class, 'user_languages')->withPivot('type');
            $langs = $user->languages()->wherePivot('type', $type)->get();
        }

        // Pluck the name from the separate 'languages' table
        $list = $langs->pluck('name')->implode(', ');

        return $list ?: 'English';
    }
}


if (!function_exists('getAllLanguages')) {
    /**
     * Get all languages for filter lists.
     * Uses Cache to avoid database queries on every page load.
     */
    function getAllLanguages()
    {
        // Cache the list for 24 hours (1440 minutes) since languages rarely change
         return Language::orderBy('name', 'asc')->get();
    }
}

