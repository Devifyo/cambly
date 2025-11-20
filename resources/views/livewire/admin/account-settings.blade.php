<div class="content container-fluid">
    {{-- Alert Handler Component (Must listen for both 'alert' and 'showAlert') --}}
    <livewire:admin.components.alert-handler />

    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Account Settings</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Account Settings</li>
                </ul>
            </div>
        </div>
    </div>

    {{-- Main Content Card with Tab System --}}
    <div class="card">
        <div class="card-header">
            {{-- Tab Navigation --}}
            <ul class="nav nav-tabs nav-tabs-bottom">
                <li class="nav-item">
                    <a class="nav-link active" href="#profile_tab" data-bs-toggle="tab">Profile Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#password_tab" data-bs-toggle="tab">Change Password</a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <div class="tab-content">

                {{-- Tab 1: Profile & Email Update --}}
                <div class="tab-pane show active" id="profile_tab" wire:ignore.self>
                    
                    {{-- Profile Picture Section --}}
                    <h5 class="card-title mb-4">Profile Picture</h5>
                    <div class="row mb-5">
                        <div class="col-md-8">
                            <form wire:submit.prevent="updateProfilePicture" 
                                x-data="{ photoPreview: null }">
                                
                                <div class="mb-3 d-flex align-items-center">
                                    <div class="me-3">
                                        <img x-bind:src="photoPreview ? photoPreview : '{{ $authUser->profile_picture ? $authUser->profile_link : 'https://placehold.co/100x100/A0A0A0/FFFFFF?text=No+Img' }}'"
                                            alt="Profile Picture" 
                                            class="rounded-circle" 
                                            style="width: 100px; height: 100px; object-fit: cover;">
                                    </div>
                                    <div>
                                        <label for="profile_upload" class="form-label">Upload New Image (Max 1MB)</label>
                                        
                                        <input type="file" 
                                            id="profile_upload" 
                                            x-ref="photo"
                                            x-on:change="
                                                    const reader = new FileReader();
                                                    reader.onload = (e) => { photoPreview = e.target.result; };
                                                    reader.readAsDataURL($refs.photo.files[0]);
                                            "
                                            wire:model="new_profile_picture" 
                                            class="form-control @error('new_profile_picture') is-invalid @enderror">
                                        
                                        @error('new_profile_picture') 
                                            <span class="invalid-feedback d-block">{{ $message }}</span> 
                                        @enderror
                                    </div>
                                </div>
                                
                                <button type="submit" 
                                        class="btn btn-warning mt-2" 
                                        wire:loading.attr="disabled" 
                                        wire:target="updateProfilePicture">
                                    
                                    <span wire:loading.remove wire:target="updateProfilePicture">Update Picture</span>
                                    
                                    <span wire:loading wire:target="updateProfilePicture">
                                        <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                        Processing...
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <hr class="mb-5">

                    {{-- Name & Email Update Section --}}
                    <h5 class="card-title mb-4">Update Details</h5>
                    <form wire:submit.prevent="updateProfile">
                        <div class="row">
                            <div class="col-md-8">
                                {{-- Name Field --}}
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" wire:model.defer="name" class="form-control @error('name') is-invalid @enderror">
                                    @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                {{-- Email Field --}}
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" wire:model.defer="email" class="form-control @error('email') is-invalid @enderror">
                                    @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="updateProfile">
                            <span wire:loading.remove wire:target="updateProfile">Save Changes</span>
                            <span wire:loading wire:target="updateProfile">
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                Saving...
                            </span>
                        </button>
                    </form>
                </div>

                {{-- Tab 2: Password Update --}}
                <div class="tab-pane" id="password_tab" wire:ignore.self>
                    <h5 class="card-title mb-4">Update Password</h5>
                    <form wire:submit.prevent="updatePassword">
                        <div class="row">
                            <div class="col-md-8">
                                {{-- Current Password Field --}}
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" wire:model.defer="current_password" class="form-control @error('current_password') is-invalid @enderror">
                                    @error('current_password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                {{-- New Password Field --}}
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" wire:model.defer="new_password" class="form-control @error('new_password') is-invalid @enderror">
                                    @error('new_password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                                </div>

                                {{-- Confirm New Password Field --}}
                                <div class="mb-3">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" wire:model.defer="new_password_confirmation" class="form-control">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="updatePassword">
                            <span wire:loading.remove wire:target="updatePassword">Change Password</span>
                            <span wire:loading wire:target="updatePassword">
                                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                Updating...
                            </span>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>