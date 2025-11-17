@extends('layouts.student.app')

@section('title', 'Select Date & Time - Booking')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    <style>
        /* minor styles for slot buttons */
        .slot-btn { margin: 0 .25rem .5rem 0; }
        .slot-btn.btn-success {
            border: 2px solid #198754;
        }

        .slot-btn .badge {
            padding: 0.15rem 0.4rem;
            font-weight: 600;
        }
    </style>
@endpush

@section('content')
<fieldset id="bookingFieldset" style="display: block;">
    <div class="card booking-card mb-0">
        <div class="card-header">
            <div class="booking-header pb-0">
                <div class="card mb-0">
                    <div class="card-body">
                        
                        {{-- Teacher Avatar and Name/Title --}}
                        <div class="d-flex align-items-center flex-wrap row-gap-2 mb-4">
                            <span class="avatar avatar-xxxl avatar-rounded me-2 flex-shrink-0">
                                <img src="{{ $teacher->profile_link }}" alt="{{ $teacher->name ?? 'Teacher' }} Profile">
                            </span>
                            
                            <div>
                                <h4 class="mb-1">
                                    {{ $teacher->name ?? $teacher->teacherProfile->preferred_name ?? 'Dr. Michael Brown' }}
                                </h4>
                                {{-- <p class="text-indigo mb-1 fw-medium">{{ $teacher->teacherProfile->title ?? 'Language Tutor' }}</p> --}}
                                
                                {{-- ENCRYPTED TEACHER ID (Moved here, directly after name/title) --}}
                                <p class="mb-0 text-muted small">
                                    Teacher ID: {{ encryptId($teacher->id) }}
                                </p>
                            </div>
                        </div>
                        
                        {{-- Teacher Description (Moved here, directly after the header block) --}}
                        @if ($teacher->teacherProfile->bio ?? false)
                            <div class="mb-4">
                                <h6 class="mb-2">About the Teacher</h6>
                                <p class="text-muted mb-0">{{ $teacher->teacherProfile->bio }}</p>
                            </div>
                            <hr class="my-3">
                        @endif
                        
                        {{-- Booking Info --}}
                        <h6 class="mb-2">Booking Info</h6>
                        <div class="row gx-2 gy-3">
                            
                            {{-- Service --}}
                            <div class="col-lg-3 col-sm-6">
                                <div>
                                    <h6 class="fs-14 fw-medium mb-1">Service</h6>
                                    <p class="mb-0">1-1 Lesson (25 Mins)</p>
                                </div>
                            </div>
                            
                            {{-- Total Lessons --}}
                            <div class="col-lg-3 col-sm-6">
                                <div>
                                    <h6 class="fs-14 fw-medium mb-1">Total Lessons</h6>
                                    <p class="mb-0">{{ $teacher->reservationsAsTeacher()->where('status','completed')->count() }}</p>
                                </div>
                            </div>
                            
                            {{-- Joined at (Using the teacher's creation date) --}}
                            <div class="col-lg-3 col-sm-6">
                                <div>
                                    <h6 class="fs-14 fw-medium mb-1">Joined at</h6>
                                    <p class="mb-0">
                                        {{ $teacher->created_at ? $teacher->created_at->format('d M, Y') : 'N/A' }}
                                    </p>
                                </div>
                            </div>
                            
                            {{-- Total Experience (Using profile data) --}}
                            <div class="col-lg-3 col-sm-6">
                                <div>
                                    <h6 class="fs-14 fw-medium mb-1">Total Experience</h6>
                                    <p class="mb-0">
                                        {{ $teacher->teacherProfile->experience ?? 'N/A' }} years
                                    </p>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body booking-body">
            <div class="card mb-0">
                <div class="card-body pb-1">
                    <div class="row">
                        <!-- LEFT: datepicker input (plugin will render the calendar) -->
                        <div class="col-lg-5">
                            <div class="card">
                                <div class="card-body p-2 pt-3">
                                    <label class="form-label">Choose Date</label>
                                    <div class="input-group mb-2">
                                        <input type="text" id="booking_datepicker" class="form-control datetimepicker" placeholder="Select date" autocomplete="off" />
                                        <span class="input-group-text"><i class="fa fa-calendar"></i></span>
                                    </div>

                                    <div class="small text-muted">
                                        Tip: pick a date and then choose a time slot on the right.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- RIGHT: slots -->
                        <div class="col-lg-7">
                            <div class="card booking-wizard-slots">
                                <div class="card-body">
                                    <div class="book-title"><h6 class="fs-14 mb-2">Morning</h6></div>
                                    <div class="token-slot mt-2 mb-2" id="morningSlots"></div>

                                    <div class="book-title"><h6 class="fs-14 mb-2">Afternoon</h6></div>
                                    <div class="token-slot mt-2 mb-2" id="afternoonSlots"></div>

                                    <div class="book-title"><h6 class="fs-14 mb-2">Evening</h6></div>
                                    <div class="token-slot mt-2 mb-2" id="eveningSlots"></div>

                                    <input type="hidden" id="picked_slot" name="picked_slot" />
                                    <input type="hidden" id="teacher_id_raw" name="teacher_id" value="{{ encryptId($teacher->id) ?? '' }}" />
                                    {{-- If you use an encrypted id helper, include it too: --}}
                                    <input type="hidden" id="teacher_id_enc" value="{{ function_exists('encryptId') ? encryptId($teacher->id ?? '') : '' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between">
                <a href="javascript:void(0);" class="btn btn-md btn-dark prev_btns inline-flex align-items-center rounded-pill">
                    <i class="isax isax-arrow-left-2 me-1"></i> Back
                </a>
                <a id="confirmBtn" href="javascript:void(0);" class="btn btn-md btn-primary-gradient next_btns inline-flex align-items-center rounded-pill disabled">
                    Book now <i class="isax isax-arrow-right-3 ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</fieldset>
{{-- @dd(route('student.booking.slots', ['teacherId' => $teacher->id]) ) --}}
@endsection

@push('scripts')
    <!-- jQuery/moment/datetimepicker - ensure these are not duplicated in layout -->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap-datetimepicker.min.js') }}"></script>

    <!-- Feather icons -->
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>

<script>
    feather.replace();

    (function () {
        // Cache DOM elements and constants
        const $morningSlots = $('#morningSlots');
        const $afternoonSlots = $('#afternoonSlots');
        const $eveningSlots = $('#eveningSlots');
        const $pickedSlot = $('#picked_slot');
        const $confirmBtn = $('#confirmBtn');
        const $datepicker = $('#booking_datepicker');
        const $bookingFieldset = $('#bookingFieldset');
        
        const teacherRawId = $('#teacher_id_raw').val() || '';
        const slotsUrl = "{{ route('student.booking.slots', ['teacherId' => encryptId($teacher->id) ?? 'TEACHER_ID']) }}".replace('TEACHER_ID', teacherRawId || '{{ $teacher->id ?? "" }}');
        const confirmUrl = "{{ route('student.booking.confirm') }}";
        const csrf = "{{ csrf_token() }}";
        const today = moment();

        // Slot button configuration
        const slotConfig = {
            bookedByViewer: {
                classes: 'btn-success position-relative',
                template: (label) => `${label} <span class="badge bg-white text-success ms-1" style="font-size: 0.65rem;">Booked by You</span>`,
                style: { opacity: '0.85', cursor: 'default' }
            },
            bookedByOther: {
                classes: 'btn-secondary',
                template: (label) => label,
                style: { opacity: '0.5', cursor: 'not-allowed', textDecoration: 'line-through' }
            },
            available: {
                classes: 'btn-outline-secondary',
                template: (label) => label,
                style: {}
            }
        };

        function createSlotButton(entry) {
            const label = entry.label_user ?? entry.label_teacher ?? entry.time_teacher ?? entry.time_user ?? entry.time_utc ?? 'Slot';
            const isBooked = entry.slot_status === 'booked';
            const bookedByViewer = entry.booked_by_viewer === true;
            
            let config;
            if (isBooked) {
                config = bookedByViewer ? slotConfig.bookedByViewer : slotConfig.bookedByOther;
            } else {
                config = slotConfig.available;
            }

            return $('<button>', {
                type: 'button',
                class: `btn btn-sm rounded-pill slot-btn ${config.classes}`,
                'data-availability-id': entry.id,
                'data-iso-user': entry.iso_user ?? entry.iso_teacher ?? entry.iso_utc ?? '',
                disabled: isBooked,
                html: config.template(label)
            }).css(config.style);
        }

        function renderSlotsArray(arr, $container) {
            $container.empty();
            
            if (!arr?.length) {
                $container.html('<div class="text-muted">No slots</div>');
                return;
            }

            const fragment = $(document.createDocumentFragment());
            arr.forEach(entry => fragment.append(createSlotButton(entry)));
            $container.append(fragment);
        }

        function clearSlots() {
            $morningSlots.add($afternoonSlots).add($eveningSlots).empty();
            $pickedSlot.val('');
            $confirmBtn.addClass('disabled');
        }

        function loadSlots(dateStr) {
            clearSlots();
            $morningSlots.add($afternoonSlots).add($eveningSlots).html('<div class="text-muted">Loading...</div>');

            $.get(slotsUrl, { date: dateStr })
                .done(({ slots = {} }) => {
                    renderSlotsArray(slots.morning, $morningSlots);
                    renderSlotsArray(slots.afternoon, $afternoonSlots);
                    renderSlotsArray(slots.evening, $eveningSlots);
                })
                .fail((xhr) => {
                    $morningSlots.add($afternoonSlots).add($eveningSlots).html('<div class="text-danger">Failed to load slots</div>');
                    console.error('Slots load error:', xhr);
                });
        }

        function initDatepicker() {
            $datepicker.datetimepicker({
                format: 'YYYY-MM-DD',
                showClose: true,
                showClear: true,
                minDate: today,
                defaultDate: today,
                icons: {
                    previous: 'fa fa-chevron-left',
                    next: 'fa fa-chevron-right'
                }
            });

            setTimeout(() => {
                const picker = $datepicker.data('DateTimePicker');
                if (picker) picker.date(today);
                $datepicker.val(today.format('YYYY-MM-DD'));
            }, 100);
        }

        function handleSlotClick() {
            const $this = $(this);
            
            if ($this.is(':disabled')) {
                const isOwn = $this.hasClass('btn-success');
                Swal.fire({
                    title: isOwn ? 'Your Booking' : 'Slot Unavailable',
                    text: isOwn ? 'This slot is already booked by you.' : 'This slot is already booked by another student.',
                    icon: isOwn ? 'info' : 'warning'
                });
                return;
            }

            $('.slot-btn:not(:disabled)').removeClass('btn-primary').addClass('btn-outline-secondary');
            $this.removeClass('btn-outline-secondary').addClass('btn-primary');
            $pickedSlot.val($this.data('availability-id'));
            $confirmBtn.removeClass('disabled');
        }

        function confirmBooking(e) {
            e.preventDefault();
            if ($(this).hasClass('disabled')) return;

            const availabilityId = $pickedSlot.val();
            if (!availabilityId) {
                Swal.fire('Select slot', 'Please choose a time slot first.', 'warning');
                return;
            }

            const label = $(`.slot-btn[data-availability-id="${availabilityId}"]`).text() || '';

            Swal.fire({
                title: 'Confirm Booking',
                html: `<p>You selected: <strong>${label}</strong></p><p><strong>1 ticket will be used.</strong></p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Proceed (Use 1 ticket)',
            }).then((result) => {
                if (!result.isConfirmed) {
                    $bookingFieldset.show();
                    return;
                }

                Swal.fire({
                    title: 'Booking...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.post(confirmUrl, {
                    _token: csrf,
                    availability_id: availabilityId,
                    teacher_id: teacherRawId
                })
                .done((res) => {
                    Swal.fire({
                        title: 'Booked!',
                        html: `<p>${res.label_start_teacher ?? res.start_teacher ?? label}</p>`,
                        icon: 'success'
                    }).then(() => {
                        $bookingFieldset.show();
                        loadSlots($datepicker.val() || today.format('YYYY-MM-DD'));
                    });
                })
                .fail((xhr) => {
                    $bookingFieldset.show();
                    const msg = xhr.responseJSON?.message || 'Failed to book slot. Please try again.';
                    Swal.fire('Error', msg, 'error');
                    loadSlots($datepicker.val() || today.format('YYYY-MM-DD'));
                });
            });
        }

        // Initialize
        initDatepicker();
        loadSlots(today.format('YYYY-MM-DD'));

        // Event listeners
        $datepicker.on('dp.change', (e) => {
            const sel = e?.date ? e.date.format('YYYY-MM-DD') : today.format('YYYY-MM-DD');
            clearSlots();
            loadSlots(sel);
        });

        $(document).on('click', '.slot-btn', handleSlotClick);
        $confirmBtn.on('click', confirmBooking);

    })();
</script>

@endpush