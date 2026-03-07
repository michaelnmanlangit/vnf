@extends('layouts.customer')

@section('title', 'My Profile')

@section('styles')
<style>
    .profile-container {
        max-width: 800px;
        margin: 1.5rem auto;
        background: white;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #ecf0f1;
    }

    .profile-header h1 {
        color: #2c3e50;
        font-size: 1.5rem;
    }

    .btn-edit {
        padding: 0.6rem 1.2rem;
        background: linear-gradient(135deg, #3498db, #2c3e50);
        border: none;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s ease;
        font-family: 'Poppins', sans-serif;
        display: inline-block;
    }

    .btn-edit:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
    }

    .profile-section {
        margin-bottom: 1.5rem;
    }

    .section-title {
        color: #2c3e50;
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .info-item {
        padding: 0.75rem;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .info-item.full-width {
        grid-column: 1 / -1;
    }

    .info-label {
        color: #7f8c8d;
        font-size: 0.8rem;
        font-weight: 500;
        margin-bottom: 0.3rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .info-value {
        color: #2c3e50;
        font-weight: 600;
        font-size: 1rem;
    }

    .alert {
        padding: 0.75rem;
        border-radius: 8px;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }

    .alert-success {
        background: #d4edda;
        border-left: 3px solid #28a745;
        color: #155724;
    }

    @media (max-width: 768px) {
        .profile-container {
            margin: 1rem;
            padding: 1.5rem;
        }

        .profile-header {
            flex-direction: column;
            gap: 0.75rem;
            align-items: flex-start;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <h1><i class="fas fa-user-circle"></i> My Profile</h1>
        <a href="{{ route('customer.profile.edit') }}" class="btn-edit">
            <i class="fas fa-edit"></i> Edit Profile
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="profile-section">
        <h3 class="section-title">
            <i class="fas fa-building"></i>
            Business Information
        </h3>
        
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Company Name</div>
                <div class="info-value">{{ $customer->business_name ?? 'Not Set' }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">Contact Person</div>
                <div class="info-value">{{ $customer->contact_person ?? 'Not Set' }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">Contact Number</div>
                <div class="info-value">{{ $customer->phone ?? 'Not Set' }}</div>
            </div>

            <div class="info-item">
                <div class="info-label">Business Type</div>
                <div class="info-value">{{ ucfirst($customer->customer_type ?? 'Not Set') }}</div>
            </div>

            <div class="info-item full-width">
                <div class="info-label">Address</div>
                <div class="info-value">{{ $customer->address ?? 'Not Set' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
