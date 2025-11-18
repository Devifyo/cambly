<?php

namespace App\Http\Controllers\Teacher;

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

class TeacherAccountController extends Controller
{
        /**
     * Display the account settings view.
     */
    public function show(Request $request): View
    {
        // The view path you provided was 'teacher.account.account.blade.php'
        // This corresponds to the view name 'teacher.account.account'
        return view('teacher.account.account', [
            'user' => $request->user()
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function updateProfile(ProfileUpdateRequest $request): RedirectResponse
    {   
        $user = $request->user();
        // dd($request->all());
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
                'native_language',
                'english_level',
                'discord_id',
                'experience',
                'short_bio',
                'country_residence'
        ]);
        
        $user->teacherProfile()->updateOrCreate(
            ['user_id' => $user->id], // Find profile by user_id
            $profileData                 // Update with all new data
        );

        return Redirect::route('teacher.account.show')->with('success', 'Your profile has been updated.');
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
}
