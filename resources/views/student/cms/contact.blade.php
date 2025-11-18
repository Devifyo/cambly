@extends('layouts.student.app')
@section('title', 'Contact Us')

@push('styles')
    {{-- This page uses your theme's built-in styles, but we add validation styles --}}
    <style>
        .form-group .error {
            color: #dc2626;
            font-size: 0.875rem;
            font-weight: 500;
            margin-top: 0.25rem;
        }
        .form-control.error {
            border-color: #dc2626 !important;
        }
        .form-control.error:focus {
            box-shadow: 0 0 0 0.25rem rgba(220, 38, 38, 0.12);
        }

        /* FAQ card tweaks */
        .faq-card .card-body { padding: 0; }
        .faq-list .faq-item + .faq-item { border-top: 1px solid rgba(0,0,0,0.06); }
        .faq-question {
            width: 100%;
            text-align: left;
            padding: 1rem;
            font-weight: 600;
            background: transparent;
            border: none;
        }
        .faq-answer {
            padding: 0 1rem 1rem 1rem;
            color: #555;
            font-size: 0.95rem;
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
                            <li class="breadcrumb-item active">Contact Us</li>
                        </ol>
                        <h2 class="breadcrumb-title">Contact Us</h2>
                    </nav>
                </div>
            </div>
        </div>
        <div class="breadcrumb-bg">
            <img src="{{ asset('assets/img/bg/breadcrumb-bg-01.png') }}" alt="img" class="breadcrumb-bg-01">
            <img src="{{ asset('assets/img/bg/breadcrumb-bg-02.png') }}" alt="img" class="breadcrumb-bg-02">
        </div>
    </div>
    {{-- This uses your theme's HTML structure --}}
    <section class="contact-section">
        <div class="container">

            {{-- Display Success Message --}}
            <x-alert />

            <div class="row">
                {{-- LEFT COLUMN: FAQs + Email card --}}
                <div class="col-lg-5 col-md-12">
                    {{-- FAQ Header (subtle) --}}
                    <div class="section-inner-header contact-inner-header mb-3">
                        <h6>Quick Help</h6>
                        <h2 class="h4">Frequently Asked Questions</h2>
                    </div>

                    {{-- FAQ Card (accordion-like) --}}
                    <div class="card faq-card mb-3">
                        <div class="card-body">
                            <div class="faq-list" id="faqList" role="tablist" aria-multiselectable="true">
                                <div class="faq-item">
                                    <button class="faq-question" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" aria-expanded="true" aria-controls="faq1">
                                        How long does it take to get a reply?
                                    </button>
                                    <div id="faq1" class="collapse show" data-bs-parent="#faqList">
                                        <div class="faq-answer">
                                            We usually respond within 24–48 hours on business days. If your request is urgent, please mark it clearly in the subject.
                                        </div>
                                    </div>
                                </div>

                                <div class="faq-item">
                                    <button class="faq-question collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" aria-expanded="false" aria-controls="faq2">
                                        Can I cancel the lesson?
                                    </button>
                                    <div id="faq2" class="collapse" data-bs-parent="#faqList">
                                        <div class="faq-answer">
                                            Yes, you can cancel your booking anytime. However, if the cancellation is made less than 12 hours before the lesson start time, the ticket will not be refunded.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="faq-item">
                                    <button class="faq-question collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" aria-expanded="false" aria-controls="faq3">
                                        I didn't receive a confirmation email. What now?
                                    </button>
                                    <div id="faq3" class="collapse" data-bs-parent="#faqList">
                                        <div class="faq-answer">
                                            First check your spam/promotions folder. If it's not there, send us a message with the approximate time and email used — we'll track it down.
                                        </div>
                                    </div>
                                </div>

                                {{-- Add more FAQ items here if needed --}}
                            </div>
                        </div>
                    </div>

                    {{-- Email Address card (kept) --}}
                    <div class="card contact-card">
                        <div class="card-body">
                            <div class="contact-icon">
                                <i class="isax isax-sms5"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Email Address</h4>
                                <p>{{ config('app.admin_email') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: Contact form --}}
                <div class="col-lg-7 col-md-12 d-flex">
                    <div class="card contact-form-card w-100" id="contact-form-card">
                        <div class="card-body">
                            <form action="{{ route('cms.contact.store') }}" method="POST">
                                @csrf
                                <div class="row">

                                {{-- --- THIS IS THE UPDATED LOGIC --- --}}
                                @guest
                                    {{-- User is a GUEST: Show name and email fields --}}
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Name</label>
                                            <input type="text" name="name" class="form-control"
                                                   value="{{ old('name') }}" required>
                                            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control"
                                                   value="{{ old('email') }}" required>
                                            @error('email') <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                @endguest

                                @auth
                                    {{-- User is LOGGED IN: Show their name/email as text --}}
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Name</label>
                                            <p class="form-control-plaintext"><strong>{{ auth()->user()->name }}</strong></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Email</label>
                                            <p class="form-control-plaintext"><strong>{{ auth()->user()->email }}</strong></p>
                                        </div>
                                    </div>
                                @endauth
                                {{-- --- END OF UPDATED LOGIC --- --}}

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Phone Number</label>
                                            <input type="text" name="phone_number" class="form-control"
                                                   value="{{ old('phone_number', auth()->user()?->phone) }}" required>
                                            @error('phone_number') <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Subject</label>
                                            <input type="text" name="subject" class="form-control"
                                                   value="{{ old('subject') }}" placeholder="e.g., General Inquiry, Booking Help">
                                            @error('subject') <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group mb-3">
                                            <label class="form-label">Message</label>
                                            <textarea name="message" class="form-control" rows="6" required>{{ old('message') }}</textarea>
                                            @error('message') <div class="text-danger">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group-btn mb-0">
                                            <button type="submit" class="btn btn-primary-gradient">Send Message</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    {{-- This page requires jQuery Validation for the form --}}
    <script>
        // Ensure Bootstrap's collapse works (Bootstrap 5 assumed).
        // Initialize jQuery Validate on the contact form
        $(function() {
            $("#contact-form-card form").validate({
                rules: {
                    name: { required: true },
                    email: { required: true, email: true },
                    phone_number: { required: true, digits: true },
                    subject: { required: true },
                    message: { required: true, minlength: 10 }
                },
                messages: {
                    phone_number: "Please enter a valid phone number",
                    message: "Please enter at least 10 characters"
                }
            });
        });
    </script>
@endpush
