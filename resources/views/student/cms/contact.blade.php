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
    {{-- This uses your new theme's HTML structure --}}
    <section class="contact-section">
        <div class="container">

            {{-- Display Success Message --}}
            <x-alert />

            <div class="row">
                <div class="col-lg-5 col-md-12">
                    <div class="section-inner-header contact-inner-header">
                        <h6>Get in touch</h6>
                        <h2>Have Any Question?</h2>
                    </div>
                    <div class="card contact-card">
                        <div class="card-body">
                            <div class="contact-icon">
                                <i class="isax isax-location5"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Address</h4>
                                <p>8432 Mante Highway, Aminaport, USA</p>
                            </div>
                        </div>
                    </div>
                    <div class="card contact-card">
                        <div class="card-body">
                            <div class="contact-icon">
                                <i class="isax isax-call5"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Phone Number</h4>
                                <p>+1 315 369 5943</p>
                            </div>
                        </div>
                    </div>
                    <div class="card contact-card">
                        <div class="card-body">
                            <div class="contact-icon">
                                <i class="isax isax-sms5"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Email Address</h4>
                                <p>support@yourdomain.com</p> {{-- Changed example.com --}}
                            </div>
                        </div>
                    </div>
                </div>
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
        // Set global defaults for jQuery Validation

        // Initialize validation on the contact form
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