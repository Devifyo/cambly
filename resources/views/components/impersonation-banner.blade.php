@if(is_impersonating())
    <div class="impersonation-banner">
        <div class="banner-content">
            <span>
                You are currently impersonating <strong>{{ Auth::user()->name }}</strong>
            </span>
            
            <span class="separator">|</span>
            
            <a href="{{ route('impersonate.leave') }}" class="leave-link">
                Return to Admin
            </a>
        </div>
    </div>

    <style>
        /* 1. The Red Bar */
        .impersonation-banner {
            background-color: #dc3545; /* Standard Danger Red */
            color: white;
            font-family: system-ui, -apple-system, sans-serif;
            font-size: 14px;
            
            /* Fix it to the top */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 99999;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        /* 2. Layout of Text */
        .banner-content {
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center;     /* Center vertically */
            padding: 10px 15px;
            flex-wrap: wrap;         /* Allow text to wrap on mobile */
            line-height: 1.4;
            text-align: center;
        }

        /* 3. The Link Style (Text only) */
        .leave-link {
            color: white;
            font-weight: bold;
            text-decoration: underline;
            margin-left: 8px;
            cursor: pointer;
        }
        .leave-link:hover {
            color: #f8f9fa; /* Slightly lighter on hover */
            text-decoration: none;
        }

        /* 4. The little separator line (|) */
        .separator {
            margin: 0 8px;
            opacity: 0.6;
        }

        /* 5. Responsive Spacing for the Body */
        /* Desktop: Banner is approx 40-45px tall */
        body { 
            margin-top: 45px; 
            transition: margin-top 0.2s; 
        }
        .sticky-top { top: 45px !important; }

        /* Mobile: Banner becomes taller (2 lines), push body down more */
        @media (max-width: 600px) {
            /* Hide the separator on mobile to save space */
            .separator { display: none; }
            
            /* Make the link display on its own line or wrap */
            .leave-link { 
                margin-left: 5px;
                display: inline-block; 
            }

            /* Increase body margin because banner is now taller */
            body { margin-top: 70px; }
            .sticky-top { top: 70px !important; }
        }
    </style>
@endif