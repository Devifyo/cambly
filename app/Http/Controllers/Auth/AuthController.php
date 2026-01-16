<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\{User, StudentProfile, TeacherProfile, Language};
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
        // 1. Validation
        $validator = Validator::make($request->all(), [
            'name'            => 'required|string|max:255',
            'email'           => 'required|string|email|max:255|unique:users',
            // 'discord_id'      => 'required|string|max:50',
            'zoom_link'           => 'required_if:role,teacher|nullable|url|max:255',
            'password'        => 'required|string|min:8|confirmed',
            'role'            => 'required|string|exists:roles,name',
            'terms'           => 'accepted',
            'japanese_level'  => 'required|string|in:none,basic,conversational,fluent,native-like',
            'country_residence' => 'required|string|max:100',
            // Updated: Expect a comma-separated string (e.g. "en,fr,jp")
            'native_languages' => 'required|string', 
            'dob'               => 'required_if:role,student|nullable|date|before_or_equal:today',
            'teaching_experience' => 'required_if:role,teacher|nullable|string|max:1000',
            'headline'        => 'nullable|string|max:255',
        ]);
        
        if ($validator->fails()) {
            // dd($request->all(),$validator->errors()->toArray());
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // 2. Create User
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 3. Assign Role
        $user->assignRole($request->role);

        // 4. Sync Languages (Handle the comma-separated string)
        $this->syncUserLanguages($user, $request);

        // 5. Create Profile (Student/Teacher)
        $timezone = getTimeZone(); // Helper function assumed to exist

        if ($user->isStudent()) {
            StudentProfile::updateOrCreate(
                ['user_id' => $user->id],
                [   
                    'preferred_name'    => $user->name,
                    'discord_id'        => $request->discord_id ?? null,
                    'tz'                => $timezone,
                    'english_level'     => $request->japanese_level,
                    'country_residence' => $request->country_residence,
                    'date_of_birth'     => $request->dob,
                ]
            );
        } elseif ($user->isTeacher()) {
            TeacherProfile::updateOrCreate(
                ['user_id' => $user->id],
                [   
                    'preferred_name'    => $user->name,
                    'discord_id'        => $request->discord_id ?? null,
                    'zoom_link'         => $request->zoom_link ?? null,
                    'tz'                => $timezone,
                    'english_level'     => $request->japanese_level,
                    'country_residence' => $request->country_residence,
                    'experience'        => $request->teaching_experience,
                    'short_bio'         => $request->headline,
                ]
            );
        }

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

    /**
     * Helper: Sync Native Languages from comma-separated string
     */
    private function syncUserLanguages($user, $request)
    {
        // 1. Get string from hidden input (e.g., "en,fr,es")
        $rawString = $request->input('native_languages');

        if (!$rawString) {
            return;
        }

        // 2. Convert to array
        $nativeCodes = explode(',', $rawString);

        // 3. Detach old native languages (clean slate)
        $user->languages()->wherePivot('type', 'native')->detach();

        // 4. Loop and Attach
        foreach ($nativeCodes as $code) {
            $code = trim($code); // Clean whitespace
            if (empty($code)) continue;

            $lang = $this->getOrCreateLanguage($code);
            
            // Attach as 'native' type
            $user->languages()->attach($lang->id, ['type' => 'native']);
        }
    }

    /**
     * Helper: Get or Create Language by Code
     */
    private function getOrCreateLanguage($code)
    {
        // Try to find by CODE first
        $language = Language::where('code', $code)->first();

        // If not found, create it dynamically
        if (!$language) {
            // Use PHP 'intl' extension to get display name if available, else use code
            $name = class_exists('Locale') ? \Locale::getDisplayLanguage($code, 'en') : ucfirst($code);
            
            $language = Language::create([
                'code' => $code,
                'name' => $name
            ]);
        }

        return $language;
    }
}
