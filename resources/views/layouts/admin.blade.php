<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'V&F Ice Plant') - Admin Panel</title>
    
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
    @yield('styles')
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>V&F Ice Plant</h2>
            <p>Admin Panel</p>
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
                <a href="#" class="menu-item">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                    </svg>
                    Temperature Monitoring
                </a>
                <a href="{{ route('admin.inventory.index') }}" class="menu-item {{ request()->routeIs('admin.inventory.*') ? 'active' : '' }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                    Inventory Management
                </a>
                <a href="#" class="menu-item">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="3" width="15" height="13"></rect>
                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                    </svg>
                    Deliveries & GPS
                </a>
                <a href="{{ route('admin.employees.index') }}" class="menu-item {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    Employee Management
                </a>
            </div>

            <div class="menu-section">
                <div class="menu-section-title">Financial</div>
                <a href="{{ route('admin.billing.index') }}" class="menu-item {{ request()->routeIs('admin.billing.*') ? 'active' : '' }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"></line>
                        <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                    </svg>
                    Billing & Payments
                </a>
                <a href="#" class="menu-item">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                    Reports
                </a>
            </div>


        </nav>
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
                    <span><strong>{{ auth()->user()->employee?->first()?->position ?? 'Admin' }} {{ auth()->user()->name }}</strong></span>
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
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

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
</body>
</html>
