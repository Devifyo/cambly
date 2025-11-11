@if ($message)
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

    <div class="alert {{ $classes }} alert-dismissible fade show d-flex align-items-start gap-2" role="alert">
        <div>
            <strong>{{ $title }}</strong> {!! $message !!}
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
