<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999; max-width: 400px;">
    @if ($show)
        @php
            $classes = match($type) {
                'success' => 'alert-success',
                'error', 'danger' => 'alert-danger',
                'warning' => 'alert-warning',
                default => 'alert-info',
            };
            $title = match($type) {
                'success' => 'Success!',
                'error', 'danger' => 'Error!',
                'warning' => 'Warning!',
                default => 'Notice:',
            };
        @endphp

        {{-- 
            Added wire:click="close" to the button to sync with PHP state 
            Added x-init to auto-dismiss after 4 seconds (Optional but good for toasts)
        --}}
        <div 
            x-data="{ show: true }" 
            x-show="show"
            x-transition.duration.500ms
            x-init="setTimeout(() => @this.call('close'), 4000)"
            class="alert {{ $classes }} alert-dismissible fade show d-flex align-items-start gap-2 shadow-lg" 
            role="alert"
        >
            <div>
                <strong>{{ $title }}</strong> {!! $message !!}
            </div>
            
            {{-- Calls the Livewire close method --}}
            <button type="button" wire:click="close" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
</div>