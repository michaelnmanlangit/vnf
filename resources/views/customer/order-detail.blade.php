@extends('layouts.customer')

@section('title', 'Order Details')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .order-detail-container {
        max-width: 1000px;
        margin: 2rem auto;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: white;
        text-decoration: none;
        padding: 0.75rem 1.5rem;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .back-button:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .order-header {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .header-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .order-title {
        color: #2c3e50;
        font-size: 2rem;
        font-weight: 600;
    }

    .status-badge {
        padding: 0.75rem 1.5rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.95rem;
        text-transform: uppercase;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-confirmed {
        background: #cfe2ff;
        color: #084298;
    }

    .status-in_transit {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-out_for_delivery {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-delivered {
        background: #d4edda;
        color: #155724;
    }

    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
    }

    .order-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 2px solid #ecf0f1;
    }

    .meta-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .meta-label {
        color: #7f8c8d;
        font-size: 0.9rem;
        font-weight: 500;
    }

    .meta-value {
        color: #2c3e50;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .order-content {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .order-items-section {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .section-title {
        color: #2c3e50;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    .item-card {
        display: flex;
        gap: 1.5rem;
        padding: 1.5rem;
        border: 2px solid #ecf0f1;
        border-radius: 15px;
        margin-bottom: 1rem;
    }

    .item-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 10px;
        background: #f8f9fa;
    }

    .item-details {
        flex: 1;
    }

    .item-name {
        color: #2c3e50;
        font-weight: 600;
        font-size: 1.1rem;
        margin-bottom: 0.5rem;
    }

    .item-qty {
        color: #7f8c8d;
        font-size: 0.95rem;
    }

    .item-price {
        color: #27ae60;
        font-weight: 700;
        font-size: 1.2rem;
    }

    .order-summary-section {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        height: fit-content;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        color: #7f8c8d;
    }

    .summary-row.total {
        border-top: 2px solid #ecf0f1;
        margin-top: 1rem;
        padding-top: 1rem;
        font-size: 1.4rem;
        font-weight: 700;
        color: #2c3e50;
    }

    .delivery-section {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        margin-top: 2rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    /* Map styles */
    #delivery-map { 
        height: 440px; 
        width: 100%; 
        border-radius: 15px; 
        z-index: 0;
        margin-top: 1rem;
    }
    
    .map-header { 
        display: flex; 
        align-items: center; 
        justify-content: space-between; 
        margin-bottom: 1rem; 
    }
    
    .map-title  { 
        font-size: 1rem; 
        font-weight: 700; 
        color: #2c3e50; 
        display: flex; 
        align-items: center; 
        gap: 0.5rem; 
    }
    
    #last-update { 
        font-size: 0.875rem; 
        color: #7f8c8d; 
    }
    
    .pulse-dot { 
        display: inline-block; 
        width: 10px; 
        height: 10px; 
        border-radius: 50%; 
        background: currentColor; 
        animation: gps-pulse 1.4s infinite; 
    }
    
    @keyframes gps-pulse { 
        0%, 100% { opacity: 1; transform: scale(1); } 
        50% { opacity: 0.4; transform: scale(1.4); } 
    }

    .delivery-status {
        background: #e8f4fd;
        padding: 1rem 1.25rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        display: inline-block;
        min-width: 220px;
        max-width: 100%;
    }

    .delivery-status h4 {
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .delivery-status p {
        color: #7f8c8d;
        margin: 0;
    }

    .delivery-address {
        background: #f8f9fa;
        padding: 1.5rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
    }

    .delivery-address h4 {
        color: #2c3e50;
        margin-bottom: 0.75rem;
    }

    .delivery-address p {
        color: #7f8c8d;
        line-height: 1.6;
        margin: 0;
    }

    @media (max-width: 968px) {
        .order-content {
            grid-template-columns: 1fr;
        }

        .header-top {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .order-meta {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .item-card {
            flex-direction: column;
        }

        .item-image {
            width: 100%;
            height: 150px;
        }
    }
</style>
@endsection

@section('content')
<div class="order-detail-container">
    <a href="{{ route('customer.orders') }}" class="back-button">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>

    <div class="order-header">
        <div class="header-top">
            <h1 class="order-title">{{ $order->order_number }}</h1>
            @php
                $headerDelivery = $order->invoice->delivery ?? null;
                $headerDeliveryStatus = $headerDelivery ? ucwords(str_replace('_', ' ', $headerDelivery->status)) : 'Pending';
                $headerRawStatus = $headerDelivery->status ?? 'pending';
                $statusStyle = match(true) {
                    in_array($order->status, ['out_for_delivery','in_transit']) => 'background:#dbeafe;color:#1e40af;',
                    $order->status === 'delivered' => 'background:#dcfce7;color:#166534;',
                    default => 'background:#fef9c3;color:#854d0e;',
                };
                $orderStatusLabel = match($order->status) {
                    'out_for_delivery', 'in_transit' => 'In Transit',
                    'confirmed', 'preparing', 'ready_for_delivery' => 'Pending',
                    default => ucwords(str_replace('_', ' ', $order->status)),
                };
                $orderStatusClass = in_array($order->status, ['out_for_delivery','in_transit']) ? 'status-in_transit' : 'status-' . $order->status;
            @endphp
            <div class="delivery-status" style="margin-bottom:0;{{ $statusStyle }}">
                <h4 style="color:inherit;">Status: {{ $orderStatusLabel }}</h4>
                @if($headerDelivery && $headerDelivery->driver)
                    <p style="color:inherit;opacity:.8;"><i class="fas fa-user"></i> Driver: {{ $headerDelivery->driver->name }}</p>
                @endif
            </div>
        </div>

        <div class="order-meta">
            <div class="meta-item">
                <span class="meta-label">Order Date</span>
                <span class="meta-value">{{ $order->created_at->format('M d, Y') }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Payment Method</span>
                <span class="meta-value">{{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</span>
            </div>
            <div class="meta-item">
                <span class="meta-label">Payment Status</span>
                <span class="meta-value">{{ ucwords($order->payment_status) }}</span>
            </div>
        </div>
    </div>

    <div class="order-content">
        <div>
            <div class="order-items-section">
                <h2 class="section-title"><i class="fas fa-box"></i> Order Items</h2>
                
                @foreach($order->items as $item)
                    <div class="item-card">
                        <img src="{{ $item->inventory && $item->inventory->product_image ? asset('storage/' . $item->inventory->product_image) : asset('images/default-product.png') }}" 
                             alt="{{ $item->product_name }}" 
                             class="item-image">
                        
                        <div class="item-details">
                            <h3 class="item-name">{{ $item->product_name }}</h3>
                            <div class="item-qty">{{ $item->quantity }} {{ $item->unit }} × ₱{{ number_format($item->price_per_unit, 2) }}</div>
                        </div>

                        <div class="item-price">₱{{ number_format($item->subtotal, 2) }}</div>
                    </div>
                @endforeach

                {{-- Unified price breakdown --}}
                <div style="border-top: 2px solid #ecf0f1; margin-top: 1rem; padding-top: 1rem;">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>₱{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    @if($order->invoice && $order->invoice->tax > 0)
                    <div class="summary-row">
                        <span>Tax (12% VAT)</span>
                        <span>₱{{ number_format($order->invoice->tax, 2) }}</span>
                    </div>
                    @endif
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>₱{{ number_format($order->subtotal + ($order->invoice->tax ?? 0), 2) }}</span>
                    </div>
                </div>

                {{-- Payment info --}}
                @if($order->invoice && $order->invoice->payments && $order->invoice->payments->count() > 0)
                <div style="border-top: 2px solid #ecf0f1; margin-top: 1rem; padding-top: 1rem;">
                    @foreach($order->invoice->payments as $payment)
                    <div style="display:flex;justify-content:space-between;align-items:center;background:#f0faf4;padding:.85rem 1rem;border-radius:9px;">
                        <div>
                            <div style="color:#2c3e50;font-weight:600;">{{ ucwords(str_replace('_',' ',$payment->payment_method)) }} — Paid</div>
                            <div style="color:#7f8c8d;font-size:.85rem;">{{ $payment->payment_date->format('M d, Y') }}</div>
                            @if($payment->payment_reference)
                            <div style="color:#7f8c8d;font-size:.85rem;">Ref: {{ $payment->payment_reference }}</div>
                            @endif
                        </div>
                        <div style="color:#27ae60;font-weight:700;font-size:1.05rem;">₱{{ number_format($payment->amount, 2) }}</div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <div class="delivery-section">
                <h2 class="section-title"><i class="fas fa-truck"></i> Delivery Tracking</h2>

                @if($order->invoice && $order->invoice->delivery)
                    @php $delivery = $order->invoice->delivery; @endphp

                    <div class="map-header">
                        <div class="map-title">
                            @if($delivery->status === 'in_transit')
                                <span class="pulse-dot" style="background:#055160;"></span> Live Tracking
                            @else
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#3498db" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                Map View
                            @endif
                        </div>
                        <span id="last-update">
                            @if($delivery->latestLocation) Updated {{ $delivery->latestLocation->created_at->diffForHumans() }} @endif
                        </span>
                    </div>

                    <div id="delivery-map"></div>

                    @if($delivery->status === 'pending')
                        <div style="text-align:center;padding:.75rem;color:#856404;background:#fff3cd;border-radius:8px;margin-top:1rem;font-size:.9rem;">
                            Waiting for delivery to start. You'll be able to track your order once it's out for delivery.
                        </div>
                    @elseif($delivery->status === 'delivered')
                        <div style="text-align:center;padding:.75rem;color:#0a3622;background:#d1e7dd;border-radius:8px;margin-top:1rem;font-size:.9rem;">
                            Delivery completed! Thank you for your order.
                        </div>
                    @endif
                @else
                    <p style="color:#7f8c8d;font-size:.9rem;">Your delivery will be scheduled shortly.</p>
                @endif

                <div class="delivery-address">
                    <h4>Delivery Address</h4>
                    <p>{{ $order->delivery_address }}</p>
                    @if($order->delivery_instructions)
                        <p style="margin-top: 0.75rem;"><strong>Instructions:</strong> {{ $order->delivery_instructions }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($order->invoice && $order->invoice->delivery)
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const GEOAPIFY_KEY = '328a40dae9644da6a37cd0a608800fa2';
const DELIVERY_ID  = {{ $order->invoice->delivery->id }};

// Initialize map
@php 
    $delivery = $order->invoice->delivery;
    $defaultLat = $delivery->latestLocation?->latitude ?? Auth::user()->customer_profile?->latitude ?? 14.1077;
    $defaultLng = $delivery->latestLocation?->longitude ?? Auth::user()->customer_profile?->longitude ?? 121.1411;
    $defaultZoom = $delivery->latestLocation ? 15 : 13;
@endphp

const defaultLat = {{ $defaultLat }};
const defaultLng = {{ $defaultLng }};
const defaultZoom = {{ $defaultZoom }};
const map = L.map('delivery-map', { attributionControl: false }).setView([defaultLat, defaultLng], defaultZoom);

L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${GEOAPIFY_KEY}`, {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, © <a href="https://www.geoapify.com/">Geoapify</a>',
    maxZoom: 20,
}).addTo(map);

// Truck icon for delivery vehicle
const truckIcon = L.divIcon({
    html: `<div style="background:#3498db;border-radius:50%;width:38px;height:38px;display:flex;align-items:center;justify-content:center;border:3px solid #fff;box-shadow:0 2px 10px rgba(0,0,0,.25);">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
            <circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
        </svg></div>`,
    className: '', iconSize: [38, 38], iconAnchor: [19, 19],
});

let marker = null;
@if($delivery->latestLocation)
marker = L.marker([defaultLat, defaultLng], { icon: truckIcon }).addTo(map);
marker.bindPopup('<b>{{ $delivery->driver?->name ?? "Driver" }}</b><br>Last known location').openPopup();
@endif

// Destination marker (customer location)
@if(Auth::user()->customer_profile?->latitude && Auth::user()->customer_profile?->longitude)
const destIcon = L.divIcon({
    html: `<div style="position:relative;width:28px;height:38px;">
        <div style="background:#e53e3e;width:28px;height:28px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.3);"></div>
        <div style="position:absolute;top:7px;left:7px;width:10px;height:10px;background:#fff;border-radius:50%;transform:none;"></div>
    </div>`,
    className: '', iconSize: [28, 38], iconAnchor: [14, 38],
});
const destLat = {{ Auth::user()->customer_profile->latitude }};
const destLng = {{ Auth::user()->customer_profile->longitude }};
const destMarker = L.marker([destLat, destLng], { icon: destIcon })
    .addTo(map)
    .bindPopup('<b>📍 Your Location</b><br><b>{{ addslashes(Auth::user()->customer_profile->business_name ?? "") }}</b><br>{{ addslashes(Auth::user()->customer_profile->address ?? "") }}');

@if($delivery->latestLocation)
// Fit map to show both truck and destination
map.fitBounds([
    [defaultLat, defaultLng],
    [destLat, destLng]
], { padding: [50, 50] });
@else
map.setView([destLat, destLng], 14);
@endif
@endif

// Live polling for in-transit deliveries
@if($delivery->status === 'in_transit')
function pollLocation() {
    fetch(`/customer/delivery/${DELIVERY_ID}/location`, { 
        headers: { 'X-Requested-With': 'XMLHttpRequest' } 
    })
    .then(r => r.json())
    .then(data => {
        if (!data.found) return;
        const latlng = [data.latitude, data.longitude];
        if (!marker) { 
            marker = L.marker(latlng, { icon: truckIcon }).addTo(map); 
        } else { 
            marker.setLatLng(latlng); 
        }
        map.panTo(latlng);
        document.getElementById('last-update').textContent = 'Updated ' + data.updated;
        
        if (data.status === 'delivered') {
            clearInterval(pollInterval);
            document.getElementById('last-update').textContent = 'Delivered';
            location.reload(); // Refresh page to show delivery completed status
        }
    }).catch(() => {});
}
pollLocation();
const pollInterval = setInterval(pollLocation, 5000); // Poll every 5 seconds
@endif
</script>
@endif
@endsection
