<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'V&F Ice Plant and Cold Storage Inc.') - Customer Portal</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
            background: linear-gradient(135deg, #1e3ba8 0%, #2f50c4 55%, #4169E1 100%);
            min-height: 100vh;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .customer-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1rem 0;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            color: white;
        }

        .logo img {
            width: 45px;
            height: 45px;
        }

        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .logo-text .company-name {
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: 0.5px;
        }

        .logo-text .company-subtitle {
            font-weight: 400;
            font-size: 0.85rem;
            opacity: 0.95;
        }

        .mobile-menu-toggle {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
        }

        .mobile-menu-close {
            display: none;
        }

        .customer-nav {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            white-space: nowrap;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .main-content {
            min-height: calc(100vh - 80px);
            padding: 2rem 0;
        }

        .footer {
            background: rgba(0, 0, 0, 0.2);
            color: rgba(255, 255, 255, 0.8);
            text-align: center;
            padding: 1.5rem 0;
            margin-top: auto;
        }

        /* Mobile responsiveness */
        @media (max-width: 968px) {
            .mobile-menu-toggle {
                display: block;
            }

            .mobile-menu-close {
                display: block;
                position: absolute;
                top: 1rem;
                right: 1rem;
                background: none;
                border: none;
                color: white;
                font-size: 1.5rem;
                cursor: pointer;
                padding: 0.5rem;
            }

            .customer-nav {
                position: fixed;
                top: 0;
                right: -100%;
                height: 100vh;
                width: 280px;
                background: rgba(30, 59, 168, 0.98);
                backdrop-filter: blur(10px);
                flex-direction: column;
                gap: 0;
                padding: 5rem 0 2rem;
                align-items: stretch;
                transition: right 0.3s ease;
                box-shadow: -5px 0 15px rgba(0, 0, 0, 0.3);
                overflow-y: auto;
            }

            .customer-nav.active {
                right: 0;
            }

            .nav-link {
                padding: 1rem 2rem;
                border-radius: 0;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }

            .nav-link:hover {
                background: rgba(255, 255, 255, 0.15);
            }

            .container {
                padding: 0 15px;
            }

            .main-content {
                padding: 1.5rem 0;
            }
        }

        @media (max-width: 480px) {
            .logo span {
                font-size: 0.95rem;
            }

            .logo img {
                width: 35px;
                height: 35px;
            }

            .logo-text .company-name {
                font-size: 0.95rem;
            }

            .logo-text .company-subtitle {
                font-size: 0.75rem;
            }

            .customer-nav {
                width: 100%;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <header class="customer-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <img src="{{ asset('logo.png') }}" alt="V&F Ice Plant Logo">
                    <div class="logo-text">
                        <span class="company-name">V&F Ice Plant</span>
                        <span class="company-subtitle">and Cold Storage Inc.</span>
                    </div>
                </div>

                @auth
                    @if(auth()->user()->isCustomer())
                        <button class="mobile-menu-toggle" id="mobileMenuToggle">
                            <i class="fas fa-bars"></i>
                        </button>

                        <nav class="customer-nav" id="customerNav">
                            <button class="mobile-menu-close" id="mobileMenuClose">
                                <i class="fas fa-times"></i>
                            </button>
                            <a href="{{ route('customer.shop') }}" class="nav-link {{ request()->routeIs('customer.shop*') ? 'active' : '' }}">
                                <i class="fas fa-store"></i> Shop
                            </a>
                            <a href="{{ route('customer.orders') }}" class="nav-link {{ request()->routeIs('customer.orders*') ? 'active' : '' }}">
                                <i class="fas fa-box"></i> Orders
                            </a>
                            <a href="{{ route('customer.profile.show') }}" class="nav-link {{ request()->routeIs('customer.profile*') ? 'active' : '' }}">
                                <i class="fas fa-user"></i> Profile
                            </a>
                            <form action="{{ route('logout') }}" method="POST" style="display: inline; width: 100%;">
                                @csrf
                                <button type="submit" class="nav-link" style="background: none; border: none; cursor: pointer; width: 100%; text-align: left;">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </button>
                            </form>
                        </nav>
                    @endif
                @endauth
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; {{ date('Y') }} V&F Ice Plant and Cold Storage Inc. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const mobileMenuClose = document.getElementById('mobileMenuClose');
        const customerNav = document.getElementById('customerNav');

        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', () => {
                customerNav.classList.add('active');
            });
        }

        if (mobileMenuClose) {
            mobileMenuClose.addEventListener('click', () => {
                customerNav.classList.remove('active');
            });
        }

        // Close menu when clicking on a nav link
        if (customerNav) {
            const navLinks = customerNav.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', () => {
                    customerNav.classList.remove('active');
                });
            });
        }

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (customerNav && !customerNav.contains(e.target) && !mobileMenuToggle?.contains(e.target)) {
                customerNav.classList.remove('active');
            }
        });
    </script>

    @yield('scripts')

    {{-- Firebase Push Notifications (customers only) --}}
    @auth
    @if(auth()->user()->role === 'customer')
    <div id="fcm-toast" style="display:none;position:fixed;bottom:1.5rem;right:1.5rem;z-index:9999;max-width:340px;background:#1e3ba8;color:#fff;padding:1rem 1.25rem;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,.2);font-size:.95rem;line-height:1.5;cursor:pointer;" onclick="this.style.display='none'">
        <strong id="fcm-toast-title" style="display:block;margin-bottom:.2rem;font-size:1rem;"></strong>
        <span id="fcm-toast-body"></span>
    </div>

    @if(config('services.firebase.api_key'))
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-messaging-compat.js"></script>
    <script>
    (async function () {
        if (!('serviceWorker' in navigator)) {
            console.warn('FCM: serviceWorker not supported');
            return;
        }
        if (!('Notification' in window)) {
            console.warn('FCM: Notifications not supported');
            return;
        }

        const firebaseConfig = {
            apiKey:            "{{ config('services.firebase.api_key') }}",
            authDomain:        "{{ config('services.firebase.auth_domain') }}",
            projectId:         "{{ config('services.firebase.project_id') }}",
            storageBucket:     "{{ config('services.firebase.storage_bucket') }}",
            messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
            appId:             "{{ config('services.firebase.app_id') }}",
        };

        console.log('FCM: starting — projectId:', firebaseConfig.projectId);

        try {
            // Handle duplicate init across page navigations
            const firebaseApp = firebase.apps.length
                ? firebase.app()
                : firebase.initializeApp(firebaseConfig);
            const messaging = firebase.messaging(firebaseApp);
            console.log('FCM: messaging instance ready');

            // Register & wait for service worker to become active
            const swReg = await navigator.serviceWorker.register('/firebase-messaging-sw.js', { scope: '/', updateViaCache: 'none' });
            console.log('FCM: SW registered, state:', swReg.active ? swReg.active.state : 'no active worker yet');

            // Wait for the SW to become active (handles first-install case)
            if (swReg.installing) {
                await new Promise((resolve) => {
                    swReg.installing.addEventListener('statechange', function handler(e) {
                        if (e.target.state === 'activated') {
                            e.target.removeEventListener('statechange', handler);
                            resolve();
                        }
                    });
                });
                console.log('FCM: SW now activated');
            }

            // Request notification permission
            const permission = await Notification.requestPermission();
            console.log('FCM: permission =', permission);
            if (permission !== 'granted') return;

            const token = await messaging.getToken({
                vapidKey: "{{ config('services.firebase.vapid_key') }}",
                serviceWorkerRegistration: swReg,
            });

            console.log('FCM: token =', token ? token.substring(0, 20) + '…' : 'null/empty');

            if (token) {
                const res = await fetch('{{ route("customer.fcm.token.save") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ token }),
                });
                console.log('FCM: save response status:', res.status);
            } else {
                console.warn('FCM: no token returned — check VAPID key and SW');
            }

            // Show in-app toast when app is open (foreground)
            messaging.onMessage(function (payload) {
                console.log('FCM: foreground message', payload);
                // Payload is data-only — read from payload.data
                var title = (payload.data && payload.data.title) || 'V&F Ice Plant';
                var body  = (payload.data && payload.data.body)  || '';
                showFcmToast(title, body);
            });

        } catch (err) {
            console.error('FCM error:', err.code || '', err.message || err);
        }

        function showFcmToast(title, body) {
            const toast = document.getElementById('fcm-toast');
            document.getElementById('fcm-toast-title').textContent = title;
            document.getElementById('fcm-toast-body').textContent  = body;
            toast.style.display = 'block';
            setTimeout(function () { toast.style.display = 'none'; }, 7000);
        }
    })();
    </script>
    @endif
    @endif
    @endauth
</body>
</html>