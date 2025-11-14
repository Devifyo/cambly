{{-- This component uses the new design, but with the old fields --}}
<form id="profile-form" method="POST" action="{{ route('teacher.profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PATCH')
    
    <div class="card settings-card-base"> {{-- Use base card for border/shadow --}}
        <div class="card-body">
            
            <div class="border-bottom pb-3 mb-3">
                <h5>Profile Settings</h5>
            </div>

            {{-- Avatar Section --}}
            <div class="setting-card">
                <label class="form-label mb-2">Profile Photo</label>
                <div class="change-avatar img-upload">
                    <div class="profile-img" id="avatar-preview-container">
                        @if(auth()->user()->profile_link && !Str::contains(auth()->user()->profile_link, 'ui-avatars'))
                            <img id="avatarPreviewImg" src="{{ auth()->user()->profile_link }}" alt="Profile">
                        @else
                            <i class="fa-solid fa-file-image"></i>
                        @endif
                    </div>
                    <div class="upload-img">
                        <div class="imgs-load d-flex align-items-center">
                            <div class="change-photo">
                                Upload New 
                                <input type="file" name="avatar" id="avatar-upload" class="upload" onchange="previewAvatar(event)">
                            </div>
                            @if(auth()->user()->teacherProfile?->avatar_url)
                                <a href="#" id="btnRemoveAvatar" class="upload-remove">Remove</a>
                            @endif
                        </div>
                        <p>Image should be Below 5 MB, Accepted format jpg, png</p>
                    </div>
                </div>
                @error('avatar')
                    <div class="text-danger mt-1"><small>{{ $message }}</small></div>
                @enderror
                <input type="hidden" name="remove_avatar" id="remove_avatar_input" value="0">
            </div>

            {{-- Information Section --}}
            <div class="setting-title">
                <h6>Information</h6>
            </div>
            <div class="setting-card">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="name">Full Name <span class="text-danger">*</span></label>
                            <input id="name" name="name" type="text" class="form-control"
                                   value="{{ old('name', auth()->user()->name) }}" required>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="email">Email Address <span class="text-danger">*</span></label>
                            <input id="email" name="email" type="email" class="form-control"
                                   value="{{ old('email', auth()->user()->email) }}" required>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="gender">Gender <span class="text-danger">*</span></label>
                            <select id="gender" name="gender" class="form-control" required>
                                <option value="" disabled {{ old('gender', auth()->user()->gender) ? '' : 'selected' }}>Please select</option>
                                @php
                                    $genders = ['male', 'female', 'other'];
                                    $currentGender = old('gender', auth()->user()->gender);
                                @endphp
                                @foreach ($genders as $gender)
                                    <option value="{{ $gender }}" {{ $currentGender == $gender ? 'selected' : '' }}>
                                        {{ ucfirst($gender) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="age">Age <span class="text-danger">*</span></label>
                            <input id="age" name="age" type="number" class="form-control"
                                   value="{{ old('age', auth()->user()->teacherProfile?->age) }}" required>
                        </div>
                    </div>
                    <div class="col-lg-12 col-md-12">
                        <div class="form-group">
                            <label class="form-label" for="native_language">Native Language <span class="text-danger">*</span></label>
                            <input id="native_language" name="native_language" type="text" class="form-control"
                                   value="{{ old('native_language', auth()->user()->teacherProfile?->native_language) }}" required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Additional Details Section --}}
            <div class="setting-title">
                <h6>Additional Details</h6>
            </div>
            <div class="setting-card">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="english_level">English Level <span class="text-danger">*</span></label>
                            {{-- REMOVED select2-init class from here --}}
                            <select id="english_level" name="english_level" class="form-control" required>
                                <option value="" disabled {{ old('english_level', auth()->user()->teacherProfile?->english_level) ? '' : 'selected' }}>Please select your level</option>
                                @php
                                    $levels = ['beginner', 'intermediate', 'advanced', 'native'];
                                    $currentLevel = old('english_level', auth()->user()->teacherProfile?->english_level);
                                @endphp
                                @foreach ($levels as $level)
                                    <option value="{{ $level }}" {{ $currentLevel == $level ? 'selected' : '' }}>
                                        {{ ucfirst($level) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="discord_id">Discord ID (Optional)</label>
                            <input id="discord_id" name="discord_id" type="text" class="form-control"
                                   value="{{ old('discord_id', auth()->user()->teacherProfile?->discord_id) }}" placeholder="myusername#1234">
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="modal-btn text-end">
            <a href="{{ route('student.dashboard') }}" class="btn btn-md btn-light rounded-pill">Cancel</a>
            <button type="submit" class="btn btn-md btn-primary-gradient rounded-pill">Save Changes</button>
        </div>
    </div>
</form>

@push('scripts')
<script>
    // Avatar preview logic
    function previewAvatar(event) {
        const file = event.target.files && event.target.files[0];
        if (!file) return;

        // Re-run validation for JS-based file size
        if (file.size > 5 * 1024 * 1024) {
            alert('Image too large. Max 5MB.');
            event.target.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const container = document.getElementById('avatar-preview-container');
            container.innerHTML = `<img id="avatarPreviewImg" src="${e.target.result}" alt="Profile">`;
            document.getElementById('remove_avatar_input').value = '0';
        };
        reader.readAsDataURL(file);
    }

    $(document).ready(function() {
        // Initialize Select2 for English Level
        if ($('.select2-init').length) {
            $('.select2-init').select2({
                minimumResultsForSearch: Infinity,
                width: '100%'
            });
        }
        
        // Remove Avatar Button
        $('#btnRemoveAvatar').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Remove profile picture?',
                text: 'This will revert to the default avatar.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it',
                confirmButtonColor: '#dc2626'
            }).then(result => {
                if (result.isConfirmed) {
                    $('#remove_avatar_input').val('1');
                    $('#profile-form').submit();
                }
            });
        });

        // Add custom rule for filesize
        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param);
        }, 'File size must be less than 5MB');

        // jQuery Validation for new form
        $("#profile-form").validate({
            rules: {
                name: { required: true, minlength: 2 },
                email: { required: true, email: true },
                age: { required: true, digits: true, min: 13, max: 120 },
                native_language: { required: true, minlength: 2 },
                english_level: { required: true },
                discord_id: { required: false },
                avatar: { accept: "image/jpeg, image/png, image/jpg", filesize: 5242880 } // 5MB
            },
            messages: {
                age: { digits: "Please enter a valid age", min: "You must be at least 13" },
                avatar: { accept: "Please use a JPG or PNG image." }
            }
        });
    });
</script>
@endpush