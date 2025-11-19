@extends('layouts.student.app')

@section('title', 'Subscription')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pricing.css') }}">
@endpush

@section('content')
<section class="pricing-section">
    <div class="container">
        
        {{-- ===========================
             CURRENT SUBSCRIPTION CARD
        ============================ --}}
@if(isset($activeSubscription) && $activeSubscription)
    <div class="row justify-content-center mb-4">
        <div class="col-lg-8">
            <div class="card current-subscription-card">
                {{-- Gradient Header --}}
                <div class="card-header text-white py-3 px-4 d-flex justify-content-between align-items-center"
                     style="background: linear-gradient(90deg, #0E82FD 0%, #06AED4 70%);
                            border-top-left-radius: .5rem; border-top-right-radius: .5rem;">
                    <h5 class="mb-0 fw-bold">
                        <i class="fa-solid fa-crown me-2"></i>
                        Current Plan: {{ $activeSubscription->plan->name ?? 'N/A' }}
                    </h5>

                    {{-- Status Badge --}}
                    @php $status = $activeSubscription->status ?? 'unknown'; @endphp
                    @if($status === 'active' && !$activeSubscription->ends_at)
                        <span class="badge bg-light text-success">Active</span>
                    @elseif($status === 'past_due')
                        <span class="badge bg-warning text-dark">Past Due</span>
                    @elseif($activeSubscription->ends_at || $status === 'cancelled')
                        <span class="badge bg-danger">Cancelled</span>
                    @elseif($activeSubscription->cancel_at_period_end)
                        <span class="badge bg-warning text-dark">Cancels at Period End</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </div>

                <div class="card-body p-4">
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Start Date</p>
                            <p class="fw-semibold">{{ $activeSubscription->current_period_start ? formatDate($activeSubscription->current_period_start) : '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Renewal Date</p>
                            <p class="fw-semibold">{{ $activeSubscription->current_period_end ? formatDate($activeSubscription->current_period_end) : '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Plan Price</p>
                            <p class="fw-semibold">
                                {{ format_currency($activeSubscription->plan->price ?? 0) }} / {{ $activeSubscription->plan->interval ?? 'month' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Next Renewal Charge</p>
                            <p class="fw-semibold">{{ format_currency($activeSubscription->plan->price ?? 0) }}</p>
                        </div>
                    </div>

                    {{-- Cancellation / Reactivation Controls --}}
                    <div class="mt-4 text-end">
                        @if(in_array($status, ['active', 'past_due']) && !($activeSubscription->ends_at))
                            <form action="{{ route('student.subscription.cancel') }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to cancel your subscription?');">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger rounded-pill px-4">
                                    <i class="fa-solid fa-ban me-2"></i> Cancel Subscription
                                </button>
                            </form>
                        @elseif($activeSubscription->cancel_at_period_end)
                            <div class="alert alert-warning mt-3 mb-0 small">
                                <i class="fa-solid fa-clock me-1"></i>
                                Your subscription will remain active until 
                                <strong>{{ formatDate($activeSubscription->current_period_end) }}</strong>.
                            </div>
                        @elseif(in_array($status, ['canceled', 'cancelled']) || $activeSubscription->ends_at)
                            <div class="alert alert-secondary mt-3 mb-0 small d-flex align-items-center">
                                <i class="fa-solid fa-circle-check me-2 text-success"></i>
                                <span>Your subscription ended on 
                                    <strong>{{ formatDate($activeSubscription->ends_at ?? $activeSubscription->current_period_end) }}</strong>.
                                </span>
                            </div>
                            {{-- Disabled cancel button --}}
                            <button class="btn btn-outline-secondary rounded-pill px-4 mt-3" disabled>
                                <i class="fa-solid fa-ban me-2"></i> Subscription Ended
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif




        {{-- ===========================
             HEADER
        ============================ --}}
        <div class="row">
            <div class="col-md-12 text-center">
                <div class="section-inner-header pricing-inner-header">
                            <h2>Choose Your Learning Plan</h2>
        <p>Flexible pricing designed to help you achieve your language goals</p>

                    {{-- <div class="plan-choose-info">
                        <label class="monthly-plan">Monthly</label>
                    </div> --}}

                    {{-- trial description --}}
                    @if(!$user?->hasEverSubscribed())
                        <p style="margin-top:12px;">
                            {{ $trialPlan->description }}
                        </p>
                    @endif
                    {{-- end of trial description --}}
                </div>
            </div>
        </div>

        {{-- ===========================
             PLAN CARDS
        ============================ --}}
        <div class="row align-items-center justify-content-center" style="margin-top:18px;">
            @foreach ($monthlyPlans as $plan)
                <div class="col-lg-4 col-md-6">
                    <div class="card pricing-card {{ $plan->is_popular ? 'active' : '' }} w-100">
                        <div class="card-body">
                            <div class="pricing-header">
                                <div class="pricing-header-info">
                                    <div class="pricing-icon">
                                        <span>
                                            <img src="{{ $plan->icon_link }}" alt="icon">
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
                                    <h2>{{ format_currency($plan->price) }} <span>/{{ $plan->interval }}</span></h2>
                                    <h6>What’s included</h6>
                                </div>
                                <div class="pricing-list">
                                    <ul>
                                        @foreach($plan->features as $feature)
                                            <li>{{ $feature }}</li>
                                        @endforeach 
                                    </ul>
                                </div>
                                <div class="pricing-btn">
                                    <x-subscription-button :plan="$plan" :active-subscription="$activeSubscription" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ===========================
             TRIAL CTA
        ============================ --}}
        @if(!$user?->hasEverSubscribed())
            <div class="row trial-cta-row align-items-center justify-content-center">
                <div class="col-lg-6 text-center">
                    <a href="{{ route('student.subscription.checkout', $trialPlan->slug) }}" class="trial-link">
                        <strong>{{ $trialPlan->name }}</strong> - {{ $trialPlan->subtitle }}
                    </a>
                </div>
            </div>
        @endif

        {{-- ===========================
             NOTES
        ============================ --}}
        <div class="row align-items-center justify-content-center" style="margin-top:18px;">
            <div class="col-lg-10">
                <div class="card pricing-card w-100 user-note">
                    <div class="card-body">
                        <div class="pricing-info">
                            <p class="user-note-text mb-0">
                                You can cancel lessons up to 12 hours before the start time for a full {{ trans_choice('app.credits_lower',2) }} return.  
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
