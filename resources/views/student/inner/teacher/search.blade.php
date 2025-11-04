@extends('layouts.student.app')

@section('title', 'Tutor List - Online Tutoring Platform')

@push('styles')
    <link rel="stylesheet" href="{{asset('assets/plugins/ion-rangeslider/css/ion.rangeSlider.css')}}">
    <link rel="stylesheet" href="{{asset('assets/plugins/ion-rangeslider/css/ion.rangeSlider.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap-datetimepicker.min.css')}}">
    <link rel="stylesheet" href="{{asset('assets/css/feather.css')}}">
@endpush

@section('content')
    <!-- Breadcrumb -->
    <div class="breadcrumb-bar overflow-visible">
        <div class="container">
            <div class="row align-items-center inner-banner">
                <div class="col-md-12 col-12 text-center">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="isax isax-home-15"></i></a></li>
                            <li class="breadcrumb-item">Tutor</li>
                            <li class="breadcrumb-item active">Tutor List</li>
                        </ol>
                        <h2 class="breadcrumb-title">Find Your Tutor</h2>
                    </nav>
                </div>
            </div>
            <div class="bg-primary-gradient rounded-pill doctors-search-box">
                <div class="search-box-one rounded-pill">
                    <form method="GET" action="{{ route('student.tutors.search') }}" id="searchForm">
                        <div class="search-input search-line">
                            <i class="isax isax-teacher bficon"></i>
                            <div class="mb-0">
                                <input 
                                    type="text" 
                                    name="name" 
                                    class="form-control" 
                                    placeholder="Search for Tutors"
                                    value="{{ request('name') }}"
                                >
                            </div>
                        </div>

                        <div class="search-input search-calendar-line">
                            <i class="isax isax-calendar-tick5"></i>
                            <div class="mb-0">
                                <input 
                                    type="text" 
                                    name="start_utc" 
                                    class="form-control datetimepicker" 
                                    placeholder="Date"
                                    value="{{ request('start_utc') }}"
                                >
                            </div>
                        </div>

                        <div class="form-search-btn">
                            <button class="btn btn-primary d-inline-flex align-items-center rounded-pill" type="submit">
                                <i class="isax isax-search-normal-15 me-2"></i>Search
                            </button>
                        </div>

                        {{-- Hidden inputs for sidebar filters --}}
                        <input type="hidden" name="gender" id="hiddenGender" value="{{ request('gender') }}">
                        @if(request('languages'))
                            @foreach(request('languages') as $lang)
                                <input type="hidden" name="languages[]" class="hiddenLanguage" value="{{ $lang }}">
                            @endforeach
                        @endif
                    </form>
                </div>
            </div>
        </div>
        <div class="breadcrumb-bg">
            <img src="{{asset('assets/img/bg/breadcrumb-bg-01.png')}}" alt="img" class="breadcrumb-bg-01">
            <img src="{{asset('assets/img/bg/breadcrumb-bg-02.png')}}" alt="img" class="breadcrumb-bg-02">
        </div>
    </div>
    <!-- /Breadcrumb -->

    <div class="content-inner mt-5">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-xl-3">
                    <div class="card filter-lists">
                        <div class="card-header">
                            <div class="d-flex align-items-center filter-head justify-content-between">
                                <h4>Filter</h4>
                                <a href="{{ route('student.tutors.search') }}" class="text-secondary text-decoration-underline">Clear All</a>
                            </div>
                        </div>
                        <div class="card-body p-0">

                            <!-- Gender -->
                            <div class="accordion-item border-bottom">
                                <div class="accordion-header" id="headingGender">
                                    <div class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseGender" aria-controls="collapseGender" role="button">
                                        <div class="d-flex align-items-center w-100">
                                            <h5>Gender</h5>
                                            <div class="ms-auto">
                                                <span><i class="fas fa-chevron-down"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="collapseGender" class="accordion-collapse show" aria-labelledby="headingGender">
                                    <div class="accordion-body pt-3">
                                        <div class="form-check mb-2">
                                            <input 
                                                class="form-check-input gender-filter" 
                                                type="radio" 
                                                name="gender_filter" 
                                                id="filterAny" 
                                                value=""
                                                {{ request('gender') === null ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label" for="filterAny">Any</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input 
                                                class="form-check-input gender-filter" 
                                                type="radio" 
                                                name="gender_filter" 
                                                id="filterMale"
                                                value="male"
                                                {{ request('gender') === 'male' ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label" for="filterMale">Male</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input 
                                                class="form-check-input gender-filter" 
                                                type="radio" 
                                                name="gender_filter" 
                                                id="filterFemale"
                                                value="female"
                                                {{ request('gender') === 'female' ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label" for="filterFemale">Female</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Native Language -->
                            <div class="accordion-item border-bottom">
                                <div class="accordion-header" id="headingLanguage">
                                    <div class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseLanguage" aria-controls="collapseLanguage" role="button">
                                        <div class="d-flex align-items-center w-100">
                                            <h5>Native Language</h5>
                                            <div class="ms-auto">
                                                <span><i class="fas fa-chevron-down"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="collapseLanguage" class="accordion-collapse show" aria-labelledby="headingLanguage">
                                    <div class="accordion-body pt-3">
                                        @php
                                            $selectedLanguages = request('languages', []);
                                        @endphp
                                        <div class="form-check mb-2">
                                            <input 
                                                class="form-check-input language-filter" 
                                                type="checkbox" 
                                                name="language_filter[]" 
                                                id="langEnglish"
                                                value="English"
                                                {{ in_array('English', $selectedLanguages) ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label" for="langEnglish">English</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input 
                                                class="form-check-input language-filter" 
                                                type="checkbox" 
                                                name="language_filter[]" 
                                                id="langJapanese"
                                                value="Japanese"
                                                {{ in_array('Japanese', $selectedLanguages) ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label" for="langJapanese">Japanese</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input 
                                                class="form-check-input language-filter" 
                                                type="checkbox" 
                                                name="language_filter[]" 
                                                id="langFrench"
                                                value="French"
                                                {{ in_array('French', $selectedLanguages) ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label" for="langFrench">French</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input 
                                                class="form-check-input language-filter" 
                                                type="checkbox" 
                                                name="language_filter[]" 
                                                id="langVietnamese"
                                                value="Vietnamese"
                                                {{ in_array('Vietnamese', $selectedLanguages) ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label" for="langVietnamese">Vietnamese</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input 
                                                class="form-check-input language-filter" 
                                                type="checkbox" 
                                                name="language_filter[]" 
                                                id="langSpanish"
                                                value="Spanish"
                                                {{ in_array('Spanish', $selectedLanguages) ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label" for="langSpanish">Spanish</label>
                                        </div>
                                        <div class="form-check">
                                            <input 
                                                class="form-check-input language-filter" 
                                                type="checkbox" 
                                                name="language_filter[]" 
                                                id="langKorean"
                                                value="Korean"
                                                {{ in_array('Korean', $selectedLanguages) ? 'checked' : '' }}
                                            >
                                            <label class="form-check-label" for="langKorean">Korean</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>


                <!-- Tutor Cards -->
                <div class="col-xl-9">
                    <div class="row align-items-center mb-4">
                        <div class="col-md-6">
                            <h3>Showing <span class="text-secondary">{{ $teachers->total() }}</span> Tutors For You</h3>
                        </div>
                    </div>

                    <!-- Tutor  -->
                    @forelse($teachers as $teacher)
                        <div class="col-lg-12 mb-4">
                            <div class="card doctor-list-card">
                                <div class="d-md-flex align-items-center">
                                    {{-- Teacher image --}}
                                    <div class="card-img card-img-hover">
                                        <a href="{{ route('student.tutors.profile',['id' => encryptId($teacher->id)]) }}">
                                            <img src="{{ asset('assets/img/profile-placeholder.jpg') }}" alt="{{ $teacher->name }}">
                                        </a>
                                        {{-- <div class="grid-overlay-item d-flex align-items-center justify-content-between">
                                            <span class="badge bg-orange">
                                                <i class="fa-solid fa-star me-1"></i>4.9
                                            </span>
                                            <a href="javascript:void(0)" class="fav-icon">
                                                <i class="fa fa-heart"></i>
                                            </a>
                                        </div> --}}
                                    </div>

                                    <div class="card-body p-0">
                                        {{-- Header --}}
                                        <div class="d-flex align-items-center justify-content-between border-bottom p-3">
                                            <a href="{{ route('student.tutors.profile',['id' => encryptId($teacher->id)]) }}" class="text-teal fw-medium fs-14">
                                                {{ $teacher->teacherProfile->preferred_name ?? $teacher->name }}
                                            </a>
                                            <span class="badge bg-success-light d-inline-flex align-items-center">
                                                <i class="fa-solid fa-circle fs-5 me-1"></i>Available
                                            </span>
                                        </div>

                                        {{-- Body --}}
                                        <div class="p-3">
                                            <div class="doctor-info-detail pb-3">
                                                <div class="row align-items-center gy-3">
                                                    <div class="col-sm-6">
                                                        <h6 class="d-flex align-items-center mb-1">
                                                            <a href="{{ route('student.tutors.profile',['id' => encryptId($teacher->id)]) }}">{{ $teacher->teacherProfile->preferred_name ?? $teacher->name }}</a>
                                                            <i class="isax isax-tick-circle5 text-success ms-2"></i>
                                                        </h6>
                                                        <p class="mb-2">
                                                            {{ $teacher->teacherProfile->bio ?? 'Certified TESOL Tutor' }}
                                                        </p>
                                                        <p class="d-flex align-items-center mb-0 fs-14">
                                                            <i class="isax isax-location me-2"></i>
                                                            {{ $teacher->teacherProfile->tz ?? 'N/A' }}
                                                        </p>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <p class="d-flex align-items-center mb-2 fs-14">
                                                            <i class="isax isax-language-circle text-dark me-2"></i>
                                                            {{ $teacher->teacherProfile->native_language ?? 'English' }}
                                                        </p>
                                                        <p class="d-flex align-items-center mb-2 fs-14">
                                                            <i class="isax isax-like-1 text-dark me-2"></i>
                                                            98% (245 Reviews)
                                                        </p>
                                                        <p class="d-flex align-items-center mb-0 fs-14">
                                                            <i class="isax isax-archive-14 text-dark me-2"></i>
                                                            5 Years Experience
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Availability section --}}
                                            @php
                                                $nextSlot = $teacher->availabilities->where('is_booked', false)->sortBy('start_utc')->first();
                                            @endphp

                                            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mt-3">
                                                <div class="me-3">
                                                    <p class="mb-1">Lesson Rate</p>
                                                    <h3 class="text-orange">1 Ticket / Lesson</h3>
                                                </div>

                                                @if($nextSlot)
                                                    <p class="mb-0">
                                                        Available at<br>
                                                        {{ \Carbon\Carbon::parse($nextSlot->start_utc)->format('h:i A \\o\\n M j, Y') }}
                                                    </p>
                                                @else
                                                    <p class="mb-0 text-muted">No upcoming slots</p>
                                                @endif

                                                <a href="#" class="btn btn-md btn-primary-gradient rounded-pill">
                                                    <i class="isax isax-calendar-1 me-2"></i>Book Lesson
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 text-center py-5">
                            <h5 class="text-muted">No teachers found matching your filters.</h5>
                        </div>
                    @endforelse
                    <!-- /Tutor  -->
                    <!-- Pagination -->
                    <div class="col-md-12">
                        <x-pagination :paginator="$teachers" />
                    </div>
                    <!-- /Pagination -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{asset('assets/js/feather.min.js')}}"></script>
    <script src="{{asset('assets/plugins/ion-rangeslider/js/ion.rangeSlider.js')}}"></script>
    <script src="{{asset('assets/js/moment.min.js')}}"></script>
    <script src="{{asset('assets/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script>
        feather.replace();
        $("#range_03").ionRangeSlider({
            type: "double",
            grid: true,
            min: 0,
            max: 100,
            from: 5,
            to: 60,
            prefix: "$"
        });
        
        $('.datetimepicker').datetimepicker({
            format: 'YYYY-MM-DD HH:mm',
            showClose: true,
            showClear: true,
            showTodayButton: true,
            icons: {
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down',
                previous: 'fa fa-chevron-left',
                next: 'fa fa-chevron-right',
            },
            stepping: 15,
            sideBySide: true
        });

        $(document).ready(function() {
            // Handle gender filter change
            $('.gender-filter').on('change', function() {
                // Update hidden input
                $('#hiddenGender').val($(this).val());
                // Submit form
                $('#searchForm').submit();
            });

            // Handle language filter change
            $('.language-filter').on('change', function() {
                // Remove all existing hidden language inputs
                $('.hiddenLanguage').remove();
                
                // Add new hidden inputs for all checked languages
                $('.language-filter:checked').each(function() {
                    $('#searchForm').append(
                        $('<input>')
                            .attr('type', 'hidden')
                            .attr('name', 'languages[]')
                            .addClass('hiddenLanguage')
                            .val($(this).val())
                    );
                });
                
                // Submit form
                $('#searchForm').submit();
            });
        });
    </script>
@endpush