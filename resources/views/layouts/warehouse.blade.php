<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'V&F Ice Plant') - Warehouse</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    
    @vite(['resources/css/warehouse.css', 'resources/js/warehouse.js'])
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
                <div class="menu-section-title">Operations</div>
                
                @if(auth()->user()->role === 'inventory_staff')
                <a href="{{ route('inventory.index') }}" class="menu-item {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                    Inventory Management
                </a>
                @elseif(auth()->user()->role === 'temperature_staff')
                <a href="{{ route('warehouse.temperature.index') }}" class="menu-item {{ request()->routeIs('warehouse.temperature.*') ? 'active' : '' }}">
                    <svg class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M14 14.76V3.5a2.5 2.5 0 0 0-5 0v11.26a4.5 4.5 0 1 0 5 0z"></path>
                    </svg>
                    Temperature Monitoring
                </a>
                @elseif(auth()->user()->role === 'payment_staff')
                <a href="{{ route('admin.billing.index') }}" class="menu-item {{ request()->routeIs('admin.billing.*') ? 'active' : '' }}">
                    <span class="menu-icon" style="display:inline-flex;align-items:center;justify-content:center;font-size:1.1rem;font-weight:700;">₱</span>
                    Billing & Invoices
                </a>
                @endif
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
                <h1>@yield('page-title', 'Dashboard')</h1>
            </div>
            <div class="navbar-right">
                <div class="user-info">
                    <span><strong>{{ auth()->user()->employee?->first()?->position ?? 'Staff' }}</strong> {{ auth()->user()->name }}</span>
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
                <button type="button" class="btn-modal btn-confirm" onclick="document.getElementById('logout-form-warehouse').submit()">Logout</button>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" id="logout-form-warehouse" style="display: none;">
        @csrf
    </form>
    
    @vite(['resources/js/warehouse.js'])
    @yield('scripts')
</body>
</html>
