<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'V&F Ice Plant') - Delivery</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    
    <link rel="stylesheet" href="/build/assets/delivery-CZbfaTb7.css">
    <script src="/build/assets/delivery-CWGeEeD9.js" defer></script>
    <style>

    /* ── Responsive nav — mirrors admin CSS behaviour ── */
    .menu-toggle {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        color: #2c3e50;
        padding: .5rem;
        margin-right: 1rem;
    }
    .menu-toggle svg { width: 24px; height: 24px; }

    @media (max-width: 1024px) {
        .sidebar       { width: 220px; }
        .main-content  { margin-left: 220px; width: calc(100% - 220px); max-width: calc(100vw - 220px); }
        .top-navbar    { padding: .75rem 1.5rem; }
    }
    @media (max-width: 768px) {
        .sidebar {
            width: 100%;
            height: auto;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height .3s ease;
            z-index: 999;
            margin-top: 60px;
        }
        .sidebar.active { max-height: 100vh; overflow-y: auto; }
        .main-content   { margin-left: 0; width: 100%; max-width: 100vw; }
        .top-navbar     { padding: .75rem 1rem; gap: .5rem; }
        .menu-toggle    { display: block !important; }
        .navbar-left    { min-width: auto; flex: 1; }
        .navbar-left h1 { font-size: 1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .navbar-right   { flex-wrap: wrap; gap: .5rem; flex-shrink: 0; }
        .user-info      { display: none; }
        .sidebar-menu   { padding: .5rem 0; }
        .menu-item      { padding: .7rem 1.25rem; font-size: .95rem; }
        .sidebar-header { padding: 1rem 1.25rem; }
        .content-area   { padding: 1.5rem 1rem !important; }
    }
    @media (max-width: 640px) {
        .top-navbar     { padding: .5rem .75rem; }
        .navbar-left h1 { font-size: .95rem; }
        .menu-item      { padding: .6rem 1rem; font-size: .9rem; }
        .btn-logout     { padding: .4rem 1rem; font-size: .85rem; }
        .sidebar-header h2 { font-size: 1.1rem; }
    }
    @media (max-width: 480px) {
        .top-navbar     { padding: .5rem; }
        .navbar-left h1 { font-size: .85rem; }
        .menu-item      { padding: .5rem .75rem; font-size: .85rem; }
        .btn-logout     { padding: .35rem .75rem; font-size: .8rem; }
        .content-area   { padding: 1rem .75rem !important; }
    }

    /* ── Logout modal active state ── */
    .modal-overlay.active { display: flex; }
    </style>
    @yield('styles')
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <img src="{{ asset('logo.png') }}" alt="V&F Logo" style="width:40px;height:40px;flex-shrink:0;">
                <h2 style="margin:0;font-size:1.1rem;line-height:1.3;">V&F Ice Plant<br><span style="font-size:0.8rem;font-weight:400;">and Cold Storage Inc.</span></h2>
            </div>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-section-title">Main</div>
                <a href="{{ route('delivery.dashboard') }}" class="menu-item {{ request()->routeIs('delivery.dashboard') ? 'active' : '' }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    Dashboard
                </a>
                <a href="{{ route('profile.show') }}" class="menu-item {{ request()->routeIs('profile.show', 'profile.edit') ? 'active' : '' }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    My Profile
                </a>
            </div>


        </nav>
    </div>

    <div class="main-content">
        <nav class="top-navbar">
            <button type="button" class="menu-toggle" id="menuToggle" style="display:none;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <div class="navbar-left">
                <h1>@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="navbar-right">
                <div class="user-info">
                    <span><strong>{{ auth()->user()->employee?->first()?->position ?? 'Staff' }} {{ auth()->user()->name }}</strong></span>
                </div>
                <div class="navbar-actions">
                    <button type="button" class="btn-logout" onclick="showLogoutModal()">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                            <polyline points="16 17 21 12 16 7"></polyline>
                            <line x1="21" y1="12" x2="9" y2="12"></line>
                        </svg>
                        Logout
                    </button>
                </div>
            </div>
        </nav>

        <div class="content-area">
            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>

        <footer>
            <p>&copy; {{ date('Y') }} V&F Ice Plant and Cold Storage Inc. All rights reserved.</p>
        </footer>
    </div>

    <!-- Logout Confirmation Modal -->
    <div class="modal-overlay" id="logoutModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Confirm Logout</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to logout from the system?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-modal btn-cancel" onclick="hideLogoutModal()">Cancel</button>
                <button type="button" class="btn-modal btn-confirm" onclick="document.getElementById('logout-form-delivery').submit()">Logout</button>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" id="logout-form-delivery" style="display: none;">
        @csrf
    </form>

    <script>
    (function () {
        var toggle  = document.getElementById('menuToggle');
        var sidebar = document.querySelector('.sidebar');
        if (!toggle || !sidebar) return;
        toggle.addEventListener('click', function () {
            sidebar.classList.toggle('active');
        });
        // Close sidebar when a nav link is tapped on mobile
        document.querySelectorAll('.menu-item').forEach(function (el) {
            el.addEventListener('click', function () {
                if (window.innerWidth <= 768) sidebar.classList.remove('active');
            });
        });
        // Close sidebar on outside click
        document.addEventListener('click', function (e) {
            if (window.innerWidth <= 768 && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });
    })();
    </script>
    @yield('scripts')
</body>
</html>
