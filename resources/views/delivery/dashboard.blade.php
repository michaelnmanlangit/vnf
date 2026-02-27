@extends('layouts.delivery')

@section('title', 'Delivery Dashboard')

@section('page-title', 'My Deliveries')

@section('styles')
<style>
    .delivery-card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.08); padding:1.5rem; margin-bottom:1.25rem; }
    .badge { display:inline-block; padding:.3rem .8rem; border-radius:20px; font-size:.82rem; font-weight:600; }
    .badge-pending    { background:#fff3cd; color:#856404; }
    .badge-in_transit { background:#cff4fc; color:#055160; }
    .badge-delivered  { background:#d1e7dd; color:#0a3622; }
    .btn-start    { background:#198754; color:#fff; border:none; border-radius:8px; padding:.75rem 1.5rem; font-size:1rem; font-weight:600; cursor:pointer; width:100%; margin-top:1rem; }
    .btn-done     { background:#1a73e8; color:#fff; border:none; border-radius:8px; padding:.75rem 1.5rem; font-size:1rem; font-weight:600; cursor:pointer; width:100%; margin-top:.5rem; }
    .btn-start:active, .btn-done:active { opacity:.85; }
    .gps-status { background:#f8f9fa; border-radius:8px; padding:.75rem 1rem; margin-top:1rem; font-size:.88rem; display:flex; align-items:center; gap:.5rem; }
    .pulse { display:inline-block; width:10px; height:10px; border-radius:50%; background:#198754; animation:pulse 1.5s infinite; }
    @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.3)} }
    .item-table { width:100%; border-collapse:collapse; font-size:.87rem; margin-top:.75rem; }
    .item-table th { padding:.5rem .75rem; text-align:left; background:#f8f9fa; font-weight:600; color:#495057; }
    .item-table td { padding:.5rem .75rem; border-bottom:1px solid #f0f0f0; }
</style>
@endsection

@section('content')
<div style="padding:1rem;">

    @if(session('success'))
        <div style="background:#d1e7dd;color:#0a3622;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;border:1px solid #a3cfbb;">{{ session('success') }}</div>
    @endif

    {{-- ===== ACTIVE DELIVERY ===== --}}
    @if($activeDelivery)
    <div class="delivery-card" style="border-left:4px solid {{ $activeDelivery->status === 'in_transit' ? '#0dcaf0' : '#ffc107' }};">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem;">
            <h2 style="margin:0;font-size:1.1rem;font-weight:700;">Active Delivery</h2>
            <span class="badge badge-{{ $activeDelivery->status }}">{{ $activeDelivery->status_label }}</span>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem;margin-bottom:.75rem;">
            <div>
                <div style="font-size:.75rem;color:#6c757d;text-transform:uppercase;letter-spacing:.05em;">Invoice</div>
                <div style="font-weight:600;">{{ $activeDelivery->invoice->invoice_number ?? '—' }}</div>
            </div>
            <div>
                <div style="font-size:.75rem;color:#6c757d;text-transform:uppercase;letter-spacing:.05em;">Customer</div>
                <div style="font-weight:600;">{{ $activeDelivery->customer->business_name ?? '—' }}</div>
            </div>
            <div style="grid-column:1/-1;">
                <div style="font-size:.75rem;color:#6c757d;text-transform:uppercase;letter-spacing:.05em;">Deliver To</div>
                <div style="font-weight:500;">{{ $activeDelivery->customer->address ?? 'Address not set' }}</div>
            </div>
        </div>

        {{-- Items --}}
        @if($activeDelivery->invoice && $activeDelivery->invoice->items->count())
        <table class="item-table">
            <thead><tr><th>Item</th><th style="text-align:right;">Qty</th></tr></thead>
            <tbody>
                @foreach($activeDelivery->invoice->items as $item)
                <tr><td>{{ $item->description }}</td><td style="text-align:right;">{{ $item->quantity }}</td></tr>
                @endforeach
            </tbody>
        </table>
        @endif

        {{-- Actions --}}
        @if($activeDelivery->status === 'pending')
        <form method="POST" action="{{ route('delivery.start', $activeDelivery->id) }}">
            @csrf
            <button type="submit" class="btn-start">
                🚚 Start Delivery
            </button>
        </form>
        @endif

        @if($activeDelivery->status === 'in_transit')
        {{-- GPS tracking active --}}
        <div class="gps-status" id="gps-status">
            <span class="pulse"></span>
            <span id="gps-text">Starting GPS...</span>
        </div>

        <form method="POST" action="{{ route('delivery.complete', $activeDelivery->id) }}" onsubmit="return confirm('Mark this delivery as completed?')">
            @csrf
            <button type="submit" class="btn-done">✅ Mark as Delivered</button>
        </form>

        {{-- GPS auto-send script --}}
        <script>
        const DELIVERY_ID = {{ $activeDelivery->id }};
        const CSRF = document.querySelector('meta[name=csrf-token]').getAttribute('content');

        function sendLocation(lat, lng) {
            fetch('/delivery/location', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ delivery_id: DELIVERY_ID, latitude: lat, longitude: lng })
            });
        }

        function startTracking() {
            if (!navigator.geolocation) {
                document.getElementById('gps-text').textContent = 'GPS not supported on this device.';
                return;
            }
            navigator.geolocation.watchPosition(
                (pos) => {
                    const lat = pos.coords.latitude;
                    const lng = pos.coords.longitude;
                    document.getElementById('gps-text').textContent =
                        `Sharing location: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
                    sendLocation(lat, lng);
                },
                (err) => {
                    document.getElementById('gps-text').textContent = 'GPS error: ' + err.message;
                },
                { enableHighAccuracy: true, maximumAge: 5000, timeout: 10000 }
            );
        }

        // Start tracking immediately when page loads
        startTracking();
        </script>
        @endif
    </div>

    @else
    {{-- No active delivery --}}
    <div class="delivery-card" style="text-align:center;padding:2.5rem 1.5rem;">
        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#dee2e6" stroke-width="1.5" style="display:block;margin:0 auto 1rem;">
            <rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
            <circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
        </svg>
        <p style="color:#6c757d;margin:0;font-size:1rem;">No active delivery assigned.<br><span style="font-size:.88rem;">Check back later or contact admin.</span></p>
    </div>
    @endif

    {{-- ===== RECENT DELIVERIES ===== --}}
    @if($pastDeliveries->count())
    <div class="delivery-card">
        <h3 style="margin:0 0 1rem;font-size:1rem;font-weight:700;">Recent Deliveries</h3>
        @foreach($pastDeliveries as $past)
        <div style="display:flex;justify-content:space-between;align-items:center;padding:.6rem 0;border-bottom:1px solid #f0f0f0;">
            <div>
                <div style="font-weight:600;font-size:.9rem;">{{ $past->customer->business_name ?? '—' }}</div>
                <div style="font-size:.8rem;color:#6c757d;">{{ $past->invoice->invoice_number ?? '—' }} · {{ $past->delivered_at?->format('M d, Y') ?? $past->created_at->format('M d, Y') }}</div>
            </div>
            <span class="badge badge-delivered">Delivered</span>
        </div>
        @endforeach
    </div>
    @endif

</div>
@endsection
