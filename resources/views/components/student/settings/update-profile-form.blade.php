{{-- Optimized profile form (cleaned & final-touch) --}}
<form id="profile-form" method="POST" action="{{ route('student.profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PATCH')

    <div class="card settings-card-base">
        <div class="card-body">

            <div class="border-bottom pb-3 mb-3">
                <h5>Account Settings</h5>
            </div>

            {{-- Avatar Section --}}
            <div class="setting-card">
                <label class="form-label mb-2">Profile Photo</label>
                <div class="change-avatar img-upload d-flex align-items-start gap-3">
                    <div class="profile-img" id="avatar-preview-container" style="width:100px;height:100px;border-radius:8px;overflow:hidden;display:flex;align-items:center;justify-content:center;border:1px solid #ddd;">
                        @if(auth()->user()->profile_link && !Str::contains(auth()->user()->profile_link, 'ui-avatars'))
                            <img id="avatarPreviewImg" src="{{ auth()->user()->profile_link }}" alt="Profile" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">
                        @else
                            <i class="fa-solid fa-file-image fa-2x text-muted" aria-hidden="true"></i>
                        @endif
                    </div>

                    <div class="upload-img">
                        <div class="imgs-load d-flex align-items-center">
                            <label class="change-photo mb-0 btn btn-sm btn-outline-secondary" for="avatar-upload" style="cursor:pointer;">
                                Upload New
                            </label>
                            <input
                                type="file"
                                name="avatar"
                                id="avatar-upload"
                                class="upload d-none"
                                accept="image/png,image/jpeg,image/jpg"
                            >
                            @if(auth()->user()->studentProfile?->avatar_url)
                                <a href="#" id="btnRemoveAvatar" class="upload-remove ms-3 btn btn-sm btn-link">Remove</a>
                            @endif
                        </div>
                        <p class="mt-2 mb-0 text-muted">Image should be below 5 MB. Accepted formats: jpg, png.</p>
                    </div>
                </div>

                @error('avatar')
                    <div class="text-danger mt-1"><small>{{ $message }}</small></div>
                @enderror
                <input type="hidden" name="remove_avatar" id="remove_avatar_input" value="0">
            </div>

            {{-- Information Section --}}
            <div class="setting-title mt-4 mb-2">
                <h6>Information</h6>
            </div>

            <div class="setting-card">
                <div class="row g-3">
                    {{-- Username --}}
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="username">Username <span class="text-danger">*</span></label>
                            <input id="username" name="name" type="text" class="form-control"
                                    value="{{ old('name', auth()->user()->name) }}" placeholder="e.g. johndoe123" required>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="email">Email Address <span class="text-danger">*</span></label>
                            <input id="email" name="email" type="email" class="form-control"
                                    value="{{ old('email', auth()->user()->email) }}" placeholder="e.g. user@example.com" required>
                        </div>
                    </div>

                    {{-- DATE OF BIRTH --}}
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="date_of_birth">Date of Birth <span class="text-danger">*</span></label>
                            <input id="date_of_birth" name="date_of_birth" type="date" class="form-control"
                                    value="{{ old('date_of_birth', auth()->user()->studentProfile?->date_of_birth ? auth()->user()->studentProfile->date_of_birth->format('Y-m-d') : null) }}"
                                    max="{{ now()->format('Y-m-d') }}"
                                    required>
                            <small class="form-text text-muted">You must be at least 13 years old.</small> <br>
                        </div>
                    </div>

                    {{-- Mother Tongue --}}
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="mother_tongue">Mother Tongue <span class="text-danger">*</span></label>
                            <input id="mother_tongue" name="native_language" type="text" class="form-control"
                                    value="{{ old('native_language', auth()->user()->studentProfile?->native_language) }}" placeholder="e.g. Hindi, Spanish" required>
                        </div>
                    </div>

                    {{-- Country of residence --}}
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group" data-toggle-group="location">
                            <label class="form-label" for="country_residence">Country of residence <span class="text-danger">*</span></label>
                            <select
                                id="country_residence"
                                name="country_residence"
                                class="form-control"
                                data-toggle="country"
                                data-country="{{ old('country_residence', auth()->user()->studentProfile?->country_residence) }}"
                                required
                                aria-required="true"
                            >
                                <option value="" disabled selected>Please select your country</option>
                            </select>
                            {{-- <small class="form-text text-muted">Required for tax / regulatory purposes.</small> --}}
                        </div>
                    </div>

                    {{-- Discord ID --}}
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="discord_id">Discord ID (Optional)</label>
                            <input id="discord_id" name="discord_id" type="text" class="form-control"
                                    value="{{ old('discord_id', auth()->user()->studentProfile?->discord_id) }}" placeholder="myusername#1234">
                        </div>
                    </div>
                </div>
            </div>
            {{-- Additional Details --}}
            <div class="setting-title mt-4 mb-2">
                <h6>Additional Details</h6>
            </div>
            <div class="setting-card">
                <div class="row">
                    {{-- English Level --}}
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="english_level">English Level <span class="text-danger">*</span></label>
                            <select id="english_level" name="english_level" class="form-control" required>
                                <option value="" disabled selected>Please select your level</option>
                                @php
                                    $levels = ['beginner', 'intermediate', 'advanced', 'native'];
                                    $currentLevel = old('english_level', auth()->user()->studentProfile?->english_level);
                                @endphp
                                @foreach ($levels as $level)
                                    <option value="{{ $level }}" {{ $currentLevel == $level ? 'selected' : '' }}>
                                        {{ ucfirst($level) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="modal-btn text-end p-3">
            <a href="{{ route('student.dashboard') }}" class="btn btn-md btn-light rounded-pill">Cancel</a>
            <button type="submit" class="btn btn-md btn-primary-gradient rounded-pill">Save Changes</button>
        </div>
    </div>
</form>

@push('scripts')
<!-- External libs -->
<script src='https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput-jquery.min.js'></script>
<script src="https://cdn.jsdelivr.net/gh/linuxguist/countries@main/script.js"></script>

<script>
       document.addEventListener("load", loadCountries()); // rquired to load countires
document.addEventListener('DOMContentLoaded', function () {
    // -- Utility constants
    const MAX_FILE_SIZE = 5 * 1024 * 1024; // 5MB in bytes
    const avatarInput = document.getElementById('avatar-upload');
    const avatarPreviewContainer = document.getElementById('avatar-preview-container');
    const removeAvatarInput = document.getElementById('remove_avatar_input');
    const btnRemoveAvatar = document.getElementById('btnRemoveAvatar');

    // --- Avatar preview + client-side file size check
    function previewAvatarFile(file) {
        if (!file) return;
        if (file.size > MAX_FILE_SIZE) {
            alert('Image size exceeds 5MB limit.');
            avatarInput.value = '';
            return;
        }
        const reader = new FileReader();
        reader.onload = function(e) {
            avatarPreviewContainer.innerHTML = `<img id="avatarPreviewImg" src="${e.target.result}" alt="Profile" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">`;
            removeAvatarInput.value = '0';
        };
        reader.readAsDataURL(file);
    }

    if (avatarInput) {
        avatarInput.addEventListener('change', function (event) {
            const file = event.target.files && event.target.files[0];
            previewAvatarFile(file);
        });
    }

    // --- Remove avatar handler (delegated to allow graceful fallback)
    if (btnRemoveAvatar) {
        btnRemoveAvatar.addEventListener('click', function (e) {
            e.preventDefault();

            const doRemoval = () => {
                removeAvatarInput.value = '1';
                // Submit only the hidden input to trigger server remove flow; keep it simple:
                document.getElementById('profile-form').submit();
            };

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'Remove profile picture?',
                    text: 'This will revert to the default avatar.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, remove it',
                    confirmButtonColor: '#dc2626'
                }).then(result => {
                    if (result.isConfirmed) doRemoval();
                });
            } else {
                if (confirm('Remove profile picture? This will revert to the default avatar.')) {
                    doRemoval();
                }
            }
        });
    }

    // --- Initialize countries script (if present)
    if (typeof countries === 'function') {
        try {
            countries(); // the external script populates #country_residence
        } catch (err) {
            // fail silently; country script shouldn't break the form
            console.warn('countries() init failed:', err);
        }
    }

    // Set pre-selected country after options are likely present
    (function setPreselectedCountry() {
        const $select = document.getElementById('country_residence');
        const selected = $select?.dataset?.country;
        if (!selected) return;

        // Try to set value multiple times in the short term while the external script populates options.
        let attempts = 0;
        const maxAttempts = 10;
        const interval = setInterval(() => {
            attempts++;
            if ($select.querySelector(`option[value="${selected}"]`)) {
                $select.value = selected;
                clearInterval(interval);
            } else if (attempts >= maxAttempts) {
                clearInterval(interval);
            }
        }, 100);
    })();

    // --- jQuery validation setup (if jQuery + validator exist)
    if (window.jQuery && $.validator) {
        (function ($) {
            // Clean single definitions for custom rules
            $.validator.addMethod('futureDate', function(value, element) {
                if (!value) return true;
                const today = new Date().toISOString().split('T')[0];
                return value <= today;
            }, 'The date of birth cannot be in the future.');

            $.validator.addMethod('notDefaultSelect', function(value, element) {
            // Returns true (valid) if the value is NOT '-1'
                return value !== '-1';
            }, 'Please select a valid option.');

            $.validator.addMethod('minAge', function(value, element, param) {
                if (!value) return true;
                const birthDate = new Date(value);
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) age--;
                return age >= param;
            }, 'You must be at least {0} years old.');

            $.validator.addMethod('filesize', function (value, element, param) {
                return this.optional(element) || (element.files && element.files[0].size <= param);
            }, 'File size must be less than {0} bytes.');

            $("#profile-form").validate({
                rules: {
                    name: { required: true, minlength: 2 },
                    email: { required: true, email: true },
                    date_of_birth: { required: true, dateISO: true, futureDate: true, minAge: 13 },
                    native_language: { required: true, minlength: 2 },
                    english_level: { required: true },
                    country_residence: { 
                        required: true,
                        notDefaultSelect: true // <-- NEW RULE APPLICATION
                    },
                    avatar: { accept: "image/jpeg,image/png,image/jpg", filesize: MAX_FILE_SIZE }
                },
                messages: {
                    name: { required: "Please provide a username.", minlength: "Username must be at least 2 characters long." },
                    email: { required: "We need your email address for account access.", email: "Please enter a valid email address (e.g., user@example.com)." },
                    date_of_birth: {
                        required: "Please enter your date of birth.",
                        dateISO: "Please enter a valid date (YYYY-MM-DD).",
                        minAge: "You must be at least 13 years old to proceed."
                    },
                    native_language: { required: "Please specify your mother tongue.", minlength: "Mother tongue must be at least 2 characters long." },
                    english_level: { required: "Please select your English proficiency level." },
                    country_residence: { 
                        required: "Please select your country of residence.",
                        notDefaultSelect: "Please select your country of residence." // <-- NEW MESSAGE
                    },
                    avatar: { accept: "The profile photo must be a JPG, PNG, or JPEG file.", filesize: "The file size cannot exceed 5MB." }
                },
                submitHandler: function(form) {
                    form.submit();
                }
            });
        })(jQuery);
    } else {
        // If jQuery validation isn't available, ensure at least browse-side date max + file accept are present.
        // (Server-side validation must still be present.)
    }
});
</script>
@endpush
