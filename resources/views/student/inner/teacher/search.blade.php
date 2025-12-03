@extends('layouts.student.app')

@section('title', 'Tutor List - Online Tutoring Platform')

@push('styles')
    <link rel="stylesheet" href="{{asset('assets/plugins/ion-rangeslider/css/ion.rangeSlider.css')}}">
    <link rel="stylesheet" href="{{asset('assets/plugins/ion-rangeslider/css/ion.rangeSlider.min.css')}}">
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
                            <li class="breadcrumb-item">Book a Lesson</li>
                            <li class="breadcrumb-item active">Tutor List</li>
                        </ol>
                        <h2 class="breadcrumb-title">Book a Lesson</h2>
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
                            <!-- <div class="accordion-item border-bottom">
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
                            </div> -->

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
                                        {{-- Iterate through the languages from the DB --}}
                                        @foreach(getAllLanguages() as $language)
                                            <div class="form-check mb-2">
                                                <input 
                                                    class="form-check-input language-filter" 
                                                    type="checkbox" 
                                                    name="language_filter[]" 
                                                    {{-- Ensure ID is unique by using the language ID --}}
                                                    id="lang_{{ $language->id }}"
                                                    {{-- IMPORTANT: Ensure this value matches what your scopeFilterByLanguage expects (name or code) --}}
                                                    value="{{ $language->code }}"
                                                    {{-- Check if this specific language name is in the selected array --}}
                                                    {{ in_array($language->code, $selectedLanguages ?? []) ? 'checked' : '' }}
                                                >
                                                <label class="form-check-label" for="lang_{{ $language->id }}">
                                                    {{ ucfirst($language->name) }}
                                                </label>
                                            </div>
                                        @endforeach
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
                        <x-teacher-card :teacher="$teacher" :authUser="$authUser" />
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
    <script src="{{asset('assets/plugins/ion-rangeslider/js/ion.rangeSlider.js')}}"></script>
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
            format: 'YYYY-MM-DD',
            minDate: moment().startOf('day'),
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