@extends('layouts.student.app')
@section('title', 'How It Works')

@push('styles')
    <style>
        /* Re-use the same styles as your other CMS pages */
        .cms-container { 
            max-width: 1000px; 
            margin: 0 auto;
            margin-top: 2rem; 
            margin-bottom: 4rem;
        }
        .cms-card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 2.5rem;
            margin-bottom: 3rem;
        }
        .section-header {
            text-align: center;
            margin-bottom: 3rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        .section-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #111827;
            margin-bottom: 0.5rem;
        }
        .section-header p {
            font-size: 1.1rem;
            color: #4b5563;
        }
        
        /* 3-Column Grid for Steps */
        .how-it-works-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }
        .step-card {
            background: #f9fafb;
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
            font-size: 1.75rem; /* For isax icons */
        }
        .step-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 0.5rem;
        }
        .step-card p {
            font-size: 0.95rem;
            line-height: 1.6;
            color: #374151;
        }

        /* Call to Action Section */
        .cta-card {
            text-align: center;
            padding: 2.5rem;
        }
        .cta-card h2 {
            font-size: 1.75rem;
            font-weight: 600;
            color: #111827;
            margin-bottom: 1.5rem;
        }
        .btn-primary-gradient {
            background: linear-gradient(90deg, #0E82FD 0%, #06AED4 70%);
            color: #fff;
            border: none;
            padding: 0.8rem 2rem;
            font-size: 1.1rem;
            font-weight: 500;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            margin: 0.5rem;
        }
        .btn-primary-gradient:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(14, 130, 253, 0.2);
        }
        .btn-secondary-outline {
            background: #fff;
            color: #0E82FD;
            border: 2px solid #0E82FD;
            padding: 0.8rem 2rem;
            font-size: 1.1rem;
            font-weight: 500;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-block;
            margin: 0.5rem;
        }
        .btn-secondary-outline:hover {
            background: #f0f4ff;
            transform: translateY(-2px);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .how-it-works-grid {
                grid-template-columns: 1fr; /* Stack the grid on mobile */
            }
            .cms-card {
                padding: 1.5rem;
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
                            <li class="breadcrumb-item active">How It Works</li>
                        </ol>
                        <h2 class="breadcrumb-title">How It Works</h2>
                    </nav>
                </div>
            </div>
        </div>
        <div class="breadcrumb-bg">
            <img src="{{ asset('assets/img/bg/breadcrumb-bg-01.png') }}" alt="img" class="breadcrumb-bg-01">
            <img src="{{ asset('assets/img/bg/breadcrumb-bg-02.png') }}" alt="img" class="breadcrumb-bg-02">
        </div>
    </div>
    <div class="cms-container">

        <div class="cms-card">
            <div class="section-header">
                <h2>For Students</h2>
                <p>Your personalized learning journey in three simple steps.</p>
            </div>

            <div class="how-it-works-grid">
                <div class="step-card">
                    <div class="step-icon"><i class="isax isax-search-normal-1"></i></div>
                    <h3>1. Discover Tutors</h3>
                    <p>
                        Search our global network of expert tutors. Filter by subject, price, and—most importantly—your schedule.
                    </p>
                </div>
                <div class="step-card">
                    <div class="step-icon"><i class="isax isax-calendar-tick"></i></div>
                    <h3>2. Book Your Lesson</h3>
                    <p>
                        Find a time that works for you and book your 1-to-1 lesson instantly. We manage the scheduling so you don't have to.
                    </p>
                </div>
                <div class="step-card">
                    <div class="step-icon"><i class="isax isax-video-play"></i></div>
                    <h3>3. Connect & Learn</h3>
                    <p>
                        You'll get a confirmation and a private meeting link. We'll even send a reminder a few hours before it starts.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="cms-card">
            <div class="section-header">
                <h2>For Tutors</h2>
                <p>Share your expertise, set your own schedule, and start earning.</p>
            </div>

            <div class="how-it-works-grid">
                <div class="step-card">
                    <div class="step-icon"><i class="isax isax-profile-add"></i></div>
                    <h3>1. Register & Build Profile</h3>
                    <p>
                        Sign up and create your tutor profile. Showcase your experience, qualifications, and a video introduction.
                    </p>
                </div>
                <div class="step-card">
                    <div class="step-icon"><i class="isax isax-clock"></i></div>
                    <h3>2. Set Your Availability</h3>
                    <p>
                        You are in complete control. Add your available time slots to your calendar so students can book you instantly.
                    </sD>
                </div>
                <div class="step-card">
                    <div class="step-icon"><i class="isax isax-dollar-circle"></i></div>
                    <h3>3. Teach & Earn</h3>
                    <p>
                        When a student books your time, you'll be notified. Just show up, teach your lesson, and get paid.
                    </p>
                </div>
            </div>
        </div>
        @guest
        <div class="cms-card cta-card">
            <h2>Ready to Get Started?</h2>
            <div>
                <a href="{{ route('student.tutors.search') }}" class="btn-primary-gradient">
                    Find a Tutor
                </a>
                <a href="#" class="btn-secondary-outline"> {{-- Update this route later --}}
                    Become a Tutor
                </a>
            </div>
        </div>
        @endguest
    </div>
@endsection

@push('scripts')
    {{-- This is needed for the icons --}}
    <script>
        feather.replace();
    </script>
@endpush