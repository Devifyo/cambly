@auth
    {{-- Only show this to Students who do NOT have an active subscription --}}
    {{-- @dd( auth()->user()->subscriptions ); --}}
    @if(auth()->user()->isStudent() && !auth()->user()->hasActiveSubscription())
        
        @php
            $user = auth()->user();
            $subscription = $user?->subscriptions->first();

                // 2. Check logic: 
                // - Must HAVE a subscription record ($subscription is not null)
                // - AND (ends_at is in the past OR current_period_end is in the past)
             
            $isExpired = $subscription && (
                optional($subscription->current_period_end)->isPast() || 
                optional($subscription->ends_at)->isPast()
            );
            $isCancelled = $subscription && $subscription->status === 'cancelled';
            if ($isExpired) {
                $type = 'danger';
                $title = 'Subscription Ended!';
                $message = 'Your subscription has ended. Please renew your plan to continue booking lessons.';
                $btnClass = 'btn-outline-danger';
            }elseif($isCancelled){
                $type = 'danger';
                $title = 'Subscription Cancelled!';
                $message = 'Your subscription has been cancelled. Please renew your plan to continue booking lessons.';
                $btnClass = 'btn-outline-danger';
            }
             else {
                $type = 'warning';
                $title = 'Start Learning!';
                $message = 'You are not subscribed yet. Please subscribe to a plan to start booking lessons.';
                $btnClass = 'btn-outline-warning'; // Darker yellow usually checks contrast better
                if($type == 'warning') $btnClass = 'btn-warning'; // Bootstrap warning outline is sometimes hard to read
            }

            $alertClass = match($type) {
                'danger' => 'alert-danger',
                'warning' => 'alert-warning',
                default => 'alert-info',
            };
        @endphp

        <div class="alert {{ $alertClass }} d-flex align-items-center justify-content-between fade show mb-0" role="alert" style="border-radius: 0;">
            <div class="d-flex align-items-center gap-2">
                {{-- Optional Icons --}}
                @if($type == 'danger')
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-octagon-fill flex-shrink-0" viewBox="0 0 16 16">
                        <path d="M11.46.146A.5.5 0 0 0 11.107 0H4.893a.5.5 0 0 0-.353.146L.146 4.54A.5.5 0 0 0 0 4.893v6.214a.5.5 0 0 0 .146.353l4.394 4.394a.5.5 0 0 0 .353.146h6.214a.5.5 0 0 0 .353-.146l4.394-4.394a.5.5 0 0 0 .146-.353V4.893a.5.5 0 0 0-.146-.353L11.46.146zM8 4c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                @endif

                <div>
                    <strong>{{ $title }}</strong> {{ $message }}
                </div>
            </div>

            {{-- Action Button --}}
            <a href="{{ route('student.account.subscription') }}" class="btn {{ $btnClass }} btn-sm text-nowrap ms-3">
                @if($type == 'danger') Renew Now @else View Plans @endif
            </a>
        </div>
    @endif
@endauth