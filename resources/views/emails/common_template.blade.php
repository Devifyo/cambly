<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject ?? $appName }}</title>
    <style>
        /* Media Queries for Mobile Responsiveness 
           (These cannot be inlined, but are supported by most modern mobile clients)
        */
        @media only screen and (max-width: 640px) {
            .wrapper {
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                border-radius: 0 !important;
                border: none !important;
            }
            .header {
                padding: 30px 20px !important;
            }
            .header h1 {
                font-size: 24px !important;
            }
            .content {
                padding: 30px 20px !important;
            }
            .footer {
                padding: 30px 20px !important;
            }
        }

        /* Defaults for dynamic content injected via {!! $content !!}
           These help format the P tags coming from your database.
        */
        .content p { margin: 0 0 24px 0; }
        .content p:last-child { margin-bottom: 0; }
        .content ul, .content ol { margin-bottom: 24px; }
        .content h2, .content h3 { color: #1a202c; margin-top: 30px; margin-bottom: 15px; }
        img { max-width: 100%; height: auto; }
    </style>
</head>
<body style="margin: 0; padding: 0; width: 100% !important; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; line-height: 1.6; color: #4a5568;">
    
    <!-- Outer Background Table (Best practice for centering in email) -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color: #f8fafc; width: 100%;">
        <tr>
            <td align="center" style="padding: 20px 0;">
                
                <!-- Main Wrapper -->
                <div class="wrapper" style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05); text-align: left;">
                    
                    <!-- Header -->
                    <!-- Fallback color #0E82FD added for clients that strip gradients -->
                    <div class="header" style="background-color: #0E82FD; background: linear-gradient(90.08deg, #0E82FD 0.09%, #06AED4 70.28%); padding: 50px 40px; text-align: center; position: relative;">
                        <h1 style="margin: 0; color: #ffffff; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 32px; font-weight: 700; letter-spacing: -0.5px;">
                            {{ $subject ?? $appName }}
                        </h1>
                        <!-- Simulated Bottom Border (since :after doesn't work inline) -->
                        <div style="position: absolute; bottom: 0; left: 0; right: 0; height: 4px; background: rgba(255, 255, 255, 0.3); width: 100%;"></div>
                    </div>

                    <!-- Content -->
                    <div class="content" style="padding: 50px 40px; color: #4a5568; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; font-size: 16px; line-height: 1.7;">
                        {!! $content !!} 
                    </div>

                    <!-- Divider -->
                    <div style="height: 1px; background-color: #e2e8f0; margin: 0;"></div>

                    <!-- Footer -->
                    <div class="footer" style="background-color: #f7fafc; padding: 40px 30px; text-align: center; font-size: 14px; color: #718096;">
                        <p style="margin: 0 0 16px 0; color: #718096;">
                            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
                        </p>
                        <p style="margin: 0;">
                            <a href="{{ route('cms.privacy') }}" style="color: #0E82FD; text-decoration: none;">Privacy Policy</a> 
                            <span style="color: #cbd5e0; margin: 0 8px;">|</span> 
                            <a href="{{ route('cms.terms') }}" style="color: #0E82FD; text-decoration: none;">Terms of Service</a> 
                            <span style="color: #cbd5e0; margin: 0 8px;">|</span> 
                            <a href="{{ route('cms.contact') }}" style="color: #0E82FD; text-decoration: none;">Contact Us</a>
                        </p>
                    </div>

                </div>

            </td>
        </tr>
    </table>

</body>
</html>