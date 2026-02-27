@extends('layouts.admin')

@section('title', 'Track Delivery #' . $delivery->id)

@section('page-title', 'Live GPS Tracking')

@section('styles')
{{-- Leaflet.js CSS --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #delivery-map {
        height: 480px;
        width: 100%;
        border-radius: 10px;
        z-index: 0;
    }
    .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
    .info-card { background: #fff; border-radius: 10px; padding: 1.25rem; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
    .info-label { font-size: .78rem; color: #6c757d; text-transform: uppercase; letter-spacing: .05em; margin-bottom: .3rem; }
    .info-value { font-size: 1.05rem; font-weight: 600; color: #212529; }
    .status-badge { display:inline-block; padding:.3rem .8rem; border-radius:20px; font-size:.85rem; font-weight:600; }
    .pulse-dot { display:inline-block; width:10px; height:10px; border-radius:50%; background:#198754; margin-right:.4rem; animation: pulse 1.5s infinite; }
    @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.3)} }
</style>
@endsection

@section('content')
<div style="padding:1.5rem;">

    {{-- Back button --}}
    <a href="{{ route('admin.deliveries.index') }}" style="display:inline-flex;align-items:center;gap:.4rem;color:#6c757d;text-decoration:none;font-size:.9rem;margin-bottom:1.25rem;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        Back to Deliveries
    </a>

    @if(session('success'))
        <div style="background:#d1e7dd;color:#0a3622;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;border:1px solid #a3cfbb;">{{ session('success') }}</div>
    @endif

    {{-- Info cards --}}
    <div class="info-grid" style="margin-bottom:1.25rem;">
        <div class="info-card">
            <div class="info-label">Invoice</div>
            <div class="info-value">
                <a href="{{ route('admin.billing.show', $delivery->invoice_id) }}" style="color:#1a73e8;text-decoration:none;">
                    {{ $delivery->invoice->invoice_number ?? '—' }}
                </a>
            </div>
        </div>
        <div class="info-card">
            <div class="info-label">Customer</div>
            <div class="info-value">{{ $delivery->customer->business_name ?? '—' }}</div>
            <div style="font-size:.82rem;color:#6c757d;margin-top:.2rem;">{{ $delivery->customer->address ?? '' }}</div>
        </div>
        <div class="info-card">
            <div class="info-label">Driver</div>
            <div class="info-value">{{ $delivery->driver->name ?? 'Unassigned' }}</div>
        </div>
        <div class="info-card">
            <div class="info-label">Status</div>
            @php
                $colors = ['pending'=>['#fff3cd','#856404'],'in_transit'=>['#cff4fc','#055160'],'delivered'=>['#d1e7dd','#0a3622'],'cancelled'=>['#f8d7da','#58151c']];
                $c = $colors[$delivery->status] ?? ['#e9ecef','#495057'];
            @endphp
            <span class="status-badge" style="background:{{ $c[0] }};color:{{ $c[1] }};">
                @if($delivery->status === 'in_transit')<span class="pulse-dot"></span>@endif
                {{ $delivery->status_label }}
            </span>
        </div>
        @if($delivery->started_at)
        <div class="info-card">
            <div class="info-label">Started</div>
            <div class="info-value" style="font-size:.95rem;">{{ $delivery->started_at->format('M d, Y h:i A') }}</div>
        </div>
        @endif
        @if($delivery->delivered_at)
        <div class="info-card">
            <div class="info-label">Delivered</div>
            <div class="info-value" style="font-size:.95rem;">{{ $delivery->delivered_at->format('M d, Y h:i A') }}</div>
        </div>
        @endif
    </div>

    {{-- Map --}}
    <div style="background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.08);padding:1.25rem;margin-bottom:1.25rem;">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <h3 style="margin:0;font-size:1rem;font-weight:600;">
                @if($delivery->status === 'in_transit')
                    <span class="pulse-dot"></span> Live Tracking
                @else
                    Map View
                @endif
            </h3>
            <span id="last-update" style="font-size:.82rem;color:#6c757d;"></span>
        </div>
        <div id="delivery-map"></div>
        @if($delivery->status === 'pending')
            <div style="text-align:center;padding:1rem;color:#856404;background:#fff3cd;border-radius:8px;margin-top:.75rem;">
                ⏳ Waiting for driver to start the delivery and share their location.
            </div>
        @elseif($delivery->status === 'delivered')
            <div style="text-align:center;padding:1rem;color:#0a3622;background:#d1e7dd;border-radius:8px;margin-top:.75rem;">
                ✅ Delivery completed. Showing last known location.
            </div>
        @endif
    </div>

    {{-- Invoice items --}}
    @if($delivery->invoice && $delivery->invoice->items->count())
    <div style="background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.08);padding:1.25rem;">
        <h3 style="margin:0 0 1rem;font-size:1rem;font-weight:600;">Delivery Contents</h3>
        <table style="width:100%;border-collapse:collapse;font-size:.88rem;">
            <thead>
                <tr style="background:#f8f9fa;border-bottom:2px solid #dee2e6;">
                    <th style="padding:.6rem .9rem;text-align:left;font-weight:600;color:#495057;">Product</th>
                    <th style="padding:.6rem .9rem;text-align:right;font-weight:600;color:#495057;">Qty</th>
                    <th style="padding:.6rem .9rem;text-align:right;font-weight:600;color:#495057;">Unit Price</th>
                    <th style="padding:.6rem .9rem;text-align:right;font-weight:600;color:#495057;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($delivery->invoice->items as $item)
                <tr style="border-bottom:1px solid #f0f0f0;">
                    <td style="padding:.6rem .9rem;">{{ $item->description }}</td>
                    <td style="padding:.6rem .9rem;text-align:right;">{{ $item->quantity }}</td>
                    <td style="padding:.6rem .9rem;text-align:right;">₱{{ number_format($item->unit_price, 2) }}</td>
                    <td style="padding:.6rem .9rem;text-align:right;font-weight:600;">₱{{ number_format($item->total_price, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Reassign driver --}}
    @if(in_array($delivery->status, ['pending']))
    <div style="background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.08);padding:1.25rem;margin-top:1.25rem;">
        <h3 style="margin:0 0 1rem;font-size:1rem;font-weight:600;">Reassign Driver</h3>
        <form method="POST" action="{{ route('admin.deliveries.reassign', $delivery->id) }}" style="display:flex;gap:.75rem;align-items:center;flex-wrap:wrap;">
            @csrf @method('PATCH')
            <select name="user_id" required style="padding:.45rem .75rem;border:1px solid #dee2e6;border-radius:6px;font-size:.9rem;flex:1;min-width:200px;">
                <option value="">Select a driver...</option>
                @foreach(\App\Models\User::where('role','delivery_personnel')->orderBy('name')->get() as $driver)
                    <option value="{{ $driver->id }}" {{ $delivery->assigned_user_id == $driver->id ? 'selected' : '' }}>{{ $driver->name }}</option>
                @endforeach
            </select>
            <button type="submit" style="padding:.45rem 1rem;background:#1a73e8;color:#fff;border:none;border-radius:6px;cursor:pointer;">Reassign</button>
        </form>
    </div>
    @endif

</div>

{{-- Leaflet + Geoapify --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const GEOAPIFY_KEY = '328a40dae9644da6a37cd0a608800fa2';
const DELIVERY_ID  = {{ $delivery->id }};
const STATUS       = '{{ $delivery->status }}';

// Default center: Batangas Philippines
const defaultLat = {{ $delivery->latestLocation?->latitude ?? 13.9565 }};
const defaultLng = {{ $delivery->latestLocation?->longitude ?? 121.0166 }};

const map = L.map('delivery-map').setView([defaultLat, defaultLng], 15);

L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${GEOAPIFY_KEY}`, {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, © <a href="https://www.geoapify.com/">Geoapify</a>',
    maxZoom: 20,
}).addTo(map);

// Truck icon
const truckIcon = L.divIcon({
    html: `<div style="background:#1a73e8;border-radius:50%;width:36px;height:36px;display:flex;align-items:center;justify-content:center;border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.3);">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2">
            <rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
            <circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
        </svg>
    </div>`,
    className: '',
    iconSize: [36, 36],
    iconAnchor: [18, 18],
});

let marker = null;

@if($delivery->latestLocation)
    marker = L.marker([defaultLat, defaultLng], { icon: truckIcon }).addTo(map);
    marker.bindPopup('<b>Delivery Truck</b><br>{{ $delivery->driver?->name ?? "Driver" }}').openPopup();
@endif

// Poll for live updates if in transit
@if($delivery->status === 'in_transit')
function poll() {
    fetch('/admin/deliveries/{{ $delivery->id }}/location')
        .then(r => r.json())
        .then(data => {
            if (!data.found) return;

            const latlng = [data.latitude, data.longitude];

            if (!marker) {
                marker = L.marker(latlng, { icon: truckIcon }).addTo(map);
                marker.bindPopup('<b>Delivery Truck</b>').openPopup();
            } else {
                marker.setLatLng(latlng);
            }

            map.panTo(latlng);
            document.getElementById('last-update').textContent = 'Updated ' + data.updated;

            // If delivered, stop polling
            if (data.status === 'delivered') {
                clearInterval(pollInterval);
                document.getElementById('last-update').textContent = '✅ Delivered';
            }
        });
}
poll();
const pollInterval = setInterval(poll, 5000);
@endif
</script>
@endsection
