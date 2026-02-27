@extends('layouts.admin')

@section('title', 'Deliveries & GPS Tracking')

@section('page-title', 'Deliveries & GPS Tracking')

@section('content')
<div class="content-wrapper" style="padding: 1.5rem;">

    {{-- Stats --}}
    <div class="stats-grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1rem;margin-bottom:1.5rem;">
        <div class="stat-card" style="background:#fff;border-radius:10px;padding:1.25rem;box-shadow:0 1px 4px rgba(0,0,0,.08);border-left:4px solid #6c757d;">
            <div style="font-size:.8rem;color:#6c757d;text-transform:uppercase;letter-spacing:.05em;">Total</div>
            <div style="font-size:1.8rem;font-weight:700;color:#212529;">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card" style="background:#fff;border-radius:10px;padding:1.25rem;box-shadow:0 1px 4px rgba(0,0,0,.08);border-left:4px solid #ffc107;">
            <div style="font-size:.8rem;color:#6c757d;text-transform:uppercase;letter-spacing:.05em;">Pending</div>
            <div style="font-size:1.8rem;font-weight:700;color:#856404;">{{ $stats['pending'] }}</div>
        </div>
        <div class="stat-card" style="background:#fff;border-radius:10px;padding:1.25rem;box-shadow:0 1px 4px rgba(0,0,0,.08);border-left:4px solid #0dcaf0;">
            <div style="font-size:.8rem;color:#6c757d;text-transform:uppercase;letter-spacing:.05em;">In Transit</div>
            <div style="font-size:1.8rem;font-weight:700;color:#055160;">{{ $stats['in_transit'] }}</div>
        </div>
        <div class="stat-card" style="background:#fff;border-radius:10px;padding:1.25rem;box-shadow:0 1px 4px rgba(0,0,0,.08);border-left:4px solid #198754;">
            <div style="font-size:.8rem;color:#6c757d;text-transform:uppercase;letter-spacing:.05em;">Delivered</div>
            <div style="font-size:1.8rem;font-weight:700;color:#0a3622;">{{ $stats['delivered'] }}</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="filter-bar" style="background:#fff;border-radius:10px;padding:1rem 1.25rem;box-shadow:0 1px 4px rgba(0,0,0,.08);margin-bottom:1.25rem;display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;">
        <form method="GET" action="{{ route('admin.deliveries.index') }}" style="display:flex;gap:.75rem;flex-wrap:wrap;width:100%;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search customer, invoice, driver..." 
                   style="flex:1;min-width:200px;padding:.45rem .75rem;border:1px solid #dee2e6;border-radius:6px;font-size:.9rem;">
            <select name="status" style="padding:.45rem .75rem;border:1px solid #dee2e6;border-radius:6px;font-size:.9rem;">
                <option value="">All Statuses</option>
                <option value="pending"    {{ request('status')=='pending'    ? 'selected' : '' }}>Pending</option>
                <option value="in_transit" {{ request('status')=='in_transit' ? 'selected' : '' }}>In Transit</option>
                <option value="delivered"  {{ request('status')=='delivered'  ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled"  {{ request('status')=='cancelled'  ? 'selected' : '' }}>Cancelled</option>
            </select>
            <button type="submit" style="padding:.45rem 1rem;background:#1a73e8;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:.9rem;">Filter</button>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.deliveries.index') }}" style="padding:.45rem .9rem;background:#6c757d;color:#fff;border-radius:6px;font-size:.9rem;text-decoration:none;">Clear</a>
            @endif
        </form>
    </div>

    @if(session('success'))
        <div style="background:#d1e7dd;color:#0a3622;padding:.75rem 1rem;border-radius:8px;margin-bottom:1rem;border:1px solid #a3cfbb;">{{ session('success') }}</div>
    @endif

    {{-- Table --}}
    <div style="background:#fff;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.08);overflow:hidden;">
        <table style="width:100%;border-collapse:collapse;font-size:.9rem;">
            <thead>
                <tr style="background:#f8f9fa;border-bottom:2px solid #dee2e6;">
                    <th style="padding:.75rem 1rem;text-align:left;font-weight:600;color:#495057;">Invoice</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-weight:600;color:#495057;">Customer</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-weight:600;color:#495057;">Driver</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-weight:600;color:#495057;">Status</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-weight:600;color:#495057;">Last Location</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-weight:600;color:#495057;">Date Created</th>
                    <th style="padding:.75rem 1rem;text-align:left;font-weight:600;color:#495057;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deliveries as $delivery)
                <tr style="border-bottom:1px solid #f0f0f0;" onmouseover="this.style.background='#fafafa'" onmouseout="this.style.background=''">
                    <td style="padding:.75rem 1rem;">
                        <a href="{{ route('admin.billing.show', $delivery->invoice_id) }}" style="color:#1a73e8;font-weight:600;text-decoration:none;">
                            {{ $delivery->invoice->invoice_number ?? '—' }}
                        </a>
                    </td>
                    <td style="padding:.75rem 1rem;">{{ $delivery->customer->business_name ?? '—' }}</td>
                    <td style="padding:.75rem 1rem;">
                        @if($delivery->driver)
                            <span style="display:inline-flex;align-items:center;gap:.4rem;">
                                <span style="width:28px;height:28px;background:#e8f0fe;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;color:#1a73e8;">
                                    {{ strtoupper(substr($delivery->driver->name, 0, 1)) }}
                                </span>
                                {{ $delivery->driver->name }}
                            </span>
                        @else
                            <span style="color:#adb5bd;">No driver assigned</span>
                        @endif
                    </td>
                    <td style="padding:.75rem 1rem;">
                        @php
                            $colors = ['pending'=>['#fff3cd','#856404'],'in_transit'=>['#cff4fc','#055160'],'delivered'=>['#d1e7dd','#0a3622'],'cancelled'=>['#f8d7da','#58151c']];
                            $c = $colors[$delivery->status] ?? ['#e9ecef','#495057'];
                        @endphp
                        <span style="padding:.25rem .65rem;border-radius:20px;font-size:.78rem;font-weight:600;background:{{ $c[0] }};color:{{ $c[1] }};">
                            {{ $delivery->status_label }}
                        </span>
                    </td>
                    <td style="padding:.75rem 1rem;font-size:.82rem;color:#6c757d;">
                        @if($delivery->latestLocation)
                            <span style="color:#198754;">● Live</span>
                            <br>{{ number_format((float)$delivery->latestLocation->latitude, 4) }},
                            {{ number_format((float)$delivery->latestLocation->longitude, 4) }}
                        @else
                            <span style="color:#adb5bd;">No GPS yet</span>
                        @endif
                    </td>
                    <td style="padding:.75rem 1rem;color:#6c757d;font-size:.85rem;">{{ $delivery->created_at->format('M d, Y') }}</td>
                    <td style="padding:.75rem 1rem;">
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
                    <td colspan="7" style="padding:3rem;text-align:center;color:#adb5bd;">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#dee2e6" stroke-width="1.5" style="display:block;margin:0 auto 1rem;"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        No deliveries found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        @if($deliveries->hasPages())
        <div style="padding:1rem 1.25rem;border-top:1px solid #f0f0f0;">{{ $deliveries->withQueryString()->links() }}</div>
        @endif
    </div>
</div>
@endsection
