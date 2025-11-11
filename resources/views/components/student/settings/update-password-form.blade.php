<div class="card settings-card-base" id="password-card">
    <form id="password-form" method="POST" action="{{ route('student.password.update') }}">
        @csrf
        @method('PUT')
        
        <div class="card-body">
            <div class="border-bottom pb-3 mb-3">
                <h5>Change Password</h5>
            </div>

            <div class="setting-card"> {{-- Added this wrapper --}}
                <div class="form-group">
                    <label class="form-label" for="current_password">Current Password <span class="text-danger">*</span></label>
                    <input id="current_password" name="current_password" type="password" class="form-control" required>
                    @error('current_password', 'updatePassword') <div class="text-danger"><small>{{ $message }}</small></div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_password">New Password <span class="text-danger">*</span></label>
                    <input id="new_password" name="new_password" type="password" class="form-control" required>
                    @error('new_password', 'updatePassword') <div class="text-danger"><small>{{ $message }}</small></div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="new_password_confirmation">Confirm New Password <span class="text-danger">*</span></label>
                    <input id="new_password_confirmation" name="new_password_confirmation" type="password" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="modal-btn text-end">
            <button type="submit" class="btn btn-md btn-primary-gradient rounded-pill">Update Password</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    $(function() {
        // Initialize validation on the password form
        $("#password-form").validate({
            rules: {
                current_password: {
                    required: true
                },
                new_password: {
                    required: true,
                    minlength: 8
                },
                new_password_confirmation: {
                    required: true,
                    minlength: 8,
                    equalTo: "#new_password"
                }
            },
            messages: {
                 current_password: {
                    required: "Please provide current password",
                 },
                new_password: {
                    required: "Please provide a new password",
                    minlength: "Your password must be at least 8 characters long"
                },
                new_password_confirmation: {
                    required: "Please confirm your new password",
                    minlength: "Your password must be at least 8 characters long",
                    equalTo: "The passwords do not match"
                }
            }
        });
    });
</script>
@endpush