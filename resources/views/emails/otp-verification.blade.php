<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - V&F Ice Plant</title>
    <style>
        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #1a202c;
            background-color: #f7fafc;
        }
        
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        /* Header */
        .email-header {
            background: linear-gradient(135deg, #1e3ba8 0%, #2f50c4 60%, #4169E1 100%);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        
        .logo-section {
            margin-bottom: 20px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }
        
        .company-tagline {
            font-size: 14px;
            opacity: 0.9;
            font-style: italic;
        }
        
        /* Content */
        .email-content {
            padding: 40px 30px;
            text-align: center;
        }
        
        .greeting {
            font-size: 24px;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 16px;
        }
        
        .message {
            font-size: 16px;
            color: #64748b;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .otp-section {
            background: #f8fafc;
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            padding: 30px;
            margin: 30px 0;
        }
        
        .otp-label {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }
        
        .otp-code {
            font-size: 36px;
            font-weight: bold;
            font-family: 'Courier New', monospace;
            color: #4169E1;
            letter-spacing: 8px;
            margin-bottom: 15px;
        }
        
        .otp-note {
            font-size: 14px;
            color: #e53e3e;
            font-weight: 500;
        }
        
        .instructions {
            background: #eef1fc;
            border-radius: 8px;
            padding: 20px;
            margin: 25px 0;
            text-align: left;
        }
        
        .instructions h4 {
            color: #1a202c;
            margin-bottom: 10px;
            font-size: 16px;
        }
        
        .instructions ul {
            list-style: none;
            padding-left: 0;
        }
        
        .instructions li {
            margin-bottom: 8px;
            padding-left: 20px;
            position: relative;
            color: #4a5568;
            font-size: 14px;
        }
        
        .instructions li:before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #4169E1;
            font-weight: bold;
        }
        
        /* Security warning */
        .security-warning {
            background: #fef5e7;
            border-left: 4px solid #f6ad55;
            padding: 15px 20px;
            margin: 25px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .security-warning p {
            margin: 0;
            font-size: 14px;
            color: #744210;
            line-height: 1.5;
        }
        
        /* Footer */
        .email-footer {
            background: #f7fafc;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        
        .footer-text {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 10px;
        }
        
        .footer-company {
            font-size: 12px;
            color: #a0aec0;
        }
        
        /* Responsive */
        @media (max-width: 600px) {
            .email-container {
                margin: 10px;
                border-radius: 12px;
            }
            
            .email-header, .email-content, .email-footer {
                padding: 25px 20px;
            }
            
            .otp-code {
                font-size: 28px;
                letter-spacing: 4px;
            }
            
            .company-name {
                font-size: 20px;
            }
            
            .greeting {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="logo-section">
                <div class="company-name">V&F Ice Plant and Cold Storage Inc.</div>
                <div class="company-tagline">Cool in Service, Warm at Heart</div>
            </div>
        </div>
        
        <!-- Content -->
        <div class="email-content">
            <h2 class="greeting">
                @if($purpose === 'email_verification')
                    Email Verification Required
                @elseif($purpose === 'password_reset')
                    Password Reset Request
                @else
                    Verification Code
                @endif
            </h2>
            
            <p class="message">
                @if($purpose === 'email_verification')
                    Welcome to V&F Ice Plant! To complete your registration and start ordering our ice and cold storage services, please verify your email address using the code below.
                @elseif($purpose === 'password_reset')
                    We received a request to reset your password. Use the verification code below to proceed with resetting your password.
                @else
                    Please use the verification code below to complete your request.
                @endif
            </p>
            
            <div class="otp-section">
                <div class="otp-label">Verification Code</div>
                <div class="otp-code">{{ $otpCode }}</div>
                <div class="otp-note">⏰ This code expires in 15 minutes</div>
            </div>
            
            <div class="instructions">
                <h4>Instructions:</h4>
                <ul>
                    <li>Enter this 6-digit code on the verification page</li>
                    <li>Do not share this code with anyone</li>
                    <li>If you didn't request this code, please ignore this email</li>
                    @if($purpose === 'email_verification')
                        <li>Complete your profile after verification to start shopping</li>
                    @endif
                </ul>
            </div>
            
            <div class="security-warning">
                <p><strong>Security Notice:</strong> This verification code is intended only for you. V&F Ice Plant staff will never ask for your verification code. If you did not request this verification, please contact our support team.</p>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="email-footer">
            <p class="footer-text">
                Need help? Contact our support team or visit our website.
            </p>
            <p class="footer-company">
                &copy; {{ date('Y') }} V&F Ice Plant and Cold Storage Inc.<br>
                Trust V&F – We Execute Beyond Standard
            </p>
        </div>
    </div>
</body>
</html>