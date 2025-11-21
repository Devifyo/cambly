<?php
use App\Models\User;

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