<div class="row mb-5">
    <div class="col-md-8">
        <form wire:submit.prevent="updateProfilePicture">
            
            <div class="mb-3 d-flex align-items-center" x-data="{ uploading: false }">
                
                <div class="me-3 position-relative">
                    <img src="{{ $user->profile_picture ? $user->profile_link : 'https://placehold.co/100x100/A0A0A0/FFFFFF?text=No+Img' }}" 
                         alt="Profile" 
                         class="rounded-circle"
                         width="100" height="100"
                         style="object-fit: cover;"
                         
                         {{-- While file is uploading (uploading=true), dim this image --}}
                         x-bind:class="{ 'opacity-50': uploading }"
                    >

                    @if ($new_profile_picture) 
                        <img src="{{ $new_profile_picture->temporaryUrl() }}" 
                             class="rounded-circle position-absolute top-0 start-0"
                             width="100" height="100"
                             style="object-fit: cover;">
                    @endif

                    <div class="position-absolute top-50 start-50 translate-middle"
                         wire:loading wire:target="new_profile_picture">
                         <div class="spinner-border spinner-border-sm text-dark" role="status"></div>
                    </div>
                </div>

                <div>
                    <label class="form-label">Upload New Image (Max 1MB)</label>
                    
                    <input type="file" 
                           wire:model="new_profile_picture" 
                           class="form-control @error('new_profile_picture') is-invalid @enderror"
                           x-on:livewire-upload-start="uploading = true"
                           x-on:livewire-upload-finish="uploading = false"
                           x-on:livewire-upload-error="uploading = false"
                    >

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
                    <span class="spinner-border spinner-border-sm me-1"></span>
                    Processing...
                </span>
            </button>
        </form>
    </div>
</div>