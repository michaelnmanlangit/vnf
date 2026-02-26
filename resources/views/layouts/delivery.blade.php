<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'V&F Ice Plant') - Delivery</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.png') }}">
    
    @vite(['resources/css/delivery.css', 'resources/js/delivery.js'])
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
            </div>


        </nav>
    </div>

    <div class="main-content">
        <nav class="top-navbar">
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
                <button type="button" class="btn-modal btn-confirm" onclick="document.getElementById('logout-form-delivery').submit()">Logout</button>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" id="logout-form-delivery" style="display: none;">
        @csrf
    </form>
    </div>

    @yield('scripts')
</body>
</html>
