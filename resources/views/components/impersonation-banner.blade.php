@if(is_impersonating())
    <div class="impersonation-banner" style="background-color: #dc3545; color: white; text-align: center; padding: 10px; position: fixed; top: 0; width: 100%; z-index: 9999;">
        <strong>
            You are currently impersonating {{ Auth::user()->name }}.
        </strong>
        
        <a href="{{ route('impersonate.leave') }}" style="color: white; text-decoration: underline; margin-left: 10px; font-weight: bold;">
            Return to Admin Site
        </a>
    </div>

    {{-- Push content down so banner doesn't cover it --}}
    <style>
        body { margin-top: 45px; } 
        /* Optional: ensure navbar sticks below banner if you use sticky-top */
        .sticky-top { top: 45px !important; }
    </style>
@endif