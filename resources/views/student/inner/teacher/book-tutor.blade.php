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
    </style>

    <!-- SweetAlert2 CSS (CDN) -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
@endpush

@section('content')
<fieldset id="bookingFieldset" style="display: block;">
    <div class="card booking-card mb-0">
        <div class="card-header">
            <div class="booking-header pb-0">
                <div class="card mb-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center flex-wrap rpw-gap-2 mb-4 flex-wrap row-gap-2">
                            <span class="avatar avatar-xxxl avatar-rounded me-2 flex-shrink-0">
                                <img src="{{ asset('assets/img/clients/client-15.jpg') }}" alt="">
                            </span>
                            <div>
                                <h4 class="mb-1">{{ $teacher->teacherProfile->preferred_name ?? $teacher->name ?? 'Dr. Michael Brown' }} <span class="badge bg-orange fs-12"><i class="fa-solid fa-star me-1"></i>5.0</span></h4>
                                <p class="text-indigo mb-3 fw-medium">{{ $teacher->teacherProfile->title ?? 'Psychologist' }}</p>
                                <p class="mb-0"><i class="isax isax-location me-2"></i>{{ $teacher->teacherProfile->location ?? '5th Street - 1011 W 5th St, Austin, TX' }}</p>
                            </div>
                        </div>
                        <h6 class="mb-2">Booking Info</h6>
                        <div class="row gx-2 gy-3">
                            <div class="col-lg-3 col-sm-6">
                                <div>
                                    <h6 class="fs-14 fw-medium mb-1">Service</h6>
                                    <p class="mb-0">Cardiology (30 Mins)</p>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div>
                                    <h6 class="fs-14 fw-medium mb-1">Service</h6>
                                    <p class="mb-0">Echocardiograms</p>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div>
                                    <h6 class="fs-14 fw-medium mb-1">Date &amp; Time</h6>
                                    <p class="mb-0">Select below</p>
                                </div>
                            </div>
                            <div class="col-lg-3 col-sm-6">
                                <div>
                                    <h6 class="fs-14 fw-medium mb-1">Appointment type</h6>
                                    <p class="mb-0">Clinic (Wellness Path)</p>
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
                    Confirm Date & Time <i class="isax isax-arrow-right-3 ms-1"></i>
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

    <!-- SweetAlert2 (CDN) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    feather.replace();

    (function () {
        const teacherRawId = $('#teacher_id_raw').val() || '';
        const teacherEnc = $('#teacher_id_enc').val() || ''; // if you use encrypted id in routes
        // Use the plain teacher id route generation (you can switch to encrypted if your routes expect it)
        const slotsUrl = "{{ route('student.booking.slots', ['teacherId' => encryptId($teacher->id) ?? 'TEACHER_ID']) }}".replace('TEACHER_ID', teacherRawId || '{{ $teacher->id ?? "" }}');
        const confirmUrl = "{{ route('student.booking.confirm') }}";
        const csrf = "{{ csrf_token() }}";

        function renderSlotsArray(arr, selector) {
            $(selector).empty();
            if (!arr || arr.length === 0) {
                $(selector).html('<div class="text-muted">No slots</div>');
                return;
            }

            arr.forEach(function (entry) {
                // pick the best label available (viewer-local first)
                const label = entry.label_user ?? entry.label_teacher ?? entry.time_teacher ?? entry.time_user ?? entry.time_utc ?? 'Slot';
                const btn = $('<button>')
                    .addClass('btn btn-outline-secondary btn-sm rounded-pill slot-btn')
                    .attr('type', 'button')
                    .attr('data-availability-id', entry.id)
                    .attr('data-iso-user', entry.iso_user ?? entry.iso_teacher ?? entry.iso_utc ?? '')
                    .text(label);

                $(selector).append(btn);
            });
        }

        function loadSlots(dateStr) {
            $('#morningSlots, #afternoonSlots, #eveningSlots').empty();
            $('#picked_slot').val('');
            $('#confirmBtn').addClass('disabled');

            // show loading placeholders
            $('#morningSlots, #afternoonSlots, #eveningSlots').html('<div class="text-muted">Loading...</div>');

            $.get(slotsUrl, { date: dateStr })
                .done(function (res) {
                    const slots = res.slots || {};
                    renderSlotsArray(slots.morning, '#morningSlots');
                    renderSlotsArray(slots.afternoon, '#afternoonSlots');
                    renderSlotsArray(slots.evening, '#eveningSlots');
                })
                .fail(function (xhr) {
                    $('#morningSlots, #afternoonSlots, #eveningSlots').html('<div class="text-danger">Failed to load slots</div>');
                    console.error('Slots load error', xhr);
                });
        }

        // init datetimepicker once
        $('#booking_datepicker').datetimepicker({
            format: 'YYYY-MM-DD',
            showClose: true,
            showClear: true,
            icons: {
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right'
            }
        });

        // select today's date on load
        const today = moment().format('YYYY-MM-DD');
        try {
            $('#booking_datepicker').data('DateTimePicker').date(moment(today));
            $('#booking_datepicker').val(today);
        } catch (e) {
            // fallback if widget not initialized yet
            $('#booking_datepicker').val(today);
        }

        // initial load
        loadSlots(today);

        // on date change, reload slots
        $('#booking_datepicker').on('dp.change', function (e) {
            const sel = e && e.date ? e.date.format('YYYY-MM-DD') : moment().format('YYYY-MM-DD');
            $('#picked_slot').val('');
            $('#confirmBtn').addClass('disabled');
            loadSlots(sel);
        });

        // single-selection of a slot
        $(document).on('click', '.slot-btn', function () {
            $('.slot-btn').removeClass('btn-primary').addClass('btn-outline-secondary');
            $(this).removeClass('btn-outline-secondary').addClass('btn-primary');

            const availabilityId = $(this).data('availability-id');
            $('#picked_slot').val(availabilityId);
            $('#confirmBtn').removeClass('disabled');
        });

        // confirm booking: send availability_id (not free-text datetime)
        $('#confirmBtn').on('click', function (e) {
            e.preventDefault();
            if ($(this).hasClass('disabled')) return;

            const availabilityId = $('#picked_slot').val();
            if (!availabilityId) {
                Swal.fire('Select slot', 'Please choose a time slot first.', 'warning');
                return;
            }

            const chosenBtn = $(`.slot-btn[data-availability-id="${availabilityId}"]`);
            const label = chosenBtn.text() || '';

            Swal.fire({
                title: 'Confirm Booking',
                html: `<p>You selected: <strong>${label}</strong></p><p><strong>1 ticket will be used.</strong></p>`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Proceed (Use 1 ticket)',
            }).then((result) => {
                if (!result.isConfirmed){
                    const $fs = $('#bookingFieldset').show();
                     return
                    };

                Swal.fire({
                    title: 'Booking...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.post(confirmUrl, {
                    _token: csrf,
                    availability_id: availabilityId,
                    // teacher id optional - server will verify availability belongs to teacher
                    teacher_id: $('#teacher_id_raw').val() || ''
                }).done(function (res) {
                    Swal.fire({
                        title: 'Booked!',
                        html: `<p>${res.label_start_teacher ?? res.start_teacher ?? label}</p>`,
                        icon: 'success'
                    }).then(() => {
                        const $fs = $('#bookingFieldset').show();

                        // refresh slots for the currently selected date
                        const currDate = $('#booking_datepicker').val() || moment().format('YYYY-MM-DD');
                        loadSlots(currDate);
                    });
                }).fail(function (xhr) {
                      const $fs = $('#bookingFieldset').show();
                    const json = xhr.responseJSON || {};
                    const msg = json.message || 'Failed to book slot. Please try again.';
                    Swal.fire('Error', msg, 'error');
                    // reload slots to reflect current DB state (in case of race)
                    const currDate = $('#booking_datepicker').val() || moment().format('YYYY-MM-DD');
                    loadSlots(currDate);
                });
            });
        });

    })();
</script>

@endpush
