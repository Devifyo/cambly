<?php

namespace App\Livewire\Admin\Profile;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\User; // Adjust namespace if needed

class ProfilePicture extends Component
{
    use WithFileUploads;

    public $user;
    public $new_profile_picture;

    public function mount($user)
    {
        $this->user = $user;
    }
    
    public function updateProfilePicture()
    {   
        $this->admin = auth()->user();
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

    // LIVEWIRE NATIVE LAZY LOADING PLACEHOLDER
    public function placeholder()
    {
        return view('livewire.admin.profile.placeholder');
    }

    public function render()
    {
        return view('livewire.admin.profile.profile-picture');
    }
}