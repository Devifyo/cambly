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
                    <form>
                        <div class="search-input search-line">
                            <i class="isax isax-teacher bficon"></i>
                            <div class=" mb-0">
                                <input type="text" class="form-control" placeholder="Search for Tutors, Skills, or Subjects">
                            </div>
                        </div>
                        <div class="search-input search-map-line">
                            <i class="isax isax-location5"></i>
                            <div class=" mb-0">
                                <input type="text" class="form-control" placeholder="Location or Time Zone">
                            </div>
                        </div>
                        <div class="search-input search-calendar-line">
                            <i class="isax isax-calendar-tick5"></i>
                            <div class=" mb-0">
                                <input type="text" class="form-control datetimepicker" placeholder="Date">
                            </div>
                        </div>
                        <div class="form-search-btn">
                            <button class="btn btn-primary d-inline-flex align-items-center rounded-pill" type="submit"><i class="isax isax-search-normal-15 me-2"></i>Search</button>
                        </div>
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
                                <a href="#" class="text-secondary text-decoration-underline">Clear All</a>
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
                                            <input class="form-check-input" type="radio" name="genderFilter" id="filterAny" checked>
                                            <label class="form-check-label" for="filterAny">Any</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="genderFilter" id="filterMale">
                                            <label class="form-check-label" for="filterMale">Male</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="genderFilter" id="filterFemale">
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
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="langEnglish">
                                            <label class="form-check-label" for="langEnglish">English</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="langJapanese">
                                            <label class="form-check-label" for="langJapanese">Japanese</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="langFrench">
                                            <label class="form-check-label" for="langFrench">French</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="langVietnamese">
                                            <label class="form-check-label" for="langVietnamese">Vietnamese</label>
                                        </div>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" id="langSpanish">
                                            <label class="form-check-label" for="langSpanish">Spanish</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="langKorean">
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
                            <h3>Showing <span class="text-secondary">3</span> Tutors For You</h3>
                        </div>
                    </div>

                    <!-- Tutor 1 -->
                    <div class="col-lg-12 mb-4">
                        <div class="card doctor-list-card">
                            <div class="d-md-flex align-items-center">
                                <div class="card-img card-img-hover">
                                    <a href="#"><img src="{{asset('assets/img/doctor-grid/doctor-list-01.jpg')}}" alt=""></a>
                                    <div class="grid-overlay-item d-flex align-items-center justify-content-between">
                                        <span class="badge bg-orange"><i class="fa-solid fa-star me-1"></i>4.9</span>
                                        <a href="javascript:void(0)" class="fav-icon"><i class="fa fa-heart"></i></a>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="d-flex align-items-center justify-content-between border-bottom p-3">
                                        <a href="#" class="text-teal fw-medium fs-14">English Conversation</a>
                                        <span class="badge bg-success-light d-inline-flex align-items-center">
                                            <i class="fa-solid fa-circle fs-5 me-1"></i>Available
                                        </span>
                                    </div>
                                    <div class="p-3">
                                        <div class="doctor-info-detail pb-3">
                                            <div class="row align-items-center gy-3">
                                                <div class="col-sm-6">
                                                    <h6 class="d-flex align-items-center mb-1">
                                                        <a href="#">Emily Carter</a>
                                                        <i class="isax isax-tick-circle5 text-success ms-2"></i>
                                                    </h6>
                                                    <p class="mb-2">Certified TESOL Tutor</p>
                                                    <p class="d-flex align-items-center mb-0 fs-14"><i class="isax isax-location me-2"></i>Tokyo, Japan</p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p class="d-flex align-items-center mb-2 fs-14"><i class="isax isax-language-circle text-dark me-2"></i>English, Japanese</p>
                                                    <p class="d-flex align-items-center mb-2 fs-14"><i class="isax isax-like-1 text-dark me-2"></i>98% (245 Reviews)</p>
                                                    <p class="d-flex align-items-center mb-0 fs-14"><i class="isax isax-archive-14 text-dark me-2"></i>5 Years Experience</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mt-3">
                                            <div class="me-3">
                                                <p class="mb-1">Lesson Rate</p>
                                                <h3 class="text-orange">1 Ticket / Lesson</h3>
                                            </div>
                                            <p class="mb-0">Next available <br>10:00 AM Today</p>
                                            <a href="#" class="btn btn-md btn-primary-gradient rounded-pill"><i class="isax isax-calendar-1 me-2"></i>Book Lesson</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tutor 2 -->
                    <div class="col-lg-12 mb-4">
                        <div class="card doctor-list-card">
                            <div class="d-md-flex align-items-center">
                                <div class="card-img card-img-hover">
                                    <a href="#"><img src="{{asset('assets/img/doctor-grid/doctor-list-02.jpg')}}" alt=""></a>
                                    <div class="grid-overlay-item d-flex align-items-center justify-content-between">
                                        <span class="badge bg-orange"><i class="fa-solid fa-star me-1"></i>4.7</span>
                                        <a href="javascript:void(0)" class="fav-icon"><i class="fa fa-heart"></i></a>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="d-flex align-items-center justify-content-between border-bottom p-3">
                                        <a href="#" class="text-indigo fw-medium fs-14">Business English</a>
                                        <span class="badge bg-success-light d-inline-flex align-items-center">
                                            <i class="fa-solid fa-circle fs-5 me-1"></i>Available
                                        </span>
                                    </div>
                                    <div class="p-3">
                                        <div class="doctor-info-detail pb-3">
                                            <div class="row align-items-center gy-3">
                                                <div class="col-sm-6">
                                                    <h6 class="d-flex align-items-center mb-1">
                                                        <a href="#">Daniel Roberts</a>
                                                        <i class="isax isax-tick-circle5 text-success ms-2"></i>
                                                    </h6>
                                                    <p class="mb-2">MBA, English Coach</p>
                                                    <p class="d-flex align-items-center mb-0 fs-14"><i class="isax isax-location me-2"></i>London, UK</p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p class="d-flex align-items-center mb-2 fs-14"><i class="isax isax-language-circle text-dark me-2"></i>English, French</p>
                                                    <p class="d-flex align-items-center mb-2 fs-14"><i class="isax isax-like-1 text-dark me-2"></i>95% (310 Reviews)</p>
                                                    <p class="d-flex align-items-center mb-0 fs-14"><i class="isax isax-archive-14 text-dark me-2"></i>8 Years Experience</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mt-3">
                                            <div class="me-3">
                                                <p class="mb-1">Lesson Rate</p>
                                                <h3 class="text-orange">1 Ticket / Lesson</h3>
                                            </div>
                                            <p class="mb-0">Next available <br>2:30 PM Today</p>
                                            <a href="#" class="btn btn-md btn-primary-gradient rounded-pill"><i class="isax isax-calendar-1 me-2"></i>Book Lesson</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tutor 3 -->
                    <div class="col-lg-12 mb-4">
                        <div class="card doctor-list-card">
                            <div class="d-md-flex align-items-center">
                                <div class="card-img card-img-hover">
                                    <a href="#"><img src="{{asset('assets/img/doctor-grid/doctor-list-03.jpg')}}" alt=""></a>
                                    <div class="grid-overlay-item d-flex align-items-center justify-content-between">
                                        <span class="badge bg-orange"><i class="fa-solid fa-star me-1"></i>4.8</span>
                                        <a href="javascript:void(0)" class="fav-icon"><i class="fa fa-heart"></i></a>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div class="d-flex align-items-center justify-content-between border-bottom p-3">
                                        <a href="#" class="text-pink fw-medium fs-14">IELTS Preparation</a>
                                        <span class="badge bg-danger-light d-inline-flex align-items-center">
                                            <i class="fa-solid fa-circle fs-5 me-1"></i>Unavailable
                                        </span>
                                    </div>
                                    <div class="p-3">
                                        <div class="doctor-info-detail pb-3">
                                            <div class="row align-items-center gy-3">
                                                <div class="col-sm-6">
                                                    <h6 class="d-flex align-items-center mb-1">
                                                        <a href="#">Sofia Nguyen</a>
                                                        <i class="isax isax-tick-circle5 text-success ms-2"></i>
                                                    </h6>
                                                    <p class="mb-2">IELTS Expert & Language Coach</p>
                                                    <p class="d-flex align-items-center mb-0 fs-14"><i class="isax isax-location me-2"></i>Hanoi, Vietnam</p>
                                                </div>
                                                <div class="col-sm-6">
                                                    <p class="d-flex align-items-center mb-2 fs-14"><i class="isax isax-language-circle text-dark me-2"></i>English, Vietnamese</p>
                                                    <p class="d-flex align-items-center mb-2 fs-14"><i class="isax isax-like-1 text-dark me-2"></i>93% (180 Reviews)</p>
                                                    <p class="d-flex align-items-center mb-0 fs-14"><i class="isax isax-archive-14 text-dark me-2"></i>6 Years Experience</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mt-3">
                                            <div class="me-3">
                                                <p class="mb-1">Lesson Rate</p>
                                                <h3 class="text-orange">1 Ticket / Lesson</h3>
                                            </div>
                                            <p class="mb-0">Next available <br>Tomorrow 9:00 AM</p>
                                            <a href="#" class="btn btn-md btn-primary-gradient rounded-pill"><i class="isax isax-calendar-1 me-2"></i>Book Lesson</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="col-md-12">
                        <div class="pagination dashboard-pagination mt-md-3 mt-0 mb-4">
                            <ul>
                                <li><a href="#" class="page-link prev">Prev</a></li>
                                <li><a href="#" class="page-link active">1</a></li>
                                <li><a href="#" class="page-link">2</a></li>
                                <li><a href="#" class="page-link">3</a></li>
                                <li><a href="#" class="page-link next">Next</a></li>
                            </ul>
                        </div>
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
            format: 'YYYY-MM-DD',
        });
    </script>
@endpush
