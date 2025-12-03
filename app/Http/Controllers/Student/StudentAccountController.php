<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

// Import the two new Form Requests
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\PasswordUpdateRequest;
use App\Models\Language;
use Illuminate\Support\Arr; // Import this

class StudentAccountController extends Controller
{
    /**
     * Display the account settings view.
     */
    public function show(Request $request): View
    {
        // The view path you provided was 'student.inner.account.account.blade.php'
        // This corresponds to the view name 'student.inner.account.account'
        return view('student.inner.account.account', [
            'user' => $request->user()
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(ProfileUpdateRequest $request): RedirectResponse
    {   
        $user = $request->user();

        // Update name/email on the model first
        $user->fill($request->only('name', 'email', 'gender'));

        // Handle remove avatar flag (optional)
        if ($request->boolean('remove_avatar')) {
            removeProfileAvatar($user);   // deletes old file
            $user->profile_picture = null;
        }

        // Handle uploaded avatar
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');

            // uploadProfile() should return the relative path, e.g. "avatars/abc.jpg"
            $path = uploadProfile($user, $file); // or your helper name uploadProfile()

            if ($path) {
                $user->profile_picture = $path;
            }
        }
        
        $user->save(); // persists name/email/profile_picture on the existing user row
        
        $profileData = $request->only([
                'date_of_birth',
                'country_residence',
                'native_language',
                'english_level',
                'discord_id'
        ]);
        $this->syncUserLanguages($user, $request);
        $user->studentProfile()->updateOrCreate(
            ['user_id' => $user->id], // Find profile by user_id
            $profileData                 // Update with all new data
        );

        return Redirect::route('student.account.show')->with('success', 'Your profile has been updated.');
    }


    /**
     * Update the user's password.
     */
    public function updatePassword(PasswordUpdateRequest $request): RedirectResponse
    {
        // The request is already validated by PasswordUpdateRequest!
        $user = $request->user();

        // Update the password
        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Redirect back with a success status
        return Redirect::route('student.account.show')->with('success', 'Your password has been updated.');
    }

    public function removeAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();
        removeProfileAvatar($user);
        $user->profile_picture = null;
        $user->save();

        return Redirect::route('student.account.show')->with('success', 'Profile picture removed.');
    }

        private function syncUserLanguages($user, $request)
    {
        // A. Mother Tongue
        $mtCode = $request->input('mother_tongue'); // 'en'
        if ($mtCode) {
            $lang = $this->getOrCreateLanguage($mtCode);
            
            // Detach old, Attach new
            $user->languages()->wherePivot('type', 'mother_tongue')->detach();
            $user->languages()->attach($lang->id, ['type' => 'mother_tongue']);
        }

        // B. Native Languages
        $nativeCodes = $request->input('native_languages', []); // ['fr', 'es']
        
        // Detach old
        $user->languages()->wherePivot('type', 'native')->detach();

        foreach ($nativeCodes as $code) {
            // Skip if same as mother tongue (optional check)
            if ($code === $mtCode) continue;

            $lang = $this->getOrCreateLanguage($code);
            $user->languages()->attach($lang->id, ['type' => 'native']);
        }
    }

    private function getOrCreateLanguage($code)
    {
        // Try to find by CODE first
        $language = Language::where('code', $code)->first();

        // If not found, create it dynamically
        if (!$language) {
            // Use PHP to get the nice name: "fr" -> "French"
            $name = class_exists('Locale') ? \Locale::getDisplayLanguage($code, 'en') : $code;
            $language = Language::create([
                'code' => $code,
                'name' => $name
            ]);
        }

        return $language;
    }
}
