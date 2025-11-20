<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
// 1. Import the WithFileUploads trait
use Livewire\WithFileUploads; 
// Removed: use Illuminate\Support\Facades\Storage; (Since logic moved to helper)

class AccountSettings extends Component
{
    // 2. Use the trait
    use WithFileUploads; 

    // Properties for form data
    public $name;
    public $email;
    public $current_password;
    public $new_password;
    public $new_password_confirmation;
    
    
    // 3. New property for profile picture file upload
    public $new_profile_picture; 

    // Livewire will now refresh the view data automatically when $admin is updated
    public $admin; 

    public function mount()
    {
        // Use default Auth::user() for the currently logged-in user
        $this->admin = Auth::user();
        if ($this->admin) {
            $this->name = $this->admin->name;
            $this->email = $this->admin->email;
        }
    }

    /**
     * Update the user's name and email profile details.
     */
    public function updateProfile()
    {       
        $this->admin = Auth::user();
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->admin->id)],
        ]);

        $this->admin->name = $this->name;
        $this->admin->email = $this->email;
        $this->admin->save();
        $this->dispatch('alert', type: 'success', message: 'Profile updated successfully!');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword()
    {       
         $this->admin = Auth::user();
        $this->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($this->current_password, $this->admin->password)) {
            $this->addError('current_password', 'The provided current password does not match our records.');
            return;
        }

        $this->admin->password = Hash::make($this->new_password);
        $this->admin->save();

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        $this->dispatch('alert', type: 'success', message: 'Password updated successfully!');
    }

    /**
     * Handle the profile picture upload and update using the custom helper.
     */
    public function updateProfilePicture()
    {   
        $this->admin = Auth::user();
        // 4. Validation rules for the uploaded file
        $this->validate([
            'new_profile_picture' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:1024'], // Max 1MB (1024 KB)
        ]);
        // Assumes your uploadProfile helper is globally available or imported
        $path = uploadProfile($this->admin, $this->new_profile_picture); 
        if ($path) {
            $this->admin->profile_picture = $path;
            $this->admin->save();
            
            // Clear the file input after successful upload
            $this->reset('new_profile_picture'); 
            
            // Dispatch a success message
            $this->dispatch('alert', type: 'success', message: 'Profile picture updated successfully!');
        } else {
             // Handle case where helper fails to return a path
             $this->dispatch('alert', type: 'error', message: 'Failed to upload profile picture. Check your uploadProfile helper.');
        }
    }

    public function render()
    {
        return view('livewire.admin.account-settings');
    }
}