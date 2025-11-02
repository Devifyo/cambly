@props(['plan', 'activeSubscription'])

@php
    $isActive = $activeSubscription?->plan_id == $plan->id;
    $label = $isActive ? 'Subscribed' : 'Choose Plan';
    $href = $isActive ? null : route('student.subscription.checkout', $plan->slug);
@endphp

<a 
    @if($href) href="{{ $href }}" @else disabled @endif
    class="btn btn-primary {{ $isActive ? 'disabled' : '' }}"
>
    {{ $label }}
</a>
