@extends('layouts.student.app')
@section('title', 'Terms and Conditions')

@push('styles')
    <style>
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
        }
        .cms-card h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #111827;
            margin-top: 2rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #0E82FD;
            padding-bottom: 0.5rem;
        }
        .cms-card h3 {
            font-size: 1.15rem;
            font-weight: 600;
            color: #374151;
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }
        .cms-card p, .cms-card li {
            font-size: 1rem;
            line-height: 1.7;
            color: #4b5563;
            margin-bottom: 1rem;
        }
        .cms-card ul {
            padding-left: 20px;
            margin-bottom: 1.5rem;
        }
        .cms-card p:last-child {
            margin-bottom: 0;
        }
        .cms-card .last-updated {
            font-size: 0.9rem;
            color: #6b7280;
            font-style: italic;
            margin-bottom: 2rem;
        }
        
        /* Highlight box for important policies */
        .policy-alert {
            background-color: #fff4f4;
            border-left: 4px solid #dc2626;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
        }
        .policy-alert strong {
            color: #dc2626;
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
                            <li class="breadcrumb-item active">Terms & Conditions</li>
                        </ol>
                        <h2 class="breadcrumb-title">Terms and Conditions</h2>
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

            <p class="last-updated">Last Updated: {{ date('F j, Y') }}</p>

            <h2>1. Acceptance of Terms</h2>
            <p>
                Welcome to <strong>{{ config('app.name') }}</strong> ("we," "our," or "us"). By creating an account, purchasing a subscription, or using our services to book lessons with teachers ("Teachers"), you agree to be bound by these Terms and Conditions ("Terms").
            </p>

            <h2>2. The Service Overview</h2>
            <p>
                {{ config('app.name') }} is a platform that connects Students with independent Teachers for educational lessons. Students browse Tutor profiles, check schedules, and book sessions using our subscription-based ticketing system.
            </p>

            <h2>3. Account Registration</h2>
            <p>
                To access the booking system, you must register for an account. You agree to provide accurate information and maintain the security of your account credentials. You are responsible for all activities that occur under your account.
            </p>

            <h2>4. Subscription and Ticketing Policy</h2>
            <p>
                Our platform operates on a monthly subscription model that utilizes "Tickets" for lesson bookings. By subscribing, you agree to the following logic:
            </p>

            <h3>A. Active Subscription Requirement</h3>
            <p>
                You must hold a valid, active subscription to search for Teachers and book lessons. If your subscription expires or is cancelled, you will lose access to booking functionality until a new subscription is purchased.
            </p>

            <h3>B. Ticket Allocation & Usage</h3>
            <p>
                Upon purchasing or renewing a subscription, your account is credited with a specific number of Tickets based on your selected plan.
            </p>
            <ul>
                <li><strong>1 Ticket = 1 Lesson Booking.</strong></li>
                <li>Tickets are the sole currency used to reserve time slots with Teachers.</li>
            </ul>

            <h3>C. No Rollover Policy (Use It or Lose It)</h3>
            <div class="policy-alert">
                <strong>Important:</strong> Tickets are valid ONLY for the specific billing month in which they are issued.
            </div>
            <p>
                At the end of your monthly billing cycle, any unused tickets remaining in your account will <strong>expire immediately</strong>. Tickets <strong>do not</strong> carry forward to the next month. When your subscription renews, your ticket balance resets according to your plan's limit.
            </p>

            <h2>5. Booking and Cancellation Rules</h2>
            <p>
                We value the time of both our Students and Teachers. The following rules apply to all lesson bookings:
            </p>

            <h3>A. Booking a Lesson</h3>
            <p>
                When you book a lesson, one (1) Ticket is immediately deducted from your account balance.
            </p>

            <h3>B. Cancellation by Student (12-Hour Rule)</h3>
            <p>
                You may cancel a scheduled lesson via your dashboard. Refund eligibility depends on <em>when</em> you cancel:
            </p>
            <ul>
                <li><strong>More than 12 hours before start time:</strong> The Ticket used for the booking will be <strong>refunded</strong> to your account automatically.</li>
                <li><strong>Less than 12 hours before start time:</strong> The Ticket is <strong>forfeited</strong>. No refund will be issued, as this is considered a late cancellation.</li>
            </ul>

            <h3>C. Cancellation by Tutor</h3>
            <p>
                If a Tutor cancels a lesson at any time (even within 12 hours), your Ticket will be fully refunded to your account balance.
            </p>

            <h2>6. User Conduct</h2>
            <p>
                You agree to use the Service professionally and respectfully. You will not:
            </p>
            <ul>
                <li>Harass, threaten, or defame any Tutor or other user.</li>
                <li>Attempt to solicit Teachers to conduct lessons outside of the {{ config('app.name') }} platform.</li>
                <li>Share offensive or inappropriate content during lessons.</li>
            </ul>

            <h2>7. Limitation of Liability</h2>
            <p>
                {{ config('app.name') }} acts as a venue for connecting users. We are not responsible for the educational quality, outcomes, or the conduct of individual Teachers. To the fullest extent permitted by law, we shall not be liable for any indirect or consequential damages arising from your use of the service.
            </p>

            <h2>8. Changes to Terms</h2>
            <p>
                We reserve the right to modify these Terms at any time. Continued use of the platform after changes are posted constitutes your acceptance of the new Terms.
            </p>

            <h2>9. Contact Us</h2>
            <p>
                If you have questions regarding these Terms, specifically regarding subscriptions or the cancellation policy, please <a href="{{ route('cms.contact') }}" target="_blank" rel="noopener noreferrer">contact us</a>.
            </p>
        </div>
    </div>
@endsection