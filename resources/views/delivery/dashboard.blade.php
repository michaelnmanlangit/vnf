@extends('layouts.delivery')

@section('title', 'Delivery Dashboard')

@section('page-title', 'My Deliveries')

@section('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
/* ── Zero out layout padding so map can be edge-to-edge ─────────────── */
.content-area { padding: 0 !important; overflow: hidden !important; }
html, body     { overflow: hidden; }

/* ── Fullscreen map ─────────────────────────────────────────────────── */
#driver-map {
    position: fixed;
    top: 60px;          /* below sticky navbar */
    left: 260px;        /* beside sidebar */
    right: 0;
    bottom: 0;
    z-index: 1;
}
@media (max-width: 1024px) { #driver-map { left: 220px; } }
@media (max-width: 768px)  { #driver-map { left: 0; top: 60px; } }

/* ── Floating flash alerts ─────────────────────────────────────────── */
.map-flash {
    position: fixed;
    top: 72px; left: 50%; transform: translateX(-50%);
    z-index: 700;
    min-width: 280px; max-width: 90vw;
    padding: .65rem 1rem;
    border-radius: 8px;
    font-size: .85rem; font-weight: 600;
    display: flex; align-items: center; justify-content: space-between; gap: .5rem;
    box-shadow: 0 4px 16px rgba(0,0,0,.18);
    animation: fadeSlide .3s ease;
}
.map-flash.error   { background:#fff0f0; color:#c0392b; border:1px solid #f5c6c6; }
.map-flash.success { background:#eafaf1; color:#1a5c35; border:1px solid #a9dfbf; }
@keyframes fadeSlide { from{ opacity:0; transform:translateX(-50%) translateY(-8px); } to{ opacity:1; transform:translateX(-50%) translateY(0); } }

/* ── Route stats chips (top-right of map) ──────────────────────────── */
.map-stats {
    position: fixed;
    top: 72px; right: 1rem;
    z-index: 500;
    display: flex; flex-direction: column; gap: .4rem;
}
.map-stat-chip {
    background: rgba(255,255,255,.93);
    border-radius: 20px;
    padding: .35rem .85rem;
    font-size: .78rem; font-weight: 700; color: #2c3e50;
    box-shadow: 0 2px 8px rgba(0,0,0,.14);
    white-space: nowrap;
    display: flex; align-items: center; gap: .35rem;
}
.map-stat-chip .chip-label { font-weight: 400; color: #7f8c8d; font-size: .72rem; }

/* ── Floating info panel (bottom) ───────────────────────────────────── */
.map-panel {
    position: fixed;
    bottom: 1rem;
    left: calc(260px + 1rem);
    right: calc(1rem + 52px); /* leave space for history toggle */
    z-index: 500;
    background: rgba(255,255,255,.97);
    border-radius: 14px;
    box-shadow: 0 6px 28px rgba(0,0,0,.18);
    padding: 1rem 1.1rem;
    max-width: 500px;
    backdrop-filter: blur(6px);
}
@media (max-width: 1024px) { .map-panel { left: calc(220px + 1rem); } }
@media (max-width: 768px)  { .map-panel { left: 1rem; right: calc(1rem + 52px); max-width: none; } }
@media (max-width: 480px)  { .map-panel { padding: .75rem .85rem; bottom: .5rem; left: .5rem; right: calc(.5rem + 48px); border-radius: 10px; } }

/* panel header row */
.map-panel-head {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: .55rem;
}
.map-customer { font-size: .95rem; font-weight: 700; color: #2c3e50; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 200px; }
.map-badge {
    display: inline-flex; align-items: center;
    padding: .2rem .65rem; border-radius: 20px;
    font-size: .72rem; font-weight: 700; letter-spacing: .03em;
    white-space: nowrap;
}
.map-invoice { font-size: .72rem; color: #95a5a6; margin-bottom: .3rem; }
.map-address { font-size: .80rem; color: #555; line-height: 1.4; margin-bottom: .55rem; }

/* GPS bar */
.map-gps-bar {
    display: flex; align-items: center; gap: .5rem;
    font-size: .76rem; color: #27ae60; font-weight: 600;
    margin-bottom: .55rem;
}
.map-gps-pulse {
    width: 9px; height: 9px; border-radius: 50%;
    background: #27ae60;
    box-shadow: 0 0 0 0 rgba(39,174,96,.5);
    animation: gpsPulse 1.6s infinite;
}
@keyframes gpsPulse {
    0%  { box-shadow: 0 0 0 0   rgba(39,174,96,.5); }
    70% { box-shadow: 0 0 0 8px rgba(39,174,96,0);  }
    100%{ box-shadow: 0 0 0 0   rgba(39,174,96,0);  }
}

/* Action buttons */
.map-btn {
    display: block; width: 100%;
    padding: .65rem 1rem; border-radius: 8px;
    font-size: .87rem; font-weight: 700; letter-spacing: .02em;
    border: none; cursor: pointer; transition: opacity .15s, transform .1s;
}
.map-btn:active { opacity: .85; transform: scale(.98); }
.map-btn-start { background: #27ae60; color: #fff; }
.map-btn-done  { background: #2c3e50; color: #fff; }

/* Info toggle (expand items) */
.map-toggle-items {
    font-size: .75rem; color: #3498db; cursor: pointer;
    font-weight: 600; margin-bottom: .45rem; user-select: none;
}
.map-items-table {
    width: 100%; border-collapse: collapse;
    font-size: .78rem; margin-bottom: .55rem;
}
.map-items-table th { color: #95a5a6; font-size: .7rem; text-transform: uppercase; letter-spacing: .04em; padding: .25rem .3rem; border-bottom: 1px solid #f0f0f0; }
.map-items-table td { padding: .3rem .3rem; border-bottom: 1px solid #f8f8f8; color: #2c3e50; }
.map-items-table td:last-child { text-align: right; font-weight: 700; width: 56px; }

/* ── History drawer toggle button ────────────────────────────────────── */
.map-history-btn {
    position: fixed;
    bottom: 1rem; right: 1rem;
    z-index: 600;
    width: 44px; height: 44px; border-radius: 50%;
    background: #2c3e50; color: #fff;
    border: none; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 4px 14px rgba(0,0,0,.25);
    transition: background .2s;
}
.map-history-btn:hover { background: #3d5166; }
@media (max-width: 480px) { .map-history-btn { bottom: .5rem; right: .5rem; } }

/* ── History drawer ───────────────────────────────────────────────────── */
.map-history-drawer {
    position: fixed;
    bottom: 0; right: 0;
    width: 340px; max-width: 95vw;
    max-height: 55vh;
    z-index: 650;
    background: #fff;
    border-radius: 14px 0 0 0;
    box-shadow: -4px -4px 24px rgba(0,0,0,.16);
    display: flex; flex-direction: column;
    transform: translateX(110%);
    transition: transform .3s cubic-bezier(.4,0,.2,1);
}
.map-history-drawer.open { transform: translateX(0); }
.map-drawer-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: .85rem 1rem .6rem;
    border-bottom: 1px solid #f0f0f0;
    font-weight: 700; font-size: .88rem; color: #2c3e50;
}
.map-drawer-close {
    background: none; border: none; cursor: pointer; font-size: 1.1rem; color: #7f8c8d; line-height: 1;
}
.map-drawer-body { overflow-y: auto; padding: .5rem 1rem 1rem; max-height: 136px; }
.map-hist-table { width: 100%; border-collapse: collapse; font-size: .78rem; }
.map-hist-table th { color: #95a5a6; font-size: .69rem; text-transform: uppercase; letter-spacing: .04em; padding: .3rem 0; border-bottom: 1px solid #f0f0f0; }
.map-hist-table td { padding: .4rem 0; border-bottom: 1px solid #f8f8f8; color: #2c3e50; }
.map-hist-done { display: inline-flex; padding: .15rem .55rem; border-radius: 20px; background: #eafaf1; color: #1a5c35; font-size: .68rem; font-weight: 700; }

/* ── No-delivery overlay ─────────────────────────────────────────────── */
.map-empty-overlay {
    position: fixed;
    top: 60px; left: 260px; right: 0; bottom: 0;
    z-index: 500;
    display: flex; align-items: center; justify-content: center;
}
@media (max-width: 1024px) { .map-empty-overlay { left: 220px; } }
@media (max-width: 768px)  { .map-empty-overlay { left: 0; } }
.map-empty-card {
    background: rgba(255,255,255,.96);
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0,0,0,.14);
    padding: 2.5rem 2rem;
    text-align: center;
    max-width: 340px; width: 90%;
}
.map-empty-card h3 { font-size: 1.1rem; font-weight: 700; color: #2c3e50; margin: 1rem 0 .5rem; }
.map-empty-card p  { font-size: .85rem; color: #7f8c8d; line-height: 1.55; margin: 0; }

/* ── Driver truck icon ──────────────────────────────────────────────── */
.dtruck-wrap {
    filter: drop-shadow(0 2px 4px rgba(0,0,0,.35));
    animation: truckBob .9s ease-in-out infinite alternate;
}
@keyframes truckBob {
    from { transform: translateY(0);   }
    to   { transform: translateY(-3px); }
}
</style>
@endsection

@section('content')

{{-- Flash alerts --}}
@if(session('success'))
<div class="map-flash success" id="flashMsg">
    <span>{{ session('success') }}</span>
    <button onclick="document.getElementById('flashMsg').remove()" style="background:none;border:none;cursor:pointer;font-size:1.1rem;color:inherit;line-height:1;">&times;</button>
</div>
@endif
@if(session('error'))
<div class="map-flash error" id="flashMsg">
    <span>{{ session('error') }}</span>
    <button onclick="document.getElementById('flashMsg').remove()" style="background:none;border:none;cursor:pointer;font-size:1.1rem;color:inherit;line-height:1;">&times;</button>
</div>
@endif

@if($activeDelivery)
@php
    $status     = $activeDelivery->status;
    $destLat    = $activeDelivery->customer->latitude  ?? null;
    $destLng    = $activeDelivery->customer->longitude ?? null;
    $hasDestPin = $destLat && $destLng;
    $badge = match($status) {
        'pending'    => ['bg'=>'#fff8e1','color'=>'#b7770d','label'=>'Pending'],
        'in_transit' => ['bg'=>'#e3f2fd','color'=>'#1565c0','label'=>'In Transit'],
        default      => ['bg'=>'#f4f5f7','color'=>'#7f8c8d','label'=>ucfirst($status)],
    };
@endphp

{{-- Fullscreen map --}}
<div id="driver-map"></div>

{{-- Route stats chips top-right --}}
<div class="map-stats">
    <div class="map-stat-chip"><span class="chip-label">Distance</span><span id="stat-dist">—</span></div>
    <div class="map-stat-chip"><span class="chip-label">ETA</span><span id="stat-time">—</span></div>
    <div class="map-stat-chip" id="map-status-chip" style="color:#3498db;">Loading&hellip;</div>
</div>

{{-- Floating info panel bottom-left --}}
<div class="map-panel" id="mapPanel">
    <div class="map-panel-head">
        <span class="map-customer">{{ $activeDelivery->customer->business_name ?? 'Unknown Customer' }}</span>
        <span class="map-badge" style="background:{{ $badge['bg'] }};color:{{ $badge['color'] }};">{{ $badge['label'] }}</span>
    </div>
    <div class="map-invoice">Invoice: {{ $activeDelivery->invoice->invoice_number ?? '—' }}</div>
    <div class="map-address">
        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#e74c3c" stroke-width="2.5" style="vertical-align:middle;margin-right:.25rem;flex-shrink:0;">
            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
        </svg>
        {{ $activeDelivery->customer->address ?? 'Address not set' }}
    </div>

    @if($activeDelivery->invoice && $activeDelivery->invoice->items->count())
    <div class="map-toggle-items" onclick="toggleItems(this)">▶ Show Items ({{ $activeDelivery->invoice->items->count() }})</div>
    <div id="itemsBlock" style="display:none;">
        <table class="map-items-table">
            <thead><tr><th>Item</th><th>Qty</th></tr></thead>
            <tbody>
                @foreach($activeDelivery->invoice->items as $item)
                <tr>
                    <td>{{ $item->product_name ?: $item->description }}</td>
                    <td>{{ rtrim(rtrim(number_format((float)$item->quantity, 2), '0'), '.') }}{{ $item->unit ? ' '.$item->unit : '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($status === 'in_transit')
    <div class="map-gps-bar">
        <div class="map-gps-pulse"></div>
        <span id="gps-text">Acquiring GPS signal&hellip;</span>
    </div>
    @endif

    @if(!$hasDestPin)
    <div style="font-size:.75rem;padding:.45rem .7rem;background:#fff8e1;border-radius:7px;border:1px solid #ffe082;color:#795548;margin-bottom:.55rem;">
        Destination pin not set. Ask admin to set the customer location.
    </div>
    @endif

    @if($status === 'pending')
    <form method="POST" action="{{ route('delivery.start', $activeDelivery->id) }}">
        @csrf
        <button type="submit" class="map-btn map-btn-start">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:middle;margin-right:.35rem;"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            Start Delivery
        </button>
    </form>
    @elseif($status === 'in_transit')
    <form method="POST" action="{{ route('delivery.complete', $activeDelivery->id) }}" onsubmit="return confirm('Mark this delivery as completed?')">
        @csrf
        <button type="submit" class="map-btn map-btn-done">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="vertical-align:middle;margin-right:.35rem;"><path d="M20 6L9 17l-5-5"/></svg>
            Mark as Delivered
        </button>
    </form>
    @endif
</div>

{{-- History toggle button --}}
@if($pastDeliveries->count())
<button class="map-history-btn" id="historyBtn" title="Recent deliveries" onclick="toggleHistory()">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
    </svg>
</button>
@endif

{{-- Leaflet map script --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function(){
    const GEO_KEY     = '328a40dae9644da6a37cd0a608800fa2';
    const DELIVERY_ID = {{ $activeDelivery->id }};
    const CSRF        = document.querySelector('meta[name=csrf-token]').content;
    const DEST_LAT    = {{ $hasDestPin ? $destLat : 'null' }};
    const DEST_LNG    = {{ $hasDestPin ? $destLng : 'null' }};
    const IS_TRANSIT  = {{ $status === 'in_transit' ? 'true' : 'false' }};
    const DEFAULT_POS = [14.1077, 121.1411];

    /* Driver truck icon */
    const driverIcon = L.divIcon({
        className: '',
        html: `<div class="dtruck-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="28" viewBox="0 0 38 28">
              <!-- truck body -->
              <rect x="1" y="7" width="24" height="15" rx="2" fill="#1565c0" stroke="#fff" stroke-width="1.2"/>
              <!-- cab -->
              <rect x="25" y="11" width="11" height="11" rx="2" fill="#1976d2" stroke="#fff" stroke-width="1.2"/>
              <!-- cab window -->
              <rect x="26.5" y="12.5" width="5.5" height="4" rx="1" fill="#bbdefb" opacity=".9"/>
              <!-- windshield triangle join -->
              <polygon points="25,11 32,7 32,11" fill="#1565c0" stroke="#fff" stroke-width=".8"/>
              <!-- rear wheel -->
              <circle cx="8" cy="24" r="3.2" fill="#263238" stroke="#fff" stroke-width="1"/>
              <circle cx="8" cy="24" r="1.3" fill="#90a4ae"/>
              <!-- front wheel -->
              <circle cx="29" cy="24" r="3.2" fill="#263238" stroke="#fff" stroke-width="1"/>
              <circle cx="29" cy="24" r="1.3" fill="#90a4ae"/>
              <!-- cargo lines -->
              <line x1="7" y1="10" x2="7" y2="20" stroke="#90caf9" stroke-width=".9" opacity=".7"/>
              <line x1="13" y1="10" x2="13" y2="20" stroke="#90caf9" stroke-width=".9" opacity=".7"/>
              <line x1="19" y1="10" x2="19" y2="20" stroke="#90caf9" stroke-width=".9" opacity=".7"/>
            </svg>
          </div>`,
        iconSize:[38,28], iconAnchor:[19,28],
    });

    /* Destination red icon */
    const destIcon = L.icon({
        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
        iconRetinaUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
        shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
        iconSize:[25,41], iconAnchor:[12,41], shadowSize:[41,41],
    });

    const map = L.map('driver-map', { attributionControl: false, zoomControl: true })
        .setView(DEST_LAT ? [DEST_LAT, DEST_LNG] : DEFAULT_POS, 14);

    L.tileLayer(
        `https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${GEO_KEY}`,
        { maxZoom: 20 }
    ).addTo(map);

    /* Move zoom control to bottom-left so it's not hidden by panel */
    map.zoomControl.setPosition('topleft');

    if (DEST_LAT) {
        L.marker([DEST_LAT, DEST_LNG], { icon: destIcon }).addTo(map)
            .bindPopup('<b>Delivery Destination</b><br><small>{{ addslashes($activeDelivery->customer->address ?? '') }}</small>');
    }

    let driverMarker = null, routeLine = null;
    let routePoints  = [];          // flat [[lat,lng],...] of current route
    let routeFetching = false;      // prevent concurrent fetches
    let lastRoute    = 0;           // timestamp of last successful route draw
    let firstRoute   = true;        // fit bounds only on first draw
    const REROUTE_MS      = 10000;  // recalculate at most every 10 s
    const OFF_ROUTE_M     = 60;     // metres off-route before instant reroute

    function setStatus(text, color) {
        const el = document.getElementById('map-status-chip');
        if (el) { el.textContent = text; el.style.color = color || '#3498db'; }
    }

    /* ── Point-to-segment distance (degrees, returns metres approx) ── */
    function ptSegDistM(lat, lng, aLat, aLng, bLat, bLng) {
        const dx = bLng - aLng, dy = bLat - aLat;
        if (dx === 0 && dy === 0) {
            return Math.hypot(lat - aLat, lng - aLng) * 111320;
        }
        const t = Math.max(0, Math.min(1,
            ((lat - aLat) * dy + (lng - aLng) * dx) / (dy * dy + dx * dx)
        ));
        return Math.hypot(lat - (aLat + t * dy), lng - (aLng + t * dx)) * 111320;
    }

    /* Returns true when driver is > OFF_ROUTE_M from any segment */
    function isOffRoute(lat, lng) {
        if (routePoints.length < 2) return false;
        for (let i = 0; i < routePoints.length - 1; i++) {
            const d = ptSegDistM(lat, lng,
                routePoints[i][0],   routePoints[i][1],
                routePoints[i+1][0], routePoints[i+1][1]);
            if (d <= OFF_ROUTE_M) return false;   // close enough to this segment
        }
        return true;
    }

    function drawRoute(dLat, dLng, forceZoom) {
        if (!DEST_LAT || routeFetching) return;
        routeFetching = true;
        setStatus('Routing…', '#f39c12');
        const url = `https://api.geoapify.com/v1/routing?waypoints=${dLat},${dLng}|${DEST_LAT},${DEST_LNG}&mode=drive&apiKey=${GEO_KEY}`;
        fetch(url)
            .then(r => r.json())
            .then(data => {
                routeFetching = false;
                if (!data.features?.length) { setStatus('No route found', '#e67e22'); return; }

                const feat = data.features[0];
                const pts  = [];
                const geo  = feat.geometry;
                (geo.type === 'MultiLineString' ? geo.coordinates : [geo.coordinates])
                    .forEach(seg => seg.forEach(c => pts.push([c[1], c[0]])));

                /* Update polyline — replace, don't re-add */
                if (routeLine) {
                    routeLine.setLatLngs(pts);
                } else {
                    routeLine = L.polyline(pts, {
                        color: '#2196f3', weight: 5, opacity: .9,
                        lineJoin: 'round', lineCap: 'round'
                    }).addTo(map);
                }

                routePoints = pts;
                lastRoute   = Date.now();

                /* Fit map only on first draw or when forced */
                if (firstRoute || forceZoom) {
                    map.fitBounds(routeLine.getBounds(), { padding: [60, 60] });
                    firstRoute = false;
                }

                const distM   = feat.properties.distance ?? 0;
                const timeSec = feat.properties.time     ?? 0;
                const mins    = Math.round(timeSec / 60);
                const el_d = document.getElementById('stat-dist');
                const el_t = document.getElementById('stat-time');
                if (el_d) el_d.textContent = distM >= 1000
                    ? (distM / 1000).toFixed(1) + ' km'
                    : Math.round(distM) + ' m';
                if (el_t) el_t.textContent = mins >= 60
                    ? Math.floor(mins / 60) + 'h ' + (mins % 60) + 'min'
                    : mins + ' min';
                setStatus('Route Active', '#27ae60');
            })
            .catch(() => { routeFetching = false; setStatus('Route unavailable', '#e74c3c'); });
    }

    function onPos(pos) {
        const lat = pos.coords.latitude, lng = pos.coords.longitude;
        if (!driverMarker) {
            driverMarker = L.marker([lat, lng], { icon: driverIcon, zIndexOffset: 1000 }).addTo(map)
                .bindPopup('<b>Your Location</b>');
        } else {
            driverMarker.setLatLng([lat, lng]);
        }
        if (IS_TRANSIT) {
            const gpsEl = document.getElementById('gps-text');
            if (gpsEl) gpsEl.textContent = `Sharing location: ${lat.toFixed(5)}, ${lng.toFixed(5)}`;
            fetch('/delivery/location', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
                body: JSON.stringify({ delivery_id: DELIVERY_ID, latitude: lat, longitude: lng }),
            });
        }
        /* Reroute immediately if off-route, otherwise respect the interval */
        const now       = Date.now();
        const offRoute  = isOffRoute(lat, lng);
        const overTimer = (now - lastRoute) > REROUTE_MS;
        if (offRoute || overTimer) {
            if (offRoute) setStatus('Rerouting…', '#e67e22');
            drawRoute(lat, lng, false);
        }
    }

    function onErr(err) {
        setStatus('GPS unavailable', '#e74c3c');
        const gpsEl = document.getElementById('gps-text');
        if (gpsEl) gpsEl.textContent = 'GPS error: ' + err.message;
        if (DEST_LAT) map.setView([DEST_LAT, DEST_LNG], 15);
    }

    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            pos => { onPos(pos); drawRoute(pos.coords.latitude, pos.coords.longitude, true); },
            onErr,
            { enableHighAccuracy: true, timeout: 15000 }
        );
        navigator.geolocation.watchPosition(onPos, onErr, { enableHighAccuracy: true, maximumAge: 5000, timeout: 10000 });
    } else {
        setStatus('GPS not supported', '#e74c3c');
        if (DEST_LAT) map.setView([DEST_LAT, DEST_LNG], 15);
    }

    setTimeout(() => map.invalidateSize(), 300);
})();
</script>

@else
{{-- No active delivery — fullscreen empty state --}}
<div id="driver-map"></div>
<div class="map-empty-overlay">
    <div class="map-empty-card">
        <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="#bdc3c7" stroke-width="1.4">
            <rect x="1" y="3" width="15" height="13"/>
            <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
            <circle cx="5.5" cy="18.5" r="2.5"/>
            <circle cx="18.5" cy="18.5" r="2.5"/>
        </svg>
        <h3>No Active Delivery</h3>
        <p>You have no delivery assigned right now.<br>Check back or contact your admin.</p>
    </div>
</div>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function(){
    const GEO_KEY = '328a40dae9644da6a37cd0a608800fa2';
    const map = L.map('driver-map', { attributionControl: false })
        .setView([14.1077, 121.1411], 13);
    L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${GEO_KEY}`, { maxZoom:20 }).addTo(map);
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(p => map.setView([p.coords.latitude, p.coords.longitude], 14));
    }
    setTimeout(() => map.invalidateSize(), 300);
})();
</script>
@endif

{{-- Recent Deliveries Drawer --}}
@if($pastDeliveries->count())
<div class="map-history-drawer" id="historyDrawer">
    <div class="map-drawer-head">
        <span>Recent Deliveries</span>
        <button class="map-drawer-close" onclick="toggleHistory()">&times;</button>
    </div>
    <div class="map-drawer-body">
        <table class="map-hist-table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Invoice</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pastDeliveries as $past)
                <tr>
                    <td style="font-weight:600;">{{ $past->customer->business_name ?? '—' }}</td>
                    <td style="color:#7f8c8d;">{{ $past->invoice->invoice_number ?? '—' }}</td>
                    <td style="color:#95a5a6;white-space:nowrap;font-size:.72rem;">{{ $past->delivered_at?->format('M d') ?? $past->created_at->format('M d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<script>
function toggleHistory() {
    const drawer = document.getElementById('historyDrawer');
    const btn    = document.getElementById('historyBtn');
    if (!drawer) return;
    drawer.classList.toggle('open');
    if (btn) btn.style.background = drawer.classList.contains('open') ? '#e74c3c' : '';
}
function toggleItems(el) {
    const block = document.getElementById('itemsBlock');
    if (!block) return;
    const visible = block.style.display !== 'none';
    block.style.display = visible ? 'none' : '';
    el.textContent = visible
        ? el.textContent.replace('▼','▶').replace('Hide','Show')
        : el.textContent.replace('▶','▼').replace('Show','Hide');
}
</script>

@endsection
