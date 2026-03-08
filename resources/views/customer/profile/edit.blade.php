@extends('layouts.customer')

@section('title', 'Edit Profile')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<style>
    .profile-edit-container {
        max-width: 900px;
        margin: 1.5rem auto;
        background: white;
        border-radius: 12px;
        padding: 1.5rem 2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .profile-header {
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
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
        color: #64748b;
    }

    .btn-group {
        display: flex;
        gap: 1rem;
        margin-top: 0.5rem;
    }

    .btn {
        flex: 1;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        font-family: 'Poppins', sans-serif;
        text-decoration: none;
        text-align: center;
    }

    .btn-primary {
        background: linear-gradient(135deg, #1e3ba8, #4169E1);
        color: white;
    }

    .btn-primary:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(65, 105, 225, 0.3);
    }

    .btn-primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        background: #95a5a6;
    }

    .btn-cancel {
        background: white;
        border: 2px solid #ddd;
        color: #64748b;
    }

    .btn-cancel:hover {
        border-color: #4169E1;
        color: #4169E1;
    }

    .alert {
        padding: 0.75rem;
        border-radius: 6px;
        margin-bottom: 1rem;
        font-size: 0.9rem;
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
        .profile-edit-container {
            margin: 1rem;
            padding: 1.5rem;
        }
        .btn-group {
            flex-direction: column;
        }
    }
</style>
@endsection

@section('content')
<div class="profile-edit-container">
    <div class="profile-header">
        <h1><i class="fas fa-user-edit"></i> Edit Profile</h1>
        <p>Update your business information</p>
    </div>

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

    <form action="{{ route('customer.profile.update') }}" method="POST" id="profileForm">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" name="name" 
                   value="{{ old('name', auth()->user()->name) }}">
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="company_name">Company Name</label>
                <input type="text" class="form-control" id="company_name" name="company_name" 
                       value="{{ old('company_name', $customer->business_name ?? '') }}" required>
            </div>

            <div class="form-group">
                <label for="contact_number">Contact Number</label>
                <input type="tel" class="form-control" id="contact_number" name="contact_number" 
                       value="{{ old('contact_number', $customer->phone ?? '') }}" required>
            </div>
        </div>

        <div class="form-group">
            <label for="business_type">Business Type</label>
            <select class="form-select" id="business_type" name="business_type" required>
                <option value="">Select type</option>
                @php
                    $currentType = old('business_type', $customer->customer_type ?? $customer->profile->business_type ?? '');
                    // Convert to lowercase for comparison
                    $currentTypeLower = strtolower($currentType);
                @endphp
                <option value="Restaurant" {{ strtolower($currentType) === 'restaurant' ? 'selected' : '' }}>Restaurant</option>
                <option value="Hotel" {{ strtolower($currentType) === 'hotel' ? 'selected' : '' }}>Hotel</option>
                <option value="Retail" {{ strtolower($currentType) === 'retail' ? 'selected' : '' }}>Retail</option>
                <option value="Wholesale" {{ strtolower($currentType) === 'wholesale' ? 'selected' : '' }}>Wholesale</option>
                <option value="Catering" {{ strtolower($currentType) === 'catering' ? 'selected' : '' }}>Catering</option>
                <option value="Other" {{ strtolower($currentType) === 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="address">Address</label>
            <div class="map-search-box">
                <input type="text" class="form-control" id="address" name="address" 
                       value="{{ old('address', $customer->address) }}" placeholder="Click on map to select location" required readonly>
                <i class="fas fa-map-marker-alt search-icon"></i>
            </div>
            <div id="map"></div>
        </div>

        <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $customer->latitude ?? $customer->profile->latitude ?? '') }}">
        <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $customer->longitude ?? $customer->profile->longitude ?? '') }}">

        <div class="btn-group">
            <a href="{{ route('customer.profile.show') }}" class="btn btn-cancel">
                <i class="fas fa-times"></i> Cancel
            </a>
            <button type="submit" class="btn btn-primary" id="saveButton">
                <i class="fas fa-check"></i> Save Changes
            </button>
        </div>
    </form>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script>
    // Initialize map
    const latValue = document.getElementById('latitude').value;
    const lngValue = document.getElementById('longitude').value;
    console.log('Latitude from input:', latValue);
    console.log('Longitude from input:', lngValue);
    
    const existingLat = parseFloat(latValue) || 14.5995;
    const existingLng = parseFloat(lngValue) || 120.9842;
    
    console.log('Parsed Latitude:', existingLat);
    console.log('Parsed Longitude:', existingLng);
    
    const map = L.map('map').setView([existingLat, existingLng], latValue && lngValue ? 16 : 11);

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
        // Programmatic value changes don't fire 'change' events, so trigger manually
        if (typeof checkForChanges === 'function') checkForChanges();
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
    if (latValue && lngValue) {
        const latlng = L.latLng(existingLat, existingLng);
        if (marker) map.removeLayer(marker);
        marker = L.marker(latlng).addTo(map);
        document.getElementById('latitude').value = latlng.lat.toFixed(8);
        document.getElementById('longitude').value = latlng.lng.toFixed(8);
    }

    // Track form changes to enable/disable save button
    const saveButton = document.getElementById('saveButton');
    const formFields = {
        name: document.getElementById('name'),
        company_name: document.getElementById('company_name'),
        contact_number: document.getElementById('contact_number'),
        business_type: document.getElementById('business_type'),
        address: document.getElementById('address'),
        latitude: document.getElementById('latitude'),
        longitude: document.getElementById('longitude')
    };

    // Store initial values AFTER the map has set the formatted coordinates
    const initialValues = {
        name: formFields.name.value,
        company_name: formFields.company_name.value,
        contact_number: formFields.contact_number.value,
        business_type: formFields.business_type.value,
        address: formFields.address.value,
        latitude: formFields.latitude.value,
        longitude: formFields.longitude.value
    };

    // Function to check if form has changes
    function checkForChanges() {
        let hasChanges = false;
        
        for (let field in formFields) {
            if (formFields[field].value !== initialValues[field]) {
                hasChanges = true;
                break;
            }
        }
        
        saveButton.disabled = !hasChanges;
    }

    // Add event listeners to all form fields
    formFields.name.addEventListener('input', checkForChanges);
    formFields.company_name.addEventListener('input', checkForChanges);
    formFields.contact_number.addEventListener('input', checkForChanges);
    formFields.business_type.addEventListener('change', checkForChanges);
    formFields.address.addEventListener('input', checkForChanges);
    formFields.latitude.addEventListener('change', checkForChanges);
    formFields.longitude.addEventListener('change', checkForChanges);

    // Check initial state
    checkForChanges();
</script>
@endsection
