<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle user login request.
     */
    public function login(Request $request)
    {
        // ✅ 1. Validate input
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        // ✅ 2. Attempt to log in
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // ✅ 3. Regenerate session (security best practice)
            $request->session()->regenerate();

            // ✅ 4. Redirect to intended page or dashboard
            return redirect()->intended('/dashboard')->with('success', 'Welcome back!');
        }

        // ❌ 5. Failed login
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')->with('status', 'You have been logged out successfully.');
    }

     /**
     * Show the registration form.
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle student registration.
     */
    public function register(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:8|confirmed',
            'role'      => 'required|string|exists:roles,name', // ensure role exists in roles table
            'terms'     => 'accepted',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Create user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role using Spatie
        $user->assignRole($request->role);

        // Auto-login the user
        Auth::login($user);

        return redirect()->route('auth.login')->with('success', 'Registration successful! Welcome aboard 🎉');
    }
}
