@extends('layouts.student.app')

@section('title', 'Select Date & Time - Booking')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">

<style>
/* ---------------- CALENDAR CONTAINER ---------------- */
.calendar-container {
    width: 100%;
}

/* Horizontal scroll ONLY for the calendar grid */
.calendar-scroll-wrap {
    width: 100%;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    -ms-overflow-style: -ms-autohiding-scrollbar;
}

/* Calendar must auto-expand */
#weeklyCalendar {
    width: 100%;
    min-width: 900px;
    display: block;
}

/* Make ONLY view harness scroll horizontally */
#weeklyCalendar .fc-view-harness,
#weeklyCalendar .fc-view-harness-passive {
    display: inline-block !important;
    width: auto !important;
    vertical-align: top;
}

/* Column widths */
.fc .fc-timegrid-col,
.fc .fc-col-header-cell {
    min-width: 180px !important;
    width: auto !important;
}

.fc .fc-timegrid-axis {
    min-width: 60px !important;
}

/* ---------------- EVENT DISPLAY ---------------- */
.fc-timegrid-event-harness { overflow: visible !important; }

.fc .fc-timegrid-event,
.fc .fc-event {
    overflow: visible !important;
    min-height: 32px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.fc .fc-event-main,
.fc .fc-event-main-frame {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2px 4px;
    overflow: visible !important;
    width: 100%;
    height: 100%;
    text-align: center;
    white-space: nowrap !important;
}

.fc .fc-event-time {
    font-size: 0.8rem;
    font-weight: 600;
    line-height: 1.4;
    overflow: visible !important;
    white-space: nowrap;
}

.fc .fc-event-title { display: none; }

.fc-event.past-event {
    opacity: 0.45;
    filter: grayscale(30%);
}

/* ---------------- LEGEND & SIDEBAR ---------------- */
.slot-legend {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    align-items: center;
}

.legend-item {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.legend-swatch {
    width: 14px;
    height: 14px;
    border-radius: 3px;
}

.booking-wizard-slots .card-body {
    max-height: 520px;
    overflow-y: auto;
}

/* ---------------- DAILY SLOT LIST ---------------- */
.slot-row {
    padding: 0.45rem;
    border-radius: 0.5rem;
    transition: 0.2s ease;
    cursor: pointer;
}

.slot-row:hover {
    background: rgba(0,0,0,0.03);
}

.slot-row.selected {
    box-shadow: 0 0 0 3px rgba(13,110,253,0.15);
    background: rgba(13,110,253,0.08);
    border: 1px solid rgba(13,110,253,0.3);
}

.slot-struck {
    text-decoration: line-through;
    opacity: 0.55;
    filter: grayscale(30%);
}

.right-card-footer {
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
    padding: 0.75rem;
    border-top: 1px solid #eee;
}

/* ---------------- TOKEN SLOT (2 BUTTONS PER ROW) ---------------- */
.token-slot {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    width: 100%;
    align-items: flex-start; /* prevent stretching height */
    box-sizing: border-box;
}

/* Each button = 48% width → two per row */
.token-slot .slot-btn-inline {
    flex: 0 0 48% !important;
    max-width: 48% !important;
    box-sizing: border-box !important;

    display: inline-flex !important;
    justify-content: center !important;
    align-items: center !important;

    white-space: nowrap !important;   /* KEEP text on single line */
    overflow: hidden;
    text-overflow: ellipsis;

    padding: 0.25rem 0.6rem !important;
    min-height: 36px !important;
    line-height: 1 !important;

    border-radius: 999px !important;
    border: 2px solid transparent !important;
    font-size: 0.85rem !important;
    font-weight: 500 !important;
    cursor: pointer;
    transition: 0.15s ease;
}

/* Hover & selected */
.token-slot .slot-btn-inline:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.12);
}

.token-slot .slot-btn-inline.selected {
    border: 2px solid #0d6efd !important;
    box-shadow: 0 0 0 3px rgba(13,110,253,0.12) !important;
    transform: none !important;
}

/* Ultra-small screens can wrap text if needed */
@media (max-width: 360px) {
    .token-slot .slot-btn-inline {
        white-space: normal !important;
        min-height: 34px !important;
        padding: 0.2rem 0.45rem !important;
    }
}

/* ---------------- RESPONSIVE ---------------- */
@media (max-width: 992px) {
    #weeklyCalendar { min-width: 1090px; }
    .fc .fc-timegrid-col, .fc .fc-col-header-cell {
        min-width: 150px !important;
    }
}

@media (max-width: 768px) {
    #weeklyCalendar { min-width: 1090px; }
    .fc .fc-event-time { font-size: 0.75rem; }
    .fc .fc-timegrid-col, .fc .fc-col-header-cell {
        width: 150px !important;
    }
}

@media (max-width: 560px) {
    #weeklyCalendar { min-width: 1090px; }
    .fc .fc-event-time { font-size: 0.7rem; }
    .fc .fc-timegrid-col, .fc .fc-col-header-cell {
        min-width: 150px !important;
    }
}

/* --- ADD THIS NEW BLOCK FOR MOBILE TOOLBAR --- */
@media (max-width: 768px) {
    .fc .fc-toolbar.fc-header-toolbar {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    /* Nav Buttons (< > Today) */
    .fc .fc-toolbar-chunk:first-child {
        order: 1; 
    }

    /* View Switcher (Week Day) - Move from right to Left under Nav */
    .fc .fc-toolbar-chunk:last-child {
        order: 2;
        display: flex;
        justify-content: flex-start;
    }

    /* Title - Move to bottom */
    .fc .fc-toolbar-chunk:nth-child(2) {
        order: 3;
        padding-top: 5px;
    }
}

    /* 1. Increase height of the actual time slots */
    .fc .fc-timegrid-slot {
        height: 3rem !important; /* Default is usually small ~1.5em. Try 3rem, 4rem, or 60px */
    }

    /* 2. Optional: Center the text in the time labels */
    .fc .fc-timegrid-slot-label-frame {
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
    }
</style>


@endpush

@section('content')
<fieldset id="bookingFieldset">
    <div class="card booking-card mb-0">
        {{-- teacher info --}}
        <div class="card-header">
            <div class="booking-header pb-0">
                <div class="card mb-0">
                    <div class="card-body">
                        {{-- 1. Header with Name + Video Link --}}
                        <div class="d-flex align-items-center flex-wrap row-gap-2 mb-4">
                            <span class="avatar avatar-xxxl avatar-rounded me-2 flex-shrink-0">
                                <img src="{{ $teacher->profile_link }}" alt="{{ $teacher->name ?? 'Teacher' }} Profile">
                            </span>
                            <div>
                                {{-- Flex container to align Name and Video Button --}}
                                <div class="d-flex align-items-center flex-wrap gap-2">
                                    <h4 class="mb-1">{{ $teacher->name ?? $teacher?->teacherProfile?->preferred_name ?? 'Teacher' }}</h4>
                                    
                                    {{-- MOVED HERE: Video Button --}}
                                    @if($teacher?->teacherProfile?->youtube_url ?? false)
                                        <a href="javascript:void(0);" 
                                        class="d-inline-flex align-items-center fs-11 text-danger bg-danger-transparent px-2 py-1 rounded-pill fw-medium"
                                        style="text-decoration: none;"
                                        onclick="openVideoPreview('{{ $teacher->teacherProfile->youtube_url }}')">
                                            <i class="fa-solid fa-play me-1"></i> Watch Intro
                                        </a>
                                    @endif
                                </div>

                                <p class="mb-0 text-muted small">Teacher ID: {{ encryptId($teacher->id) }}</p>
                            </div>
                        </div>

                        {{-- Original Bio Section (Headline/Short Bio) --}}
                        @if ($teacher?->teacherProfile?->short_bio ?? false)
                            <div class="mb-4">
                                <h6 class="mb-2">About the Teacher</h6>
                                <p class="text-muted mb-0">{{ $teacher?->teacherProfile?->short_bio }}</p>
                            </div>
                            <hr class="my-3">
                        @endif

                        <h6 class="mb-2">Booking Info</h6>
                        
                        {{-- Row 1: Existing Stats --}}
                        <div class="row gx-2 gy-3">
                            <div class="col-lg-3 col-sm-6">
                                <h6 class="fs-14 fw-medium mb-1">Service</h6>
                                <p class="mb-0">1-1 Lesson (25 Mins)</p>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <h6 class="fs-14 fw-medium mb-1">Total Lessons</h6>
                                <p class="mb-0">{{ $teacher->reservationsAsTeacher()->where('status', 'completed')->count() }}</p>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <h6 class="fs-14 fw-medium mb-1">Joined at</h6>
                                <p class="mb-0">{{ $teacher->created_at ? $teacher->created_at->format('d M, Y') : 'N/A' }}</p>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <h6 class="fs-14 fw-medium mb-1">Total Experience</h6>
                                <p class="mb-0">{{ $teacher?->teacherProfile?->experience ?? '-' }} years</p>
                            </div>

                            {{-- Row 2: NEW FIELDS (Japanese Level & Games only - Video moved up) --}}

                            {{-- Japanese Level (using english_level) --}}
                            <div class="col-lg-3 col-sm-6">
                                <h6 class="fs-14 fw-medium mb-1">Japanese Level</h6>
                                <p class="mb-0">{{ ucfirst($teacher?->teacherProfile?->english_level ?? '-') }}</p>
                            </div>
                            {{-- Native Languages --}}
                            <div class="col-lg-3 col-sm-6">
                                <h6 class="fs-14 fw-medium mb-1">Native Languages</h6>
                                <p class="mb-0">{{ format_user_languages($teacher, 'native-language')?? '-' }}</p>
                            </div>
                            
                            {{-- Games --}}
                            <div class="col-lg-3 col-sm-6">
                                <h6 class="fs-14 fw-medium mb-1">Games</h6>
                                <p class="mb-0">{{ $teacher?->teacherProfile?->games ?? '-' }}</p>
                            </div>
                        </div>
                        
                        {{-- Row 3: Introduction (Full Width) --}}
                        @if($teacher?->teacherProfile?->introduction)
                            <hr class="my-3">
                            <div class="row gx-2 gy-3">
                                <div class="col-12">
                                    <h6 class="fs-14 fw-medium mb-1">Introduction</h6>
                                    <p class="mb-0 text-muted" style="white-space: pre-wrap;">{{ $teacher?->teacherProfile?->introduction }}</p>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
        {{-- end of teacher info --}}
        <div class="card-body booking-body">
            <div class="card mb-0">
                <div class="card-body pb-1">
                    <div class="row">
                        <!-- Calendar Column -->
                        <div class="col-lg-9" style="min-width:75%;">
                            <div class="card">
                                <div class="card-body p-2 pt-3">
                                    
                                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                                        <h5 class="card-title mb-0 fw-bold">Weekly Availability</h5>
                                        
                                        <div class="d-inline-flex align-items-center bg-light border rounded-pill px-3 py-1">
                                          <i class="fa-solid fa-globe text-primary me-2 fs-14"></i>
                                            <span class="text-muted fs-12 me-1">Timezone:</span>
                                            <span id="userTimezone" class="text-dark fw-bold fs-12">
                                                {{ auth()->user()->studentProfile->tz ?? 'UTC' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="calendar-scroll-wrap">
                                        <div id="weeklyCalendar"></div>
                                    </div>

                                    <div class="slot-legend mt-3 pt-2 border-top">
                                        <div class="d-flex flex-wrap gap-3">
                                            <div class="d-flex align-items-center fs-12">
                                                <span class="legend-swatch rounded-circle me-1" style="background:#198754; width:10px; height:10px;"></span> 
                                                Your booking
                                            </div>
                                            <div class="d-flex align-items-center fs-12">
                                                <span class="legend-swatch rounded-circle me-1" style="background:#0d6efd; width:10px; height:10px;"></span> 
                                                Available
                                            </div>
                                            <div class="d-flex align-items-center fs-12">
                                                <span class="legend-swatch rounded-circle me-1" style="background:#6c757d; width:10px; height:10px;"></span> 
                                                Booked
                                            </div>
                                            <div class="d-flex align-items-center fs-12">
                                                <span class="legend-swatch rounded-circle me-1" style="background:#dc3545; width:10px; height:10px;"></span> 
                                                Cancelled
                                            </div>
                                        </div>
                                    </div>

                                    <div class="small text-muted mt-2 fst-italic">
                                        <i class="bi bi-info-circle me-1"></i> Calendar uses 24-hour format.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Slots Panel -->
                        <div class="col-lg-2" style="min-width:25%;">
                            <div class="card booking-wizard-slots">
                                <div class="card-body">
                                    <h6 class="fs-14 mb-2">Slots for <span id="selectedDayLabel"></span></h6>
                                    <div id="daySlots"></div>

                                    <hr>
                                    <div class="book-title"><h6 class="fs-14 mb-2">Morning</h6></div>
                                    <div class="token-slot mt-2 mb-2" id="morningSlots"></div>
                                    
                                    <div class="book-title"><h6 class="fs-14 mb-2">Afternoon</h6></div>
                                    <div class="token-slot mt-2 mb-2" id="afternoonSlots"></div>
                                    
                                    <div class="book-title"><h6 class="fs-14 mb-2">Evening</h6></div>
                                    <div class="token-slot mt-2 mb-2" id="eveningSlots"></div>
                                </div>

                                <div class="right-card-footer">
                                    <input type="hidden" id="picked_slot" name="picked_slot" value="">
                                    <button id="rightCardBookNow" class="btn btn-primary" disabled>Book now</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</fieldset>

<!-- Event Modal -->
<div class="modal fade" id="eventDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Slot Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Time:</strong> <span id="modalTimeRange"></span></p>
                <p><strong>Status:</strong> <span id="modalStatus"></span></p>
                <p id="modalExtra" class="text-muted small"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="modalBookBtn" class="btn btn-primary">Book this slot</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
(function() {
    'use strict';
    
    feather.replace();

    // Configuration
    const CONFIG = {
        teacherId: "{{ encryptId($teacher->id) }}",
        weekSlotsUrl: "{{ route('student.booking.week-slots', ['teacherId' => ':TEACHER']) }}".replace(':TEACHER', "{{ encryptId($teacher->id) }}"),
        confirmUrl: "{{ route('student.booking.confirm') }}",
        csrf: "{{ csrf_token() }}",
        now: new Date()
    };

    // State Management
    const state = {
        serverEvents: [],
        selectedDay: null,
        selectedSlot: null,
        calendar: null
    };

    // Utility Functions
    const utils = {
        toTimeRange24(startIso, endIso) {
            if (!startIso || !endIso) return '';
            const start = new Date(startIso);
            const end = new Date(endIso);
            const opts = { hour: '2-digit', minute: '2-digit', hour12: false };
            return `${start.toLocaleTimeString([], opts)} - ${end.toLocaleTimeString([], opts)}`;
        },

        escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;',
                '/': '&#x2F;',
                '`': '&#x60;',
                '=': '&#x3D;'
            };
            return String(text || '').replace(/[&<>"'`=\/]/g, s => map[s]);
        },

        getStatusColors(status, bookedByViewer) {
            const colors = {
                completed: { bg: '#6f42c1', border: '#5a2fa5', text: '#fff' },
                booked_viewer: { bg: '#198754', border: '#0f5132', text: '#fff' },
                booked: { bg: '#6c757d', border: '#5a6268', text: '#fff' },
                cancelled: { bg: '#dc3545', border: '#b02a37', text: '#fff' },
                available: { bg: '#0d6efd', border: '#0b5ed7', text: '#fff' }
            };

            if (status === 'completed') return colors.completed;
            if (status === 'booked' && bookedByViewer) return colors.booked_viewer;
            if (status === 'booked') return colors.booked;
            if (status === 'cancelled') return colors.cancelled;
            return colors.available;
        },

        isPastSlot(startIso) {
            return new Date(startIso) <= CONFIG.now;
        }
    };

    // Event Handlers
    const handlers = {
        transformEvents(serverEvents) {
            return serverEvents.map(ev => {
                const status = ev.extendedProps?.status || 'available';
                const bookedByViewer = !!ev.extendedProps?.booked_by_viewer;
                const colors = utils.getStatusColors(status, bookedByViewer);

                return {
                    id: ev.id,
                    title: '',
                    start: ev.start,
                    end: ev.end,
                    extendedProps: { 
                        ...ev.extendedProps, 
                        rawTitle: ev.title,
                        status 
                    },
                    backgroundColor: colors.bg,
                    borderColor: colors.border,
                    textColor: colors.text
                };
            });
        },

        selectSlot(ev) {
            state.selectedSlot = ev;
            const availabilityId = ev.extendedProps?.availability_id || '';
            $('#picked_slot').val(availabilityId);

            const evDate = new Date(ev.start);
            const evDay = new Date(evDate.getFullYear(), evDate.getMonth(), evDate.getDate());
            
            if (!state.selectedDay || evDay.getTime() !== state.selectedDay.getTime()) {
                handlers.renderDaySlots(evDay);
            } else {
                handlers.highlightSelected();
            }

            const isPast = utils.isPastSlot(ev.start);
            const status = ev.extendedProps?.status;
            const bookable = !isPast && ['available', 'free'].includes(status);
            $('#rightCardBookNow').prop('disabled', !bookable);
        },

        highlightSelected() {
            // Remove all selected states
            $('#daySlots .slot-row').removeClass('selected');
            $('.slot-btn-inline').removeClass('selected');
            
            if (!state.selectedSlot) return;

            const targetId = String(state.selectedSlot.extendedProps?.availability_id);
            
            // Highlight day slot row
            $('#daySlots .slot-row').each(function() {
                const ev = $(this).data('ev');
                if (ev && String(ev.extendedProps?.availability_id) === targetId) {
                    $(this).addClass('selected');
                }
            });
            
            // Highlight all matching period pills
            $('.slot-btn-inline').each(function() {
                if (String($(this).attr('data-availability-id')) === targetId) {
                    $(this).addClass('selected');
                }
            });
        },

        renderDaySlots(date) {
            if (!date) return;
            
            state.selectedDay = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            $('#selectedDayLabel').text(state.selectedDay.toLocaleDateString());

            const dayEvents = state.serverEvents
                .filter(ev => {
                    const evDate = new Date(ev.start);
                    return evDate.toDateString() === state.selectedDay.toDateString();
                })
                .sort((a, b) => new Date(a.start) - new Date(b.start));

            const $container = $('#daySlots').empty();

            if (!dayEvents.length) {
                $container.html('<div class="text-muted">No slots for this day</div>');
            } else {
                dayEvents.forEach(ev => handlers.renderSlotRow(ev, $container));
            }

            handlers.renderTimeSlots(dayEvents);
            
            // Apply selection highlighting after rendering
            if (state.selectedSlot) {
                handlers.highlightSelected();
            }
        },

        renderSlotRow(ev, $container) {
            const timeRange = utils.toTimeRange24(ev.start, ev.end);
            const status = ev.extendedProps?.status;
            const bookedByViewer = !!ev.extendedProps?.booked_by_viewer;
            const isPast = utils.isPastSlot(ev.start);
            const availabilityId = ev.extendedProps?.availability_id || '';

            const $row = $('<div class="d-flex align-items-center justify-content-between slot-row mb-2"></div>');
            const $left = $('<div class="me-2 flex-grow-1"></div>');
            const $right = $('<div class="ms-2"></div>');

            let labelHtml = `<div><strong>${utils.escapeHtml(timeRange)}</strong></div>`;
            if (ev.extendedProps?.rawTitle) {
                labelHtml += `<div class="small text-muted">${utils.escapeHtml(ev.extendedProps.rawTitle)}</div>`;
            }
            const $label = $('<div>').html(labelHtml);

            $row.attr('data-availability-id', availabilityId);

            // Add status badges
            if (status === 'completed') {
                $label.append(' <span class="badge" style="background:#6f42c1">Completed</span>');
                $right.append('<span class="text-muted">✓</span>');
            } else if (status === 'booked' && bookedByViewer) {
                $label.append(' <span class="badge bg-success">Booked by you</span>');
                $right.append('<span class="text-success">✔</span>');
            } else if (status === 'booked') {
                $label.addClass('slot-struck');
                $label.append(' <span class="badge bg-danger">Booked</span>');
                $right.append('<span class="text-danger">✖</span>');
            } else if (status === 'cancelled') {
                $label.append(' <span class="badge bg-danger">Cancelled</span>');
                $right.append('<span class="text-danger">!</span>');
            } else if (isPast) {
                $label.append(' <span class="badge bg-light text-dark">Past</span>');
                $label.addClass('text-muted');
                $right.append('<span class="text-muted">-</span>');
            } else {
                $label.append(' <span class="badge bg-primary">Available</span>');
                $right.append('<span class="text-primary">•</span>');
            }

            // Click handler
            $row.on('click', () => {
                if (!isPast && ['available', 'free'].includes(status)) {
                    handlers.selectSlot(ev);
                } else {
                    handlers.openModal(ev);
                }
            });

            $left.append($label);
            $row.append($left, $right);
            $row.data('ev', ev);
            $container.append($row);
        },

        renderTimeSlots(events) {
            const periods = { morning: [], afternoon: [], evening: [] };
            
            events.forEach(ev => {
                const hour = new Date(ev.start).getHours();
                if (hour < 12) periods.morning.push(ev);
                else if (hour < 17) periods.afternoon.push(ev);
                else periods.evening.push(ev);
            });

            handlers.renderPeriodSlots('#morningSlots', periods.morning);
            handlers.renderPeriodSlots('#afternoonSlots', periods.afternoon);
            handlers.renderPeriodSlots('#eveningSlots', periods.evening);
        },

        renderPeriodSlots(selector, slots) {
            const $container = $(selector).empty();
            
            if (!slots.length) {
                $container.html('<div class="text-muted">No slots</div>');
                return;
            }

            slots.forEach(ev => {
                const timeRange = utils.toTimeRange24(ev.start, ev.end);
                const status = ev.extendedProps?.status;
                const bookedByViewer = !!ev.extendedProps?.booked_by_viewer;
                const colors = utils.getStatusColors(status, bookedByViewer);
                const isPast = utils.isPastSlot(ev.start);
                const availabilityId = ev.extendedProps?.availability_id || '';

                const $btn = $('<button type="button" class="slot-btn-inline"></button>')
                    .text(timeRange)
                    .css({
                        backgroundColor: colors.bg,
                        color: colors.text,
                        borderColor: colors.border
                    })
                    .attr('data-availability-id', availabilityId);

                $btn.on('click', function(e) {
                    e.stopPropagation();
                    if (!isPast && ['available', 'free'].includes(status)) {
                        handlers.selectSlot(ev);
                    } else {
                        handlers.openModal(ev);
                    }
                });

                $container.append($btn);
            });
            
            // Apply selection after rendering
            if (state.selectedSlot) {
                const targetId = String(state.selectedSlot.extendedProps?.availability_id);
                $container.find(`[data-availability-id="${targetId}"]`).addClass('selected');
            }
        },

        openModal(ev) {
            const timeRange = utils.toTimeRange24(ev.start, ev.end);
            const status = ev.extendedProps?.status;
            const bookedByViewer = !!ev.extendedProps?.booked_by_viewer;
            const availabilityId = ev.extendedProps?.availability_id;
            const isPast = utils.isPastSlot(ev.start);

            $('#modalTimeRange').text(timeRange);
            $('#modalStatus').text(status ? status.charAt(0).toUpperCase() + status.slice(1) : 'Available');
            $('#modalExtra').html('');

            // Add status messages
            if (status === 'booked' && bookedByViewer) {
                $('#modalExtra').append('<small class="text-success d-block mt-2">This slot is booked by you.</small>');
            } else if (status === 'booked') {
                $('#modalExtra').append('<small class="text-danger d-block mt-2">This slot is booked by another student.</small>');
            } else if (status === 'completed') {
                $('#modalExtra').append('<small class="text-muted d-block mt-2">This lesson is completed.</small>');
            } else if (status === 'cancelled') {
                $('#modalExtra').append('<small class="text-danger d-block mt-2">This slot has been cancelled.</small>');
            } else if (isPast) {
                $('#modalExtra').append('<small class="text-muted d-block mt-2">This is a past slot.</small>');
            }

            // Book button
            const bookable = !isPast && ['available', 'free'].includes(status);
            if (bookable) {
                $('#modalBookBtn').show().off('click').on('click', () => {
                    $('#eventDetailModal').modal('hide');
                    handlers.confirmBooking(availabilityId, timeRange);
                });
            } else {
                $('#modalBookBtn').hide();
            }

            handlers.selectSlot(ev);
            new bootstrap.Modal('#eventDetailModal').show();
        },

        confirmBooking(availabilityId, timeRange) {
            if (!availabilityId) {
                Swal.fire('Error', 'Cannot book this slot.', 'error');
                return;
            }

            Swal.fire({
                title: 'Confirm Booking',
                html: `<p>Time: <strong>${utils.escapeHtml(timeRange)}</strong></p><p><strong>1 ticket will be used.</strong></p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Proceed'
            }).then(result => {
                if (!result.isConfirmed) return;

                Swal.fire({
                    title: 'Booking...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.post(CONFIG.confirmUrl, {
                    _token: CONFIG.csrf,
                    availability_id: availabilityId,
                    teacher_id: CONFIG.teacherId
                })
                .done(resp => {
                    const displayTime = resp.label_start_teacher || resp.start_teacher || timeRange;
                    Swal.fire({
                        title: 'Booked!',
                        html: `<p>${utils.escapeHtml(displayTime)}</p>`,
                        icon: 'success'
                    }).then(() => state.calendar.refetchEvents());
                })
                .fail(xhr => {
                    const msg = xhr.responseJSON?.message || 'Booking failed. Please try again.';
                    Swal.fire('Error', msg, 'error');
                    state.calendar.refetchEvents();
                });
            });
        }
    };

    // Initialize Calendar
    state.calendar = new FullCalendar.Calendar(document.getElementById('weeklyCalendar'), {
        initialView: 'timeGridWeek',
        nowIndicator: true,
        allDaySlot: false,
        slotMinTime: '00:00:00',
        slotMaxTime: '24:00:00',
        
        // --- UPDATED HERE ---
        slotDuration: '00:30:00', // Changed from 00:25:00 to 00:30:00
        // --------------------

        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek,timeGridDay'
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        slotLabelFormat: {
            hour: '2-digit',
            minute: '2-digit',
            hour12: false
        },
        eventContent(arg) {
            const timeRange = utils.toTimeRange24(arg.event.start, arg.event.end);
            
            // Create container with explicit inline styles for maximum visibility
            const container = document.createElement('div');
            container.style.cssText = 'overflow: visible; white-space: nowrap; padding: 3px 6px; display: flex; align-items: center; width: 100%; height: 100%;';
            
            const timeDiv = document.createElement('div');
            timeDiv.textContent = timeRange;
            timeDiv.style.cssText = 'font-size: 0.8rem; font-weight: 600; line-height: 1.4; color: inherit; overflow: visible; white-space: nowrap;';
            
            container.appendChild(timeDiv);
            return { domNodes: [container] };
        },
        events(info, successCallback, failureCallback) {
            $.get(CONFIG.weekSlotsUrl, {
                start: info.start.toISOString(),
                end: info.end.toISOString()
            })
            .done(res => {
                state.serverEvents = res.events || [];
                const mapped = handlers.transformEvents(state.serverEvents);
                successCallback(mapped);
                handlers.renderDaySlots(state.selectedDay || info.start);
            })
            .fail(xhr => {
                console.error('Failed to fetch slots', xhr);
                failureCallback(xhr);
            });
        },
        eventClick(info) {
            info.jsEvent.preventDefault();
            const raw = state.serverEvents.find(x => 
                String(x.id) === String(info.event.id) || 
                String(x.extendedProps?.availability_id) === String(info.event.extendedProps?.availability_id)
            ) || info.event;
            
            const normalized = {
                id: info.event.id,
                start: info.event.start ? info.event.start.toISOString() : raw.start,
                end: info.event.end ? info.event.end.toISOString() : raw.end,
                extendedProps: { ...raw.extendedProps, ...info.event.extendedProps }
            };
            handlers.openModal(normalized);
        },
        datesSet(view) {
            handlers.renderDaySlots(view.start);
        },
        eventDidMount(info) {
            if (utils.isPastSlot(info.event.start)) {
                info.el.classList.add('past-event');
            }
            // Force overflow visible on event element
            info.el.style.overflow = 'visible';
            
            // Find and force visibility on inner elements
            const mainFrame = info.el.querySelector('.fc-event-main-frame');
            if (mainFrame) mainFrame.style.overflow = 'visible';
            
            const eventMain = info.el.querySelector('.fc-event-main');
            if (eventMain) {
                eventMain.style.overflow = 'visible';
                eventMain.style.whiteSpace = 'nowrap';
            }
        }
    });

    state.calendar.render();

    // Event Listeners
    $(document).on('click', '.fc-col-header-cell', function() {
        const date = $(this).attr('data-date') || $(this).find('[data-date]').attr('data-date');
        if (date) {
            handlers.renderDaySlots(new Date(date));
            $('.booking-wizard-slots .card-body').scrollTop(0);
        }
    });

    $('#rightCardBookNow').on('click', function() {
        if (!state.selectedSlot) {
            Swal.fire('Select Slot', 'Please select an available slot.', 'warning');
            return;
        }

        const ev = state.selectedSlot;
        const availabilityId = ev.extendedProps?.availability_id;
        if (!availabilityId) {
            Swal.fire('Error', 'Selected slot has no availability ID.', 'error');
            return;
        }

        // Determine if the slot is bookable (not past, and status available/free)
        const isPast = utils.isPastSlot(ev.start);
        const status = ev.extendedProps?.status;
        const bookable = !isPast && ['available', 'free'].includes(status);

        if (bookable) {
            // Directly run the single confirmation flow (SweetAlert inside confirmBooking)
            // Use the same display time shown in modal (or compute from event)
            const timeRange = utils.toTimeRange24(ev.start, ev.end);
            handlers.confirmBooking(availabilityId, timeRange);
        } else {
            // Not bookable: open modal to show details / reason
            handlers.openModal(ev);
        }
    });


    // Expose helper function for debugging
    window.getPickedAvailabilityId = function() {
        return $('#picked_slot').val();
    };
})();
</script>
@endpush