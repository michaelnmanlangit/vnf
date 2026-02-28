<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - V&F Management System</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand:       #4169E1;
            --brand-dark:  #2f50c4;
            --brand-light: #eef1fc;
            --text-head:   #1a202c;
            --text-sub:    #64748b;
            --border:      #e2e8f0;
            --radius:      12px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', 'Segoe UI', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        /* ── Wrapper ─────────────────────────────────────── */
        .login-wrapper {
            display: flex;
            width: 100%;
            max-width: 960px;
            min-height: 580px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 24px 64px rgba(0,0,0,.18);
        }

        /* ── Left brand panel ────────────────────────────── */
        .brand-panel {
            flex: 1.1;
            background: linear-gradient(145deg, #4169E1 0%, #2f50c4 60%, #1e3ba8 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem 2.5rem;
            position: relative;
            overflow: hidden;
        }

        /* ── Animated background circles ─────────────── */
        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255,255,255,.09);
            pointer-events: none;
            will-change: transform;
        }
        .circle-1 {
            width: 340px; height: 340px;
            top: -100px; right: -100px;
            animation: floatA 9s ease-in-out infinite;
        }
        .circle-2 {
            width: 200px; height: 200px;
            bottom: -60px; left: -60px;
            background: rgba(255,255,255,.07);
            animation: floatB 11s ease-in-out infinite;
        }
        .circle-3 {
            width: 130px; height: 130px;
            top: 50%; left: 10%;
            background: rgba(255,255,255,.06);
            animation: floatC 7s ease-in-out infinite;
        }
        .circle-4 {
            width: 80px; height: 80px;
            bottom: 20%; right: 12%;
            background: rgba(255,255,255,.08);
            animation: floatD 8s ease-in-out infinite 1.5s;
        }
        .circle-5 {
            width: 55px; height: 55px;
            top: 22%; left: 18%;
            background: rgba(255,255,255,.07);
            animation: floatA 6s ease-in-out infinite 2s;
        }

        @keyframes floatA {
            0%,100% { transform: translate(0, 0) scale(1); }
            33%      { transform: translate(-18px, 22px) scale(1.05); }
            66%      { transform: translate(14px, -12px) scale(.96); }
        }
        @keyframes floatB {
            0%,100% { transform: translate(0, 0) scale(1); }
            40%      { transform: translate(20px, -26px) scale(1.07); }
            70%      { transform: translate(-10px, 16px) scale(.94); }
        }
        @keyframes floatC {
            0%,100% { transform: translate(0, 0); }
            50%      { transform: translate(16px, -20px); }
        }
        @keyframes floatD {
            0%,100% { transform: translate(0, 0) rotate(0deg); }
            50%      { transform: translate(-14px, 18px) rotate(20deg); }
        }

        .brand-logo-wrap {
            background: white;
            border-radius: 20px;
            padding: 18px;
            box-shadow: 0 8px 30px rgba(0,0,0,.18);
            margin-bottom: 2rem;
            transition: transform .3s ease, box-shadow .3s ease;
        }
        .brand-logo-wrap:hover {
            transform: translateY(-4px) scale(1.04);
            box-shadow: 0 16px 40px rgba(0,0,0,.24);
        }
        .brand-logo-wrap img {
            width: 80px;
            height: 80px;
            display: block;
            object-fit: contain;
        }

        .brand-name {
            color: white;
            font-size: 1.45rem;
            font-weight: 800;
            letter-spacing: -.3px;
            text-align: center;
            white-space: nowrap;
            line-height: 1.2;
            margin-bottom: 1.75rem;
            z-index: 1;
        }

        .brand-tagline {
            display: flex;
            flex-direction: column;
            gap: .65rem;
            z-index: 1;
            text-align: center;
        }
        .brand-tagline p {
            color: rgba(255,255,255,.8);
            font-size: .82rem;
            font-weight: 400;
            font-style: italic;
            line-height: 1.5;
            margin: 0;
        }
        .brand-tagline p strong {
            font-style: normal;
            font-weight: 600;
            color: white;
        }

        .brand-footer {
            position: absolute;
            bottom: 1.25rem;
            color: rgba(255,255,255,.45);
            font-size: .72rem;
            text-align: center;
            z-index: 1;
        }

        /* ── Right form panel ────────────────────────────── */
        .form-panel {
            flex: 1;
            background: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem 3rem;
        }

        .form-heading {
            margin-bottom: 2rem;
        }
        .form-heading h2 {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-head);
            margin-bottom: .35rem;
        }
        .form-heading p {
            font-size: .875rem;
            color: var(--text-sub);
        }

        /* ── Form controls ───────────────────────────────── */
        .form-group {
            margin-bottom: 1.4rem;
        }
        .form-group label {
            display: block;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--text-sub);
            margin-bottom: .45rem;
        }

        .input-wrap {
            position: relative;
        }
        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px; height: 18px;
            color: #94a3b8;
            pointer-events: none;
            transition: color .2s;
        }

        .form-control {
            width: 100%;
            padding: .75rem .75rem .75rem 2.6rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            font-size: .9rem;
            font-family: inherit;
            color: var(--text-head);
            background: #f8fafc;
            transition: border-color .25s, box-shadow .25s, background .25s;
            outline: none;
        }
        .form-control:hover {
            border-color: #b8c7f7;
            background: white;
        }
        .form-control:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3.5px rgba(65,105,225,.12);
            background: white;
        }
        .input-wrap:focus-within .input-icon {
            color: var(--brand);
        }
        .form-control.error {
            border-color: #dc3545;
        }
        .form-control.error:focus {
            box-shadow: 0 0 0 3.5px rgba(220,53,69,.12);
        }

        /* ── Password toggle ─────────────────────────────── */
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
            padding: .4rem;
            display: flex;
            align-items: center;
            color: #94a3b8;
            transition: color .2s;
        }
        .password-toggle:hover { color: var(--brand); }
        .password-toggle svg { width: 18px; height: 18px; stroke: currentColor; stroke-width: 2; fill: none; }

        /* ── Checkbox ────────────────────────────────────── */
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: .5rem;
            margin-bottom: 1.25rem;
        }
        .checkbox-group input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: var(--brand);
            cursor: pointer;
        }
        .checkbox-group label {
            margin: 0;
            cursor: pointer;
            font-size: .875rem;
            color: var(--text-sub);
        }

        /* ── Sign In button ──────────────────────────────── */
        .btn-login {
            width: 100%;
            padding: .9rem;
            background: var(--brand);
            color: white;
            border: none;
            border-radius: var(--radius);
            font-size: .9rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            cursor: pointer;
            transition: background .25s, transform .2s, box-shadow .25s;
            box-shadow: 0 4px 14px rgba(65,105,225,.35);
        }
        .btn-login:hover {
            background: var(--brand-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(65,105,225,.45);
        }
        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 3px 8px rgba(65,105,225,.3);
        }

        /* ── reCAPTCHA wrapper ───────────────────────────── */
        .recaptcha-wrap {
            display: flex;
            justify-content: center;
            margin-bottom: 1.25rem;
            transform: scale(.88);
            transform-origin: center;
        }

        /* ── Toast ───────────────────────────────────────── */
        .toast-container {
            position: fixed;
            top: 20px; right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 400px;
        }
        .toast {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0,0,0,.12);
            padding: 1rem 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            min-width: 300px;
            animation: slideIn .3s ease-out;
            border-left: 4px solid;
        }
        .toast.error   { border-left-color: #dc3545; }
        .toast.success { border-left-color: #28a745; }
        .toast.warning { border-left-color: #ffc107; }
        .toast-icon { flex-shrink:0; width:24px; height:24px; }
        .toast-icon.error   { color:#dc3545; }
        .toast-icon.success { color:#28a745; }
        .toast-icon.warning { color:#ffc107; }
        .toast-content { flex: 1; }
        .toast-title   { font-weight:600; font-size:.95rem; margin-bottom:.25rem; color:#333; }
        .toast-message { font-size:.85rem; color:#666; line-height:1.4; }
        .toast-close   { background:none; border:none; cursor:pointer; padding:0; color:#999; font-size:1.25rem; line-height:1; transition:color .2s; }
        .toast-close:hover { color:#333; }

        @keyframes slideIn  { from { transform:translateX(400px); opacity:0; } to { transform:translateX(0); opacity:1; } }
        @keyframes slideOut { from { transform:translateX(0); opacity:1; } to { transform:translateX(400px); opacity:0; } }
        .toast.hiding { animation: slideOut .3s ease-in forwards; }

        /* ── Mobile header/footer (hidden on desktop) ────── */
        .mobile-top, .mobile-bottom { display: none; }

        /* ── Responsive ──────────────────────────────────── */
        @media (max-width: 720px) {
            .login-wrapper { flex-direction: column; max-width: 420px; min-height: auto; border-radius: 16px; }
            .brand-panel   { display: none; }
            .form-panel    { border-radius: 16px; padding: 2rem 2rem 1.5rem; display: flex; flex-direction: column; }
            .form-heading  { margin-bottom: 1.5rem; }
            .recaptcha-wrap { transform: scale(.82); transform-origin: center; }

            /* show mobile header */
            .mobile-top {
                display: flex;
                flex-direction: column;
                align-items: center;
                padding: 2.5rem 1rem 1.75rem;
                text-align: center;
                gap: .6rem;
            }
            .mobile-top img {
                width: 56px; height: 56px;
                border-radius: 14px;
                box-shadow: 0 4px 14px rgba(65,105,225,.25);
                object-fit: contain;
                background: white;
                border: 1px solid #e2e8f0;
                padding: 6px;
            }
            .mobile-top span {
                font-size: .95rem;
                font-weight: 700;
                color: #1a202c;
                line-height: 1.3;
            }

            /* show mobile footer */
            .mobile-bottom {
                display: block;
                text-align: center;
                padding: 1.25rem 1rem 1.5rem;
                font-size: .72rem;
                color: #94a3b8;
                margin-top: auto;
            }
        }
        /* ≤ 520px — start shrinking */
        @media (max-width: 520px) {
            .login-wrapper      { max-width: 98vw; }
            .form-panel         { padding: 1.6rem 1.5rem 1.25rem; }
            .form-heading h2    { font-size: 1.4rem; }
            .form-heading p     { font-size: .8rem; }
            .form-group label   { font-size: .67rem; }
            .form-control       { font-size: .82rem; padding: .62rem .62rem .62rem 2.3rem; }
            .input-icon         { width: 16px; height: 16px; left: 12px; }
            .checkbox-group label { font-size: .8rem; }
            .btn-login          { font-size: .82rem; padding: .78rem; }
            .recaptcha-wrap     { transform: scale(.74); transform-origin: center; }
            .mobile-top         { padding: 1.75rem 1rem 1.25rem; }
            .mobile-top img     { width: 46px; height: 46px; }
            .mobile-top span    { font-size: .85rem; }
        }

        /* ≤ 400px — smaller still */
        @media (max-width: 400px) {
            .login-wrapper      { max-width: 100vw; margin: .5rem; }
            .form-panel         { padding: 1.25rem 1.1rem 1rem; }
            .form-heading h2    { font-size: 1.2rem; }
            .form-heading p     { font-size: .75rem; }
            .form-group         { margin-bottom: 1rem; }
            .form-group label   { font-size: .64rem; }
            .form-control       { font-size: .78rem; padding: .55rem .55rem .55rem 2.1rem; border-radius: 9px; }
            .input-icon         { width: 14px; height: 14px; left: 10px; }
            .password-toggle svg { width: 15px; height: 15px; }
            .checkbox-group     { margin-bottom: .9rem; }
            .checkbox-group label { font-size: .75rem; }
            .btn-login          { font-size: .78rem; padding: .7rem; border-radius: 9px; }
            .recaptcha-wrap     { transform: scale(.65); transform-origin: center; margin-bottom: -8px; }
            .mobile-top         { padding: 1.4rem 1rem 1rem; gap: .4rem; }
            .mobile-top img     { width: 40px; height: 40px; border-radius: 11px; }
            .mobile-top span    { font-size: .8rem; }
            .mobile-bottom      { font-size: .67rem; padding: .9rem 1rem 1rem; }
            .toast-container    { left:10px; right:10px; max-width:none; }
            .toast              { min-width: auto; }
        }
    </style>
</head>
<body>
    <!-- Toast Container -->
    <div class="toast-container" id="toastContainer"></div>

    <div class="login-wrapper">

        <!-- ── Left brand panel ── -->
        <div class="brand-panel">
            <div class="circle circle-1"></div>
            <div class="circle circle-2"></div>
            <div class="circle circle-3"></div>
            <div class="circle circle-4"></div>
            <div class="circle circle-5"></div>
            <div class="brand-logo-wrap">
                <img src="{{ asset('logo.png') }}" alt="V&F Logo">
            </div>
            <div class="brand-name">V&amp;F Ice Plant and Cold Storage Inc.</div>
            <div class="brand-tagline">
                <p><strong>Cool in Service, Warm at Heart.</strong></p>
                <p>Trust V&amp;F – We Execute Beyond Standard</p>
            </div>
            <div class="brand-footer">&copy; {{ date('Y') }} V&amp;F Ice Plant and Cold Storage Inc.</div>
        </div>

        <!-- ── Right form panel ── -->
        <div class="form-panel">
            <!-- mobile only top -->
            <div class="mobile-top">
                <img src="{{ asset('logo.png') }}" alt="V&F Logo">
                <span>V&amp;F Ice Plant and Cold Storage Inc.</span>
            </div>

            <div class="form-heading">
                <h2>Login</h2>
                <p>Welcome back! Please sign in to your account.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrap">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 4H4a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V6a2 2 0 00-2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            placeholder="you@example.com"
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap password-group">
                        <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0110 0v4"/>
                        </svg>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            required
                            placeholder="••••••••"
                            style="padding-right:45px;"
                        >
                        <button type="button" class="password-toggle" id="togglePassword">
                            <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>

                <div class="recaptcha-wrap">
                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                </div>

                <button type="submit" class="btn-login">Sign In</button>
            </form>

            <!-- mobile only bottom -->
            <div class="mobile-bottom">&copy; {{ date('Y') }} V&amp;F Ice Plant and Cold Storage Inc.</div>
        </div>

    </div><!-- /.login-wrapper -->

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
