<div class="row">
    <div class="col-12 mb-3">
        <label class="form-label">Name</label>
        <input type="text" wire:model.blur="name" class="form-control @error('name') is-invalid @enderror">
        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="col-12 mb-3">
        <label class="form-label">Email</label>
        <input type="email" wire:model.blur="email" class="form-control @error('email') is-invalid @enderror">
        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label">Gender</label>
        <select wire:model.blur="gender" class="form-control @error('gender') is-invalid @enderror">
            <option value="">Select Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>
        @error('gender') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label">Native Language</label>
        <input type="text" wire:model.blur="native_language" class="form-control @error('native_language') is-invalid @enderror">
        @error('native_language') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Password</label>
        <input type="password" wire:model.blur="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
        @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
        @if ($editMode)
            <small class="form-text text-muted">Leave blank to keep current password.</small>
        @endif
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" wire:model.blur="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" autocomplete="new-password">
        @error('password_confirmation') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="col-12 mb-3">
        <label class="form-label">Status</label>
        <select wire:model.blur="status" class="form-control @error('status') is-invalid @enderror">
            <option value="1">Active</option> 
            <option value="0">Inactive</option>
        </select>
        @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
</div>