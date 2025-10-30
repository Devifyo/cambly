@extends('layouts.student.app')

@section('title', 'Subscription')
@push('styles')
    <link rel="stylesheet" href="{{asset('assets/css/pricing.css')}}">
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
                      {{ $trialPlan->description }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Plan cards -->
        <div class="row align-items-center justify-content-center" style="margin-top:18px;">
            @foreach ($monthlyPlans as $plan )
                <div class="col-lg-4 col-md-6">
                    <div class="card pricing-card {{ $plan->is_popular  ? 'active' : ''}} w-100">
                        <div class="card-body">
                            <div class="pricing-header">
                                <div class="pricing-header-info">
                                    <div class="pricing-icon">
                                        <span>
                                            <img src="{{ asset($plan->icon_path) }}" alt="icon">
                                        </span>
                                    </div>
                                    <div class="pricing-title">
                                        <p>{{ $plan->subtitle }}</p>
                                        <h4>{{ $plan->name }}</h4>
                                    </div>
                                </div>
                                @if($plan->is_popular)
                                    <div>
                                        <span class="badge">Popular</span>
                                    </div>
                                @endif
                            </div>
                            <div class="pricing-info">
                                <div class="pricing-amount">
                                    <h2>{{ format_currency($plan->price) }} <span>/{{$plan->interval}}</span></h2>
                                    <h6>What’s included</h6>
                                </div>
                                <div class="pricing-list">
                                    <ul>
                                        @foreach($plan->features as $feature)
                                        <li>{{$feature}}</li>
                                        @endforeach 
                                    </ul>
                                </div>
                                <div class="pricing-btn">
                                    <a href="{{ route('student.subscription.checkout', $plan->slug) }}" class="btn btn-primary">Choose Plan</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Single, centered trial CTA under the cards -->
        <div class="row trial-cta-row align-items-center justify-content-center">
            <div class="col-lg-6 text-center">
                <a href="{{ route('student.subscription.checkout', $trialPlan->slug) }}" class="trial-link"> <strong>{{ $trialPlan->name }}</strong> - {{ $trialPlan->subtitle }}</a>
            </div>
        </div>

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
@push('scripts')
    
@endpush
