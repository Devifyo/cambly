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
    </style>
@endpush

@section('content')
<div class="container py-4 py-md-5">
    <div class="row">

        <!-- ===== LEFT COLUMN : Dashboard Widgets ===== -->
        <div class="col-xl-4 d-flex">
            <div class="dashboard-box-col w-100">
                <!-- Upcoming Sessions -->
                <div class="dashboard-widget-box">
                    <div class="dashboard-content-info">
                        <h6>Upcoming Sessions</h6>
                        <h4>5</h4>
                        <span class="text-success"><i class="fa-solid fa-arrow-up"></i> +2% From Last Week</span>
                    </div>
                    <div class="dashboard-widget-icon">
                        <span class="dash-icon-box"><i class="fa-solid fa-calendar-days"></i></span>
                    </div>
                </div>

                <!-- Completed Sessions -->
                <div class="dashboard-widget-box">
                    <div class="dashboard-content-info">
                        <h6>Completed Sessions</h6>
                        <h4>15</h4>
                        <span class="text-success"><i class="fa-solid fa-arrow-up"></i> +10% From Last Week</span>
                    </div>
                    <div class="dashboard-widget-icon">
                        <span class="dash-icon-box"><i class="fa-solid fa-user-check"></i></span>
                    </div>
                </div>

                <!-- Bonus Credits -->
                <div class="dashboard-widget-box">
                    <div class="dashboard-content-info">
                        <h6>Bonus Credits</h6>
                        <h4>20</h4>
                        <span class="text-warning"><i class="fa-solid fa-arrow-up"></i> +5% From Last Week</span>
                    </div>
                    <div class="dashboard-widget-icon">
                        <span class="dash-icon-box"><i class="fa-solid fa-gift"></i></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== MIDDLE COLUMN : Book Tutor + Credits ===== -->
        <div class="col-xl-8 d-flex">
            <div class="dashboard-card w-100">
                <div class="dashboard-card-head border-0">
                    <div class="header-title">
                        <h5>Account & Credits Overview</h5>
                    </div>
                </div>
                <div class="dashboard-card-body">
                    <div class="row">
                        <!-- Book Tutor -->
                        <div class="col-sm-7">
                            <div class="book-appointment-head mb-3 d-flex justify-content-between align-items-center">
                                <h3><span>Book a new</span> Tutor</h3>
                                <span class="add-icon"><a href="{{ url('search-tutors') }}"><i
                                            class="fa-solid fa-circle-plus"></i></a></span>
                            </div>

                            <!-- Upcoming Meetings Table -->
                            <div class="dashboard-card mt-2">
                                <div class="dashboard-card-head d-flex justify-content-between align-items-center">
                                    <div class="header-title">
                                        <h6>Upcoming Meetings</h6>
                                    </div>
                                    <div class="card-view-link"><a href="#">View All</a></div>
                                </div>
                                <div class="dashboard-card-body pt-2">
                                    <div class="table-responsive">
                                        <table class="table dashboard-table appoint-table mb-0">
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="patient-info-profile">
                                                            <a href="#" class="table-avatar">
                                                                <img src="{{ asset('assets/img/tutors/tutor-01.jpg') }}" alt="Tutor">
                                                            </a>
                                                            <div class="patient-name-info">
                                                                <h5><a href="#">Mr. Smith</a></h5>
                                                                <span>Math</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="appointment-date-created">
                                                            <h6>26 Oct 2025 - 10:00 AM</h6>
                                                            <span class="badge table-badge bg-success">Upcoming</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="apponiment-actions d-flex align-items-center">
                                                            <a href="#" class="text-success-icon me-2"><i class="fa-solid fa-video"></i></a>
                                                            <a href="#" class="text-danger-icon"><i class="fa-solid fa-xmark"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <div class="patient-info-profile">
                                                            <a href="#" class="table-avatar">
                                                                <img src="{{ asset('assets/img/tutors/tutor-02.jpg') }}" alt="Tutor">
                                                            </a>
                                                            <div class="patient-name-info">
                                                                <h5><a href="#">Ms. Johnson</a></h5>
                                                                <span>English</span>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="appointment-date-created">
                                                            <h6>Tomorrow - 2:00 PM</h6>
                                                            <span class="badge table-badge bg-info">Scheduled</span>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="apponiment-actions d-flex align-items-center">
                                                            <a href="#" class="text-success-icon me-2"><i class="fa-solid fa-video"></i></a>
                                                            <a href="#" class="text-danger-icon"><i class="fa-solid fa-xmark"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
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
                                <h6>Credits Left</h6>
                                <div class="circle-bar circle-bar3 report-chart mb-2">
                                    <div class="circle-graph3" data-percent="75">
                                        <p>Credits Used<br>90 / 120</p>
                                    </div>
                                </div>
                                <span class="health-percentage d-block mb-2">You have enough credits for 3 more sessions</span>
                                <a href="{{ route('student.account.subscription') }}" class="btn btn-dark w-100 rounded-pill">Manage Subscriptions<i
                                        class="fa-solid fa-chevron-right ms-2"></i></a>
                            </div>
                        </div>

                    </div>

                    <!-- Subscription Info -->
                    <div class="report-gen-date mt-4">
                        <p>Subscription valid till: <strong>31 Dec 2025</strong> <span><i class="fa-solid fa-copy"></i></span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== CALENDAR ===== -->
        <div class="col-xl-12 mt-4">
            <div class="dashboard-card w-100">
                <div class="dashboard-card-head">
                    <div class="header-title">
                        <h5>Scheduled Sessions</h5>
                    </div>
                    <div class="card-view-link"><a href="#">View Calendar</a></div>
                </div>
                <div class="dashboard-card-body p-3">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const today = new Date();

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                height: 600,
                events: [
                    { title: 'Math - Mr. Smith', start: '2025-10-26T10:00:00' },
                    { title: 'English - Ms. Johnson', start: '2025-10-27T14:00:00' },
                    { title: 'Physics - Dr. Brown', start: '2025-10-20T11:00:00' },
                    { title: 'Chemistry - Ms. Davis', start: '2025-10-27T16:00:00' }
                ],
                eventDidMount: function(info) {
                    const eventDate = new Date(info.event.start);
                    if (eventDate.toDateString() === today.toDateString()) info.el.classList.add('event-today');
                    else if (eventDate < today) info.el.classList.add('event-past');
                    else info.el.classList.add('event-upcoming');
                }
            });

            calendar.render();
        });
    </script>
@endpush
