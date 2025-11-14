@extends('layouts.student.app')

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
                            <li class="breadcrumb-item"><a href="{{ route('student.lessons.list') }}">My Lessons</a></li>
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
            <a href="{{ route('student.lessons.list') }}" class="btn-action btn-view">
                <i data-feather="arrow-left" style="width:16px; height:16px;"></i>
                Back to All Lessons
            </a>
        </div>

        <div class="lesson-details-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap" style="background: #f9fafb;">
                <h4 class="card-title mb-0 me-3">Lesson with {{ $lesson->teacher_name }}</h4>
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

                @if($lesson->lesson_meeting_link && $lesson->display_status !== 'completed' && $lesson->display_status !== 'cancelled')
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
                @endif
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
                      action="{{ route('student.booking.cancel', ['reservation' => encryptId($lesson->id)]) }}">
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
</script>
@endpush