@php
    $layout = match(auth()->user()->role) {
        'admin'               => 'layouts.admin',
        'delivery_personnel'  => 'layouts.delivery',
        default               => 'layouts.warehouse',
    };
@endphp
@extends($layout)

@section('title', 'My Profile')
@section('page-title', 'My Profile')

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <h2>User Profile</h2>
    </div>

    @if(session('success'))
        <div class="alert alert-success" id="successAlert" style="display:flex;align-items:center;gap:0.6rem;margin-bottom:1rem;">
            <span>{{ session('success') }}</span>
            <button onclick="document.getElementById('successAlert').remove()" style="background:none;border:none;cursor:pointer;margin-left:auto;color:inherit;font-size:1.2rem;line-height:1;">&times;</button>
        </div>
    @endif

    <div class="profile-card">
        <div class="profile-avatar-section">
            @if($user->profile_image)
                <img src="{{ $user->profile_image }}" alt="{{ $user->name }}" class="profile-avatar">
            @else
                <div class="profile-avatar-placeholder">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </div>
            @endif
        </div>

        <div class="profile-content">
            <div class="profile-section">
                <label>Full Name</label>
                <p>{{ $user->name }}</p>
            </div>

            <div class="profile-section">
                <label>Email Address</label>
                <p>{{ $user->email }}</p>
            </div>

            <div class="profile-section">
                <label>Account Created</label>
                <p>{{ $user->created_at->format('M d, Y') }}</p>
            </div>

            <div class="profile-actions">
                <a href="{{ route('profile.edit') }}" class="btn-primary">Edit Information</a>
            </div>
        </div>
    </div>
</div>

<style>
.profile-container {
    max-width: 600px;
    margin: 2rem auto;
    padding: 0 1.5rem;
}

.profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.profile-header h2 {
    margin: 0;
    font-size: 1.75rem;
    color: #333;
}

.profile-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.profile-avatar-section {
    display: flex;
    justify-content: center;
    padding: 2rem 2rem 1rem;
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}

.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.profile-avatar-placeholder {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #999;
    border: 4px solid white;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.profile-content {
    padding: 2rem;
}

.profile-section {
    margin-bottom: 1.5rem;
}

.profile-section:last-child {
    margin-bottom: 0;
}

.profile-section label {
    display: block;
    font-weight: 600;
    color: #666;
    font-size: 0.875rem;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
    letter-spacing: 0.5px;
}

.profile-section p {
    color: #333;
    font-size: 1rem;
    margin: 0;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 6px;
}

.profile-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e9ecef;
}

.btn-primary {
    padding: 0.75rem 1.5rem;
    background: #3498db;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.3s ease;
}

.btn-primary:hover {
    background: #2980b9;
}

.badge {
    display: inline-block;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
}

.badge-info {
    background: #d1ecf1;
    color: #0c5460;
}

.alert {
    padding: 1rem;
    border-radius: 6px;
    margin-bottom: 1.5rem;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

@media (max-width: 576px) {
    .profile-container {
        padding: 0 0.75rem;
        margin: 1rem auto;
    }
    .profile-header h2 {
        font-size: 1.25rem;
    }
    .profile-avatar,
    .profile-avatar-placeholder {
        width: 80px;
        height: 80px;
    }
    .profile-avatar-section {
        padding: 1.25rem 1rem 0.75rem;
    }
    .profile-content {
        padding: 1.25rem 1rem;
    }
    .profile-section label {
        font-size: 0.75rem;
    }
    .profile-section p {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }
    .btn-primary {
        padding: 0.6rem 1rem;
        font-size: 0.85rem;
    }
    .profile-actions {
        margin-top: 1.25rem;
        padding-top: 1.25rem;
    }
}
</style>
@endsection
