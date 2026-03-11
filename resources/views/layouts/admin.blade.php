<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'V&F Ice Plant and Cold Storage Inc.') - Admin Panel</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    
    <link rel="stylesheet" href="/build/assets/admin-CoWPeOez.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* ── Sidebar redesign – landing page palette ── */
        .sidebar {
            background: linear-gradient(180deg, #1e3ba8 0%, #2f50c4 55%, #4169E1 100%);
            box-shadow: 4px 0 20px rgba(30, 59, 168, 0.35);
        }

        .sidebar-header {
            background: rgba(0, 0, 0, 0.18);
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            padding: 1.25rem 1.5rem;
        }

        .sidebar-header h2 {
            color: #fff;
            font-weight: 700;
        }

        .sidebar-header h2 span {
            color: rgba(255, 255, 255, 0.75);
        }

        /* Scrollbar */
        .sidebar::-webkit-scrollbar, .sidebar .sidebar-menu::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track, .sidebar .sidebar-menu::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb, .sidebar .sidebar-menu::-webkit-scrollbar-thumb { background: rgba(255,255,255,.25); border-radius: 4px; }

        /* Section labels */
        .menu-section-title {
            color: rgba(255, 255, 255, 0.5);
            font-size: .68rem;
            letter-spacing: .08em;
            padding: .5rem 1.5rem .3rem;
        }

        /* Divider between sections */
        .menu-section + .menu-section {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: .5rem;
            margin-top: .25rem;
        }

        /* Base item */
        .menu-item,
        .menu-dropdown-toggle {
            color: rgba(255, 255, 255, 0.82);
            border-left: 3px solid transparent;
            border-radius: 0;
            transition: background .2s, border-left-color .2s, color .2s, padding-left .2s;
        }

        /* Hover */
        .menu-item:hover,
        .menu-dropdown-toggle:hover {
            background: rgba(255, 255, 255, 0.13);
            border-left-color: rgba(255, 255, 255, 0.7);
            color: #fff;
            padding-left: 1.75rem;
        }

        /* Active */
        .menu-item.active,
        .menu-dropdown-toggle.active {
            background: rgba(255, 255, 255, 0.2);
            border-left-color: #fff;
            color: #fff;
            font-weight: 600;
        }

        /* Sub-items */
        .menu-subitem {
            color: rgba(255, 255, 255, 0.62);
            border-left: 3px solid transparent;
        }

        .menu-subitem:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-left-color: rgba(255, 255, 255, 0.6);
            padding-left: 3.5rem;
        }

        .menu-subitem:hover::before,
        .menu-subitem.active::before {
            background: #fff !important;
        }

        .menu-subitem.active {
            background: rgba(255, 255, 255, 0.18);
            border-left-color: #fff;
            color: #fff;
            font-weight: 600;
        }

        /* ── Sidebar Collapse ── */
        .sidebar {
            width: 234px !important; /* 10% narrower than compiled 260px */
            transition: width 0.25s ease;
            overflow-x: hidden;   /* clip only horizontally — restores compiled overflow-y:auto */
            display: flex !important;
            flex-direction: column !important;
        }
        /* Adjust main content for new sidebar width */
        .main-content {
            margin-left: 234px !important;
            width: calc(100% - 234px) !important;
            max-width: calc(100vw - 234px) !important;
        }
        /* Make the nav area scrollable and fill remaining height */
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
        /* Center logo icon in header */
        .sidebar.collapsed .sidebar-header {
            padding: .75rem !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .sidebar.collapsed .sidebar-header > div {
            gap: 0 !important;
            justify-content: center !important;
        }
        /* Remove all padding so icon sits dead-center */
        .sidebar.collapsed .menu-item,
        .sidebar.collapsed .menu-dropdown-toggle {
            font-size: 0 !important;
            justify-content: center !important;
            padding: .875rem 0 !important;
        }
        .sidebar.collapsed .menu-item:hover,
        .sidebar.collapsed .menu-dropdown-toggle:hover {
            padding: .875rem 0 !important;
        }
        /* Keep icon visible — clear the right-margin so it centres exactly */
        .sidebar.collapsed .menu-icon {
            font-size: 1rem !important;
            width: 20px !important;
            height: 20px !important;
            margin-right: 0 !important;
        }
        /* Shift the main area — all three properties from compiled CSS must be overridden */
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

        /* ── No-flash pre-collapsed state (applied before first paint via html class) ── */
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

    </style>
    <script>if(localStorage.getItem('sidebarCollapsed')==='true')document.documentElement.classList.add('sidebar-pre-collapsed');</script>
    <script src="/build/assets/admin-DLbE0-9j.js" defer></script>
    @yield('styles')
</head>
<body>
    <div class="sidebar"><script>if(localStorage.getItem('sidebarCollapsed')==='true')document.currentScript.parentElement.classList.add('collapsed');</script>
        <div class="sidebar-header">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <img src="{{ asset('logo.png') }}" alt="V&F Logo" style="width:40px;height:40px;flex-shrink:0;">
                <h2 class="sidebar-logo-text" style="margin:0;font-size:1.1rem;line-height:1.3;">V&F Ice Plant<br><span style="font-size:0.8rem;font-weight:400;">and Cold Storage Inc.</span></h2>
            </div>
        </div>
        <nav class="sidebar-menu">
            <div class="menu-section">
                <div class="menu-section-title">Main</div>
                <a href="{{ route('admin.dashboard') }}" class="menu-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="7" height="7"></rect>
                        <rect x="14" y="3" width="7" height="7"></rect>
                        <rect x="14" y="14" width="7" height="7"></rect>
                        <rect x="3" y="14" width="7" height="7"></rect>
                    </svg>
                    Dashboard
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Operations</div>
                <a href="{{ route('admin.temperature.index') }}" class="menu-item {{ request()->routeIs('admin.temperature.*') ? 'active' : '' }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                    </svg>
                    Temperature
                </a>
                <a href="{{ route('admin.inventory.index') }}" class="menu-item {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                    Inventory
                </a>
                <a href="{{ route('admin.deliveries.index') }}" class="menu-item {{ request()->routeIs('admin.deliveries.*') ? 'active' : '' }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                    Delivery
                </a>
                <a href="{{ route('admin.employees.index') }}" class="menu-item {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                    <i class="fas fa-users menu-icon" style="font-size:1rem;width:20px;text-align:center;"></i>
                    Employee
                </a>
                <a href="{{ route('admin.billing.customers') }}" class="menu-item {{ request()->routeIs('admin.billing.customers*') ? 'active' : '' }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                        <circle cx="11" cy="9" r="2"></circle>
                        <path d="M7 15c0-2 1.8-3 4-3s4 1 4 3"></path>
                    </svg>
                    Customer
                </a>
                <a href="{{ route('admin.tasks.index') }}" class="menu-item {{ request()->routeIs('admin.tasks.*') ? 'active' : '' }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M9 11l3 3L22 4"></path>
                        <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                    </svg>
                    Assignment
                </a>
                <a href="{{ route('admin.attendance.index') }}" class="menu-item {{ request()->routeIs('admin.attendance.*') ? 'active' : '' }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    Attendance
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Financial</div>
                <a href="{{ route('admin.payroll.index') }}" class="menu-item {{ request()->routeIs('admin.payroll.*') ? 'active' : '' }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><line x1="12" y1="12" x2="12" y2="16"/><line x1="10" y1="14" x2="14" y2="14"/></svg>
                    Payroll
                </a>
                <a href="{{ route('admin.billing.index') }}" class="menu-item {{ request()->routeIs('admin.billing.*') && !request()->routeIs('admin.billing.customers*') ? 'active' : '' }}">
                    <span class="menu-icon" style="display:inline-flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:700;">₱</span>
                    Billing & Invoice
                </a>
                <button type="button" class="menu-item menu-dropdown-toggle {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" id="reportsToggle" aria-expanded="{{ request()->routeIs('admin.reports.*') ? 'true' : 'false' }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                    <span class="menu-label" style="flex:1;text-align:left;">Reports</span>
                    <svg class="menu-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="6 9 12 15 18 9"></polyline>
                    </svg>
                </button>
                <div class="menu-submenu {{ request()->routeIs('admin.reports.*') ? 'open' : '' }}" id="reportsSubmenu">
                    <a href="{{ route('admin.reports.inventory') }}"   class="menu-subitem {{ request()->routeIs('admin.reports.inventory') ? 'active' : '' }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                        Inventory
                    </a>
                    <a href="{{ route('admin.reports.temperature') }}" class="menu-subitem {{ request()->routeIs('admin.reports.temperature') ? 'active' : '' }}">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path></svg>
                        Temperature
                    </a>
                    <a href="{{ route('admin.reports.financial') }}"   class="menu-subitem {{ request()->routeIs('admin.reports.financial') ? 'active' : '' }}">
                        <span style="flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;width:14px;font-size:.8rem;font-weight:700;line-height:1;">₱</span>
                        Financial
                    </a>
                </div>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Account</div>
                <a href="{{ route('profile.show') }}" class="menu-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
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
            <button type="button" class="menu-toggle" id="menuToggle" style="display: none;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
            <div class="navbar-left">
                <h1 class="navbar-title">@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="navbar-right">
                <div class="user-info">
                    <span><strong>{{ auth()->user()->employee?->first()?->position ?? 'Admin' }}</strong> {{ auth()->user()->name }}</span>
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
                <button type="button" class="btn-modal btn-confirm" onclick="document.getElementById('logout-form-admin').submit()">Logout</button>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" id="logout-form-admin" style="display: none;">
        @csrf
    </form>

    @yield('scripts')

    <script>
    (function () {
        var toggle  = document.getElementById('reportsToggle');
        var submenu = document.getElementById('reportsSubmenu');
        if (!toggle || !submenu) return;

        // If already open server-side, add animate class so transitions work going forward
        if (submenu.classList.contains('open')) {
            submenu.classList.add('open-animate');
        }

        toggle.addEventListener('click', function () {
            var isOpen = submenu.classList.contains('open-animate');
            if (isOpen) {
                submenu.classList.remove('open-animate', 'open');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.classList.remove('active');
            } else {
                submenu.classList.add('open-animate', 'open');
                toggle.setAttribute('aria-expanded', 'true');
                toggle.classList.add('active');
            }
        });
    })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr('.fp-date', {
            altInput: true,
            altFormat: 'm/d/Y',
            dateFormat: 'Y-m-d',
            allowInput: true,
        });
    });
    </script>
    <script>
    (function () {
        var sidebar = document.querySelector('.sidebar');
        var btn     = document.getElementById('sidebarToggle');
        if (!sidebar || !btn) return;
        // Collapsed class already applied synchronously — just wire the toggle button
        btn.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
        // Re-enable transitions after first paint
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                document.documentElement.classList.remove('sidebar-pre-collapsed');
            });
        });
    })();
    </script>
</body>
</html>
