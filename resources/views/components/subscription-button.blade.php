@props(['plan', 'activeSubscription'])

@php
    $isActive = $activeSubscription?->plan_id == $plan->id && !$activeSubscription->ends_at;
    $label = $isActive && !$activeSubscription->ends_at ? 'Subscribed' : 'Choose Plan';
    $href = $isActive && !$activeSubscription->ends_at ? null : route('student.subscription.checkout', $plan->slug);
@endphp

<a 
    @if($href) href="{{ $href }}" @else disabled @endif
    class="btn btn-primary {{ $isActive ? 'disabled' : '' }}"
>
    {{ $label }}
</a>
