@extends('layouts.student.app')

@section('title', 'Tutor Profile - Online Tutoring Platform')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/fancybox/jquery.fancybox.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/feather.css') }}">
@endpush

@section('content')
    <!-- Breadcrumb -->
    <div class="breadcrumb-bar">
        <div class="container">
            <div class="row align-items-center inner-banner">
                <div class="col-md-12 col-12 text-center">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#"><i class="isax isax-home-15"></i></a></li>
                            <li class="breadcrumb-item">Tutor</li>
                            <li class="breadcrumb-item active">Tutor Profile</li>
                        </ol>
                        <h2 class="breadcrumb-title">Tutor Profile</h2>
                    </nav>
                </div>
            </div>
        </div>
        <div class="breadcrumb-bg">
            <img src="{{ asset('assets/img/bg/breadcrumb-bg-01.png') }}" alt="img" class="breadcrumb-bg-01">
            <img src="{{ asset('assets/img/bg/breadcrumb-bg-02.png') }}" alt="img" class="breadcrumb-bg-02">
            <img src="{{ asset('assets/img/bg/breadcrumb-icon.png') }}" alt="img" class="breadcrumb-bg-03">
            <img src="{{ asset('assets/img/bg/breadcrumb-icon.png') }}" alt="img" class="breadcrumb-bg-04">
        </div>
    </div>
    <!-- /Breadcrumb -->

    <div class="content-inner mt-5">
        <div class="container">
            <!-- Tutor Widget -->
            <div class="card doc-profile-card">
                <div class="card-body">
                    <div class="doctor-widget doctor-profile-two">
                        <div class="doc-info-left">
                            <div class="doctor-img">
                                <img src="{{ asset('assets/img/profile-placeholder.jpg') }}" class="img-fluid" alt="Tutor Image">
                            </div>
                            <div class="doc-info-cont">
                                <span class="badge doc-avail-badge">
                                    <i class="fa-solid fa-circle"></i>
                                    Available
                                </span>
                                <h4 class="doc-name">
                                    John Doe
                                    <img src="{{ asset('assets/img/icons/badge-check.svg') }}" alt="Img">
                                    <span class="badge doctor-role-badge">
                                        <i class="fa-solid fa-circle"></i>
                                        TESOL Certified
                                    </span>
                                </h4>
                                <p>Certified TESOL Tutor</p>
                                <p>Speaks: English, French</p>
                                <p class="address-detail">
                                    <span class="loc-icon"><i class="feather-map-pin"></i></span>
                                    Online (UTC)
                                    <span class="view-text">(View Location)</span>
                                </p>
                            </div>
                        </div>
                        <div class="doc-info-right">
                            <ul class="doctors-activities">
                                <li>
                                    <div class="hospital-info">
                                        <span class="list-icon"><img src="{{ asset('assets/img/icons/watch-icon.svg') }}" alt="Img"></span>
                                        <p>Full Time, Online Lessons Available</p>
                                    </div>
                                    <ul class="sub-links">
                                        <li><a href="#"><i class="feather-heart"></i></a></li>
                                        <li><a href="#"><i class="feather-share-2"></i></a></li>
                                        <li><a href="#"><i class="feather-link"></i></a></li>
                                    </ul>
                                </li>
                                <li>
                                    <div class="hospital-info">
                                        <span class="list-icon"><img src="{{ asset('assets/img/icons/thumb-icon.svg') }}" alt="Img"></span>
                                        <p><b>98% </b> Recommended</p>
                                    </div>
                                </li>
                                <li>
                                    <div class="hospital-info">
                                        <span class="list-icon"><img src="{{ asset('assets/img/icons/building-icon.svg') }}" alt="Img"></span>
                                        <p>Online Tutoring Platform</p>
                                    </div>
                                    <h5 class="accept-text">
                                        <span><i class="feather-check"></i></span>
                                        Accepting New Students
                                    </h5>
                                </li>
                                <li>
                                    <div class="rating">
                                        <i class="fas fa-star filled"></i>
                                        <i class="fas fa-star filled"></i>
                                        <i class="fas fa-star filled"></i>
                                        <i class="fas fa-star filled"></i>
                                        <i class="fas fa-star filled"></i>
                                        <span>5.0</span>
                                        <a href="#" class="d-inline-block average-rating">150 Reviews</a>
                                    </div>
                                    <ul class="contact-doctors">
                                        <li><a href="#"><span><img src="{{ asset('assets/img/icons/device-message2.svg') }}" alt="Img"></span>Chat</a></li>
                                        <li><a href="#"><span class="bg-violet"><i class="feather-phone-forwarded"></i></span>Audio Lesson</a></li>
                                        <li><a href="#"><span class="bg-indigo"><i class="fa-solid fa-video"></i></span>Video Lesson</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="doc-profile-card-bottom">
                        <ul>
                            <li>
                                <span class="bg-blue"><img src="{{ asset('assets/img/icons/calendar3.svg') }}" alt="Img"></span>
                                Nearly 200+ Lessons Booked
                            </li>
                            <li>
                                <span class="bg-dark-blue"><img src="{{ asset('assets/img/icons/bullseye.svg') }}" alt="Img"></span>
                                In Practice for 5 Years
                            </li>
                            <li>
                                <span class="bg-green"><img src="{{ asset('assets/img/icons/bookmark-star.svg') }}" alt="Img"></span>
                                15+ Certifications
                            </li>
                        </ul>
                        <div class="bottom-book-btn">
                            <p><span>Price: 1 Ticket / Lesson</span></p>
                            <div class="clinic-booking">
                                <a class="apt-btn" href="#">Book Lesson</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Tutor Widget -->

            <div class="doctors-detailed-info">
                <ul class="information-title-list">
                    <li class="active">
                        <a href="#availability">Availability</a>
                    </li>
                    <li>
                        <a href="#tutor_bio">Bio</a>
                    </li>
                    <li>
                        <a href="#more_info">More Info</a>
                    </li>
                </ul>
                <div class="doc-information-main">
                    <div class="doc-information-details" id="availability">
                        <div class="detail-title slider-nav d-flex justify-content-between align-items-center">
                            <h4>Availability</h4>
                            <div class="nav nav-container slide-2"></div>
                        </div>
                        <div class="availability-slots-slider owl-carousel">
                            <div class="availability-date">
                                <div class="book-date">
                                    <h6>Tue Nov 4</h6>
                                    <span>09:00 AM - 10:00 AM</span>
                                </div>
                            </div>
                            <div class="availability-date">
                                <div class="book-date">
                                    <h6>Wed Nov 5</h6>
                                    <span>02:00 PM - 03:00 PM</span>
                                </div>
                            </div>
                            <div class="availability-date">
                                <div class="book-date">
                                    <h6>Thu Nov 6</h6>
                                    <span>11:00 AM - 12:00 PM</span>
                                </div>
                            </div>
                            <div class="availability-date">
                                <div class="book-date">
                                    <h6>Fri Nov 7</h6>
                                    <span>04:00 PM - 05:00 PM</span>
                                </div>
                            </div>
                            <div class="availability-date">
                                <div class="book-date">
                                    <h6>Sat Nov 8</h6>
                                    <span>10:00 AM - 11:00 AM</span>
                                </div>
                            </div>
                            <div class="availability-date">
                                <div class="book-date">
                                    <h6>Sun Nov 9</h6>
                                    <span>01:00 PM - 02:00 PM</span>
                                </div>
                            </div>
                            <div class="availability-date">
                                <div class="book-date">
                                    <h6>Sun Nov 9</h6>
                                    <span>01:00 PM - 02:00 PM</span>
                                </div>
                            </div>
                            <div class="availability-date">
                                <div class="book-date">
                                    <h6>Sun Nov 10</h6>
                                    <span>01:00 PM - 02:00 PM</span>
                                </div>
                            </div>
                            <div class="availability-date">
                                <div class="book-date">
                                    <h6>Sun Nov 11</h6>
                                    <span>01:00 PM - 02:00 PM</span>
                                </div>
                            </div>
                            
                        </div>
                    </div>

                    <div class="doc-information-details bio-detail" id="tutor_bio" style="display: none;">
                        <div class="detail-title">
                            <h4>Tutor Bio</h4>
                        </div>
                        <p>Highly motivated and experienced tutor with a passion for providing excellent guidance to students. Experienced in various educational settings, with expertise in language teaching, ESL, and personalized learning. Committed to delivering compassionate, effective sessions.</p>
                        <a href="#" class="show-more d-flex align-items-center">See More<i class="fa-solid fa-chevron-down ms-2"></i></a>
                    </div>

                    <div class="doc-information-details" id="more_info" style="display: none;">
                        <div class="detail-title">
                            <h4>More Info</h4>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Total Sessions Completed:</strong> 250</p>
                                <p><strong>Last Joined At:</strong> October 15, 2025</p>
                                <p><strong>Qualification:</strong> TESOL Certified</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Total Experience in Field:</strong> 5 Years</p>
                                <p><strong>Native Language:</strong> English</p>
                                <p><strong>Gender:</strong> Male</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('assets/js/feather.min.js') }}"></script>
    <script src="{{ asset('assets/js/owl.carousel.min.js') }}"></script>
    {{-- <script src="{{ asset('assets/plugins/fancybox/jquery.fancybox.min.js') }}"></script> --}}
    <script>
        feather.replace();

        // Tab functionality
        $('.information-title-list li a').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');
            $('.information-title-list li').removeClass('active');
            $(this).parent().addClass('active');
            $('.doc-information-details').hide();
            $(target).show();
        });
    </script>
@endpush