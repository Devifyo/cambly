@extends('layouts.student.app')
@section('title', 'Privacy Policy')

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
                            <li class="breadcrumb-item active">Privacy Policy</li>
                        </ol>
                        <h2 class="breadcrumb-title">Privacy Policy</h2>
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
                <strong>Disclaimer:</strong> This is a template and not legal advice. You must consult with a legal professional to ensure this policy is complete and compliant with all relevant laws for your jurisdiction.
            </div> --}}

            <p class="last-updated">Last Updated: {{ date('F j, Y') }}</p>

            <h2>1. Introduction</h2>
            <p>
                Welcome to <strong>{{ config('app.name') }}</strong> ("we," "our," or "us"). We are committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our platform, which connects Students with Tutors for 1-to-1 lessons (the "Services").
            </p>
            <p>
                By using our Services, you agree to the collection and use of information in accordance with this policy.
            </p>

            <h2>2. Information We Collect</h2>
            <p>We may collect information about you in several ways. The information we collect on the platform includes:</p>

            <h3>A. Information You Provide to Us</h3>
            <ul>
                <li><strong>Student Account Data:</strong> When you register as a student, we collect personal information, such as your name, email address, password, age, gender, native language, and English level.</li>
                <li><strong>Tutor Account Data:</strong> (If you expand to tutor signups) When a tutor registers, we may collect their name, email, qualifications, subjects, video introduction, and payment information.</li>
                <li><strong>Payment Data:</strong> To book lessons, we may collect data necessary to process your payments, such as your payment instrument number (e.g., a credit card number) and the security code associated with it. All payment data is stored by our payment processor (e.g., Stripe, PayPal).</li>
                <li><strong>Communications:</strong> When you contact us via our contact form, (e.g., Support Tickets), we collect your name, email, phone number, and the contents of your message.</li>
            </ul>

            <h3>B. Information Collected Automatically</h3>
            <ul>
                <li><strong>Lesson Data:</strong> We may (subject to your consent) record and store lesson videos and chat message logs. This is used for quality control, dispute resolution, and to help you review your lessons.</li>
                <li><strong>Log and Usage Data:</strong> Like most websites, we collect information your browser sends, such as your IP address, browser type, pages visited, and the time and date of your visit.</li>
                <li><strong>Cookies:</strong> We use cookies to track activity on our platform and hold certain information. You can instruct your browser to refuse all cookies, but some portions of our Service may not function.</li>
            </ul>

            <h2>3. How We Use Your Information</h2>
            <p>We use the information we collect for the following purposes:</p>
            <ul>
                <li>To provide, operate, and maintain our Services.</li>
                <li>To facilitate account creation and the login process.</li>
                <li>To connect Students with Tutors.</li>
                <li>To process payments and transactions.</li>
                <li>To send you transactional emails, such as lesson confirmations, reminders, and payment receipts.</li>
                <li>To send you administrative or security alerts.</li>
                <li>To respond to your support tickets and requests.</li>
                <li>For quality control, to review lessons to improve our platform and resolve disputes.</li>
                <li>To monitor and analyze usage and trends to improve your experience.</li>
            </ul>

            <h2>4. How We Share Your Information</h2>
            <p>We do not sell your personal information. We may share information in the following situations:</p>
            <ul>
                <li><strong>With Tutors and Students:</strong> To facilitate a lesson, we share your name with the Tutor you book, and your Tutor's profile name and details are shared with you.</li>
                <li><strong>With Payment Processors:</strong> We share payment data with our payment processors (e.g., Stripe) to handle transactions. We do not store your full credit card information on our servers.</li>
                <li><strong>With Service Providers:</strong> We may share your data with third-party vendors who perform services for us, such as email delivery, web hosting, and customer service.</li>
                <li><strong>For Legal Reasons:</strong> We may disclose your information if required to do so by law or in response to valid requests by public authorities (e.g., a court or government agency).</li>
            </ul>

            <h2>5. Data Security</h2>
            <p>
                We use administrative, technical, and physical security measures to help protect your personal information. While we have taken reasonable steps to secure the personal information you provide to us, please be aware that no security system is impenetrable.
            </p>

            <h2>6. Your Data Rights</h2>
            <p>
                Depending on your location, you may have the following rights regarding your personal data:
            </p>
            <ul>
                <li><strong>The right to access:</strong> You have the right to request copies of your personal data.</li>
                <li><strong>The right to correction:</strong> You have the right to request that we correct any information you believe is inaccurate. You can update most of this information directly in your "Account Settings" page.</li>
                <li><strong>The right to deletion:</strong> You have the right to request that we delete your personal data, under certain conditions.</li>
            </ul>

            <h2>7. Children's Privacy</h2>
            <p>
                Our service is not intended for use by children under the age of 13. We do not knowingly collect personally identifiable information from children under 13. If we become aware that we have collected such data, we will take steps to delete it.
            </p>

            <h2>8. Changes to This Policy</h2>
            <p>
                We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new policy on this page and updating the "Last Updated" date.
            </p>

            <h2>9. Contact Us</h2>
            <p>
                If you have any questions about this Privacy Policy, please <a href="{{ route('cms.contact') }}">contact us</a>.
            </p>
        </div>
    </div>
@endsection