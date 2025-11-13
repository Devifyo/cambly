@extends('layouts.student.app')
@section('title', 'Terms and Conditions')

@push('styles')
    <style>
        /* This CSS is identical to your privacy.blade.php for consistency */
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
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            border-bottom: 2px solid #0E82FD;
            padding-bottom: 0.5rem;
        }
        .cms-card h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #111827;
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }
        .cms-card p, .cms-card li {
            font-size: 1rem;
            line-height: 1.7;
            color: #374151;
            margin-bottom: 1.5rem;
        }
        .cms-card ul {
            padding-left: 20px;
        }
        .cms-card p:last-child {
            margin-bottom: 0;
        }
        .cms-card .last-updated {
            font-size: 0.9rem;
            color: #6b7280;
            font-style: italic;
        }
        .disclaimer-box {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
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

            {{-- <div class="disclaimer-box">
                <strong>Disclaimer:</strong> This is a template and not legal advice. You must consult with a legal professional to ensure this policy is complete and compliant with all relevant laws.
            </div> --}}

            <p class="last-updated">Last Updated: {{ date('F j, Y') }}</p>

            <h2>1. Acceptance of Terms</h2>
            <p>
                Welcome to <strong>{{ config('app.name') }}</strong> ("we," "our," or "us"). These Terms and Conditions ("Terms") govern your use of our website and our platform (the "Services") that connects Students with independent Tutors. By creating an account or using our Services, you agree to be bound by these Terms.
            </p>

            <h2>2. The Service</h2>
            <h3>A. Our Platform is a Venue</h3>
            <p>
                {{ config('app.name') }} provides a platform for Students to find and book 1-to-1 lessons with Tutors. The Tutors are independent contractors, not employees or agents of {{ config('app.name') }}. We are not responsible for the content of lessons or the teaching methods used by Tutors.
            </p>
            <h3>B. Student Responsibilities</h3>
            <p>
                You are responsible for selecting the right Tutor for your needs. While we may verify Tutors, we do not guarantee any specific learning outcomes from your lessons.
            </p>

            <h2>3. Account Registration</h2>
            <p>
                To use most features of the Service, you must register for an account. You agree to:
            </p>
            <ul>
                <li>Provide accurate, current, and complete information during the registration process (as seen in your "Account Settings" page).</li>
                <li>Maintain the security of your password. You are responsible for all activities that occur under your account.</li>
                <li>Be at least 13 years of age. If you are under 18, you must have your parent or legal guardian's permission to use the Service.</li>
            </ul>

            <h2>4. Bookings, Payments, and Cancellations</h2>
            <h3>A. Booking a Lesson</h3>
            <p>
                When you book a lesson, you agree to pay the fees listed by the Tutor. The booking is confirmed only upon successful payment.
            </p>
            <h3>B. Payments</h3>
            <p>
                All payments are processed through our third-party payment processor (e.g., Stripe, PayPal). We do not store your full credit card details. You agree to provide valid payment information and authorize us to charge your payment method for the lessons you book.
            </p>
            <h3>C. Cancellation and Refund Policy</h3>
            <p>
                <strong>[This is a critical section you must define]</strong>
                You must replace this with your official policy. For example:
            </p>
            <p>
                <em>"Cancellations by Student: You may cancel a lesson up to 24 hours before the scheduled start time for a full refund or credit. Cancellations made within 24 hours of the start time are non-refundable.
                Cancellations by Tutor: If a Tutor cancels a lesson at any time, you will receive a full refund or credit to your account."</em>
            </p>

            <h2>5. User Conduct</h2>
            <p>
                You agree not to use the Service in any way that is unlawful, harmful, or violates these Terms. You will not:
            </p>
            <ul>
                <li>Harass, threaten, or defame any Tutor or other user.</li>
                <li>Use the platform to solicit Tutors or Students to meet or transact off-platform.</li>
                <li>Share lesson materials, recordings, or other content in violation of copyright laws.</li>
                <li>Attempt to gain unauthorized access to our systems or another user's account.</li>
            </ul>

            <h2>6. Termination of Account</h2>
            <p>
                We may suspend or terminate your account at our discretion, without notice, if you breach these Terms or engage in conduct that we deem harmful to the platform or other users.
            </p>

            <h2>7. Disclaimers and Limitation of Liability</h2>
            <h3>A. Disclaimer of Warranties</h3>
            <p>
                The Service is provided "as is" and "as available" without any warranties of any kind. We do not guarantee that the platform will be error-free or uninterrupted, or that any Tutor will meet your expectations.
            </p>
            <h3>B. Limitation of Liability</h3>
            <p>
                To the fullest extent permitted by law, {{ config('app.name') }} shall not be liable for any indirect, incidental, special, or consequential damages (including lost profits or data) arising from your use of the Service, or for any disputes between you and a Tutor.
            </D>

            <h2>8. Changes to These Terms</h2>
            <p>
                We reserve the right to modify these Terms at any time. We will provide notice by updating the "Last Updated" date at the top of this page. Your continued use of the Service after any such change constitutes your acceptance of the new Terms.
            </p>

            <h2>9. Contact Us</h2>
            <p>
                If you have any questions about these Terms and Conditions, please <a href="{{ route('cms.contact') }}">contact us</a>.
            </p>
        </div>
    </div>
@endsection