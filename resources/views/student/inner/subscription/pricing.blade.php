@extends('layouts.student.app')

@section('title', 'Subscription')
@push('styles')
<style>
        .user-note {
            transition: all 0.3s ease;
            cursor: default;
        }

        .user-note:hover {
            background-color: var(--primary, #0E82FD) !important;
        }

        .user-note:hover .pricing-info,
        .user-note:hover .user-note-text {
            color: #fff !important;
        }
</style>

@endpush
@section('content')
<section class="pricing-section">
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="section-inner-header pricing-inner-header">
                    <h2>Our Pricing Plan</h2>

                    <div class="plan-choose-info">
                        <label class="monthly-plan">Monthly</label>
                    </div>

                    <p style="margin-top:12px;">
                        25-minute 1:1 English lessons via Discord.  
                        Start with a $1 trial — one credit, one booking.  
                        Prices shown exclude tax.
                    </p>
                </div>
            </div>
        </div>

        <!-- Plan cards -->
        <div class="row align-items-center justify-content-center" style="margin-top:18px;">
            <div class="col-lg-4 col-md-6">
                <div class="card pricing-card w-100">
                    <div class="card-body">
                        <div class="pricing-header">
                            <div class="pricing-header-info">
                                <div class="pricing-icon">
                                    <span>
                                        <img src="{{ asset('assets/img/icons/price-icon1.svg') }}" alt="icon">
                                    </span>
                                </div>
                                <div class="pricing-title">
                                    <p>For individuals</p>
                                    <h4>Basic</h4>
                                </div>
                            </div>
                        </div>
                        <div class="pricing-info">
                            <div class="pricing-amount">
                                <h2>¥4,500 <span>/monthly</span></h2>
                                <h6>What’s included</h6>
                            </div>
                            <div class="pricing-list">
                                <ul>
                                    <li>4 lessons per month (25 minutes each)</li>
                                    <li>Book directly from teacher calendar</li>
                                    <li>Email confirmations & reminders</li>
                                    <li>Discord call with your teacher</li>
                                </ul>
                            </div>
                            <div class="pricing-btn">
                                <a href="#" class="btn btn-primary">Choose Plan</a>
                            </div>
                            <div style="margin-top:8px; text-align:center;">
                                <a href="#" class="btn btn-rounded btn-outline-primary" >Start $1 trial</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card pricing-card active w-100">
                    <div class="card-body">
                        <div class="pricing-header">
                            <div class="pricing-header-info">
                                <div class="pricing-icon">
                                    <span>
                                        <img src="{{ asset('assets/img/icons/price-icon2.svg') }}" alt="icon">
                                    </span>
                                </div>
                                <div class="pricing-title">
                                    <p>For regular learners</p>
                                    <h4>Premium</h4>
                                </div>
                            </div>
                            <div>
                                <span class="badge">Popular</span>
                            </div>
                        </div>
                        <div class="pricing-info">
                            <div class="pricing-amount">
                                <h2>¥8,000 <span>/monthly</span></h2>
                                <h6>What’s included</h6>
                            </div>
                            <div class="pricing-list">
                                <ul>
                                    <li>8 lessons per month</li>
                                    <li>Priority booking</li>
                                    <li>Lesson history & reminders</li>
                                    <li>Priority support</li>
                                </ul>
                            </div>
                            <div class="pricing-btn">
                                <a href="#" class="btn btn-primary">Choose Plan</a>
                            </div>
                            <div style="margin-top:8px; text-align:center;">
                                <a href="#" class="btn btn-rounded btn-outline-primary" >Start $1 trial</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-6">
                <div class="card pricing-card w-100">
                    <div class="card-body">
                        <div class="pricing-header">
                            <div class="pricing-header-info">
                                <div class="pricing-icon">
                                    <span>
                                        <img src="{{ asset('assets/img/icons/price-icon3.svg') }}" alt="icon">
                                    </span>
                                </div>
                                <div class="pricing-title">
                                    <p>For intensive learners</p>
                                    <h4>Enterprise</h4>
                                </div>
                            </div>
                        </div>
                        <div class="pricing-info">
                            <div class="pricing-amount">
                                <h2>¥9,000 <span>/monthly</span></h2>
                                <h6>What’s included</h6>
                            </div>
                            <div class="pricing-list">
                                <ul>
                                    <li>12 lessons per month</li>
                                    <li>Highest booking limits</li>
                                    <li>Full lesson history</li>
                                    <li>Dedicated support</li>
                                </ul>
                            </div>
                            <div class="pricing-btn">
                                <a href="#" class="btn btn-primary">Choose Plan</a>
                            </div>
                            <div style="margin-top:8px; text-align:center;">
                                <a href="#" class="btn btn-rounded btn-outline-primary" >Start $1 trial</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Simple note for users -->
        <!-- Simple note for users -->
        <div class="row align-items-center justify-content-center" style="margin-top:18px;">
            <div class="col-lg-10">
                <div class="card pricing-card w-100 user-note">
                    <div class="card-body">
                        <div class="pricing-info">
                            <p class="user-note-text" style="margin-bottom:0;">
                                You can cancel lessons up to 12 hours before the start time for a full credit return.  
                                All calls happen on Discord — we’ll email your teacher’s Discord ID and meeting link before each session.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>
</section>
@endsection
