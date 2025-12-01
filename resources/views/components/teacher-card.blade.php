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

{{-- Custom CSS to ensure layout works without changing logic --}}
<style>
    .teacher-card-row {
        display: flex;
        flex-wrap: wrap;
        align-items: stretch; /* Key: Forces equal height */
        width: 100%;
    }
    
    @media (min-width: 768px) {
        .teacher-card-row {
            flex-wrap: nowrap;
        }
        .teacher-img-col {
            width: 25%; /* Compact Width */
            min-width: 180px;
            flex-shrink: 0;
        }
        .teacher-text-col {
            width: 75%;
            flex-grow: 1;
        }
    }

    .teacher-img-fit {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Key: Fills height without distortion */
        display: block;
    }

    .teacher-card-body {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        padding: 0.75rem 1rem; /* Compact Padding */
    }
    
    .teacher-bottom-actions {
        margin-top: auto; /* Key: Pushes section to bottom */
        padding-top: 0.75rem;
        border-top: 1px solid #f0f0f0; /* Optional separator */
    }
</style>

<div class="card doctor-list-card overflow-hidden mb-3 shadow-sm border-0">
    <div class="teacher-card-row">
        
        {{-- Teacher image --}}
        <div class="teacher-img-col">
            <div style="height: 100%;">
                 {{-- <a href="{{ route('student.tutors.profile',['id' => encryptId($teacher->id)]) }}"> --}}
                <img 
                    src="{{$teacher->profile_link }}" 
                    alt="{{ $teacher->name }}" 
                    class="teacher-img-fit"
                >
                {{-- </a> --}}
            </div>
        </div>

        {{-- card body --}}
        <div class="teacher-text-col">
            <div class="teacher-card-body">
                
                {{-- TOP SECTION --}}
                <div>
                    {{-- Header: Name (left) + Availability Badge (right) --}}
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <h6 class="mb-1 teacher-name fw-bold">
                                {{ $teacher->teacherProfile->preferred_name ?? $teacher->name }}
                               <i class="isax isax-tick-circle5 text-success ms-2"></i>
                            </h6>

                            {{-- optional subtitle: username + id --}}
                            <small class="text-muted d-block fs-12">
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
                                <span class="badge bg-success-light d-inline-flex align-items-center px-2 py-1 availability-badge fs-11">
                                    <i class="fa-solid fa-circle fs-6 me-1"></i> Available
                                </span>
                            @else
                                <span class="badge bg-danger-light d-inline-flex align-items-center px-2 py-1 availability-badge fs-11">
                                    <i class="fa-solid fa-circle-xmark fs-6 me-1"></i> Not Available
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Two-column details --}}
                    <div class="doctor-info-detail pb-2">
                        <div class="row align-items-center gy-2">
                            <div class="col-md-6">
                                <p class="mb-1 fs-13">
                                    {{ $teacher->teacherProfile->short_bio ?? 'Certified TESOL Tutor' }}
                                </p>
                                <p class="d-flex align-items-center mb-0 fs-12 text-muted">
                                    <i class="isax isax-location me-2"></i>
                                    {{ $teacher->teacherProfile->tz ?? 'N/A' }}
                                </p>
                            </div>

                            <div class="col-md-6 text-md-end">
                                <p class="d-flex align-items-center justify-content-md-end mb-1 fs-12">
                                    <i class="isax isax-language-circle text-dark me-2"></i>
                                    {{ ucfirst($teacher?->teacherProfile?->native_language  ?? 'English' ) }}
                                </p>

                                <p class="d-flex align-items-center justify-content-md-end mb-1 fs-12">
                                    <i class="isax isax-archive-14 text-dark me-2"></i>
                                    {{ $teacher?->teacherProfile?->experience ?? '**' }} Years Experience
                                </p>

                                {{-- Availability Info --}}
                                @if($isAvailable)
                                    <p class="d-flex align-items-center justify-content-md-end mb-0 fs-12 text-success">
                                        <i class="isax isax-calendar-1 me-2"></i>
                                        <strong>{{ convertUtcToLocal($nextSlot->start_utc, $authUser)['local_formatted_date'] }}</strong>
                                    </p>
                                @else
                                    <p class="d-flex align-items-center justify-content-md-end mb-0 fs-12 text-muted">
                                        <i class="isax isax-calendar-off me-2"></i>
                                        No upcoming slots
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BOTTOM SECTION (Availability & Action Row) --}}
                <div class="teacher-bottom-actions">
                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-2">
                        
                        {{-- Column 1: Lesson Rate --}}
                        <div class="me-3">
                            <p class="mb-0 fs-11 text-muted">Lesson Rate</p>
                            <h4 class="text-orange mb-0 fs-16 fw-bold">{{ config('app.ticket_per_meeting') }} Ticket / Lesson</h4>
                        </div>
                    
                        {{-- Column 2: Button --}}
                        <div class="ms-auto text-end">
                            <a href="{{ route('student.tutors.booking.datetime',['teacherId' => encryptId($teacher->id)]) }}" class="btn btn-primary-gradient rounded-pill px-4 py-2 fs-13">
                                <i class="isax isax-calendar-1 me-2"></i> Book a Lesson
                            </a>
                        </div>
                    
                    </div>
                </div>
                
            </div>
        </div>
        {{-- end card body --}}
    </div>
</div>