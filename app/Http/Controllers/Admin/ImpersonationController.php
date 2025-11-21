<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ImpersonationController extends Controller
{
    public function impersonate($id)
    {
        // Assuming you have a helper 'decryptId', otherwise use simple ID
        $id = decryptId($id); 
        $userToImpersonate = User::findOrFail($id);

        // 1. Check if the current user is allowed to impersonate
        if (!Auth::user()->canImpersonate()) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Check if the target user can actually be impersonated
        if (!$userToImpersonate->canBeImpersonated()) {
             session()->flash('error', 'You cannot impersonate this user (likely an Admin).');
             return redirect()->back();
        }

        // 3. Perform the impersonation
        Auth::user()->impersonate($userToImpersonate);

        // 4. Redirect based on role
        session()->put('impersonate_return_url', url()->previous());
        if ($userToImpersonate->isStudent()) {
            return redirect('/search/tutors')->with('success', "You are now impersonating {$userToImpersonate->name}");
        } elseif ($userToImpersonate->isTeacher()) {
             // Example teacher route
            return redirect()->route('teacher.dashboard')->with('success', "You are now impersonating {$userToImpersonate->name}");
        }

        return redirect('/');
    }

    public function stopImpersonating()
    {
        Auth::user()->leaveImpersonation();

        $returnUrl = session()->pull('impersonate_return_url', route('admin.dashboard'));

        return redirect($returnUrl)->with('success', 'Welcome back!');
    }
}
