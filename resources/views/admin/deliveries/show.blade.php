@extends('layouts.admin')

@section('title', 'Track Delivery #' . $delivery->id)
@section('page-title', 'GPS Tracking')

@section('styles')
<link rel="stylesheet" href="/build/assets/billing-mM0IVGZh.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    /* â”€â”€ Layout â”€â”€ */
    .track-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 1.25rem;
        align-items: start;
    }
    @media (max-width: 900px) { .track-grid { grid-template-columns: 1fr; } }

    /* â”€â”€ Map â”€â”€ */
    #delivery-map { height: 440px; width: 100%; border-radius: 10px; z-index: 0; }

    /* â”€â”€ Cards â”€â”€ */
    .track-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 1px 4px rgba(0,0,0,.08);
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.25rem;
    }
    .track-card:last-child { margin-bottom: 0; }
    .track-card-title {
        font-size: .78rem;
        font-weight: 700;
        color: #2c3e50;
        text-transform: uppercase;
        letter-spacing: .05em;
        margin: 0 0 .9rem;
        padding-bottom: .65rem;
        border-bottom: 1px solid #f0f2f5;
    }

    /* â”€â”€ Info rows â”€â”€ */
    .info-row { display:flex; flex-direction:column; gap:.1rem; padding:.5rem 0; border-bottom:1px solid #f0f2f5; }
    .info-row:last-child { border-bottom: none; }
    .info-label { font-size:.68rem; color:#7f8c8d; text-transform:uppercase; letter-spacing:.05em; font-weight:600; }
    .info-value { font-size:.9rem; color:#2c3e50; font-weight:600; }
    .info-sub   { font-size:.78rem; color:#6c757d; }

    /* â”€â”€ Status pill â”€â”€ */
    .status-pill { display:inline-flex; align-items:center; gap:.35rem; padding:.28rem .7rem; border-radius:999px; font-size:.72rem; font-weight:700; }
    .status-pill.pending    { background:#fef9e7; color:#b7950b; }
    .status-pill.in_transit { background:#cff4fc; color:#055160; }
    .status-pill.delivered  { background:#d5f5e3; color:#1e8449; }
    .status-pill.cancelled  { background:#fde8e8; color:#c0392b; }

    /* â”€â”€ Pulse â”€â”€ */
    .pulse-dot { display:inline-block; width:8px; height:8px; border-radius:50%; background:currentColor; animation:gps-pulse 1.4s infinite; }
    @keyframes gps-pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(1.4)} }

    /* â”€â”€ Driver select â”€â”€ */
    .driver-select { width:100%; padding:.5rem .75rem; border:1px solid #dee2e6; border-radius:8px; font-size:.875rem; color:#2c3e50; margin-bottom:.75rem; outline:none; transition:border-color .15s; }
    .driver-select:focus { border-color:#1a73e8; }

    .btn-assign { width:100%; padding:.55rem; background:#1a73e8; color:#fff; border:none; border-radius:8px; font-size:.875rem; font-weight:600; cursor:pointer; transition:background .15s,opacity .15s; display:flex; align-items:center; justify-content:center; gap:.4rem; }
    .btn-assign:hover { background:#1558c0; }
    .btn-assign:disabled { opacity:.6; cursor:default; }

    /* ── Table ── */
    .contents-table { width:100%; border-collapse:collapse; font-size:.875rem; }
    .contents-table th { background:#f8f9fa; color:#7f8c8d; font-weight:600; font-size:.7rem; text-transform:uppercase; letter-spacing:.04em; padding:.6rem 1rem; border-bottom:1px solid #ecf0f1; }
    .contents-table td { padding:.6rem 1rem; border-bottom:1px solid #f0f2f5; color:#2c3e50; }
    .contents-table tr:last-child td { border-bottom:none; }
    .contents-table tr:hover td { background:#fafbfc; }

    /* â”€â”€ Map header â”€â”€ */
    .map-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:.85rem; }
    .map-title  { font-size:.875rem; font-weight:700; color:#2c3e50; display:flex; align-items:center; gap:.45rem; }
    #last-update { font-size:.78rem; color:#6c757d; }

    /* â”€â”€ Toast â”€â”€ */
    #toast-container { position:fixed; top:1.25rem; right:1.25rem; z-index:9999; display:flex; flex-direction:column; gap:.5rem; }
    .toast { padding:.7rem 1.1rem; border-radius:8px; font-size:.875rem; font-weight:600; color:#fff; box-shadow:0 4px 16px rgba(0,0,0,.15); display:flex; align-items:center; gap:.5rem; animation:toast-in .2s ease; min-width:220px; }
    .toast.success { background:#198754; }
    .toast.error   { background:#dc3545; }
    @keyframes toast-in { from{opacity:0;transform:translateX(20px)} to{opacity:1;transform:translateX(0)} }
    @keyframes spin { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
</style>
@endsection

@section('content')

<div id="toast-container"></div>

<a href="{{ route('admin.deliveries.index') }}" class="manage-customers-btn" style="display:inline-flex;margin-top:-0.5rem;margin-bottom:1rem;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="19" y1="12" x2="5" y2="12"></line>
        <polyline points="12 19 5 12 12 5"></polyline>
    </svg>
    Back to Deliveries
</a>

@if(session('success'))
    <div class="alert alert-success" style="display:flex;align-items:center;gap:.6rem;margin-bottom:1rem;">
        <span>{{ session('success') }}</span>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;margin-left:auto;color:inherit;font-size:1.2rem;">&times;</button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-error" style="display:flex;align-items:center;gap:.6rem;margin-bottom:1rem;">
        <span>{{ session('error') }}</span>
        <button onclick="this.parentElement.remove()" style="background:none;border:none;cursor:pointer;margin-left:auto;color:inherit;font-size:1.2rem;">&times;</button>
    </div>
@endif

<div class="track-grid">

    {{-- â”€â”€ LEFT: Map + Contents â”€â”€ --}}
    <div>
        <div class="track-card">
            <div class="map-header">
                <div class="map-title">
                    @if($delivery->status === 'in_transit')
                        <span class="pulse-dot" style="background:#055160;"></span> Live Tracking
                    @else
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1a73e8" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        Map View
                    @endif
                </div>
                <span id="last-update">
                    @if($delivery->latestLocation) Updated {{ $delivery->latestLocation->created_at->diffForHumans() }} @endif
                </span>
            </div>

            <div id="delivery-map"></div>

            @if($delivery->status === 'pending')
                <div style="text-align:center;padding:.75rem;color:#856404;background:#fff3cd;border-radius:8px;margin-top:.85rem;font-size:.82rem;">
                    Waiting for driver to start the delivery and share their location.
                </div>
            @elseif($delivery->status === 'delivered')
                <div style="text-align:center;padding:.75rem;color:#0a3622;background:#d1e7dd;border-radius:8px;margin-top:.85rem;font-size:.82rem;">
                    Delivery completed. Showing last known location.
                </div>
            @endif
        </div>

        @if($delivery->invoice && $delivery->invoice->items->count())
        <div class="track-card">
            <p class="track-card-title">Delivery Contents</p>
            <div style="overflow-x:auto;">
                <table class="contents-table">
                    <thead>
                        <tr>
                            <th style="text-align:left;">Product</th>
                            <th style="text-align:right;">Qty</th>
                            <th style="text-align:right;">Unit Price</th>
                            <th style="text-align:right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($delivery->invoice->items as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td style="text-align:right;">{{ (int) $item->quantity }} {{ $item->unit }}</td>
                            <td style="text-align:right;">&#8369;{{ number_format($item->unit_price, 2) }}</td>
                            <td style="text-align:right;font-weight:700;">&#8369;{{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" style="text-align:right;font-size:.72rem;color:#7f8c8d;padding:.5rem 1rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Subtotal</td>
                            <td style="text-align:right;padding:.5rem 1rem;color:#2c3e50;">&#8369;{{ number_format($delivery->invoice->subtotal, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align:right;font-size:.72rem;color:#7f8c8d;padding:.5rem 1rem;font-weight:600;text-transform:uppercase;letter-spacing:.04em;">Tax (12%)</td>
                            <td style="text-align:right;padding:.5rem 1rem;color:#2c3e50;">&#8369;{{ number_format($delivery->invoice->tax, 2) }}</td>
                        </tr>
                        <tr style="border-top:2px solid #ecf0f1;">
                            <td colspan="3" style="text-align:right;font-size:.72rem;color:#7f8c8d;padding:.65rem 1rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;">
                                Total Amount <span style="font-size:.68rem;font-weight:600;color:#7f8c8d;">(incl. tax)</span>
                            </td>
                            <td style="text-align:right;font-weight:700;font-size:1rem;color:#1a73e8;padding:.65rem 1rem;">&#8369;{{ number_format($delivery->invoice->total_amount, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        @endif
    </div>

    {{-- â”€â”€ RIGHT: Sidebar â”€â”€ --}}
    <div>
        <div class="track-card">
            <p class="track-card-title">Delivery Info</p>

            <div class="info-row">
                <span class="info-label">Invoice</span>
                <span class="info-value">
                    <a href="{{ route('admin.billing.show', $delivery->invoice_id) }}" style="color:#1a73e8;text-decoration:none;">
                        {{ $delivery->invoice->invoice_number ?? 'â€”' }}
                    </a>
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Customer</span>
                <span class="info-value">{{ $delivery->customer->business_name ?? 'â€”' }}</span>
                @if($delivery->customer?->address)
                    <span class="info-sub">{{ $delivery->customer->address }}</span>
                @endif
            </div>

            <div class="info-row">
                <span class="info-label">Driver</span>
                <span class="info-value" id="driver-name-display">
                    {{ $delivery->driver->name ?? 'Unassigned' }}
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">Status</span>
                <div style="margin-top:.25rem;" id="status-pill-wrap">
                    <span class="status-pill {{ $delivery->status }}">
                        @if($delivery->status === 'in_transit')<span class="pulse-dot"></span>@endif
                        {{ $delivery->status_label }}
                    </span>
                </div>
            </div>

            <div class="info-row">
                <span class="info-label">Created</span>
                <span class="info-value">{{ $delivery->created_at->format('M d, Y') }}</span>
                <span class="info-sub">{{ $delivery->created_at->diffForHumans() }}</span>
            </div>

            @if($delivery->started_at)
            <div class="info-row">
                <span class="info-label">Started</span>
                <span class="info-value">{{ $delivery->started_at->format('M d, Y') }}</span>
                <span class="info-sub">{{ $delivery->started_at->format('h:i A') }}</span>
            </div>
            @endif

            @if($delivery->delivered_at)
            <div class="info-row">
                <span class="info-label">Delivered</span>
                <span class="info-value">{{ $delivery->delivered_at->format('M d, Y') }}</span>
                <span class="info-sub">{{ $delivery->delivered_at->format('h:i A') }}</span>
            </div>
            @endif
        </div>

        @if(!in_array($delivery->status, ['delivered', 'cancelled']) && !$delivery->assigned_user_id)
        <div class="track-card" id="assign-driver-card">
            <p class="track-card-title">Assign Driver</p>
            <select id="driver-select" class="driver-select">
                <option value="">Select a driver...</option>
                @forelse($availableDrivers as $drv)
                    <option value="{{ $drv->id }}" {{ $delivery->assigned_user_id == $drv->id ? 'selected' : '' }}>
                        {{ $drv->name }}
                    </option>
                @empty
                    <option value="" disabled>No drivers available</option>
                @endforelse
            </select>
            <button id="btn-assign" class="btn-assign" onclick="assignDriver()">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                Assign Driver
            </button>
        </div>
        @endif

        @php $displayNotes = ($delivery->invoice->notes ?? null) ?: $delivery->notes; @endphp
        @if($displayNotes)
        <div class="track-card">
            <p class="track-card-title">Notes</p>
            <p style="font-size:.875rem;color:#495057;margin:0;line-height:1.6;">{{ $displayNotes }}</p>
        </div>
        @endif
    </div>

</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const GEOAPIFY_KEY = '328a40dae9644da6a37cd0a608800fa2';
const DELIVERY_ID  = {{ $delivery->id }};
const CSRF         = '{{ csrf_token() }}';

// â”€â”€ Toast â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function toast(msg, type = 'success', ms = 3500) {
    const el = document.createElement('div');
    el.className = `toast ${type}`;
    const icon = type === 'success'
        ? `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>`
        : `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>`;
    el.innerHTML = `${icon} ${msg}`;
    document.getElementById('toast-container').appendChild(el);
    setTimeout(() => el.remove(), ms);
}

// â”€â”€ Map â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
const defaultLat = {{ $delivery->latestLocation?->latitude ?? 14.1077 }};
const defaultLng = {{ $delivery->latestLocation?->longitude ?? 121.1411 }};
const defaultZoom = {{ $delivery->latestLocation ? 15 : 13 }};
const map = L.map('delivery-map', { attributionControl: false }).setView([defaultLat, defaultLng], defaultZoom);

L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${GEOAPIFY_KEY}`, {
    attribution: 'Â© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Â© <a href="https://www.geoapify.com/">Geoapify</a>',
    maxZoom: 20,
}).addTo(map);

const truckIcon = L.divIcon({
    html: `<div style="background:#1a73e8;border-radius:50%;width:38px;height:38px;display:flex;align-items:center;justify-content:center;border:3px solid #fff;box-shadow:0 2px 10px rgba(0,0,0,.25);">
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

// ── Destination marker (customer pinned location) ──────────────────────────
@if($delivery->customer?->latitude && $delivery->customer?->longitude)
const destIcon = L.divIcon({
    html: `<div style="position:relative;width:28px;height:38px;">
        <div style="background:#e53e3e;width:28px;height:28px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid #fff;box-shadow:0 2px 8px rgba(0,0,0,.3);"></div>
        <div style="position:absolute;top:7px;left:7px;width:10px;height:10px;background:#fff;border-radius:50%;transform:none;"></div>
    </div>`,
    className: '', iconSize: [28, 38], iconAnchor: [14, 38],
});
const destLat = {{ $delivery->customer->latitude }};
const destLng = {{ $delivery->customer->longitude }};
const destMarker = L.marker([destLat, destLng], { icon: destIcon })
    .addTo(map)
    .bindPopup('<b>&#x1F4CD; Destination</b><br><b>{{ addslashes($delivery->customer->business_name ?? "") }}</b><br>{{ addslashes($delivery->customer->address ?? "") }}');

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

// â”€â”€ Live polling â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
@if($delivery->status === 'in_transit')
function pollLocation() {
    fetch(`/admin/deliveries/${DELIVERY_ID}/location`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            if (!data.found) return;
            const latlng = [data.latitude, data.longitude];
            if (!marker) { marker = L.marker(latlng, { icon: truckIcon }).addTo(map); }
            else { marker.setLatLng(latlng); }
            map.panTo(latlng);
            document.getElementById('last-update').textContent = 'Updated ' + data.updated;
            if (data.status === 'delivered') {
                clearInterval(pollInterval);
                document.getElementById('last-update').textContent = 'âœ… Delivered';
            }
        }).catch(() => {});
}
pollLocation();
const pollInterval = setInterval(pollLocation, 5000);
@endif

// â”€â”€ Assign driver (AJAX) â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€
function assignDriver() {
    const select = document.getElementById('driver-select');
    const btn    = document.getElementById('btn-assign');
    const userId = select.value;

    if (!userId) { toast('Please select a driver.', 'error'); return; }

    btn.disabled = true;
    btn.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="animation:spin .7s linear infinite"><path d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" stroke-dasharray="30" stroke-dashoffset="10"/></svg> Assigningâ€¦`;

    fetch(`/admin/deliveries/${DELIVERY_ID}/reassign`, {
        method : 'PATCH',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN':CSRF, 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' },
        body   : JSON.stringify({ user_id: userId }),
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById('driver-name-display').textContent = data.driver_name;
            toast(data.message);
            const assignCard = document.getElementById('assign-driver-card');
            if (assignCard) assignCard.style.display = 'none';
        } else {
            toast(data.message ?? 'Something went wrong.', 'error');
        }
    })
    .catch(() => toast('Request failed. Please try again.', 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg> Assign Driver`;
    });
}

// ── Auto-refresh every 5 seconds ──────────────────────────────────────────
function refreshDeliveryInfo() {
    fetch(`/admin/deliveries/${DELIVERY_ID}/refresh`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            // Update driver name
            const driverEl = document.getElementById('driver-name-display');
            if (driverEl) driverEl.textContent = data.driver_name;

            // Update status pill
            const pillWrap = document.getElementById('status-pill-wrap');
            if (pillWrap) {
                const pulse = data.status === 'in_transit' ? '<span class="pulse-dot"></span>' : '';
                pillWrap.innerHTML = `<span class="status-pill ${data.status}">${pulse} ${data.status_label}</span>`;
            }

            // Hide assign driver card if driver is now assigned
            const assignCard = document.getElementById('assign-driver-card');
            if (assignCard && data.assigned_user_id) {
                assignCard.style.display = 'none';
            }

            // Update last-updated text
            if (data.last_updated) {
                const luEl = document.getElementById('last-update');
                if (luEl) luEl.textContent = 'Updated ' + data.last_updated;
            }
        }).catch(() => {});
}
setInterval(refreshDeliveryInfo, 5000);
</script>
@endsection
