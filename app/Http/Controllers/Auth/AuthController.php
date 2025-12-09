<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\{User, StudentProfile, TeacherProfile};
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
        //  1. Validate input
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);
        //  2. Attempt to log in
        if (Auth::attempt($credentials, $request->boolean('remember'))) {

            $user = Auth::user(); // Get the authenticated user model instance

            if ((int) $user->status !== 1) { 
                
                // Log the user out immediately
                Auth::logout();
                
                // Invalidate the session and regenerate the token for security
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                // Redirect back with an error message
              return back()->withErrors([
                    'email' => 'Your account is disabled. Please contact support for assistance. 
                    <a href="'.route('cms.contact').'">Contact Us</a>'
                ])->onlyInput('email');

            }
            //  3. Regenerate session (security best practice)
            $request->session()->regenerate();
            try {
               
                $this->updateUserTimezone($user);
            } catch (\Exception $e) {
                // Log any errors but do not block the login.
                // This makes the feature robust.
                Log::error('Failed to update timezone on login for user: ' . $user->id, [
                    'error' => $e->getMessage()
                ]);
            } 

            if($user->isStudent() && $user->hasActiveSubscription() ){
                //  4. Redirect to intended page or dashboard
                return redirect()->intended('/dashboard')->with('success', 'Welcome back!');
            }elseif($user->isStudent() && !$user->hasActiveSubscription()){
                 return redirect()->route('student.account.subscription')->with('success', 'Welcome back!');
            }elseif($user->isTeacher()){
               return redirect()->route('teacher.dashboard')->with('success', 'Welcome back!');
            }elseif($user->isAdmin() || $user->isSubAdmin()){
                return redirect()->route('admin.dashboard')->with('success', 'Welcome back!');
            }elseif($user->isOps()){
                return redirect()->route('ops.dashboard')->with('success', 'Welcome back!');
            }
        }

        //  5. Failed login
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
            'discord_id' => 'required|string|max:50',
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
        $timezone = getTimeZone();
        if($user->isStudent()){
            StudentProfile::updateOrCreate(
                ['user_id' => $user->id],
                [   
                    'preferred_name' => $user->name,
                    'discord_id' => $request->discord_id,
                    'tz' => $timezone
                ]);
        }elseif($user->isTeacher()){
          TeacherProfile::updateOrCreate(
            ['user_id' => $user->id],
            [   
                'preferred_name' => $user->name,
                'discord_id' => $request->discord_id,
                'tz' => $timezone
            ]);
        }

        // Auto-login the user
        // Auth::login($user);

        return redirect()->route('auth.login')->with('success', 'Registration successful! Welcome aboard 🎉');
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
