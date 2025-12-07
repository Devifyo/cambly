@extends('layouts.student.app')
@section('title', 'Find Your Perfect Teacher - Learn 1-on-1 with Expert Teachers')

@push('styles')
    <style>
        /* --- General Layout & Typography --- */
        html, body {
            overflow-x: hidden;
            font-family: 'Inter', sans-serif;
        }

        .page-section {
            padding: 5rem 0;
            position: relative;
        }

        .bg-light-gray {
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }

        .text-gradient {
            background: linear-gradient(90deg, #0E82FD 0%, #06AED4 70%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* --- Section Headers --- */
        .section-header {
            text-align: center;
            margin-bottom: 3rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }
        .section-header h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 1rem;
        }
        .section-header p {
            font-size: 1.15rem;
            color: #4b5563;
            line-height: 1.6;
        }

        /* --- Hero Section --- */
        .hero-section {
            padding: 6rem 0 5rem 0;
            background: #fff;
        }
        .hero-content {
            max-width: 600px;
        }
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            color: #111827;
            margin-bottom: 1.5rem;
        }
        .hero-subtitle {
            font-size: 1.25rem;
            color: #4b5563;
            line-height: 1.7;
            margin-bottom: 2.5rem;
        }
        .hero-image-wrapper {
            position: relative;
            z-index: 1;
        }
        .hero-image-wrapper img {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(14, 130, 253, 0.15);
            transform: rotate(2deg);
            transition: transform 0.3s ease;
        }
        .hero-image-wrapper:hover img {
            transform: rotate(0deg);
        }

        /* --- Mission Section --- */
        .mission-content {
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
        }
        .mission-title {
            font-size: 2.75rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            background: linear-gradient(90deg, #0E82FD 0%, #06AED4 70%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .mission-text {
            font-size: 1.25rem;
            line-height: 1.8;
            color: #374151;
        }

        /* --- Step Cards --- */
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }
        .step-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 2.5rem 2rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        .step-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.08);
            border-color: #d1d5db;
        }
        .step-icon-circle {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #eff6ff;
            color: #0E82FD;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            transition: all 0.3s ease;
        }
        .step-card:hover .step-icon-circle {
            background: linear-gradient(90deg, #0E82FD 0%, #06AED4 70%);
            color: #fff;
        }
        .step-card h3 {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #1f2937;
        }
        .step-card p {
            color: #6b7280;
            font-size: 1rem;
            line-height: 1.6;
        }

        /* --- Why Us List --- */
        .why-us-list {
            list-style: none;
            padding-left: 0;
            margin-top: 1.5rem;
        }
        .why-us-list li {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
            color: #4b5563;
        }
        .why-us-list li .feather {
            flex-shrink: 0;
            color: #0E82FD;
            margin-top: 4px;
            width: 24px;
            height: 24px;
        }
        .why-us-list strong {
            color: #111827;
            font-weight: 700;
        }

        /* --- CTA Section --- */
        .cta-section {
            padding: 6rem 1rem;
            background: linear-gradient(90deg, #0E82FD 0%, #06AED4 70%);
            color: #fff;
            text-align: center;
        }
        .cta-content h2 {
            font-size: 2.75rem;
            font-weight: 800;
            margin-bottom: 2.5rem;
        }
        
        /* Buttons */
        .btn-primary-gradient {
            background: linear-gradient(90deg, #0E82FD 0%, #06AED4 70%);
            color: #fff;
            border: none;
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 4px 15px rgba(14, 130, 253, 0.3);
        }
        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(14, 130, 253, 0.4);
            color: #fff;
        }

        .btn-light-white {
            background: #fff;
            color: #0E82FD;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .btn-light-white:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
            background: #f8fafc;
        }

        .btn-outline-white {
            background: transparent;
            color: #fff;
            border: 2px solid #fff;
            padding: 1rem 2.5rem;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
        }
        .btn-outline-white:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-3px);
        }

        .btn-outline-dark {
            background: transparent;
            color: #374151;
            border: 2px solid #e5e7eb;
            padding: 0.9rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-outline-dark:hover {
            border-color: #0E82FD;
            color: #0E82FD;
            background: #fff;
        }

        /* --- Responsive --- */
        @media (max-width: 991px) {
            .hero-title { font-size: 2.5rem; }
            .mission-title { font-size: 2.25rem; }
            .steps-grid { grid-template-columns: 1fr; gap: 1.5rem; }
            .hero-image-wrapper { margin-top: 3rem; transform: rotate(0); }
            .hero-section { padding-top: 3rem; text-align: center; }
            .hero-content { margin: 0 auto; }
            .btn-outline-dark { margin-left: 0; margin-top: 1rem; display: inline-block; }
            .why-us-list li { flex-direction: column; gap: 0.5rem; }
        }
    </style>
@endpush

@section('content')

    {{-- 1. Hero Section --}}
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1 class="hero-title">
                            Unlock Your Potential with <span class="text-gradient">Expert Tutors.</span>
                        </h1>
                        <p class="hero-subtitle">
                            Master any subject with personalized 1-on-1 lessons. 
                            From school syllabus to professional skills, find the perfect mentor today.
                        </p>
                        <div class="d-flex flex-wrap gap-3" style="column-gap: 15px;">
                            <a href="{{ route('student.tutors.search') }}" class="btn-primary-gradient">
                                <i class="isax isax-search-normal-1"></i> Find a Teacher
                            </a>
                            <a href="{{ route('auth.register') }}" class="btn-outline-dark">
                                Become a Teacher
                            </a>
                        </div>
                        
                        <div class="mt-4 pt-2">
                            <p class="mb-0 text-muted small">
                                <i class="isax isax-shield-tick text-success me-1"></i> Verified Teachers
                                <span class="mx-2">•</span>
                                <i class="isax isax-card text-success me-1"></i> Secure Payment
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image-wrapper">
                        {{-- Placeholder image --}}
                        <img src="{{ asset('assets/img/about.webp') }}" alt="Student Learning" class="img-fluid">
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 2. Mission Section (Added from text) --}}
    <section class="page-section">
        <div class="container">
            <div class="mission-content">
                <h1 class="mission-title">Learn Your Way.</h1>
                <p class="mission-text">
                    <strong>{{ config('app.name') }}</strong> is your platform for 1-to-1 learning. We connect you with expert teachers, so you can book lessons tailored to your schedule and goals. We believe learning is personal. In a world of standardized tests, we built a place where you can learn at your own pace, on your own terms.
                </p>
            </div>
        </div>
    </section>

    {{-- 3. For Students Section (Detailed Steps from text) --}}
    <section class="page-section bg-light-gray">
        <div class="container">
            <div class="section-header">
                <h2>For Students</h2>
                <p>Your personalized learning journey in three simple steps.</p>
            </div>
            
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-icon-circle">
                        <i class="isax isax-search-normal-1"></i>
                    </div>
                    <h3>1. Discover Teachers</h3>
                    <p>Search our global network of expert teachers. Filter by subject, price, and—most importantly—your schedule.</p>
                </div>
                <div class="step-card">
                    <div class="step-icon-circle">
                        <i class="isax isax-calendar-tick"></i>
                    </div>
                    <h3>2. Book Your Lesson</h3>
                    <p>Find a time that works for you and book your 1-to-1 lesson instantly. We manage the scheduling so you don't have to.</p>
                </div>
                <div class="step-card">
                    <div class="step-icon-circle">
                        <i class="isax isax-video-play"></i>
                    </div>
                    <h3>3. Connect & Learn</h3>
                    <p>You'll get a confirmation and a private meeting link. We'll even send a reminder a few hours before it starts.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. For Tutors Section (Detailed Steps from text) --}}
    <section class="page-section">
        <div class="container">
            <div class="section-header">
                <h2>For Teachers</h2>
                <p>Share your expertise, set your own schedule, and start earning.</p>
            </div>
            
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-icon-circle">
                        <i class="isax isax-profile-add"></i>
                    </div>
                    <h3>1. Register & Build Profile</h3>
                    <p>Sign up and create your teacher profile. Showcase your experience, qualifications, and a video introduction.</p>
                </div>
                <div class="step-card">
                    <div class="step-icon-circle">
                        <i class="isax isax-clock"></i>
                    </div>
                    <h3>2. Set Your Availability</h3>
                    <p>You are in complete control. Add your available time slots to your calendar so students can book you instantly.</p>
                </div>
                <div class="step-card">
                    <div class="step-icon-circle">
                        <i class="isax isax-dollar-circle"></i>
                    </div>
                    <h3>3. Teach & Earn</h3>
                    <p>When a student books your time, you'll be notified. Just show up, teach your lesson, and get paid.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. Why Choose Us (Updated Content from text) --}}
    <section class="page-section bg-light-gray">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="{{ asset('assets/img/mahogo-teacher.webp') }}" alt="Why Choose Us" class="img-fluid rounded-3 shadow-lg" style="border-radius: 12px;">
                </div>
                <div class="col-lg-6 ps-lg-5">
                    <h2 class="fw-bold mb-3" style="font-size: 2.25rem;">A Platform <span class="text-gradient">Built For You</span></h2>
                    <p class="lead text-muted mb-4">
                        We're not just another directory. We are a complete learning platform built to ensure quality, trust, and ease of use.
                    </p>
                    
                    <ul class="why-us-list">
                        <li>
                            <i data-feather="check-circle"></i> 
                            <div>
                                <strong>Verified, Expert Teachers:</strong> Every teacher on our platform is vetted for expertise and teaching quality.
                            </div>
                        </li>
                        <li>
                            <i data-feather="check-circle"></i> 
                            <div>
                                <strong>Flexible Scheduling:</strong> Find teachers in any time zone and book lessons when it truly works for you.
                            </div>
                        </li>
                        <li>
                            <i data-feather="check-circle"></i> 
                            <div>
                                <strong>All-in-One Dashboard:</strong> Manage your schedule, payments, and lesson history all in one secure place.
                            </div>
                        </li>
                        <li>
                            <i data-feather="check-circle"></i> 
                            <div>
                                <strong>Personalized Support:</strong> We're here to help you succeed. Our support team is ready to assist you every step of the way.
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- 6. CTA Section (Updated with dual buttons) --}}
    <div class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Ready to Get Started?</h2>
                <div class="d-flex flex-wrap justify-content-center gap-3">
                    <a href="{{ route('student.tutors.search') }}" class="btn-light-white">
                        Find a Teacher
                    </a>
                    <a href="{{ route('auth.register') }}" class="btn-outline-white"> {{-- Update route later --}}
                        Become a Teacher
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        feather.replace();
    </script>
@endpush