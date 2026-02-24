<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - V&F Management System</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }

        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
        }

        .login-header {
            background: #4169E1;
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .login-header h1 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .login-header p {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .login-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #4169E1;
            box-shadow: 0 0 0 3px rgba(65, 105, 225, 0.1);
        }

        .form-control.error {
            border-color: #dc3545;
        }

        .form-control.error:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .checkbox-group label {
            margin: 0;
            cursor: pointer;
        }

        .btn-login {
            width: 100%;
            padding: 0.875rem;
            background: #4169E1;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .login-footer {
            text-align: center;
            padding: 1rem;
            background: #f8f9fa;
            color: #666;
            font-size: 0.85rem;
        }

        .password-group {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-toggle svg {
            width: 20px;
            height: 20px;
            stroke: #666;
            stroke-width: 2;
            transition: all 0.3s;
        }

        .password-toggle:hover svg {
            stroke: #4169E1;
        }

        /* Toast Notification Styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 400px;
        }

        .toast {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            padding: 1rem 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            min-width: 300px;
            animation: slideIn 0.3s ease-out;
            border-left: 4px solid;
        }

        .toast.error {
            border-left-color: #dc3545;
        }

        .toast.success {
            border-left-color: #28a745;
        }

        .toast.warning {
            border-left-color: #ffc107;
        }

        .toast-icon {
            flex-shrink: 0;
            width: 24px;
            height: 24px;
        }

        .toast-icon.error {
            color: #dc3545;
        }

        .toast-icon.success {
            color: #28a745;
        }

        .toast-icon.warning {
            color: #ffc107;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
            color: #333;
        }

        .toast-message {
            font-size: 0.85rem;
            color: #666;
            line-height: 1.4;
        }

        .toast-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            color: #999;
            font-size: 1.25rem;
            line-height: 1;
            transition: color 0.2s;
        }

        .toast-close:hover {
            color: #333;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        .toast.hiding {
            animation: slideOut 0.3s ease-in forwards;
        }

        @media (max-width: 500px) {
            .toast-container {
                left: 10px;
                right: 10px;
                max-width: none;
            }

            .toast {
                min-width: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>
    <div class="login-container">
        <div class="login-header">
            <h1>V&F</h1>
            <p>Integrated Management System</p>
        </div>

        <div class="login-body">

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-control" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus
                        placeholder="Enter your email"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-group">
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-control" 
                            required
                            placeholder="Enter your password"
                            style="padding-right: 45px;"
                        >
                        <button type="button" class="password-toggle" id="togglePassword">
                            <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>

                <div class="form-group" style="display: flex; justify-content: center;">
                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                </div>

                <button type="submit" class="btn-login">
                    Sign In
                </button>
            </form>
        </div>

        <div class="login-footer">
            &copy; {{ date('Y') }} V&F Ice Plant and Cold Storage Inc.
        </div>
    </div>

    <script>
        // Password toggle functionality
        const passwordInput = document.getElementById('password');
        const toggleButton = document.getElementById('togglePassword');
        const eyeIcon = document.getElementById('eyeIcon');

        toggleButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle>';
            }
        });

        // Form validation - check reCAPTCHA before submission
        let isSubmitting = false;
        const loginForm = document.getElementById('loginForm');
        loginForm.addEventListener('submit', function(e) {
            // Prevent multiple submissions
            if (isSubmitting) {
                e.preventDefault();
                return false;
            }

            const recaptchaResponse = grecaptcha.getResponse();
            
            if (!recaptchaResponse || recaptchaResponse.length === 0) {
                e.preventDefault();
                showToast('warning', 'Verification Required', 'Please complete the reCAPTCHA verification before signing in.', 6000);
                
                // Scroll to reCAPTCHA if needed
                const recaptchaElement = document.querySelector('.g-recaptcha');
                if (recaptchaElement) {
                    recaptchaElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                
                return false;
            }

            // Mark as submitting to prevent duplicate submissions
            isSubmitting = true;
        });

        // Toast notification system
        function showToast(type, title, message, duration = 5000) {
            const container = document.getElementById('toastContainer');
            
            // Check if a toast with the same message already exists
            const existingToasts = container.querySelectorAll('.toast-message');
            for (let existingToast of existingToasts) {
                if (existingToast.textContent === message) {
                    return; // Don't show duplicate toast
                }
            }

            const toast = document.createElement('div');
            toast.className = `toast ${type}`;

            const icons = {
                error: '<svg class="toast-icon error" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>',
                success: '<svg class="toast-icon success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>',
                warning: '<svg class="toast-icon warning" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>'
            };

            toast.innerHTML = `
                ${icons[type]}
                <div class="toast-content">
                    <div class="toast-title">${title}</div>
                    <div class="toast-message">${message}</div>
                </div>
                <button class="toast-close" onclick="this.parentElement.remove()">&times;</button>
            `;

            container.appendChild(toast);

            // Auto remove after duration
            setTimeout(() => {
                toast.classList.add('hiding');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }

        // Check for Laravel validation errors on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Show toast notifications for errors
            @if ($errors->any())
                let hasCredentialError = false;
                @foreach ($errors->all() as $error)
                    @if (Str::contains($error, 'reCAPTCHA') || Str::contains($error, 'recaptcha') || Str::contains($error, 'complete the'))
                        showToast('warning', 'Verification Required', '{{ $error }}', 6000);
                    @elseif (Str::contains($error, 'credentials') || Str::contains($error, 'do not match'))
                        hasCredentialError = true;
                        showToast('error', 'Login Failed', '{{ $error }}', 6000);
                    @else
                        showToast('error', 'Error', '{{ $error }}', 5000);
                    @endif
                @endforeach

                // For credential errors, highlight both fields since either could be wrong
                if (hasCredentialError) {
                    document.getElementById('email').classList.add('error');
                    document.getElementById('password').classList.add('error');
                }
            @endif

            @if (session('error'))
                showToast('error', 'Error', '{{ session('error') }}', 5000);
            @endif

            @if (session('success'))
                showToast('success', 'Success', '{{ session('success') }}', 5000);
            @endif

            // Remove error class on input
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('error');
                });
            });
        });
    </script>
</body>
</html>
