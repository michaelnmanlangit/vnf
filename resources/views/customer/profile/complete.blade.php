@extends('layouts.customer')

@section('title', 'Complete Your Profile')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<style>
    .profile-complete-container {
        max-width: 900px;
        margin: 1.5rem auto;
        background: white;
        border-radius: 12px;
        padding: 1.5rem 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .profile-header {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .profile-header h1 {
        color: #1a202c;
        font-size: 1.5rem;
        margin-bottom: 0.3rem;
    }

    .profile-header p {
        color: #64748b;
        font-size: 0.9rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        color: #1a202c;
        margin-bottom: 0.4rem;
        font-size: 0.9rem;
    }

    .form-group label.required::after {
        content: ' *';
        color: #e74c3c;
    }

    .form-control, .form-select {
        width: 100%;
        padding: 0.6rem 0.8rem;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-size: 0.9rem;
        font-family: 'Poppins', sans-serif;
        transition: all 0.2s ease;
        color: #1a202c;
    }

    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: #4169E1;
        box-shadow: 0 0 0 2px rgba(65, 105, 225, 0.1);
    }

    #map {
        height: 250px;
        border-radius: 8px;
        border: 1px solid #ddd;
        margin-bottom: 1rem;
    }

    .map-search-box {
        position: relative;
        margin-bottom: 0.75rem;
    }

    .map-search-box input {
        padding-right: 2.5rem;
    }

    .map-search-box .search-icon {
        position: absolute;
        right: 0.75rem;
        top: 50%;
        transform: translateY(-50%);
        color: #7f8c8d;
    }

    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Poppins', sans-serif;
    }

    .btn-primary {
        background: linear-gradient(135deg, #1e3ba8, #4169E1);
        color: white;
        width: 100%;
        margin-top: 0.5rem;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(65, 105, 225, 0.3);
    }

    .alert {
        padding: 0.75rem;
        border-radius: 6px;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }

    .alert-success {
        background: #d4edda;
        border-left: 3px solid #28a745;
        color: #155724;
    }

    .alert-error {
        background: #f8d7da;
        border-left: 3px solid #dc3545;
        color: #721c24;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
        .profile-complete-container {
            margin: 1rem;
            padding: 1.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="profile-complete-container">
    <div class="profile-header">
        <h1>Complete Your Profile</h1>
        <p>Provide your business information to continue</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            <ul style="margin:0;padding-left:20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('customer.profile.store') }}" method="POST" id="profileForm">
        @csrf

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" 
                   value="{{ auth()->user()->name }}" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
            <small style="color: #64748b; font-size: 0.8rem;">Name from your account</small>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="company_name" class="required">Company Name</label>
                <input type="text" class="form-control" id="company_name" name="company_name" 
                       value="{{ old('company_name') }}" required>
            </div>

            <div class="form-group">
                <label for="contact_number" class="required">Contact Number</label>
                <input type="tel" class="form-control" id="contact_number" name="contact_number" 
                       value="{{ old('contact_number') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="business_type" class="required">Business Type</label>
            <select class="form-select" id="business_type" name="business_type" required>
                <option value="">Select type</option>
                <option value="restaurant" {{ old('business_type') === 'restaurant' ? 'selected' : '' }}>Restaurant</option>
                <option value="hotel" {{ old('business_type') === 'hotel' ? 'selected' : '' }}>Hotel</option>
                <option value="retail" {{ old('business_type') === 'retail' ? 'selected' : '' }}>Retail</option>
                <option value="wholesale" {{ old('business_type') === 'wholesale' ? 'selected' : '' }}>Wholesale</option>
                <option value="catering" {{ old('business_type') === 'catering' ? 'selected' : '' }}>Catering</option>
                <option value="other" {{ old('business_type') === 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="address" class="required">Address</label>
            <div class="map-search-box">
                <input type="text" class="form-control" id="address" name="address" 
                       value="{{ old('address') }}" placeholder="Click on map to select location" required readonly>
                <i class="fas fa-map-marker-alt search-icon"></i>
            </div>
            <div id="map"></div>
        </div>

        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-check-circle"></i> Complete Profile
        </button>
    </form>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script>
    // Initialize map
    const map = L.map('map').setView([14.5995, 120.9842], 11); // Default: Manila

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Marker
    let marker = null;
    let geocodeTimeout = null;

    // Add geocoder control with search functionality
    const geocoder = L.Control.Geocoder.nominatim();
    const searchControl = L.Control.geocoder({
        geocoder: geocoder,
        defaultMarkGeocode: false,
        placeholder: 'Search address...',
        errorMessage: 'Location not found'
    })
    .on('markgeocode', function(e) {
        const latlng = e.geocode.center;
        updateMarker(latlng);
        document.getElementById('address').value = e.geocode.name;
        map.setView(latlng, 16);
    })
    .addTo(map);

    // Click on map to set location
    map.on('click', function(e) {
        updateMarker(e.latlng);
        reverseGeocodeOptimized(e.latlng);
    });

    function updateMarker(latlng) {
        if (marker) {
            map.removeLayer(marker);
        }
        marker = L.marker(latlng).addTo(map);
        document.getElementById('latitude').value = latlng.lat.toFixed(8);
        document.getElementById('longitude').value = latlng.lng.toFixed(8);
    }

    function reverseGeocodeOptimized(latlng) {
        const addressField = document.getElementById('address');
        
        // Clear any pending geocode requests
        if (geocodeTimeout) {
            clearTimeout(geocodeTimeout);
        }
        
        // Show loading message
        const originalValue = addressField.value;
        addressField.placeholder = 'Loading address...';
        addressField.style.opacity = '0.6';
        
        // Debounce the geocoding request
        geocodeTimeout = setTimeout(() => {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}&zoom=18&addressdetails=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        addressField.value = data.display_name;
                        addressField.placeholder = 'Click on map to select location';
                        addressField.style.opacity = '1';
                        console.log('Address loaded:', data.display_name);
                    } else {
                        addressField.placeholder = 'Click on map to select location';
                        addressField.style.opacity = '1';
                        console.log('No address found');
                    }
                })
                .catch(error => {
                    console.error('Geocoding error:', error);
                    addressField.placeholder = 'Click on map to select location';
                    addressField.style.opacity = '1';
                });
        }, 300);
    }

    // Set initial marker if coordinates exist
    const oldLat = document.getElementById('latitude').value;
    const oldLng = document.getElementById('longitude').value;
    if (oldLat && oldLng) {
        const latlng = L.latLng(oldLat, oldLng);
        updateMarker(latlng);
        map.setView(latlng, 16);
    }
</script>
@endsection
