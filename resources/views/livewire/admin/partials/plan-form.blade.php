<div class="row">
    <div class="col-12 col-sm-6">
        <div class="mb-3">
            <label class="mb-2">Plan Name <span class="text-danger">*</span></label>
            <input type="text" wire:model="name" class="form-control @error('name') is-invalid @enderror">
            @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="mb-3">
            <label class="mb-2">Price <span class="text-danger">*</span></label>
            <input type="text" wire:model="price" class="form-control @error('price') is-invalid @enderror">
            @error('price') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="mb-3">
            <label class="mb-2">Tickets <span class="text-danger">*</span></label>
            <input type="number" wire:model="credits_per_cycle" class="form-control @error('credits_per_cycle') is-invalid @enderror">
            @error('credits_per_cycle') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="mb-3">
            <label class="mb-2">Is Popular? <span class="text-danger">*</span></label>
            <select wire:model="is_popular" class="form-select @error('is_popular') is-invalid @enderror">
                <option value="">Select</option>
                <option value="0">No</option>
                <option value="1">Yes</option>
            </select>
            @error('is_popular') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="mb-3">
            <label class="mb-2">Status <span class="text-danger">*</span></label>
            <select wire:model="status" class="form-select @error('status') is-invalid @enderror">
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
            @error('status') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="col-12 col-sm-6">
        <div class="mb-3">
            <label class="mb-2">Subtitle</label>
            <input type="text" wire:model="subtitle" class="form-control">
            @error('subtitle') <span class="text-danger small">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="col-12">
        <div class="mb-3">
            <label class="mb-2">Description</label>
            <textarea wire:model="description" class="form-control" rows="3"></textarea>
        </div>
    </div>
    <div class="col-12">
        <div class="mb-3">
            <label class="mb-2">Features</label>
            <textarea wire:model="features" class="form-control" rows="3" placeholder="One per line"></textarea>
        </div>
    </div>
    {{-- <div class="col-12">
        <div class="mb-3">
            <label class="mb-2">Or Icon Link</label>
            <input type="text" wire:model="icon_link" class="form-control @error('icon_link') is-invalid @enderror">
            @error('icon_link') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div> --}}
    <div class="col-12">
        <div class="mb-3">
            <label class="mb-2">Icon Upload</label>
            <input type="file" wire:model="icon_path" class="form-control @error('icon_path') is-invalid @enderror">
            @error('icon_path') <span class="invalid-feedback">{{ $message }}</span> @enderror
            
            {{-- Preview Logic --}}
            <div class="mt-2">
                @if ($icon_path) 
                    <img src="{{ $icon_path->temporaryUrl() }}" width="50">
                @elseif($existing_icon_url)
                    <img src="{{ $existing_icon_url }}" width="50">
                @endif
            </div>
        </div>
    </div>
</div>