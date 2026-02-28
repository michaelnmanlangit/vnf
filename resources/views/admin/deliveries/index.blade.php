@extends('layouts.admin')

@section('title', 'Delivery Monitoring')

@section('page-title', 'Delivery Monitoring')

@section('styles')
<link rel="stylesheet" href="/build/assets/billing-mM0IVGZh.css">
<style>
    .status-badge.in_transit  { background:#cff4fc; color:#055160; }
    .status-badge.delivered   { background:#d5f5e3; color:#1e8449; }
    .status-badge.pending     { background:#fef9e7; color:#b7950b; }
    .status-badge.cancelled   { background:#fde8e8; color:#c0392b; }

    /* ── Filter dropdown fix ── */
    .multi-select-wrapper { position: relative; }
    .multi-select-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 6px);
        right: 0;
        left: auto;
        min-width: 220px;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,.12);
        z-index: 999;
        padding: .75rem 0 .5rem;
    }
    .multi-select-dropdown.active { display: block; }
    .filter-section { padding: 0 1rem .5rem; }
    .section-title {
        font-size: .68rem;
        font-weight: 700;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: .06em;
        margin-bottom: .5rem;
    }
    .filter-option {
        display: flex;
        align-items: center;
        gap: .55rem;
        padding: .38rem .25rem;
        font-size: .875rem;
        color: #2c3e50;
        cursor: pointer;
        border-radius: 6px;
        transition: background .12s;
    }
    .filter-option:hover { background: #f0f4ff; }
    .filter-option input[type="radio"] { accent-color: #1a73e8; width: 15px; height: 15px; }
    .filter-actions {
        display: flex;
        gap: .5rem;
        padding: .6rem 1rem 0;
        border-top: 1px solid #f0f2f5;
        margin-top: .4rem;
    }
    .filter-apply {
        flex: 1;
        padding: .45rem;
        background: #1a73e8;
        color: #fff;
        border: none;
        border-radius: 7px;
        font-size: .83rem;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s;
    }
    .filter-apply:hover { background: #1558c0; }
    .filter-reset {
        flex: 1;
        padding: .45rem;
        background: #f0f2f5;
        color: #2c3e50;
        border-radius: 7px;
        font-size: .83rem;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        transition: background .15s;
    }
    .filter-reset:hover { background: #e2e6ea; }
</style>
@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success" id="successAlert" style="display:flex;align-items:center;gap:.6rem;margin-bottom:1rem;">
        <span>{{ session('success') }}</span>
        <button onclick="document.getElementById('successAlert').remove()" style="background:none;border:none;cursor:pointer;margin-left:auto;color:inherit;font-size:1.2rem;line-height:1;">&times;</button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-error" id="errorAlert" style="display:flex;align-items:center;gap:.6rem;margin-bottom:1rem;">
        <span>{{ session('error') }}</span>
        <button onclick="document.getElementById('errorAlert').remove()" style="background:none;border:none;cursor:pointer;margin-left:auto;color:inherit;font-size:1.2rem;line-height:1;">&times;</button>
    </div>
@endif

<div class="billing-grid-container">

    {{-- Stat Cards --}}
    <div class="status-stats">
        <div class="status-cards">
            <div class="status-card status-total">
                <div class="status-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                        <circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
                    </svg>
                </div>
                <div class="status-content">
                    <span class="status-name">Total</span>
                    <div class="status-bottom"><span class="status-count">{{ $stats['total'] }}</span></div>
                </div>
            </div>
            <div class="status-card status-pending">
                <div class="status-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                </div>
                <div class="status-content">
                    <span class="status-name">Pending</span>
                    <div class="status-bottom">
                        <span class="status-count">{{ $stats['pending'] }}</span>
                        <span class="status-percentage">{{ $stats['total'] > 0 ? round($stats['pending'] / $stats['total'] * 100, 1) : 0 }}%</span>
                    </div>
                </div>
            </div>
            <div class="status-card status-partial">
                <div class="status-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/>
                    </svg>
                </div>
                <div class="status-content">
                    <span class="status-name">In Transit</span>
                    <div class="status-bottom">
                        <span class="status-count">{{ $stats['in_transit'] }}</span>
                        <span class="status-percentage">{{ $stats['total'] > 0 ? round($stats['in_transit'] / $stats['total'] * 100, 1) : 0 }}%</span>
                    </div>
                </div>
            </div>
            <div class="status-card status-paid">
                <div class="status-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
                <div class="status-content">
                    <span class="status-name">Delivered</span>
                    <div class="status-bottom">
                        <span class="status-count">{{ $stats['delivered'] }}</span>
                        <span class="status-percentage">{{ $stats['total'] > 0 ? round($stats['delivered'] / $stats['total'] * 100, 1) : 0 }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="billing-toolbar">
        <form method="GET" action="{{ route('admin.deliveries.index') }}" id="filterForm" class="toolbar-form">
            <div class="search-box">
                <input type="text" name="search" placeholder="Search customer, invoice, driver..." value="{{ request('search') }}">
                <svg class="search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </div>

            <div class="filter-group">
                <div class="multi-select-wrapper">
                    <button type="button" class="multi-select-toggle" id="filterToggle">
                        <span class="filter-label">Filters</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>
                    <div class="multi-select-dropdown" id="filterDropdown">
                        <div class="filter-section">
                            <div class="section-title">Status</div>
                            <label class="filter-option">
                                <input type="radio" name="status" value="" {{ !request('status') ? 'checked' : '' }}>
                                <span>All Statuses</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="status" value="pending" {{ request('status')=='pending' ? 'checked' : '' }}>
                                <span>Pending</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="status" value="in_transit" {{ request('status')=='in_transit' ? 'checked' : '' }}>
                                <span>In Transit</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="status" value="delivered" {{ request('status')=='delivered' ? 'checked' : '' }}>
                                <span>Delivered</span>
                            </label>
                            <label class="filter-option">
                                <input type="radio" name="status" value="cancelled" {{ request('status')=='cancelled' ? 'checked' : '' }}>
                                <span>Cancelled</span>
                            </label>
                        </div>
                        <div class="filter-actions">
                            <button type="submit" class="filter-apply">Apply</button>
                            <a href="{{ route('admin.deliveries.index') }}" class="filter-reset">Reset</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="toolbar-actions">
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.deliveries.index') }}" class="clear-filters">Clear All</a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="billing-list">
        <table class="billing-table">
            <thead>
                <tr>
                    <th>Invoice</th>
                    <th>Customer</th>
                    <th>Driver</th>
                    <th>Status</th>
                    <th>Date Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveries as $delivery)
                <tr>
                    <td>
                        <a href="{{ route('admin.billing.show', $delivery->invoice_id) }}" style="color:#1a73e8;font-weight:600;text-decoration:none;">
                            {{ $delivery->invoice->invoice_number ?? '—' }}
                        </a>
                    </td>
                    <td>{{ $delivery->customer->business_name ?? '—' }}</td>
                    <td>
                        @if($delivery->driver)
                            {{ $delivery->driver->name }}
                        @else
                            <span style="color:#adb5bd;">No driver assigned</span>
                        @endif
                    </td>
                    <td>
                        <span class="status-badge {{ $delivery->status }}">{{ $delivery->status_label }}</span>
                    </td>
                    <td>{{ $delivery->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('admin.deliveries.show', $delivery->id) }}" class="btn-action view" title="View">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                View
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                                <circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
                            </svg>
                            <h3>No deliveries found</h3>
                            <p>{{ request('search') || request('status') ? 'Try adjusting your filters' : 'No deliveries have been created yet' }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($deliveries->hasPages())
            <div class="pagination-wrapper">
                <div class="pagination">{{ $deliveries->withQueryString()->links() }}</div>
            </div>
        @endif
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterToggle = document.getElementById('filterToggle');
    const filterDropdown = document.getElementById('filterDropdown');
    if (filterToggle && filterDropdown) {
        filterToggle.addEventListener('click', function (e) {
            e.preventDefault(); e.stopPropagation();
            filterToggle.classList.toggle('active');
            filterDropdown.classList.toggle('active');
        });
        document.addEventListener('click', function (e) {
            if (!filterToggle.contains(e.target) && !filterDropdown.contains(e.target)) {
                filterToggle.classList.remove('active');
                filterDropdown.classList.remove('active');
            }
        });
    }
});
</script>

@endsection

@section('content')

@if(session('success'))
    <div class="alert alert-success" id="successAlert" style="display:flex;align-items:center;gap:.6rem;margin-bottom:1rem;">
        <span>{{ session('success') }}</span>
        <button onclick="document.getElementById('successAlert').remove()" style="background:none;border:none;cursor:pointer;margin-left:auto;color:inherit;font-size:1.2rem;line-height:1;">&times;</button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-error" id="errorAlert" style="display:flex;align-items:center;gap:.6rem;margin-bottom:1rem;">
        <span>{{ session('error') }}</span>
        <button onclick="document.getElementById('errorAlert').remove()" style="background:none;border:none;cursor:pointer;margin-left:auto;color:inherit;font-size:1.2rem;line-height:1;">&times;</button>
    </div>
@endif

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:1.5rem;">
    <div style="background:#fff;border-radius:10px;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,.08);border-left:4px solid #6c757d;">
        <div style="font-size:.73rem;color:#7f8c8d;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.4rem;">Total</div>
        <div style="font-size:1.8rem;font-weight:700;color:#2c3e50;">{{ $stats['total'] }}</div>
    </div>
    <div style="background:#fff;border-radius:10px;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,.08);border-left:4px solid #ffc107;">
        <div style="font-size:.73rem;color:#7f8c8d;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.4rem;">Pending</div>
        <div style="font-size:1.8rem;font-weight:700;color:#856404;">{{ $stats['pending'] }}</div>
    </div>
    <div style="background:#fff;border-radius:10px;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,.08);border-left:4px solid #0dcaf0;">
        <div style="font-size:.73rem;color:#7f8c8d;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.4rem;">In Transit</div>
        <div style="font-size:1.8rem;font-weight:700;color:#055160;">{{ $stats['in_transit'] }}</div>
    </div>
    <div style="background:#fff;border-radius:10px;padding:1.5rem;box-shadow:0 1px 4px rgba(0,0,0,.08);border-left:4px solid #198754;">
        <div style="font-size:.73rem;color:#7f8c8d;text-transform:uppercase;letter-spacing:.04em;margin-bottom:.4rem;">Delivered</div>
        <div style="font-size:1.8rem;font-weight:700;color:#0a3622;">{{ $stats['delivered'] }}</div>
    </div>
</div>

{{-- Filters --}}
<div style="background:#fff;border-radius:10px;padding:1rem 1.25rem;box-shadow:0 1px 4px rgba(0,0,0,.08);margin-bottom:1.25rem;">
    <form method="GET" action="{{ route('admin.deliveries.index') }}" style="display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer, invoice, driver..."
               style="width:260px;padding:.45rem .75rem;border:1px solid #dee2e6;border-radius:6px;font-size:.875rem;color:#2c3e50;outline:none;">
        <select name="status" style="padding:.45rem .75rem;border:1px solid #dee2e6;border-radius:6px;font-size:.875rem;color:#2c3e50;">
            <option value="">All Statuses</option>
            <option value="pending"    {{ request('status')=='pending'    ? 'selected' : '' }}>Pending</option>
            <option value="in_transit" {{ request('status')=='in_transit' ? 'selected' : '' }}>In Transit</option>
            <option value="delivered"  {{ request('status')=='delivered'  ? 'selected' : '' }}>Delivered</option>
            <option value="cancelled"  {{ request('status')=='cancelled'  ? 'selected' : '' }}>Cancelled</option>
        </select>
        <button type="submit" style="padding:.45rem 1rem;background:#1a73e8;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:.875rem;font-weight:600;">Filter</button>
        @if(request('search') || request('status'))
            <a href="{{ route('admin.deliveries.index') }}" style="padding:.45rem .9rem;background:#6c757d;color:#fff;border-radius:6px;font-size:.875rem;text-decoration:none;">Clear</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div style="background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.08);overflow:hidden;">
    <table style="width:100%;border-collapse:collapse;font-size:.875rem;">
        <thead>
            <tr>
                <th style="background:#f8f9fa;color:#7f8c8d;font-weight:600;font-size:.73rem;text-transform:uppercase;letter-spacing:.04em;padding:.65rem 1rem;border-bottom:1px solid #ecf0f1;text-align:left;">Invoice</th>
                <th style="background:#f8f9fa;color:#7f8c8d;font-weight:600;font-size:.73rem;text-transform:uppercase;letter-spacing:.04em;padding:.65rem 1rem;border-bottom:1px solid #ecf0f1;text-align:left;">Customer</th>
                <th style="background:#f8f9fa;color:#7f8c8d;font-weight:600;font-size:.73rem;text-transform:uppercase;letter-spacing:.04em;padding:.65rem 1rem;border-bottom:1px solid #ecf0f1;text-align:left;">Driver</th>
                <th style="background:#f8f9fa;color:#7f8c8d;font-weight:600;font-size:.73rem;text-transform:uppercase;letter-spacing:.04em;padding:.65rem 1rem;border-bottom:1px solid #ecf0f1;text-align:left;">Status</th>
                <th style="background:#f8f9fa;color:#7f8c8d;font-weight:600;font-size:.73rem;text-transform:uppercase;letter-spacing:.04em;padding:.65rem 1rem;border-bottom:1px solid #ecf0f1;text-align:left;">Last Location</th>
                <th style="background:#f8f9fa;color:#7f8c8d;font-weight:600;font-size:.73rem;text-transform:uppercase;letter-spacing:.04em;padding:.65rem 1rem;border-bottom:1px solid #ecf0f1;text-align:left;">Date Created</th>
                <th style="background:#f8f9fa;color:#7f8c8d;font-weight:600;font-size:.73rem;text-transform:uppercase;letter-spacing:.04em;padding:.65rem 1rem;border-bottom:1px solid #ecf0f1;text-align:left;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($deliveries as $delivery)
            <tr onmouseover="this.style.background='#fafbfc'" onmouseout="this.style.background=''">
                <td style="padding:.65rem 1rem;border-bottom:1px solid #f0f2f5;color:#2c3e50;">
                    <a href="{{ route('admin.billing.show', $delivery->invoice_id) }}" style="color:#1a73e8;font-weight:600;text-decoration:none;">
                        {{ $delivery->invoice->invoice_number ?? '—' }}
                    </a>
                </td>
                <td style="padding:.65rem 1rem;border-bottom:1px solid #f0f2f5;color:#2c3e50;">{{ $delivery->customer->business_name ?? '—' }}</td>
                <td style="padding:.65rem 1rem;border-bottom:1px solid #f0f2f5;color:#2c3e50;">
                    @if($delivery->driver)
                        <span style="display:inline-flex;align-items:center;gap:.4rem;">
                            <span style="width:28px;height:28px;background:#e8f0fe;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:#1a73e8;flex-shrink:0;">
                                {{ strtoupper(substr($delivery->driver->name, 0, 1)) }}
                            </span>
                            {{ $delivery->driver->name }}
                        </span>
                    @else
                        <span style="color:#adb5bd;">No driver assigned</span>
                    @endif
                </td>
                <td style="padding:.65rem 1rem;border-bottom:1px solid #f0f2f5;">
                    @php
                        $colors = ['pending'=>['#fff3cd','#856404'],'in_transit'=>['#cff4fc','#055160'],'delivered'=>['#d1e7dd','#0a3622'],'cancelled'=>['#f8d7da','#58151c']];
                        $c = $colors[$delivery->status] ?? ['#e9ecef','#495057'];
                    @endphp
                    <span style="padding:.25rem .65rem;border-radius:999px;font-size:.73rem;font-weight:600;background:{{ $c[0] }};color:{{ $c[1] }};">
                        {{ $delivery->status_label }}
                    </span>
                </td>
                <td style="padding:.65rem 1rem;border-bottom:1px solid #f0f2f5;font-size:.82rem;color:#6c757d;">
                    @if($delivery->latestLocation)
                        <span style="color:#198754;font-weight:600;">● Live</span><br>
                        {{ number_format((float)$delivery->latestLocation->latitude, 4) }},
                        {{ number_format((float)$delivery->latestLocation->longitude, 4) }}
                    @else
                        <span style="color:#adb5bd;">No GPS yet</span>
                    @endif
                </td>
                <td style="padding:.65rem 1rem;border-bottom:1px solid #f0f2f5;color:#6c757d;">{{ $delivery->created_at->format('M d, Y') }}</td>
                <td style="padding:.65rem 1rem;border-bottom:1px solid #f0f2f5;">
                    <div style="display:flex;gap:.4rem;flex-wrap:wrap;">
                        <a href="{{ route('admin.deliveries.show', $delivery->id) }}"
                           style="padding:.3rem .7rem;background:#1a73e8;color:#fff;border-radius:5px;font-size:.8rem;text-decoration:none;display:inline-flex;align-items:center;gap:.3rem;">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                            Track
                        </a>
                        @if(in_array($delivery->status, ['pending','in_transit']))
                        <form method="POST" action="{{ route('admin.deliveries.cancel', $delivery->id) }}" onsubmit="return confirm('Cancel this delivery?')">
                            @csrf @method('PATCH')
                            <button type="submit" style="padding:.3rem .7rem;background:#dc3545;color:#fff;border:none;border-radius:5px;font-size:.8rem;cursor:pointer;">Cancel</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="padding:3rem;text-align:center;">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#dee2e6" stroke-width="1.5" style="display:block;margin:0 auto 1rem;"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    <span style="color:#adb5bd;font-size:.875rem;">No deliveries found.</span>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($deliveries->hasPages())
    <div style="padding:1rem 1.25rem;border-top:1px solid #f0f0f0;">{{ $deliveries->withQueryString()->links() }}</div>
    @endif
</div>

@endsection
