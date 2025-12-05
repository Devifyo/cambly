{{-- This component uses the new design with separated fields --}}
<form id="profile-form" method="POST" action="{{ route('teacher.profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PATCH')
    
    {{-- CSS for Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        /* Your specific Select2 Styling fixes */
        .select2-container .select2-selection--multiple {
            min-height: 45px !important;
            border: 1px solid #ced4da;
            display: flex !important;
            align-items: center !important;
            padding: 2px 5px !important;
        }
        .select2-container .select2-selection--multiple .select2-selection__rendered {
            display: block !important;
            width: 100%;
            padding: 0;
            margin: 0;
            line-height: normal;
        }
        .select2-container .select2-search--inline .select2-search__field {
            height: 30px !important;
            margin-top: 0 !important;
            line-height: 30px !important;
            padding-left: 5px;
            vertical-align: middle;
            font-family: inherit;
        }
        .select2-search__field::placeholder {
            color: #6c757d !important;
            opacity: 1;
        }
        .select2-container .select2-selection--single {
            height: 45px !important;
            padding-top: 8px;
            border: 1px solid #ced4da;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            top: 10px;
        }
        .select2-selection__arrow {
            display: none !important;
        }
    </style>

    <div class="card settings-card-base"> 
        <div class="card-body">
            
            <div class="border-bottom pb-3 mb-3">
                <h5>Account Settings</h5>
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
                            <label class="form-label" for="name">Username <span class="text-danger">*</span></label>
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

                    {{-- <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="date_of_birth">Date of Birth <span class="text-danger">*</span></label>
                            <input id="date_of_birth" name="date_of_birth" type="date" class="form-control"
                                   value="{{ old('date_of_birth', auth()->user()->teacherProfile?->date_of_birth ? auth()->user()->teacherProfile->date_of_birth->format('Y-m-d') : null) }}" required>
                        </div>
                    </div> --}}

                        {{-- === NATIVE LANGUAGE COMPONENT === --}}
                        <div class="col-lg-6 col-md-6">
                            <x-inputs.native-language-select />
                        </div>
                    {{-- === MOTHER TONGUE COMPONENT === --}}
                    {{-- <div class="col-lg-6 col-md-6">
                        <x-inputs.mother-tongue-select />
                    </div> --}}


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
                            <label class="form-label" for="english_level">Japanese Level <span class="text-danger">*</span></label>
                            <select id="english_level" name="english_level" class="form-control" required>
                                <option value="" disabled {{ old('english_level', auth()->user()->teacherProfile?->english_level) ? '' : 'selected' }}>Please select your level</option>
                                @php
                                    $levels = ['native-like','fluent', 'conversational', 'basic', 'none'];
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
                            <label class="form-label" for="experience">Teaching Experience (Years) <span class="text-danger">*</span></label>
                            <input type="number" id="experience" name="experience" class="form-control" 
                                   value="{{ old('experience', auth()->user()->teacherProfile?->experience) }}" 
                                   placeholder="e.g., 5" min="0" max="60" required>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="discord_id">Discord username <span class="text-danger">*</span></label>
                            <input id="discord_id" name="discord_id" type="text" class="form-control"
                                   value="{{ old('discord_id', auth()->user()->teacherProfile?->discord_id) }}" placeholder="myusername#1234">
                        </div>
                    </div>
                    
                    <div class="col-lg-6 col-md-6">
                        <div class="form-group" data-toggle-group="location">
                            <label class="form-label" for="country_residence">Country of residence <span class="text-danger">*</span></label>
                            <select
                                id="country_residence"
                                name="country_residence"
                                class="form-control"
                                data-toggle="country"
                                data-country="{{ old('country_residence', auth()->user()->teacherProfile?->country_residence) }}"
                                required
                                aria-required="true"
                            >
                                <option value="" disabled selected>Please select your country</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="form-label" for="short_description">Headline <span class="text-danger">*</span></label>
                            <textarea id="short_description" name="short_bio" class="form-control" rows="3" 
                                      placeholder="A short, catchy headline for your profile (e.g., 'Friendly Native Speaker Specializing in Business English')" required>{{ old('short_bio', auth()->user()->teacherProfile?->short_bio) }}</textarea>
                            <small class="form-text text-muted">Max 50 characters.</small></br>
                        </div>
                    </div>
                    {{-- Introduction --}}
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="form-label" for="introduction">Introduction (Optional)</label>
                            <textarea id="introduction" name="introduction" class="form-control" rows="6"
                                    placeholder="Introduce yourself to students (max 2000 characters).">{{ old('introduction', auth()->user()->teacherProfile?->introduction) }}</textarea>
                            <small class="form-text text-muted">Max 2000 characters.</small>
                        </div>
                    </div>
                    {{-- End Introduction --}}

                    {{-- Games --}}
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="form-label" for="games">Games (Optional)</label>
                            <input id="games" type="text" name="games" class="form-control"
                                placeholder="Games you like (max 100 characters)"
                                value="{{ old('games', auth()->user()->teacherProfile?->games) }}">
                            <small class="form-text text-muted">Max 100 characters.</small>
                        </div>
                    </div>
                    {{-- End Games --}}

                    {{-- Video URL (YouTube) --}}
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="form-label" for="youtube_url">Video (YouTube URL, Optional)</label>
                            <input id="youtube_url" type="url" name="youtube_url" class="form-control"
                                placeholder="https://www.youtube.com/watch?v=xxxxxxx"
                                value="{{ old('youtube_url', auth()->user()->teacherProfile?->youtube_url) }}">
                            <small class="form-text text-muted">Paste a valid YouTube link. The video will be embedded on your profile.</small>
                        </div>
                        @if(auth()->user()->teacherProfile?->youtube_url)
                            <a href="javascript:void(0);" 
                            class="d-inline-flex align-items-center mt-2"
                            onclick="openVideoPreview('{{ auth()->user()->teacherProfile->youtube_url }}')">
                                <i class="fa-solid fa-play me-2"></i> View Video
                            </a>
                        @endif
                    </div>
                    {{-- End Video --}}
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
{{-- Libraries --}}
{{-- Note: ISO-639-1 is now loaded by your component, but keeping it here is fine as a fallback or global resource --}}

<script>
    document.addEventListener("load", loadCountries()); 

    function previewAvatar(event) {
        const file = event.target.files && event.target.files[0];
        if (!file) return;
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
        // --- NOTE: Language logic removed here because the x-inputs components handle it individually ---

        // --- VALIDATION RULES ---
        $.validator.addMethod('filesize', function (value, element, param) {
            return this.optional(element) || (element.files[0].size <= param);
        }, 'File size must be less than 5MB');

        $.validator.addMethod('futureDate', function(value, element) {
            if (!value) return true;
            return value <= new Date().toISOString().split('T')[0];
        }, 'Date cannot be in the future.');

        $.validator.addMethod('notDefaultSelect', function(value, element) {
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

        $.validator.addMethod("youtubeUrl", function (value, element) {
            // Return true if the field is empty (optional)
            if (this.optional(element)) {
                return true;
            }
            
            const pattern = /^(https?:\/\/)?((www\.|m\.)?youtube\.com|youtu\.be)\//i;
            
            return pattern.test(value);
        }, "Please enter a valid YouTube URL (e.g., youtube.com or youtu.be).");

        // Initialize Validation
        $("#profile-form").validate({
            ignore: [], 
            rules: {
                name: { required: true, minlength: 2 },
                email: { required: true, email: true },
                // date_of_birth: { required: true, futureDate: true, minAge: 13 },
                // These rules still work because your components output inputs with these names
                // mother_tongue: { required: true },
                discord_id: {required:true},
                "native_languages[]": { required: true, maxlength: 3 },
                english_level: { required: true },
                country_residence: { required: true, notDefaultSelect: true },
                experience: { required: true, digits: true, min: 0, max: 60 },
                short_bio: { required: true, minlength: 10, maxlength: 50 },
                introduction: { maxlength: 2000 },
                games: { maxlength: 100 },
                youtube_url: { url: true, youtubeUrl: true }
            },
            messages: {
                mother_tongue: { required: "Please select your mother tongue." },
                "native_languages[]": {
                    required: "Please select at least one native language.",
                    maxlength: "You cannot select more than 3."
                },
                english_level: {required: "Please select your Japanese level."},
                discord_id: {  required: "Please provide a discord username."},
                date_of_birth: { required: "Please enter your date of birth", futureDate: "The date of birth cannot be in the future.", minAge: "You must be at least 13 years old." },
                experience: {
                    required: "Please enter your years of experience",
                    digits: "Please enter a valid number",
                    min: "Please enter a valid number (0 or more)",
                    max: "Please enter a valid number (60 or less)"
                },
                short_bio: { 
                    required: "Headline is required.",
                    minlength: "Headline must be at least 5 characters.",
                    maxlength: "Headline cannot exceed 50 characters."
                },

                introduction: { 
                    maxlength: "Introduction cannot exceed 2000 characters."
                },

                games: { 
                    maxlength: "Games field cannot exceed 100 characters."
                },

                youtube_url: { 
                    url: "Please enter a valid URL.",
                    youtubeUrl: "Please enter a valid YouTube link."
                },
            },
            errorPlacement: function(error, element) {
                // For Select2 elements
                if (element.hasClass('select2-hidden-accessible')) {
                    error.insertAfter(element.next('.select2'));
                } 
                // FIX: If the input has helper text (class .form-text), place error AFTER the helper text
                else if (element.next('.form-text').length > 0) {
                    error.insertAfter(element.next('.form-text'));
                    // Add a line break to ensure the error sits on a new line below the helper text
                    $("<br>").insertBefore(error);
                } 
                // Default placement
                else {
                    error.insertAfter(element);
                }
            }
        });

        $('#btnRemoveAvatar').on('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Remove profile picture?',
                text: 'Revert to default?',
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
    });
</script>
@endpush