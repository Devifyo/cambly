@extends('layouts.student.app')

@section('title', 'Student Dashboard')

@push('styles')
    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css" rel="stylesheet" />

    <style>
        /* ======== FULLCALENDAR CUSTOM DASHBOARD STYLING ======== */
        #calendar {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #fff;
            border-radius: 16px;
            padding: 1rem;
            height: 600px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
        }

        /* Toolbar (Month name and navigation buttons) */
        .fc .fc-toolbar {
            flex-wrap: wrap;
            gap: 10px;
            justify-content: space-between;
            align-items: center;
        }

        .fc .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2c3e50;
        }

        .fc .fc-button {
            border-radius: 8px !important;
            font-weight: 500 !important;
            padding: 0.4rem 0.9rem;
            border: none !important;
            transition: all 0.2s;
            text-transform: capitalize;
        }

        .fc .fc-button-primary {
            background: #4c6ef5 !important;
            color: #fff !important;
        }

        .fc .fc-button-primary:hover {
            background: #3b5bdb !important;
            transform: translateY(-2px);
        }

        .fc .fc-button-active {
            background: #364fc7 !important;
            color: #fff !important;
        }

        /* Calendar Grid */
        .fc-theme-standard td,
        .fc-theme-standard th {
            border: 1px solid #e9ecef !important;
        }

        .fc .fc-col-header-cell {
            background: #f6f8fa !important;
            text-transform: uppercase;
        }

        .fc .fc-col-header-cell-cushion {
            color: #495057 !important;
            font-weight: 600;
            padding: 10px 0;
            font-size: 0.85rem;
        }

        /* Day Numbers */
        .fc .fc-daygrid-day-number {
            color: #343a40;
            font-weight: 500;
            padding: 6px;
            font-size: 0.85rem;
        }

        /* Events */
        .fc-event {
            border: none !important;
            border-radius: 8px !important;
            padding: 5px 8px !important;
            font-size: 0.8rem !important;
            font-weight: 500;
            color: #fff !important;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Different event types */
        .fc-event.upcoming,
        .fc-event.event-upcoming {
            background: linear-gradient(135deg, #4c6ef5, #5f3dc4) !important;
        }

        .fc-event.past,
        .fc-event.event-past {
            background: linear-gradient(135deg, #adb5bd, #868e96) !important;
        }

        .fc-event.event-today {
            background: linear-gradient(135deg, #37b24d, #2b8a3e) !important;
        }

        /* Today Highlight */
        .fc .fc-daygrid-day.fc-day-today {
            background-color: #edf2ff !important;
        }

        /* Event Hover */
        .fc-event:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.15);
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .fc .fc-toolbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .fc .fc-toolbar-chunk {
                margin-bottom: 10px;
            }

            #calendar {
                padding: 0.75rem;
                height: 500px;
            }
        }

.cancel-lesson {
    background: none !important;
    border: none !important;
    padding: 0 !important;
    margin: 0 !important;
    color: #dc3545;
    cursor: pointer;
    transition: all 0.2s ease;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    text-decoration: none;
    
    /* Circle outline */
    border: 2px solid #dc3545 !important;

    /* Center the icon inside */
    display: flex;
    align-items: center;
    justify-content: center;
}

.cancel-lesson:hover {
    color: #fff !important;
    background-color: #dc3545 !important;  /* fill red on hover */
    border-color: #dc3545 !important;
}

.cancel-lesson i {
    font-size: 14px;
    line-height: 1;
}

        
    </style>
@endpush

@section('content')
<div class="container py-4 py-md-5">
    <div class="row">
        <!-- ===== RIGHT COLUMN : Book Tutor + Credits ===== -->
        <div class="col-xl-8 d-flex">
            <div class="dashboard-card w-100">
                <div class="dashboard-card-head border-0">
                    <div class="header-title">
                        <h5>Account & {{ trans_choice('app.credits',2) }} Overview</h5>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <div class="row">
                        <!-- Book Tutor -->
                        <div class="col-sm-7">
                            <div class="book-appointment-head mb-3 d-flex justify-content-between align-items-center">
                                <h3><span>Book a new</span> Lesson</h3>
                                <span class="add-icon"><a href="{{ route('student.tutors.search') }}"><i
                                            class="fa-solid fa-circle-plus"></i></a></span>
                            </div>

                            <!-- Upcoming Lessons Table -->
                            <div class="dashboard-card mt-2">
                                <div class="dashboard-card-head d-flex justify-content-between align-items-center">
                                    <div class="header-title">
                                        <h6>Upcoming Lessons</h6>
                                    </div>
                                    <div class="card-view-link"><a href="{{ route('student.lessons.list') }}">View All</a></div>
                                </div>
                                <div class="dashboard-card-body pt-2">
                                    <div class="table-responsive">
                                        <table class="table dashboard-table appoint-table mb-0">
                                            <tbody>
                                                @forelse ($dashboardDetails['top_upcoming_lessons'] as $lesson)
                                                    <tr>
                                                        <td>
                                                            <div class="patient-info-profile">
                                                                <a href="{{ route('student.lessons.details', ['id' => encryptId($lesson['id'])]) }}" class="table-avatar">
                                                                    <img src="{{ $lesson['teacher_avatar'] }}" alt="{{ $lesson['teacher_name'] }}">
                                                                </a>
                                                                <div class="patient-name-info">
                                                                    <h5><a href="{{ route('student.lessons.details', ['id' => encryptId($lesson['id'])]) }}">{{ $lesson['teacher_name'] }}</a></h5>
                                                                    <span>{{ $lesson['teacher_title'] }}</span>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="appointment-date-created">
                                                                <h6>{{ $lesson['user_formatted_datetime'] }}</h6>
                                                                    {{-- @if($lesson['is_hold'])
                                                                        <span class="badge table-badge bg-warning text-dark">
                                                                            <i class="fa-solid fa-pause-circle me-1"></i>
                                                                            On Hold — Insufficient Credits
                                                                        </span>
                                                                    @else
                                                                        <span class="badge table-badge {{ $lesson['is_today'] ? 'bg-warning' : 'bg-success' }}">
                                                                            {{ $lesson['time_from_now'] }}
                                                                        </span>
                                                                    @endif --}}
                                                                        <span class="badge table-badge {{ $lesson['is_today'] ? 'bg-warning' : 'bg-success' }}">
                                                                            {{ $lesson['time_from_now'] }}
                                                                        </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="apponiment-actions d-flex align-items-center">
                                                                {{-- Join Lesson (commented out) --}}
                                                                {{-- <a href="#" class="text-success-icon me-2" title="Join Lesson">
                                                                    <i class="fa-solid fa-video"></i>
                                                                </a> --}}

                                                                <form action="{{ route('student.booking.cancel', ['reservation' => encryptId($lesson['id'])]) }}" method="POST" class="cancel-lesson-form d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-link text-danger-icon p-0 border-0 bg-transparent cancel-lesson" title="Cancel Lesson">
                                                                        <i class="fa-solid fa-xmark"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3" class="text-center py-4">
                                                            <div class="text-muted">
                                                                <i class="fa-solid fa-calendar-xmark fa-2x mb-2"></i>
                                                                <p class="mb-0">No upcoming lessons scheduled</p>
                                                                <a href="{{ route('student.tutors.search') }}" class="btn btn-primary btn-sm mt-2">
                                                                    Book a Lesson
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>


                            {{-- Upcoming meeting end --}}
                        </div>

                        <!-- Credits Left -->
                        <div class="col-sm-5">
                            <div class="chart-over-all-report text-center">
                                <h6>{{ trans_choice('app.credits',2) }} Left</h6>
                                <div class="circle-bar circle-bar3 report-chart mb-2">
                                    <div class="circle-graph3" data-percent="{{ isset($currentCredits['consume_percentage']) ? $currentCredits['consume_percentage'] : 0 }}">
                                        <p>
                                            {{ trans_choice('app.credits', 2) }} Left<br>
                                            {{ $currentCredits['available'] ?? 0 }} / {{ $currentCredits['issued'] ?? 0 }}
                                        </p>
                                    </div>
                                </div>
                                @if(isset($currentCredits['available']) && $currentCredits['available']  > 1 )
                                    <span class="health-percentage d-block mb-2">You have enough {{ trans_choice('app.credits_lower',2) }} for {{$currentCredits['available'] ?? 0}} more lessons</span>
                                    @else
                                    <span class="health-percentage d-block mb-2">You have {{$currentCredits['available'] ?? 0}} tickets </span>
                                @endif
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#creditDetailsModal" class="text-decoration-none small fw-bold mb-3 d-inline-block text-primary">
    <i class="fa-solid fa-circle-info me-1"></i> View Expiration Details
</a>
                                <a href="{{ route('student.account.subscription') }}" class="btn btn-dark w-100 rounded-pill">Manage Subscriptions<i
                                        class="fa-solid fa-chevron-right ms-2"></i></a>
                            </div>
                        </div>

                    </div>

                    <!-- Subscription Info -->
                    {{-- @if($activeSubscription)
                    <div class="report-gen-date mt-4">
                        <p>Subscription valid till: <strong>{{formatDate($activeSubscription['current_period_start'])}}</strong> <span><i class="fa-solid fa-copy"></i></span></p>
                    </div>
                    @endif --}}
                </div>
            </div>
        </div>

        <!-- ===== RIGHT COLUMN : Dashboard Widgets ===== -->
        <div class="col-xl-4 d-flex">
            <div class="dashboard-box-col w-100">
                <!-- Total Sessions -->
                <div class="dashboard-widget-box">
                    <div class="dashboard-content-info">
                        <h6>Total Lessons</h6>
                        <h4>{{$dashboardDetails['lesson_stats']['total']}}</h4>
                        {{-- <span class="text-success"><i class="fa-solid fa-arrow-up"></i> +2% From Last Week</span> --}}
                    </div>
                    <div class="dashboard-widget-icon">
                        <span class="dash-icon-box"><i class="fa-solid fa-calendar-days"></i></span>
                    </div>
                </div>
                <!-- Upcoming Sessions -->
                <div class="dashboard-widget-box">
                    <div class="dashboard-content-info">
                        <h6>Upcoming Lessons</h6>
                        <h4>{{$dashboardDetails['lesson_stats']['upcoming']}}</h4>
                        {{-- <span class="text-success"><i class="fa-solid fa-arrow-up"></i> +2% From Last Week</span> --}}
                    </div>
                    <div class="dashboard-widget-icon">
                        <span class="dash-icon-box"><i class="fa-solid fa-calendar-days"></i></span>
                    </div>
                </div>
                <!-- Completed Sessions -->
                <div class="dashboard-widget-box">
                    <div class="dashboard-content-info">
                        <h6>Completed Lessons</h6>
                        <h4>{{$dashboardDetails['lesson_stats']['completed']}}</h4>
                        {{-- <span class="text-success"><i class="fa-solid fa-arrow-up"></i> +10% From Last Week</span> --}}
                    </div>
                    <div class="dashboard-widget-icon">
                        <span class="dash-icon-box"><i class="fa-solid fa-user-check"></i></span>
                    </div>
                </div>

            </div>
        </div>

        <!-- ===== CALENDAR ===== -->
        <div class="col-xl-12 mt-4">
            <div class="dashboard-card w-100">
                <div class="dashboard-card-head">
                    <div class="header-title">
                        <h5>Scheduled Lessons</h5>
                    </div>
                    <div class="card-view-link"><a href="#">View Lessons</a></div>
                </div>
                <div class="dashboard-card-body p-3">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

    </div>
</div>
@include('student.partials.credit-details-modal')
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                selectable: false,
                editable: false,
                eventDisplay: 'block',
                eventTimeFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    meridiem: 'short' // This will produce 'pm' or 'am'
                },
                // called when the visible date-range changes
                datesSet: function(dateInfo) {
                    // useful if you want to show the current range in UI
                    console.log('Visible range:', dateInfo.view.activeStart.toISOString().split('T')[0], '->', dateInfo.view.activeEnd.toISOString().split('T')[0]);
                },

                // FullCalendar will call this whenever it needs events for the current view
                events: function(fetchInfo, successCallback, failureCallback) {
                    // fetchInfo.startStr and fetchInfo.endStr are ISO (YYYY-MM-DD) strings
                    const start = fetchInfo.startStr; // inclusive start
                    const end = fetchInfo.endStr;     // exclusive end (FullCalendar common behavior)
                    console.log('Fetching events for range:', start, '->', end);

                    // call your endpoint with both start and end
                    fetch(`/dashboard/calendar-events?start=${encodeURIComponent(start)}&end=${encodeURIComponent(end)}`)
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.json();
                        })
                        .then(serverData => {
                            // If your endpoint already returns events in FullCalendar format, just pass them through:
                            // successCallback(serverData);

                            // Otherwise map your server objects to FullCalendar event objects.
                            // Expected server item example: { id, title, start, end, status, is_today, extendedProps: { reservation_id } }
                            const events = serverData.map(item => {
                                // default colors
                                let bg = '#0d6efd';
                                let bord = '#0d6efd';
                                let text = '#ffffff';

                                if (item.status === 'completed') {
                                    bg = '#198754'; bord = '#198754';
                                } else if (item.status === 'cancelled') {
                                    bg = '#dc3545'; bord = '#dc3545'; text = '#ffffff';
                                }

                                // return FullCalendar event object
                                return {
                                    id: item.id,
                                    title: item.title || 'Untitled',
                                    start: item.start,
                                    end: item.end,
                                    allDay: !!item.allDay,
                                    backgroundColor: bg,
                                    borderColor: bord,
                                    textColor: text,
                                    display: 'block',
                                    classNames: [
                                        item.status === 'upcoming' ? 'event-upcoming' : '',
                                        item.status === 'past' ? 'event-past' : '',
                                        item.is_today ? 'event-today' : '',
                                        item.status === 'cancelled' ? 'event-cancelled' : ''
                                    ].filter(Boolean),
                                    extendedProps: {
                                        reservation_id: item.extendedProps?.reservation_id ?? item.id,
                                        status: item.status
                                    }
                                };
                            });

                            successCallback(events);
                        })
                        .catch(err => {
                            console.error('Error fetching calendar events:', err);
                            failureCallback(err);
                        });
                },

                // style DOM after event is added
                eventDidMount: function(info) {
                    const status = info.event.extendedProps?.status;
                    if (status === 'completed') {
                        info.el.style.backgroundColor = '#198754';
                        info.el.style.borderColor = '#198754';
                        info.el.style.color = '#fff';
                    } else if (status === 'cancelled') {
                        info.el.style.backgroundColor = '#dc3545';
                        info.el.style.borderColor = '#dc3545';
                        info.el.style.opacity = '0.85';
                        info.el.style.color = '#fff';
                    } else {
                        // default blue (you can omit and let FullCalendar use event.backgroundColor)
                        info.el.style.backgroundColor = info.event.backgroundColor || '#0d6efd';
                        info.el.style.borderColor = info.event.borderColor || info.event.backgroundColor || '#0d6efd';
                        info.el.style.color = info.event.textColor || '#fff';
                    }
                },
                
                eventClick: function(info) {
                    // 1. Stop the browser from navigating
                    info.jsEvent.preventDefault(); 
                    
                    // 2. Get the event data
                    const event = info.event;
                    const status = event.extendedProps?.status || 'No status';
                    
                    // --- THIS IS THE FIX ---
                    let timeString = '';
                    if (event.start) {
                        // Get the calendar instance from the 'info' object
                        const calendarInstance = info.view.calendar;

                        // Define the format we want (same as your eventTimeFormat)
                        const timeFormat = {
                            hour: 'numeric',
                            minute: '2-digit',
                            meridiem: 'short'
                        };

                        // Use the calendar's built-in formatter
                        const startTime = calendarInstance.formatDate(event.start, timeFormat);
                        
                        let endTime = '';
                        if(event.end) {
                            endTime = calendarInstance.formatDate(event.end, timeFormat);
                        }
                        timeString = endTime ? `${startTime} - ${endTime}` : startTime;
                    }
                    // --- END OF FIX ---

                    // 4. Show the SweetAlert!
                    Swal.fire({
                        title: event.title,
                        html: `
                            <div style="text-align: left; padding: 0 1rem;">
                                <p><strong>Time:</strong> ${timeString}</p>
                                <p><strong>Status:</strong> <span style="text-transform: capitalize;">${status}</span></p>
                            </div>
                        `,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Go to Details',
                        cancelButtonText: 'Close',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // 5. If they click "Go to Details", navigate
                            const reservationId = event.extendedProps?.reservation_id || event.id;
                            if (reservationId) {
                                window.location = 'lessons/details/' + reservationId;
                            }
                        }
                    });
                }
            });

            calendar.render();  
        });


        (function () {
            let isSubmitting = false;

            $(document).on('submit', '.cancel-lesson-form', function (ev) {
                const $form = $(this);
                const $btn = $form.find('.cancel-lesson');

                // If already confirmed, allow the form to submit naturally
                if (isSubmitting) {
                    return;
                }

                // Stop first submit
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
                        isSubmitting = true;           // Mark as confirmed
                        $btn.prop('disabled', true);   // Prevent button spam
                        $form.submit();                // Submit again (allowed this time)
                    }
                });
            });
        })();

    </script>

@endpush
