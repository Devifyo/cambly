@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<style>
    /* Choices.js Bootstrap 5 Integration Fixes */
    .choices__inner {
        min-height: 38px;
        background-color: #fff;
        border: 1px solid #dee2e6; /* Bootstrap border color */
        border-radius: 0.375rem;   /* Bootstrap border radius */
        padding: 0.375rem 0.75rem; /* Bootstrap padding */
        display: flex;
        align-items: center;
    }
    
    .choices__list--single {
        padding: 0;
    }

    .choices__item {
        font-size: 1rem; /* Match Bootstrap font size */
        color: #212529;  /* Match Bootstrap text color */
    }

    /* Focus state */
    .is-focused .choices__inner {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    /* Dropdown list styling */
    .choices__list--dropdown .choices__item--selectable.is-highlighted {
        background-color: #e9ecef; /* Light gray hover */
        color: #1e2125;
    }
    
    /* Hide the original select input search field style override */
    .choices[data-type*="select-one"] .choices__input {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 5px;
    }
</style>
@endpush

<div class="row">
    <div class="col-12 mb-3">
        <label class="form-label">Name <span class="text-danger">*</span></label>
        <input type="text" wire:model.blur="name" class="form-control @error('name') is-invalid @enderror" required>
        @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="col-12 mb-3">
        <label class="form-label">Email <span class="text-danger">*</span></label>
        <input type="email" wire:model.blur="email" class="form-control @error('email') is-invalid @enderror" required>
        @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Discord Username <span class="text-danger">*</span></label>
        <input type="text" wire:model.blur="discord_id" class="form-control @error('discord_id') is-invalid @enderror" placeholder="username#1234" required>
        @error('discord_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Gender <span class="text-danger">*</span></label>
        <select wire:model.blur="gender" class="form-control @error('gender') is-invalid @enderror" required>
            <option value="">Select Gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>
        @error('gender') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Country of Residence <span class="text-danger">*</span></label>
        
        <div wire:ignore>
            <select class="form-control choices-country">
                <option value="" placeholder>Select Country</option>
            </select>
        </div>
        
        <input type="hidden" wire:model="country_residence" class="country-hidden">
        
        @error('country_residence') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Japanese Level <span class="text-danger">*</span></label>
        <select wire:model.blur="japanese_level" class="form-control @error('japanese_level') is-invalid @enderror" required>
            <option value="">Select Level</option>
            <option value="none">None</option>
            <option value="basic">Basic</option>
            <option value="conversational">Conversational</option>
            <option value="fluent">Fluent</option>
            <option value="native-like">Native-like</option>
        </select>
        @error('japanese_level') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Teaching Experience (Years)</label>
        <input type="number" wire:model.blur="teaching_experience" class="form-control @error('teaching_experience') is-invalid @enderror">
        @error('teaching_experience') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="col-12 mb-3">
        <label class="form-label">Headline / Bio</label>
        <input type="text" wire:model.blur="headline" class="form-control @error('headline') is-invalid @enderror" placeholder="e.g. Senior English Teacher">
        @error('headline') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Password <span class="text-danger">*</span></label>
        <input type="password" wire:model.blur="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
        @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
        @if ($editMode)
            <small class="form-text text-muted">Leave blank to keep current password.</small>
        @endif
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
        <input type="password" wire:model.blur="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" autocomplete="new-password">
        @error('password_confirmation') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>

    <div class="col-12 mb-3">
        <label class="form-label">Status <span class="text-danger">*</span></label>
        <select wire:model.blur="status" class="form-control @error('status') is-invalid @enderror" required>
            <option value="1">Active</option> 
            <option value="0">Inactive</option>
        </select>
        @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
    </div>
</div>