{{-- resources/views/components/teacher-card.blade.php --}}

@props(['teacher', 'authUser'])

@php

    // All logic related to this specific teacher is now encapsulated here
    $nextSlot = $teacher->availabilities
    ->where('is_booked', false)
    ->where('start_utc', '>=', now()) 
    ->sortBy('start_utc')
    ->first();

    $isAvailable = $nextSlot && !\Carbon\Carbon::parse($nextSlot->start_utc)->isPast();
@endphp

<div class="card doctor-list-card">
    <div class="d-md-flex align-items-center">
        {{-- Teacher image --}}
        <div class="card-img card-img-hover">
            {{-- <a href="{{ route('student.tutors.profile',['id' => encryptId($teacher->id)]) }}"> --}}
                <img src="{{$teacher->profile_link }}" alt="{{ $teacher->name }}">
            {{-- </a> --}}
        </div>
        {{-- card body --}}
        <div class="card-body p-0">
            <div class="p-3">
                {{-- ---------- Header: Name (left) + Availability Badge (right) ---------- --}}
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="mb-1 teacher-name">
                            {{ $teacher->teacherProfile->preferred_name ?? $teacher->name }}
                           <i class="isax isax-tick-circle5 text-success ms-2"></i>
                        </h6>

                        {{-- optional subtitle: username + id --}}
                        <small class="text-muted d-block">
                            @if(!empty($teacher->username))
                                @{{ $teacher->username }}
                                <span class="mx-1">•</span>
                            @endif
                            ID: <span class="text-body">{{ '#'.encryptId($teacher->id) }}</span>
                        </small>
                    </div>

                    {{-- availability badge --}}
                    <div class="text-end availability-wrap">
                        @if($isAvailable)
                            <span class="badge bg-success-light d-inline-flex align-items-center px-3 py-2 availability-badge">
                                <i class="fa-solid fa-circle fs-6 me-2"></i> Available
                            </span>
                        @else
                            <span class="badge bg-danger-light d-inline-flex align-items-center px-3 py-2 availability-badge">
                                <i class="fa-solid fa-circle-xmark fs-6 me-2"></i> Not Available
                            </span>
                        @endif
                    </div>
                </div>

                {{-- ---------- Two-column details (UPDATED) ---------- --}}
                <div class="doctor-info-detail pb-3">
                    <div class="row align-items-center gy-3">
                        <div class="col-md-6">
                            <p class="mb-2">
                                {{ $teacher->teacherProfile->short_bio ?? 'Certified TESOL Tutor' }}
                            </p>
                            <p class="d-flex align-items-center mb-0 fs-14 text-muted">
                                <i class="isax isax-location me-2"></i>
                                {{ $teacher->teacherProfile->tz ?? 'N/A' }}
                            </p>
                        </div>

                        <div class="col-md-6 text-md-end">
                            <p class="d-flex align-items-center justify-content-md-end mb-2 fs-14">
                                <i class="isax isax-language-circle text-dark me-2"></i>
                                {{ ucfirst($teacher?->teacherProfile?->native_language  ?? 'English' ) }}
                            </p>

                            <p class="d-flex align-items-center justify-content-md-end mb-2 fs-14"> {{-- Changed to mb-2 --}}
                                <i class="isax isax-archive-14 text-dark me-2"></i>
                                {{ $teacher?->teacherProfile?->experience ?? '**' }} Years Experience
                            </p>

                            {{-- START: Availability Info Moved Here --}}
                            @if($isAvailable)
                                <p class="d-flex align-items-center justify-content-md-end mb-0 fs-14 text-success">
                                    <i class="isax isax-calendar-1 me-2"></i>
                                    <strong>{{ convertUtcToLocal($nextSlot->start_utc, $authUser)['local_formatted_date'] }}</strong>
                                </p>
                            @else
                                <p class="d-flex align-items-center justify-content-md-end mb-0 fs-14 text-muted">
                                    <i class="isax isax-calendar-off me-2"></i>
                                    No upcoming slots
                                </p>
                            @endif
                            {{-- END: Availability Info --}}

                        </div>
                    </div>
                </div>

                {{-- ---------- Availability & Action Row (UPDATED) ---------- --}}
                <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mt-3">
    
                    {{-- Column 1: Lesson Rate (stays on the left) --}}
                    <div class="me-3">
                        <p class="mb-1">Lesson Rate</p>
                        <h3 class="text-orange mb-0">{{ config('app.ticket_per_meeting') }} Ticket / Lesson</h3>
                    </div>
                
                    {{-- Column 2: Button (grouped on the right) --}}
                    <div class="ms-auto text-end">
                        {{-- Availability info was removed from here --}}
                        <a href="{{ route('student.tutors.booking.datetime',['teacherId' => encryptId($teacher->id)]) }}" class="btn btn-primary-gradient rounded-pill btn-lg px-4">
                            <i class="isax isax-calendar-1 me-2"></i> Book a Lesson
                        </a>
                    </div>
                
                </div>
            </div>
        </div>
        {{-- end card body --}}
    </div>
</div>