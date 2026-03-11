<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'V&F Ice Plant and Cold Storage Inc.') - Delivery</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    
    <link rel="stylesheet" href="/build/assets/delivery-CZbfaTb7.css">
    <script src="/build/assets/delivery-CWGeEeD9.js" defer></script>
    <style>
        /* ── Sidebar redesign – landing page palette ── */
        .sidebar {
            background: linear-gradient(180deg, #1e3ba8 0%, #2f50c4 55%, #4169E1 100%) !important;
            box-shadow: 4px 0 20px rgba(30,59,168,.35);
        }
        .sidebar-header {
            background: rgba(0,0,0,.18) !important;
            border-bottom: 1px solid rgba(255,255,255,.12) !important;
        }
        .sidebar-header h2 { color: #fff !important; font-weight: 700; }
        .sidebar-header h2 span { color: rgba(255,255,255,.75) !important; }
        .sidebar::-webkit-scrollbar, .sidebar .sidebar-menu::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track, .sidebar .sidebar-menu::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb, .sidebar .sidebar-menu::-webkit-scrollbar-thumb { background: rgba(255,255,255,.25); border-radius: 4px; }
        .menu-section-title { color: rgba(255,255,255,.5) !important; font-size: .68rem; letter-spacing: .08em; }
        .menu-section + .menu-section { border-top: 1px solid rgba(255,255,255,.1); padding-top: .5rem; margin-top: .25rem; }
        .menu-item, .menu-dropdown-toggle {
            color: rgba(255,255,255,.82) !important;
            border-left: 3px solid transparent !important;
            transition: background .2s, border-left-color .2s, color .2s, padding-left .2s;
        }
        .menu-item:hover, .menu-dropdown-toggle:hover {
            background: rgba(255,255,255,.13) !important;
            border-left-color: rgba(255,255,255,.7) !important;
            color: #fff !important;
            padding-left: 1.75rem;
        }
        .menu-item.active, .menu-dropdown-toggle.active {
            background: rgba(255,255,255,.2) !important;
            border-left-color: #fff !important;
            color: #fff !important;
            font-weight: 600;
        }
        .menu-subitem { color: rgba(255,255,255,.62) !important; border-left: 3px solid transparent !important; }
        .menu-subitem:hover { background: rgba(255,255,255,.1) !important; color: #fff !important; border-left-color: rgba(255,255,255,.6) !important; padding-left: 3.5rem; }
        .menu-subitem:hover::before, .menu-subitem.active::before { background: #fff !important; }
        .menu-subitem.active { background: rgba(255,255,255,.18) !important; border-left-color: #fff !important; color: #fff !important; font-weight: 600; }

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

    /* ── Sidebar Collapse (desktop only) ── */
    .sidebar {
        width: 234px !important;
        transition: width 0.25s ease;
        overflow-x: hidden;
        display: flex !important;
        flex-direction: column !important;
    }
    .main-content {
        margin-left: 234px !important;
        width: calc(100% - 234px) !important;
        max-width: calc(100vw - 234px) !important;
    }
    .sidebar .sidebar-menu {
        flex: 1;
        overflow-y: auto;
        overflow-x: hidden;
    }
    .sidebar.collapsed { width: 64px !important; }
    .sidebar.collapsed .menu-label,
    .sidebar.collapsed .menu-section-title,
    .sidebar.collapsed .sidebar-logo-text,
    .sidebar.collapsed .menu-chevron,
    .sidebar.collapsed .menu-submenu { display: none !important; }
    .sidebar.collapsed .sidebar-header {
        padding: .75rem !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .sidebar.collapsed .sidebar-header > div { gap: 0 !important; justify-content: center !important; }
    .sidebar.collapsed .menu-item,
    .sidebar.collapsed .menu-dropdown-toggle {
        font-size: 0 !important;
        justify-content: center !important;
        padding: .875rem 0 !important;
    }
    .sidebar.collapsed .menu-item:hover,
    .sidebar.collapsed .menu-dropdown-toggle:hover { padding: .875rem 0 !important; }
    .sidebar.collapsed .menu-icon {
        font-size: 1rem !important;
        width: 20px !important;
        height: 20px !important;
        margin-right: 0 !important;
    }
    .sidebar.collapsed ~ .main-content {
        margin-left: 64px !important;
        width: calc(100% - 64px) !important;
        max-width: calc(100vw - 64px) !important;
    }
    .main-content { transition: margin-left 0.25s ease, width 0.25s ease, max-width 0.25s ease; }
    .sidebar-toggle-footer {
        flex-shrink: 0;
        border-top: 1px solid rgba(255,255,255,.2);
        background: rgba(30, 59, 168, 0.45);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }
    .sidebar-toggle {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .65rem 1.5rem;
        width: 100%;
        background: none;
        border: none;
        color: rgba(255,255,255,.82);
        cursor: pointer;
        font-size: .875rem;
        transition: background .2s, color .2s;
    }
    .sidebar-toggle:hover { background: rgba(255,255,255,.13); color: #fff; }
    .sidebar.collapsed .sidebar-toggle { justify-content: center; padding: .65rem 0; gap: 0; }
    .sidebar-toggle-icon { flex-shrink: 0; width: 18px; height: 18px; transition: transform .25s; }
    .sidebar.collapsed .sidebar-toggle-icon { transform: rotate(180deg); }
    .toggle-label { font-size: .875rem; }
    .sidebar.collapsed .toggle-label { display: none; }
    /* Disable collapse on mobile — mobile already has its own nav toggle */
    @media (max-width: 768px) {
        .sidebar.collapsed { width: 100%; }
        .sidebar.collapsed ~ .main-content { margin-left: 0 !important; width: 100% !important; max-width: 100vw !important; }
        .sidebar.collapsed .sidebar-header { padding: 1rem 1.25rem !important; display: block !important; }
        .sidebar.collapsed .sidebar-header > div { gap: 0.75rem !important; justify-content: flex-start !important; }
        .sidebar.collapsed .menu-item,
        .sidebar.collapsed .menu-dropdown-toggle { font-size: inherit !important; justify-content: flex-start !important; padding: .7rem 1.25rem !important; }
        .sidebar.collapsed .menu-item:hover,
        .sidebar.collapsed .menu-dropdown-toggle:hover { padding-left: 1.75rem !important; }
        .sidebar.collapsed .menu-icon { font-size: 1rem !important; width: auto !important; height: auto !important; margin-right: .75rem !important; }
        .sidebar.collapsed .menu-label,
        .sidebar.collapsed .menu-section-title,
        .sidebar.collapsed .sidebar-logo-text,
        .sidebar.collapsed .menu-submenu { display: revert !important; }
        .sidebar-toggle-footer { display: none; }
    }

    /* ── No-flash pre-collapsed state ── */
    html.sidebar-pre-collapsed .sidebar {
        width: 64px !important;
        transition: none !important;
        overflow-x: hidden !important;
    }
    html.sidebar-pre-collapsed .main-content {
        margin-left: 64px !important;
        width: calc(100% - 64px) !important;
        max-width: calc(100vw - 64px) !important;
        transition: none !important;
    }
    @media (max-width: 768px) {
        html.sidebar-pre-collapsed .sidebar { width: 100% !important; }
        html.sidebar-pre-collapsed .main-content { margin-left: 0 !important; width: 100% !important; max-width: 100vw !important; }
    }

    </style>
    <script>if(localStorage.getItem('sidebarCollapsed')==='true')document.documentElement.classList.add('sidebar-pre-collapsed');</script>
    @yield('styles')
</head>
<body>
    <div class="sidebar"><script>if(localStorage.getItem('sidebarCollapsed')==='true'&&window.innerWidth>768)document.currentScript.parentElement.classList.add('collapsed');</script>
        <div class="sidebar-header">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <img src="{{ asset('logo.png') }}" alt="V&F Logo" style="width:40px;height:40px;flex-shrink:0;">
                <h2 class="sidebar-logo-text" style="margin:0;font-size:1rem;line-height:1.3;white-space:nowrap;">V&F Ice Plant<br><span style="font-size:0.75rem;font-weight:400;white-space:nowrap;">and Cold Storage Inc.</span></h2>
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
        <div class="sidebar-toggle-footer">
            <button class="sidebar-toggle" id="sidebarToggle" title="Toggle sidebar">
                <svg class="sidebar-toggle-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <polyline points="15 18 9 12 15 6"></polyline>
                </svg>
                <span class="toggle-label">Collapse</span>
            </button>
        </div>
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
                    <span><strong>{{ ['delivery_personnel' => 'Driver'][auth()->user()->role] ?? ucfirst(str_replace('_', ' ', auth()->user()->role)) }}</strong> {{ auth()->user()->name }}</span>
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
    <script>
    (function () {
        var sidebar = document.querySelector('.sidebar');
        var btn     = document.getElementById('sidebarToggle');
        if (!sidebar || !btn) return;
        btn.addEventListener('click', function () {
            if (window.innerWidth > 768) {
                sidebar.classList.toggle('collapsed');
                localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
            }
        });
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                document.documentElement.classList.remove('sidebar-pre-collapsed');
            });
        });
    })();
    </script>
    @yield('scripts')
</body>
</html>
