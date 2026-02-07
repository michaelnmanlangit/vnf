<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'V&F Ice Plant') - Management System</title>
    
    @vite(['resources/css/app-layout.css', 'resources/js/app-layout.js'])
    @yield('styles')
</head>
<body>
    @auth
    <nav class="navbar">
        <div class="navbar-brand">
            V&F Ice Plant
        </div>
        <div class="navbar-menu">
            <span>Welcome, {{ auth()->user()->name }}</span>
            <button type="button" class="btn-logout" onclick="showLogoutModal()">Logout</button>
        </div>
    </nav>
    @endauth

    <div class="container">
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
                <button type="button" class="btn-modal btn-confirm" onclick="document.getElementById('logout-form-app').submit()">Logout</button>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('logout') }}" id="logout-form-app" style="display: none;">
        @csrf
    </form>

    @yield('scripts')
</body>
</html>
