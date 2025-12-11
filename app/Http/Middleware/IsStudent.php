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
            } elseif (Auth::user()->hasRole('admin') || Auth::user()->hasRole('subadmin')) {
                return redirect()->route('admin.dashboard');
            } elseif (Auth::user()->hasRole('ops')) {
                 return redirect()->route('ops.dashboard');
            }

            abort(403, 'Unauthorized');
        }
        $this->updateUserTimezone(Auth::user());

        return $next($request);
    }


    private function updateUserTimezone($user)
    {
        $timezone = getTimeZone();

        if (session('current_timezone') === $timezone) {
            return;
        }

        $profile = null;

        if ($user->isStudent()) {
            $profile = $user->studentProfile;
            
            if (!$profile) {
                $profile = $user->studentProfile()->create([
                    'tz' => $timezone, 
                    'preferred_name' => $user->name
                ]);
            }
        } elseif ($user->isTeacher()) {
            $profile = $user->teacherProfile;

            if (!$profile) {
                $profile = $user->teacherProfile()->create([
                    'tz' => $timezone, 
                    'preferred_name' => $user->name
                ]);
            }
        }

        if ($profile) {
            if ($profile->tz !== $timezone) {
                $profile->tz = $timezone;
                $profile->save();
            }

            session(['current_timezone' => $timezone]);
        }
    }
}
