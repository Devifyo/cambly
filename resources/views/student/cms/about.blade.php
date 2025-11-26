@extends('layouts.student.app')
@section('title', 'About Us')

@push('styles')
    <style>
        html, body {
            overflow-x: hidden;
        }

        .page-section {
            padding: 4rem 0;
        }
        .bg-light-gray {
            background-color: #f9fafb;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }
        .container-tight {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        .section-header h2 {
            font-size: 2.25rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 1rem;
        }
        .section-header p {
            font-size: 1.1rem;
            color: #4b5563;
        }

        /* --- Section 1: Mission --- */
        .mission-section {
            padding-top: 3rem;
            padding-bottom: 3rem;
            /* --- FIX #2: Pushes section down from breadcrumb --- */
            margin-top: 2rem; 
        }
        .mission-section h1 {
            font-size: 2.75rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(90deg, #0E82FD 0%, #06AED4 70%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .mission-section p {
            font-size: 1.2rem;
            line-height: 1.7;
            color: #374151;
            max-width: 800px;
            margin: 0 auto;
        }

        /* --- Section 2: How It Works (The Grid) --- */
        .how-it-works-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }
        .step-card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .step-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
        .step-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(90deg, #0E82FD 0%, #06AED4 70%);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
        }
        .step-icon i {
            font-size: 1.75rem;
        }
        .step-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.5rem;
        }

        /* --- Section 3: Why Choose Us --- */
        .why-us-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: center;
        }
        .why-us-image img {
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .why-us-content ul {
            list-style: none;
            padding-left: 0;
            margin-top: 1.5rem;
        }
        .why-us-content li {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            font-size: 1.05rem;
            margin-bottom: 1rem;
        }
        .why-us-content li .feather {
            flex-shrink: 0;
            color: #0E82FD;
            margin-top: 4px;
        }

        /* --- Section 4: CTA --- */
        .cta-section {
            text-align: center;
            padding: 4rem 1rem;
            background: linear-gradient(90deg, #0E82FD 0%, #06AED4 70%);
            color: #fff;
        }
        .cta-section h2 {
            font-size: 2rem;
            font-weight: 700;
            /* --- FIX #1: Adds space between H2 and button --- */
            margin-bottom: 1.5rem; 
        }
        .btn-light-outline {
            background: #fff;
            color: #0E82FD;
            border: none;
            padding: 0.8rem 2rem;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-light-outline:hover {
            background: #f0f4ff;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* --- Responsive --- */
        @media (max-width: 768px) {
            .page-section {
                padding: 2.5rem 0;
            }
            .section-header h2 {
                font-size: 1.75rem;
            }
            .mission-section h1 {
                font-size: 2.25rem;
            }
            .mission-section p {
                font-size: 1.1rem;
            }

            .how-it-works-grid,
            .why-us-grid {
                grid-template-columns: 1fr; /* Stack the grids */
            }
            
            .why-us-image {
                order: -1; 
            }
        }

    </style>
@endpush

@section('content')
    <div class="breadcrumb-bar overflow-visible">
        <div class="container">
            <div class="row align-items-center inner-banner">
                <div class="col-md-12 col-12 text-center">
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('student.dashboard') }}"><i class="isax isax-home-15"></i></a></li>
                            <li class="breadcrumb-item active">About Us</li>
                        </ol>
                        <h2 class="breadcrumb-title">About {{ config('app.name') }}</h2>
                    </nav>
                </div>
            </div>
        </div>
        <div class="breadcrumb-bg">
            <img src="{{ asset('assets/img/bg/breadcrumb-bg-01.png') }}" alt="img" class="breadcrumb-bg-01">
            <img src="{{ asset('assets/img/bg/breadcrumb-bg-02.png') }}" alt="img" class="breadcrumb-bg-02">
        </div>
    </div>
    <div class="page-section mission-section">
        <div class="container-tight">
            <h1>Learn Your Way.</h1>
            <p>
                <strong>{{ config('app.name') }}</strong> is your platform for 1-to-1 learning. We connect you with expert tutors, so you can book lessons tailored to your schedule and goals. We believe learning is personal. In a world of standardized tests, we built a place where you can learn at your own pace, on your own terms.
            </p>
        </div>
    </div>

    <div class="page-section bg-light-gray">
        <div class="container-tight">
            <div class="section-header">
                <h2>How It Works</h2>
                <p>Get started in three simple steps. Your learning journey is just a few clicks away.</p>
            </div>

            <div class="how-it-works-grid">
                <div class="step-card">
                    <div class="step-icon"><i class="isax isax-search-normal-1"></i></div>
                    <h3>Book a Lesson</h3>*
                    <p>Search our global network of verified tutors. Filter by subject, price, and availability to find the perfect match.</p>
                </div>
                <div class="step-card">
                    <div class="step-icon"><i class="isax isax-calendar-tick"></i></div>
                    <h3>Book a Lesson</h3>
                    <p>View your tutor's schedule and book a 1-to-1 lesson at a time that fits your life, all from your dashboard.</p>
                </div>
                <div class="step-card">
                    <div class="step-icon"><i class="isax isax-teacher"></i></div>
                    <h3>Start Learning</h3>
                    <p>Join your lesson and start achieving your goals. It's that simple. Personalized, focused, and all about you.</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="page-section">
        <div class="container-tight">
            <div class="why-us-grid">
                <div class="why-us-content">
                    <div class="section-header" style="text-align: left; margin-bottom: 2rem;">
                        <h2>A Platform Built For You</h2>
                    </div>
                    <p>We're not just another directory. We are a complete learning platform built to ensure quality, trust, and ease of use.</p>
                    <ul>
                        <li><i data-feather="check-circle"></i> <strong>Verified, Expert Tutors:</strong> Every tutor on our platform is vetted for expertise and teaching quality.</li>
                        <li><i data-feather="check-circle"></i> <strong>Flexible Scheduling:</strong> Find tutors in any time zone and book lessons when it truly works for you.</li>
                        <li><i data-feather="check-circle"></i> <strong>All-in-One Dashboard:</strong> Manage your schedule, payments, and lesson history all in one secure place.</li>
                        <li><i data-feather="check-circle"></i> <strong>Personalized Support:</strong> We're here to help you succeed. Our support team is ready to assist you every step of the way.</li>
                    </ul>
                </div>
                <div class="why-us-image">
                    <img src="{{ asset('assets/img/about.webp') }}" alt="Student learning online" loading="lazy">
                    {{-- Tip: A great image here would be a smiling student on a laptop. --}}
                </div>
            </div>
        </div>
    </div>

    <div class="cta-section">
        <div class="container-tight">
            <h2>Ready to get started?</h2>
            <a href="{{ route('student.tutors.search') }}" class="btn-light-outline mt-4">
                Find Your Perfect Tutor
            </a>
        </div>
    </div>
    
@endsection

@push('scripts')
    {{-- This is needed for the check-circle icons in the "Why Us" section --}}
    <script>
        feather.replace();
    </script>
@endpush