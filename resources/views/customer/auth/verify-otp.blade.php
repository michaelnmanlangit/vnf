<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Verify OTP - V&F Ice Plant</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .otp-container {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .logo-section img {
            width: 80px;
            height: 80px;
            margin-bottom: 1rem;
        }

        .logo-section h1 {
            color: #2c3e50;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .logo-section p {
            color: #7f8c8d;
            font-size: 0.95rem;
        }

        .otp-info {
            background: #e8f4fd;
            border-left: 4px solid #3498db;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .otp-info p {
            color: #2c3e50;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        .otp-info strong {
            color: #3498db;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .otp-input-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin: 1.5rem 0;
        }

        .otp-input {
            width: 50px;
            height: 60px;
            font-size: 1.5rem;
            text-align: center;
            border: 2px solid #ddd;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 600;
            color: #2c3e50;
        }

        .otp-input:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .btn {
            width: 100%;
            padding: 1rem;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .resend-section {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #ecf0f1;
        }

        .resend-section p {
            color: #7f8c8d;
            font-size: 0.9rem;
            margin-bottom: 0.75rem;
        }

        .btn-resend {
            background: none;
            border: 2px solid #3498db;
            color: #3498db;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        .btn-resend:hover {
            background: #3498db;
            color: white;
        }

        .btn-resend:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: none;
        }

        .alert.show {
            display: block;
        }

        .alert-success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }

        .alert-error {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }

        .spinner {
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 0.8s linear infinite;
            display: inline-block;
            margin-left: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 480px) {
            .otp-container {
                padding: 2rem 1.5rem;
            }

            .otp-input {
                width: 45px;
                height: 55px;
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="otp-container">
        <div class="logo-section">
            <img src="{{ asset('logo.png') }}" alt="V&F Logo">
            <h1>Verify Your Email</h1>
            <p>Enter the 6-digit code sent to your email</p>
        </div>

        <div class="otp-info">
            <p>We've sent a verification code to <strong>{{ $email }}</strong></p>
        </div>

        <div class="alert alert-success" id="successAlert"></div>
        <div class="alert alert-error" id="errorAlert"></div>

        <form id="otpForm">
            @csrf
            <div class="form-group">
                <label>Enter OTP Code</label>
                <div class="otp-input-group">
                    <input type="text" class="otp-input" maxlength="1" pattern="\d" inputmode="numeric" autocomplete="off">
                    <input type="text" class="otp-input" maxlength="1" pattern="\d" inputmode="numeric" autocomplete="off">
                    <input type="text" class="otp-input" maxlength="1" pattern="\d" inputmode="numeric" autocomplete="off">
                    <input type="text" class="otp-input" maxlength="1" pattern="\d" inputmode="numeric" autocomplete="off">
                    <input type="text" class="otp-input" maxlength="1" pattern="\d" inputmode="numeric" autocomplete="off">
                    <input type="text" class="otp-input" maxlength="1" pattern="\d" inputmode="numeric" autocomplete="off">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" id="verifyBtn">
                <i class="fas fa-check-circle"></i> Verify Code
            </button>
        </form>

        <div class="resend-section">
            <p>Didn't receive the code?</p>
            <button type="button" class="btn-resend" id="resendBtn">
                <i class="fas fa-redo"></i> Resend Code
            </button>
        </div>
    </div>

    <script>
        // OTP Input handling
        const otpInputs = document.querySelectorAll('.otp-input');
        const otpForm = document.getElementById('otpForm');
        const verifyBtn = document.getElementById('verifyBtn');
        const resendBtn = document.getElementById('resendBtn');
        const successAlert = document.getElementById('successAlert');
        const errorAlert = document.getElementById('errorAlert');

        // Auto-focus next input
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1 && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });

            // Only allow numbers
            input.addEventListener('keypress', (e) => {
                if (!/\d/.test(e.key)) {
                    e.preventDefault();
                }
            });
        });

        // Auto-focus first input
        otpInputs[0].focus();

        // Verify OTP
        otpForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const otp = Array.from(otpInputs).map(input => input.value).join('');

            if (otp.length !== 6) {
                showError('Please enter all 6 digits');
                return;
            }

            verifyBtn.disabled = true;
            verifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verifying...<span class="spinner"></span>';

            try {
                const response = await fetch('{{ route("customer.otp.verify") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ otp })
                });

                const data = await response.json();
                console.log('Verification response:', data);

                if (data.success) {
                    showSuccess('Verification successful! Redirecting...');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1000);
                } else {
                    showError(data.message || 'Invalid OTP code');
                    if (data.remaining_attempts !== undefined) {
                        showError(data.message + ` (${data.remaining_attempts} attempts remaining)`);
                    }
                    verifyBtn.disabled = false;
                    verifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verify Code';
                    otpInputs.forEach(input => input.value = '');
                    otpInputs[0].focus();
                }
            } catch (error) {
                console.error('Verification error:', error);
                showError('An error occurred. Please try again.');
                verifyBtn.disabled = false;
                verifyBtn.innerHTML = '<i class="fas fa-check-circle"></i> Verify Code';
            }
        });

        // Resend OTP
        resendBtn.addEventListener('click', async () => {
            resendBtn.disabled = true;
            resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

            try {
                const response = await fetch('{{ route("customer.otp.resend") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                console.log('Resend response:', data);

                if (data.success) {
                    showSuccess(data.message || 'OTP code sent successfully!');
                    // Immediately show success state
                    resendBtn.innerHTML = '<i class="fas fa-check"></i> Sent!';
                    // Then allow resending after cooldown
                    setTimeout(() => {
                        resendBtn.disabled = false;
                        resendBtn.innerHTML = '<i class="fas fa-redo"></i> Resend Code';
                    }, 3000); // 3 seconds cooldown
                } else {
                    showError(data.message || 'Failed to resend OTP');
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = '<i class="fas fa-redo"></i> Resend Code';
                }
            } catch (error) {
                console.error('Resend error:', error);
                showError('An error occurred. Please try again.');
                resendBtn.disabled = false;
                resendBtn.innerHTML = '<i class="fas fa-redo"></i> Resend Code';
            }
        });

        function showSuccess(message) {
            successAlert.textContent = message;
            successAlert.classList.add('show');
            errorAlert.classList.remove('show');
        }

        function showError(message) {
            errorAlert.textContent = message;
            errorAlert.classList.add('show');
            successAlert.classList.remove('show');
        }
    </script>
</body>
</html>
