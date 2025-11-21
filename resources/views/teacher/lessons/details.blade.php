@extends('layouts.teacher.app')

@section('title', 'Lesson Details')

@push('styles')
    <style>
        .lessons-container { 
            max-width: 1200px; 
            margin: 0 auto;
            /* Margin to push content below the overlapping header */
            margin-top: 2rem; 
        }

        /* Card styles for details */
        .lesson-details-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .details-grid {
            display: grid;
            /* Creates 1, 2, or 3 columns based on space */
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .detail-item {
            padding: 1rem;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        .detail-item label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            margin-bottom: 0.25rem;
        }
        .detail-item p {
            font-size: 1rem;
            font-weight: 500;
            color: #111827;
            margin: 0;
            word-wrap: break-word; /* For long meeting links */
        }

        /* Status Badge (Copied from list) */
        .status-badge {
            padding: 0.375rem 0.875rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }
        .status-upcoming { background: #dbeafe; color: #1e40af; }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .status-confirmed { background: #e0e7ff; color: #4338ca; }
        
        /* Buttons (Copied from list) */
        .btn-action {
            padding: 0.6rem 1.25rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-view {
            background: #f3f4f6;
            color: #374151;
        }
        .btn-view:hover {
            background: #e5e7eb;
            color: #111827;
        }
        .btn-cancel {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }
        .btn-cancel:hover:not(:disabled) {
            background: #fee2e2;
            border-color: #fca5a5;
        }
        .btn-cancel:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        /* New Join Button */
        .btn-join {
            background: linear-gradient(90deg, #10b981 0%, #059669 70%);
            color: white;
            font-weight: 600;
        }
        .btn-join:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

    </style>
@endpush

@section('content')

    <div class="breadcrumb-bar overflow-visible">
        <div class="container">
            <div class="row align-items-center inner-banner">
                <div class="col-md-12 col-12 text-center">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/"><i class="isax isax-home-15"></i></a></li>
                            <li class="breadcrumb-item"><a href="{{ route('teacher.lessons.list') }}">{{ is_impersonating() ? 'Student Lessons' : 'My Lessons' }}</a></li>
                            <li class="breadcrumb-item active">Lesson Details</li>
                        </ol>
                        <h2 class="breadcrumb-title">Lesson Details</h2>
                    </nav>
                </div>
            </div>
            </div>
        <div class="breadcrumb-bg">
            <img src="{{asset('assets/img/bg/breadcrumb-bg-01.png')}}" alt="img" class="breadcrumb-bg-01">
            <img src="{{asset('assets/img/bg/breadcrumb-bg-02.png')}}" alt="img" class="breadcrumb-bg-02">
        </div>
    </div>

    <div class="lessons-container">
        
        <div class="mb-3">
            <a href="{{ route('teacher.lessons.list') }}" class="btn-action btn-view">
                <i data-feather="arrow-left" style="width:16px; height:16px;"></i>
                Back to All Lessons
            </a>
        </div>

        <div class="lesson-details-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="background: #f9fafb;">
                <h4 class="card-title mb-0 me-3">Lesson with {{ $lesson->student_name }}</h4>
                @php
                    $statusClass = match($lesson->display_status) {
                        'cancelled' => 'status-cancelled',
                        'completed' => 'status-completed',
                        'confirmed' => 'status-confirmed',
                        default => 'status-upcoming'
                    };
                @endphp
                <span class="status-badge {{ $lesson->is_hold ? 'bg-warning text-dark' : $statusClass }}">
                    {{-- @if($lesson->is_hold)
                        <i class="fa-solid fa-pause-circle me-1"></i>
                        Hold — Insufficient Credits
                    @else
                        {{ ucfirst($lesson->display_status) }}
                    @endif --}}
                     {{ ucfirst($lesson->display_status) }}
                </span>
            </div>

            <div class="card-body">
                <div class="details-grid">
                    <div class="detail-item">
                        <label>Starts (Your Time)</label>
                        <p>
                            @if($lesson->start_at_local)
                                {{ formatLessonDateTime($lesson->start_at_local) }}
                                <small class="d-block text-muted">({{ $userTimezone }})</small>
                            @else
                                —
                            @endif
                        </p>
                    </div>
                    <div class="detail-item">
                        <label>Ends (Your Time)</label>
                        <p>
                            @if($lesson->end_at_local)
                                {{ formatLessonDateTime($lesson->end_at_local) }}
                            @else
                                —
                            @endif
                        </p>
                    </div>
                    <div class="detail-item">
                        <label>Duration</label>
                        <p>{{ $lesson->duration ? $lesson->duration . ' minutes' : 'N/A' }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Teacher</label>
                        <p>{{ $lesson->teacher_name }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Lesson ID</label>
                        <p>{{ $lesson->id }}</p>
                    </div>
                    <div class="detail-item">
                        <label>Official Status</label>
                        <p class="text-capitalize">{{ $lesson->status }}</p>
                    </div>
                </div>

                {{-- @if($lesson->lesson_meeting_link && $lesson->display_status !== 'completed' && $lesson->display_status !== 'cancelled')
                    <div class="details-grid mt-3">
                        <div class="detail-item" style="grid-column: 1 / -1;">
                            <label>Meeting Link</label>
                            <p>
                                <a href="{{ $lesson->lesson_meeting_link }}" target="_blank">
                                    {{ $lesson->lesson_meeting_link }}
                                </a>
                            </p>
                        </div>
                    </div>
                @endif --}}
                <div class="details-grid mt-3">
                    <div class="detail-item" style="grid-column: 1 / -1;">
                        <label>Meeting Link</label>
                        
                        <div id="meeting-link-wrapper-{{ encryptId($lesson->id) }}">
                            @if($lesson->lesson_meeting_link)
                                <p class="mb-0">
                                    <a href="{{ $lesson->lesson_meeting_link }}" target="_blank" class="meeting-link-href">
                                        {{ $lesson->lesson_meeting_link }}
                                    </a>
                                </p>
                            @else
                                <p class="mb-0 text-muted meeting-link-href">
                                    No meeting link has been added.
                                </p>
                            @endif
                        </div>

                        @if($lesson->display_status !== 'completed' && $lesson->display_status !== 'cancelled')
                            <button type="button" 
                                class="btn btn-sm btn-outline-primary mt-2 btn-update-link" 
                                data-url="{{ route('teacher.lessons.update-link', ['id' => encryptId($lesson->id)]) }}"
                                data-current-link="{{ $lesson->lesson_meeting_link ?? '' }}"
                                data-wrapper-id="meeting-link-wrapper-{{ encryptId($lesson->id) }}">
                                <i class="fa-solid fa-pen-to-square me-1"></i>
                                <span class="btn-text">{{ $lesson->lesson_meeting_link ? 'Edit Link' : 'Add Link' }}</span>
                            </button>
                        @endif
                    </div>
                </div>
                {{--  --}}
            </div>

            <div class="card-footer d-flex flex-wrap gap-2" style="background: #f9fafb;">
                @if ($lesson->can_join)
                    <a href="{{ $lesson->lesson_meeting_link }}" target="_blank" class="btn-action btn-join">
                        <i data-feather="video" style="width:16px; height:16px;"></i>
                        Join Lesson Now
                    </a>
                @endif

                <form class="d-inline cancel-form" 
                      method="POST" 
                      action="{{ route('teacher.booking.cancel', ['reservation' => encryptId($lesson->id)]) }}">
                    @csrf
                    <input type="hidden" name="reservation_id" value="{{ encryptId($lesson->id) }}">
                    <button
                        type="submit"
                        class="btn-action btn-cancel"
                        {{ $lesson->can_cancel ? '' : 'disabled' }}
                    >
                        <i data-feather="x-circle" style="width:16px; height:16px;"></i>
                        {{ $lesson->can_cancel ? 'Cancel Lesson' : 'Cannot Cancel' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // This is needed to render icons (arrow-left, video, etc.)
    feather.replace();

    (function () {
        let isSubmitting = false;
        
        // Cancel form confirmation (same as list page)
        $(document).on('submit', '.cancel-form', function (ev) {
            const $form = $(this);
            const $btn = $form.find('button[type="submit"]');
            
            if ($btn.is(':disabled')) {
                ev.preventDefault();
                return;
            }

            // If already confirmed, allow submission
            if (isSubmitting) {
                isSubmitting = false;
                return;
            }

            ev.preventDefault();
            
            Swal.fire({
                title: 'Cancel this lesson?',
                html: '<p>Are you sure you want to cancel this lesson?<br>Cancellation policies may apply.</p>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, cancel it',
                cancelButtonText: 'Keep lesson',
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
            }).then((result) => {
                if (result.isConfirmed) {
                    isSubmitting = true;
                    $form.submit();
                }
            });
        });
    })();

    $(document).ready(function() {

            // Setup AJAX to automatically send the CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            /**
             * Parses a standard Laravel AJAX error response.
             * @param {object} xhr - The jQuery XHR object.
             * @returns {string} A human-readable error message.
             */
            function getErrorMessage(xhr) {
                let errorMsg = 'An error occurred. Please try again.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.errors && xhr.responseJSON.errors.lesson_meeting_link) {
                        // Gets the first validation error
                        errorMsg = xhr.responseJSON.errors.lesson_meeting_link[0];
                    } else if (xhr.responseJSON.message) {
                        // Gets a custom message from the controller
                        errorMsg = xhr.responseJSON.message;
                    }
                }
                return errorMsg;
            }

            // Use async function for event handler
            $(document).on('click', '.btn-update-link', async function(e) {
                e.preventDefault();

                const $button = $(this);
                const postUrl = $button.data('url');
                const currentLink = $button.data('current-link');
                const wrapperId = $button.data('wrapper-id');
                const $btnText = $button.find('.btn-text');
                const originalBtnText = $btnText.text(); // Store the original text

                try {
                    // 1. Show SweetAlert prompt and "await" the user's response
                    const result = await Swal.fire({
                        title: 'Update Meeting Link',
                        text: 'Enter the new meeting URL (e.g., Zoom, Google Meet). Leave blank to remove.',
                        input: 'url',
                        inputValue: currentLink,
                        inputPlaceholder: 'https://...',
                        showCancelButton: true,
                        confirmButtonText: 'Save Link',
                        inputValidator: (value) => {
                            if (value && !value.startsWith('http://') && !value.startsWith('https://')) {
                                return 'Please enter a valid URL (starting with http:// or https://)';
                            }
                        }
                    });

                    // 2. Stop if the user clicked "Cancel"
                    if (!result.isConfirmed) {
                        return;
                    }

                    // 3. Set loading state
                    $button.prop('disabled', true);
                    $btnText.text('Saving...');

                    const newLink = result.value || '';

                    // 4. "await" the AJAX POST request
                    const response = await $.ajax({
                        type: 'POST',
                        url: postUrl,
                        data: {
                            lesson_meeting_link: newLink
                        }
                    });

                    // 5. Handle success
                    if (!response.success) {
                        // Throw an error to be caught by the 'catch' block
                        throw new Error(response.message || 'Could not update the link.');
                    }

                    const $wrapper = $('#' + wrapperId);
                    
                    if (response.new_link) {
                        // --- Link Added/Updated ---
                        $wrapper.html(
                            `<p class="mb-0">
                                <a href="${response.new_link}" target="_blank" class="meeting-link-href">
                                    ${response.new_link}
                                </a>
                            </p>`
                        );
                        $button.data('current-link', response.new_link);
                        $btnText.text('Edit Link');
                    } else {
                        // --- Link Removed ---
                        $wrapper.html(
                            `<p class="mb-0 text-muted meeting-link-href">
                                No meeting link has been added.
                            </p>`
                        );
                        $button.data('current-link', '');
                        $btnText.text('Add Link');
                    }

                    Swal.fire({
                        title: 'Success!',
                        text: 'Meeting link has been updated.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });

                } catch (error) {
                    // 6. Handle ALL errors (AJAX, validation, thrown)
                    const errorMessage = (error.responseJSON) ? getErrorMessage(error) : error.message;
                    Swal.fire('Error', errorMessage, 'error');
                    
                    // Restore the original button text on failure
                    $btnText.text(originalBtnText);

                } finally {
                    // 7. ALWAYS re-enable the button
                    $button.prop('disabled', false);
                }
            });
    });
</script>
@endpush