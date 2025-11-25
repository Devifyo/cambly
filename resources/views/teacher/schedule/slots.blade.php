@extends('layouts.teacher.app')

@section('title', 'Manage Your Availability')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
<style>
  /* ---------------- CALENDAR CONTAINER ---------------- */
    .calendar-container { width: 100%; }
    .calendar-scroll-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    #weeklyCalendar { width: 100%; min-width: 900px; display: block; }
    #weeklyCalendar .fc-view-harness,
    #weeklyCalendar .fc-view-harness-passive { display: inline-block !important; width: auto !important; vertical-align: top; }
    .fc .fc-timegrid-col, .fc .fc-col-header-cell { min-width: 180px !important; width: auto !important; }
    .fc .fc-timegrid-axis { min-width: 60px !important; }
    .fc-timegrid-col:not(.fc-event-selected) .fc-timegrid-bg-harness { cursor: pointer; }
    .fc .fc-timegrid-slot-lane { cursor: pointer; }

    /* ---------------- EVENT DISPLAY ---------------- */
    .fc-timegrid-event-harness { overflow: visible !important; }
    .fc .fc-timegrid-event, .fc .fc-event {
        overflow: visible !important; min-height: 32px; border-radius: 4px;
        display: flex; align-items: center; justify-content: center; cursor: pointer;
    }
    .fc .fc-event-main, .fc .fc-event-main-frame {
        display: flex; align-items: center; justify-content: center; padding: 2px 4px;
        overflow: visible !important; width: 100%; height: 100%;
        text-align: center; white-space: nowrap !important;
    }
    .fc .fc-event-time { font-size: 0.8rem; font-weight: 600; line-height: 1.4; overflow: visible !important; white-space: nowrap; }
    .fc .fc-event-title { display: none; }
    .fc-event.past-event { opacity: 0.65; filter: grayscale(60%); }
    .fc-event.is-booked { cursor: not-allowed; }

    /* ---------------- LEGEND ---------------- */
    .slot-legend { display: flex; gap: 1rem; flex-wrap: wrap; align-items: center; }
    .legend-item { display: flex; gap: 0.5rem; align-items: center; }
    .legend-swatch { width: 14px; height: 14px; border-radius: 3px; }

    @media (max-width: 992px) {
        #weeklyCalendar { min-width: 1090px; }
        .fc .fc-timegrid-col, .fc .fc-col-header-cell { min-width: 150px !important; }
    }
    
    /* Make the readonly end time look correct */
    #modalLocalEnd:read-only {
        background-color: #e9ecef;
        opacity: 1;
        cursor: not-allowed;
    }
</style>
@endpush

@section('content')
<div class="card booking-card mb-0">
    <div class="card-header">
        <h4 class="mb-0">Manage Your Weekly Slots</h4>
    </div>

    <div class="card-body booking-body">
        <div class="card mb-0">
            <div class="card-body pb-1">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body p-2 pt-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="form-label mb-0">Your Weekly Availability</label>
                                    <button id="addSlotManualBtn" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-plus me-1"></i> Add Slot
                                    </button>
                                </div>

                                <div class="calendar-scroll-wrap">
                                    <div id="weeklyCalendar"></div>
                                </div>

                                <div class="slot-legend mt-3">
                                    <div class="legend-item"><span class="legend-swatch" style="background:#0d6efd"></span> Available</div>
                                    <div class="legend-item"><span class="legend-swatch" style="background:#dc3545"></span> Booked</div>
                                    <div class="legend-item"><span class="legend-swatch" style="background:#6c757d"></span> Completed (Past)</div>
                                </div>

                                <div class="small text-muted mt-2">
                                    <i class="fa-solid fa-mouse-pointer me-1"></i> <strong>Click and drag</strong> on an empty time to create a new slot.
                                    <br>
                                    <i class="fa-solid fa-hand-pointer me-1"></i> <strong>Click an existing slot</strong> to edit or delete it.
                                    <br>
                                    <i class="fa-solid fa-clock me-1"></i> <strong>Note:</strong> Slots can only start at :00 or :30 minutes.
                                    <br>
                                    <i class="fa-solid fa-ban me-1"></i> <strong>Important:</strong> You cannot create slots in the past.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="slotModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="slotForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Manage Slot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="modalSlotId" value="">
                    
                    <p id="modalDescription">Create or edit your availability slot.</p>
                    
                    <div class="mb-3">
                        <label class="form-label" for="modalLocalStart">Your Local Start Time</label>
                        <input type="datetime-local" class="form-control" id="modalLocalStart" step="1800" required>
                        <small class="form-text text-muted">
                            Slots can only start at :00 or :30 minutes.
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label" for="modalLocalEnd">Your Local End Time</label>
                        <input type="datetime-local" class="form-control" id="modalLocalEnd" readonly required>
                        <small class="form-text text-muted">
                            End time is automatically set to a {{ config('app.max_meeting_duration', 25) }} minute duration.
                        </small>
                    </div>

                    <div id="modalError" class="text-danger small"></div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" id="modalDeleteBtn" class="btn btn-danger" style="display: none;">Delete Slot</button>
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="modalSaveBtn" class="btn btn-primary">Save Slot</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@php
    // choose the user's timezone: prefer studentProfile, then teacherProfile, then app default
    $user = auth()->user();
    $tz = $user->studentProfile->timezone ?? $user->teacherProfile->timezone ?? config('app.timezone');

    // current offset in seconds for that timezone (accounts for DST)
    $offsetSeconds = \Carbon\Carbon::now($tz)->getOffset();
@endphp
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
(function() {
    'use strict';
    
    // Modal instance
    const slotModal = new bootstrap.Modal('#slotModal');
     const USER_TZ = @json($tz);
    const USER_OFFSET_SECONDS = {{ $offsetSeconds }};
    // Configuration
    const CONFIG = {
        slotsUrl: "{{ route('teacher.schedule.slots') }}", // GET and POST
        updateUrl: "{{ url('teacher/schedule/schedule-slots') }}", // Base for PUT/DELETE
        csrf: "{{ csrf_token() }}",
        now: (function(){
            const nowUtcMs = Date.now();
            return new Date(nowUtcMs + USER_OFFSET_SECONDS * 1000);
        })(),
        slotDuration: {{ config('app.max_meeting_duration', 25) }},
        userTz: USER_TZ,
        userOffsetSeconds: USER_OFFSET_SECONDS

    };
    console.log(CONFIG.now);
    // State Management
    const state = {
        serverEvents: [],
        calendar: null,
        currentSlotId: null 
    };

    // Utility Functions
    const utils = {
        toTimeRange24(start, end) {
            if (!start || !end) return '';
            const opts = { hour: '2-digit', minute: '2-digit', hour12: false };
            return `${start.toLocaleTimeString([], opts)} - ${end.toLocaleTimeString([], opts)}`;
        },
        getSlotStatus(ev) {
            const isPast = new Date(ev.start) <= CONFIG.now;
            if (isPast) {
                return { status: 'Completed', bg: '#6c757d', border: '#5a6268', text: '#fff' };
            }
            if (ev.extendedProps?.is_booked) {
                return { status: 'Booked', bg: '#dc3545', border: '#b02a37', text: '#fff' };
            }
            return { status: 'Available', bg: '#0d6efd', border: '#0b5ed7', text: '#fff' };
        },
        isPastSlot(startIso) {
            return new Date(startIso) <= CONFIG.now;
        },
        toLocalISOString(date) {
            var tzoffset = date.getTimezoneOffset() * 60000;
            var localDate = new Date(date.getTime() - tzoffset);
            var localISO = localDate.toISOString().slice(0, 16);
            return localISO;
        },
        // ========== NEW: Snap time to nearest :00 or :30 ==========
        snapToHalfHour(date) {
            const snapped = new Date(date);
            const minutes = snapped.getMinutes();
            
            // Round to nearest 30-minute interval
            if (minutes < 15) {
                snapped.setMinutes(0);
            } else if (minutes < 45) {
                snapped.setMinutes(30);
            } else {
                snapped.setMinutes(0);
                snapped.setHours(snapped.getHours() + 1);
            }
            
            snapped.setSeconds(0);
            snapped.setMilliseconds(0);
            return snapped;
        },
        // ========== NEW: Validate time is on :00 or :30 ==========
        isValidHalfHourTime(date) {
            const minutes = date.getMinutes();
            return minutes === 0 || minutes === 30;
        }
    };

    // Event Handlers
    const handlers = {
        transformEvents(serverEvents) {
            return serverEvents.map(ev => {
                const { status, bg, border, text } = utils.getSlotStatus(ev);
                const isPast = utils.isPastSlot(ev.start);
                
                return {
                    id: ev.id,
                    title: '',
                    start: ev.start,
                    end: ev.end,
                    extendedProps: { 
                        ...ev.extendedProps,
                        status: status
                    },
                    backgroundColor: bg,
                    borderColor: border,
                    textColor: text,
                    editable: !ev.extendedProps.is_booked && !isPast,
                    className: (ev.extendedProps.is_booked || isPast) ? 'is-booked' : ''
                };
            });
        },

        openCreateModal() {
            const $modal = $('#slotModal');
            state.currentSlotId = null;

            $modal.find('#modalTitle').text('Create New Availability Slot');
            $modal.find('#modalDescription').text('Select the start and end time in your local timezone.');
            $modal.find('#modalSlotId').val('');
            
            $modal.find('#modalLocalStart').val('');
            $modal.find('#modalLocalEnd').val('');
            
            $modal.find('#modalError').text('');
            $('#modalDeleteBtn').hide();
            
            slotModal.show();
        },

        openSlotModal(info) {
            const $modal = $('#slotModal');
            const $deleteBtn = $('#modalDeleteBtn');
            const $errorEl = $('#modalError').text('');
            
            let isEditing = !!info.event;
            let canDelete = false;

            if (isEditing) {
                // --- EDIT MODE ---
                const ev = info.event;
                const raw = state.serverEvents.find(e => String(e.id) === String(ev.id));
                
                if (raw.is_booked) {
                    Swal.fire('Not Allowed', 'Booked slots cannot be modified.', 'warning');
                    return;
                }
                if (utils.isPastSlot(raw.start)) {
                    Swal.fire('Not Allowed', 'Past slots cannot be modified.', 'warning');
                    return;
                }

                state.currentSlotId = ev.id;
                
                const hoursUntilStart = (ev.start.getTime() - CONFIG.now.getTime()) / (1000 * 60 * 60);
                if (hoursUntilStart > 12) {
                    canDelete = true;
                } else {
                    $errorEl.text('This slot starts within 12 hours and can no longer be deleted.');
                }

                $modal.find('#modalTitle').text('Edit Availability Slot');
                $modal.find('#modalDescription').text('Update the start and end time for this slot.');
                $modal.find('#modalSlotId').val(ev.id);
                
                $modal.find('#modalLocalStart').val(utils.toLocalISOString(ev.start));
                $modal.find('#modalLocalEnd').val(utils.toLocalISOString(ev.end));

            } else {
                // --- CREATE MODE (from drag-select) ---
                state.currentSlotId = null;

                // ========== SNAP TO :00 OR :30 ==========
                const snappedStart = utils.snapToHalfHour(info.start);
                
                // ========== PREVENT CREATING PAST SLOTS ==========
                if (snappedStart <= CONFIG.now) {
                    Swal.fire('Not Allowed', 'Cannot create slots in the past.', 'warning');
                    return;
                }

                $modal.find('#modalTitle').text('Create New Availability Slot');
                $modal.find('#modalDescription').text('Select the start and end time in your local timezone.');
                $modal.find('#modalSlotId').val('');
                
                const endDate = new Date(snappedStart.getTime() + CONFIG.slotDuration * 60000);
                
                $modal.find('#modalLocalStart').val(utils.toLocalISOString(snappedStart));
                $modal.find('#modalLocalEnd').val(utils.toLocalISOString(endDate));
            }
            
            $deleteBtn.toggle(canDelete);
            slotModal.show();
        },

        handleSaveSlot(e) {
            e.preventDefault();
            const $btn = $('#modalSaveBtn');
            const $errorEl = $('#modalError');
            $btn.prop('disabled', true).text('Saving...');
            $errorEl.text('');

            const slotId = $('#modalSlotId').val();
            const isEditing = !!slotId;

            const localStartString = $('#modalLocalStart').val();
            
            if (!localStartString) {
                $errorEl.text('Please enter a valid start time.');
                $btn.prop('disabled', false).text('Save Slot');
                return;
            }

            const localStart = new Date(localStartString);

            if (isNaN(localStart)) {
                $errorEl.text('Please enter a valid start time.');
                $btn.prop('disabled', false).text('Save Slot');
                return;
            }

            // ========== VALIDATE TIME IS :00 OR :30 ==========
            if (!utils.isValidHalfHourTime(localStart)) {
                $errorEl.text('Start time must be at :00 or :30 minutes.');
                $btn.prop('disabled', false).text('Save Slot');
                return;
            }

            // ========== VALIDATE NOT IN THE PAST ==========
            if (localStart <= CONFIG.now) {
                $errorEl.text('Cannot create or update slots in the past.');
                $btn.prop('disabled', false).text('Save Slot');
                return;
            }

            const startUTC = localStart.toISOString();
            const localEnd = new Date(localStart.getTime() + CONFIG.slotDuration * 60000);
            const endUTC = localEnd.toISOString();

            const url = isEditing ? `${CONFIG.updateUrl}/${slotId}` : CONFIG.slotsUrl;
            const method = isEditing ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: 'POST', 
                data: {
                    _token: CONFIG.csrf,
                    _method: method,
                    start_time: startUTC,
                    end_time: endUTC 
                }
            })
            .done(resp => {
                slotModal.hide();
                state.calendar.refetchEvents();
                const successMsg = isEditing ? 'Slot updated!' : 'Slot created!';
                Swal.fire('Success', successMsg, 'success');
            })
            .fail(xhr => {
                let msg = 'Could not save slot.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    msg = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    msg = Object.values(xhr.responseJSON.errors).join(' ');
                }
                $errorEl.text(msg);
            })
            .always(() => {
                $btn.prop('disabled', false).text('Save Slot');
            });
        },

        handleDeleteSlot() {
            const slotId = $('#modalSlotId').val();
            if (!slotId) return;

            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete this available slot.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (!result.isConfirmed) return;

                $.ajax({
                    url: `${CONFIG.updateUrl}/${slotId}`,
                    type: 'POST',
                    data: {
                        _token: CONFIG.csrf,
                        _method: 'DELETE'
                    }
                })
                .done(resp => {
                    slotModal.hide();
                    state.calendar.refetchEvents();
                    Swal.fire('Deleted!', 'The slot has been removed.', 'success');
                })
                .fail(xhr => {
                    const msg = xhr.responseJSON?.message || 'Could not delete slot.';
                    $('#modalError').text(msg);
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
        // slotMaxTime: '24:00:00',
        slotDuration: '00:30:00', // ========== CHANGED TO 30 MINUTES ==========
        snapDuration: '00:30:00', // ========== NEW: SNAP TO 30-MIN INTERVALS ==========
        height: 'auto',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek,timeGridDay'
        },
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        slotLabelFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        selectable: true,
        selectMirror: true,
        selectConstraint: { // ========== NEW: CONSTRAIN SELECTION ==========
            start: '00:00',
            end: '24:00'
        },
        // ========== PREVENT SELECTING PAST TIMES ==========
        selectAllow: function(selectInfo) {
            return selectInfo.start > CONFIG.now;
        },
        select: handlers.openSlotModal,
        eventClick: handlers.openSlotModal,
        eventContent(arg) {
            const timeRange = utils.toTimeRange24(arg.event.start, arg.event.end);
            const status = arg.event.extendedProps.status || 'Available';
            const container = document.createElement('div');
            container.style.cssText = 'overflow: visible; white-space: nowrap; padding: 3px 6px; display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 100%;';
            const timeDiv = document.createElement('div');
            timeDiv.textContent = timeRange;
            timeDiv.style.cssText = 'font-size: 0.8rem; font-weight: 600; line-height: 1.2; color: inherit;';
            const statusDiv = document.createElement('div');
            statusDiv.textContent = status;
            statusDiv.style.cssText = 'font-size: 0.7rem; font-weight: 400; line-height: 1; color: inherit; margin-top: 2px;';
            container.appendChild(timeDiv);
            container.appendChild(statusDiv);
            return { domNodes: [container] };
        },
        events(info, successCallback, failureCallback) {
            $.get(CONFIG.slotsUrl, {
                start: info.start.toISOString(),
                end: info.end.toISOString()
            })
            .done(res => {
                state.serverEvents = res || [];
                const mapped = handlers.transformEvents(state.serverEvents);
                successCallback(mapped);
            })
            .fail(xhr => {
                console.error('Failed to fetch slots', xhr);
                failureCallback(xhr);
            });
        },
        eventDidMount(info) {
            if (utils.isPastSlot(info.event.start)) {
                info.el.classList.add('past-event');
            }
            info.el.style.overflow = 'visible';
            const mainFrame = info.el.querySelector('.fc-event-main-frame');
            if (mainFrame) mainFrame.style.overflow = 'visible';
        }
    });

    state.calendar.render();

    // --- Modal Event Listeners ---
    $('#slotForm').on('submit', handlers.handleSaveSlot);
    $('#modalDeleteBtn').on('click', handlers.handleDeleteSlot);
    $('#addSlotManualBtn').on('click', handlers.openCreateModal);
    
    // ========== AUTO-CALCULATE END TIME & VALIDATE ==========
    $('#modalLocalStart').on('input change', function() {
        const localStartString = $(this).val();
        const $errorEl = $('#modalError');
        
        if (localStartString) {
            try {
                const startDate = new Date(localStartString);
                if (!isNaN(startDate)) {
                    // Check if time is in the past
                    if (startDate <= CONFIG.now) {
                        $errorEl.text('Cannot create slots in the past.');
                        $('#modalLocalEnd').val('');
                        return;
                    }
                    
                    // Validate minutes are :00 or :30
                    if (!utils.isValidHalfHourTime(startDate)) {
                        $errorEl.text('Start time must be at :00 or :30 minutes.');
                        $('#modalLocalEnd').val('');
                        return;
                    } else {
                        $errorEl.text(''); // Clear error if valid
                    }
                    
                    // Calculate end time
                    const endDate = new Date(startDate.getTime() + CONFIG.slotDuration * 60000);
                    $('#modalLocalEnd').val(utils.toLocalISOString(endDate));
                }
            } catch (e) {
                $('#modalLocalEnd').val('');
            }
        } else {
            $('#modalLocalEnd').val('');
            $errorEl.text('');
        }
    });

})();
</script>
@endpush