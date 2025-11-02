<?php

namespace App\View\Components;

use Illuminate\View\Component;

class SubscriptionButton extends Component
{
    public $plan;
    public $activeSubscription;

    /**
     * Create a new component instance.
     */
    public function __construct($plan, $activeSubscription = null)
    {
        $this->plan = $plan;
        $this->activeSubscription = $activeSubscription;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('components.subscription-button');
    }

    /**
     * Determine if the plan is currently active.
     */
    public function isActive(): bool
    {
        return $this->activeSubscription?->plan_id === $this->plan->id;
    }

    /**
     * Get the button text.
     */
    public function label(): string
    {
        return $this->isActive() ? 'Subscribed' : 'Choose Plan';
    }

    /**
     * Get the button href.
     */
    public function link(): ?string
    {
        return $this->isActive()
            ? null
            : route('student.subscription.checkout', $this->plan->slug);
    }
}
