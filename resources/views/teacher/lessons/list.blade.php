@extends('layouts.student.app')

@section('title', 'My Lessons')

@push('styles')
    <style>
        .lessons-container { 
            max-width: 1200px; 
            margin: 0 auto;
            /* This margin pushes the content below the overlapping header */
            margin-top: 5.0rem; 
        }
        
        /* Filter Pills */
        .filter-pills {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
            margin-top: 2rem; 
        }
        .filter-pill {
            padding: 0.5rem 1.25rem;
            border-radius: 50px;
            border: 2px solid #e0e0e0;
            background: white;
            color: #666;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .filter-pill:hover {
            border-color: #0E82FD; /* Simpler hover for gradient borders */
            color: #0E82FD;
            transform: translateY(-2px);
        }
        .filter-pill.active {
            background: linear-gradient(90deg, #0E82FD 0%, #06AED4 70%);
            border-color: #0E82FD;
            color: white;
        }
        .filter-badge {
            background: rgba(255,255,255,0.3);
            padding: 0.125rem 0.5rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .filter-pill.active .filter-badge {
            background: rgba(255,255,255,0.3);
        }

        /* Lesson Cards */
        .lesson-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            border: 1px solid #e5e7eb;
            transition: all 0.2s;
        }
        .lesson-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }
        .lesson-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }
        .lesson-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.25rem;
        }
        .lesson-teacher {
            color: #6b7280;
            font-size: 0.9rem;
        }
        .lesson-meta {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
            color: #6b7280;
            font-size: 0.9rem;
        }
        .lesson-meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .lesson-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        /* Status Badge */
        .status-badge {
            padding: 0.375rem 0.875rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-upcoming { background: #dbeafe; color: #1e40af; }
        .status-completed { background: #d1fae5; color: #065f46; }
        .status-cancelled { background: #fee2e2; color: #991b1b; }
        .status-confirmed { background: #e0e7ff; color: #4338ca; }

        /* Buttons */
        .btn-action {
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
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

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #9ca3af;
        }
        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            /* Stack lesson card details */
            .lesson-header {
                flex-direction: column;
                gap: 1rem;
            }
            .lesson-meta {
                flex-direction: column;
                gap: 0.5rem;
            }

            /* Stack search bar */
            .doctors-search-box .search-box-one {
                flex-direction: column;
                gap: 0.75rem;
            }
            .doctors-search-box .search-input {
                width: 100%;
            }
            .search-calendar-line {
                /* Remove vertical line and add a top border */
                border-left: none;
                padding-left: 0;
                border-top: 1px solid #e0e0e0;
                padding-top: 0.75rem;
            }
            .doctors-search-box .form-search-btn {
                width: 100%;
            }
            .doctors-search-box .form-search-btn .btn {
                width: 100%;
                justify-content: center;
            }
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
                            <li class="breadcrumb-item active">My Lessons</li>
                        </ol>
                        <h2 class="breadcrumb-title">My Lessons</h2>
                    </nav>
                </div>
            </div>
            <div class="bg-primary-gradient rounded-pill doctors-search-box">
                <div class="search-box-one rounded-pill">
                    
                    <form method="GET" action="{{ route('student.lessons.list') }}" id="searchForm">
                        <input type="hidden" name="filter" value="{{ request('filter') }}">

                        <div class="search-input search-line">
                            <i class="isax isax-teacher bficon"></i>
                            <div class="mb-0">
                                <input 
                                    type="text" 
                                    name="teacher" 
                                    class="form-control" 
                                    placeholder="Search by teacher name..."
                                    value="{{ request('teacher') }}"
                                >
                            </div>
                        </div>

                        <div class="search-input search-calendar-line">
                            <i class="isax isax-calendar-tick5"></i>
                            <div class="mb-0">
                                <input 
                                    type="text" 
                                    name="date" 
                                    id="lessons_datepicker"  {{-- ID is crucial for JS --}}
                                    class="form-control datetimepicker" 
                                    placeholder="Filter by date (YYYY-MM-DD)"
                                    autocomplete="off"
                                    value="{{ request('date') }}"
                                >
                            </div>
                        </div>

                        <div class="form-search-btn">
                            <button class="btn btn-primary d-inline-flex align-items-center rounded-pill" type="submit">
                                <i class="isax isax-search-normal-15 me-2"></i>Search
                            </button>
                        </div>
                    </form>
                    </div>
            </div>
        </div>
        <div class="breadcrumb-bg">
            <img src="{{asset('assets/img/bg/breadcrumb-bg-01.png')}}" alt="img" class="breadcrumb-bg-01">
            <img src="{{asset('assets/img/bg/breadcrumb-bg-02.png')}}" alt="img" class="breadcrumb-bg-02">
        </div>
    </div>

<div class="lessons-container">
    <div class="filter-pills">
        <a href="{{ route('student.lessons.list') }}" 
           class="filter-pill {{ !request('filter') ? 'active' : '' }}">
            All Lessons
            <span class="filter-badge">{{ $stats['upcoming'] + $stats['completed'] + $stats['cancelled'] }}</span>
        </a>
        <a href="{{ route('student.lessons.list', ['filter' => 'upcoming'] + request()->except('filter')) }}" 
           class="filter-pill {{ request('filter') === 'upcoming' ? 'active' : '' }}">
            📅 Upcoming
            <span class="filter-badge">{{ $stats['upcoming'] }}</span>
        </a>
        <a href="{{ route('student.lessons.list', ['filter' => 'completed'] + request()->except('filter')) }}" 
           class="filter-pill {{ request('filter') === 'completed' ? 'active' : '' }}">
            ✅ Completed
            <span class="filter-badge">{{ $stats['completed'] }}</span>
        </a>
        <a href="{{ route('student.lessons.list', ['filter' => 'cancelled'] + request()->except('filter')) }}" 
           class="filter-pill {{ request('filter') === 'cancelled' ? 'active' : '' }}">
            ❌ Cancelled
            <span class="filter-badge">{{ $stats['cancelled'] }}</span>
        </a>
    </div>

    <div id="lessonsContainer">
        @if ($lessons->count() === 0)
            <div class="empty-state">
                <div class="empty-state-icon">📚</div>
                <h3>No lessons found</h3>
                <p>Try adjusting your filters or book a new lesson</p>
            </div>
        @else
            @foreach ($lessons as $item)
                @php
                    // *** UPDATED LINE ***
                    // Use the pre-formatted, localized date from the service
                    $startDisplay = formatLessonDateTime($item->start_at_local);
                    
                    // These are the same as before
                    $durationText = $item->duration ? $item->duration . ' min' : 'N/A';
                    $statusDisplay = $item->display_status ?? $item->status;
                    
                    $statusClass = match($statusDisplay) {
                        'cancelled' => 'status-cancelled',
                        'completed' => 'status-completed',
                        'confirmed' => 'status-confirmed',
                        default => 'status-upcoming'
                    };
                @endphp

                <div class="lesson-card">
                   <div class="lesson-header">
                        <div>
                            <div class="lesson-title">{{ $startDisplay }}</div>
                            <div class="lesson-teacher">with {{ $item->teacher_name }}</div>
                        </div>

                        <span class="status-badge {{ $item->is_hold ? 'bg-warning text-dark' : $statusClass }}">
                            {{-- @if($item->is_hold)
                                <i class="fa-solid fa-pause-circle me-1"></i>
                                Hold — Insufficient Credits
                            @else
                                {{ ucfirst($statusDisplay) }}
                            @endif --}}
                             {{ ucfirst($statusDisplay) }}
                        </span>
                    </div>


                    <div class="lesson-meta">
                        <div class="lesson-meta-item">
                            <i data-feather="calendar" style="width: 16px; height: 16px;"></i>
                            {{ $startDisplay }} {{-- This now shows local time --}}
                        </div>
                        <div class="lesson-meta-item">
                            <i data-feather="clock" style="width: 16px; height: 16px;"></i>
                            {{ $durationText }}
                        </div>
                        <div class="lesson-meta-item">
                            <i data-feather="hash" style="width: 16px; height: 16px;"></i>
                            ID: {{ encryptId($item->id) }}
                        </div>
                    </div>

                    <div class="lesson-actions">
                        <a href="{{ route('student.lessons.details', ['id' => encryptId($item->id)]) }}" 
                           class="btn-action btn-view">
                            View Details
                        </a>

                        <form class="d-inline cancel-form" 
                              method="POST" 
                              action="{{ route('student.booking.cancel', ['reservation' => encryptId($item->id)]) }}">
                            @csrf
                            <input type="hidden" name="reservation_id" value="{{ encryptId($item->id) }}">
                            <button
                                type="submit"
                                class="btn-action btn-cancel"
                                {{ $item->can_cancel ? '' : 'disabled' }}
                            >
                                {{ $item->can_cancel ? 'Cancel Lesson' : 'Cannot Cancel' }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            <div class="mt-4">
                {{ $lessons->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
{{-- Assuming jQuery, Bootstrap, Moment, and Feather JS are in the main layout --}}

<script>
    // This is needed to render icons in the lesson cards (calendar, clock, etc.)
    // If it's already in your main layout script, you can remove this line.
    feather.replace();

    (function () {
        // Initialize datepicker
        // This will find the input by its ID in the new header
        if ($('#lessons_datepicker').length) {
            $('#lessons_datepicker').datetimepicker({
                format: 'YYYY-MM-DD',
                showClose: true,
                showClear: true,
                icons: {
                    time: 'fa fa-clock', // Ensure all icons are defined
                    date: 'fa fa-calendar',
                    up: 'fa fa-chevron-up',
                    down: 'fa fa-chevron-down',
                    previous: 'fa fa-chevron-left',
                    next: 'fa fa-chevron-right',
                    today: 'fa fa-calendar-check',
                    clear: 'fa fa-trash',
                    close: 'fa fa-times'
                }
            });
        }

        let isSubmitting = false;

        // Cancel form confirmation
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