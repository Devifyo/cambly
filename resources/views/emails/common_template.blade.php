<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? $appName }}</title>
    <style>
        /* Base Styles */
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            background-color: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            line-height: 1.6;
        }
        
        /* Wrapper */
        .wrapper {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        }

        /* --- Header --- */
        .header {
            background: linear-gradient(90.08deg, #0E82FD 0.09%, #06AED4 70.28%);
            padding: 50px 40px;
            text-align: center;
            position: relative;
        }
        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 32px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .header:after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
        }

        /* --- Content --- */
        .content {
            padding: 50px 40px;
            color: #4a5568;
        }
        .content p {
            font-size: 16px;
            line-height: 1.7;
            margin-bottom: 24px;
        }
        .content p:last-of-type {
            margin-bottom: 0;
        }
        .content ul, .content ol {
            padding-left: 24px;
            margin-bottom: 24px;
        }
        .content li {
            margin-bottom: 12px;
            padding-left: 8px;
        }
        .content strong {
            color: #1a202c;
            font-weight: 600;
        }
        .content h2 {
            color: #1a202c;
            font-size: 24px;
            font-weight: 600;
            margin-top: 32px;
            margin-bottom: 16px;
        }
        .content h3 {
            color: #1a202c;
            font-size: 20px;
            font-weight: 600;
            margin-top: 28px;
            margin-bottom: 14px;
        }

        /* --- The Button --- */
        .button-primary {
            display: inline-block;
            background: linear-gradient(90.08deg, #0E82FD 0.09%, #06AED4 70.28%);
            color: #ffffff !important;
            text-decoration: none !important;
            padding: 16px 32px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 16px;
            text-align: center;
            margin: 24px 0;
            box-shadow: 0 4px 12px rgba(14, 130, 253, 0.3);
            transition: all 0.3s ease;
        }
        .button-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(14, 130, 253, 0.4);
        }

        /* --- Divider --- */
        .divider {
            height: 1px;
            background: #e2e8f0;
            margin: 32px 0;
        }

        /* --- Footer --- */
        .footer {
            background-color: #f7fafc;
            padding: 40px 30px;
            text-align: center;
            font-size: 14px;
            color: #718096;
            border-top: 1px solid #e2e8f0;
        }
        .footer p {
            margin: 0 0 16px 0;
        }
        .footer a {
            color: #0E82FD;
            text-decoration: none;
            transition: color 0.2s ease;
        }
        .footer a:hover {
            color: #06AED4;
            text-decoration: underline;
        }
        .social-links {
            margin-top: 20px;
        }
        .social-links a {
            display: inline-block;
            margin: 0 8px;
            color: #718096;
            font-size: 14px;
        }

        /* Responsive Adjustments */
        @media (max-width: 640px) {
            .wrapper {
                margin: 10px auto;
                border-radius: 0;
            }
            .header {
                padding: 40px 20px;
            }
            .header h1 {
                font-size: 28px;
            }
            .content {
                padding: 40px 20px;
            }
            .footer {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>{{ $subject ?? $appName }}</h1>
        </div>

        <div class="content">
            {{-- This content is passed from your controller and seeder --}}
            {!! $content !!} 
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
            <p>
                <a href="{{ route('cms.privacy') }}">Privacy Policy</a> &nbsp;|&nbsp; 
                <a href="{{ route('cms.terms') }}">Terms of Service</a> &nbsp;|&nbsp; 
                <a href="{{ route('cms.contact') }}">Contact Us</a>
            </p>
            {{-- <div class="social-links">
                <a href="#">Facebook</a> &nbsp;|&nbsp;
                <a href="#">Twitter</a> &nbsp;|&nbsp;
                <a href="#">Instagram</a>
            </div> --}}
        </div>
    </div>
</body>
</html>