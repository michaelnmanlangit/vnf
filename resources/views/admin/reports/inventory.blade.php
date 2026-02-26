@extends('layouts.admin')

@section('title', 'Reports – Inventory')
@section('page-title', 'Reports')

@section('styles')
@vite(['resources/css/billing.css'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
/* ── Shared Report Styles ────────────────────────────── */
.report-filter{display:flex;flex-wrap:nowrap;align-items:center;gap:.6rem;background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.1);padding:.6rem .8rem;margin-bottom:2rem;width:fit-content;margin-left:auto;}
.report-filter label{font-size:.73rem;font-weight:600;color:#7f8c8d;text-transform:uppercase;letter-spacing:.04em;margin:0;white-space:nowrap;}
.report-filter input[type=date]{padding:.45rem .75rem;border:1px solid #dde1e7;border-radius:8px;font-size:.875rem;font-family:'Poppins',sans-serif;color:#2c3e50;outline:none;transition:border-color .2s;}
.report-filter input[type=date]:focus{border-color:#3498db;}
.btn-rpt{display:inline-flex;align-items:center;gap:.4rem;padding:.5rem 1.1rem;border:none;border-radius:8px;font-size:.875rem;font-family:'Poppins',sans-serif;font-weight:500;cursor:pointer;transition:background .2s;}
.btn-rpt-apply{background:#3498db;color:#fff;}.btn-rpt-apply:hover{background:#2980b9;}
.btn-rpt-reset{background:#ecf0f1;color:#7f8c8d;}.btn-rpt-reset:hover{background:#dde1e7;color:#2c3e50;}
.rpt-section{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07);padding:1.5rem;margin-bottom:1.5rem;}
.rpt-section-title{font-size:1rem;font-weight:600;color:#2c3e50;margin:0 0 1.25rem;display:flex;align-items:center;gap:.5rem;}
.rpt-two-col{display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;}
@media(max-width:768px){.rpt-two-col{grid-template-columns:1fr;}}
.rpt-chart-wrap{position:relative;height:260px;display:flex;align-items:center;justify-content:center;}
.rpt-table{width:100%;border-collapse:collapse;font-size:.875rem;}
.rpt-table th{background:#f8f9fa;color:#7f8c8d;font-weight:600;font-size:.73rem;text-transform:uppercase;letter-spacing:.04em;padding:.65rem 1rem;border-bottom:1px solid #ecf0f1;text-align:left;}
.rpt-table td{padding:.65rem 1rem;border-bottom:1px solid #f0f2f5;color:#2c3e50;}
.rpt-table tr:last-child td{border-bottom:none;}.rpt-table tr:hover td{background:#fafbfc;}
.badge{display:inline-block;padding:.25em .65em;border-radius:999px;font-size:.73rem;font-weight:600;}
.badge-success{background:#d5f5e3;color:#1e8449;}.badge-warning{background:#fef9e7;color:#b7950b;}
.badge-danger{background:#fde8e8;color:#c0392b;}.badge-info{background:#d6eaf8;color:#1a5276;}
.badge-secondary{background:#f0f3f4;color:#717d7e;}
.bar-track{background:#ecf0f1;border-radius:999px;height:8px;overflow:hidden;}
.bar-fill{height:8px;border-radius:999px;}
/* Responsive */
@media(max-width:768px){
  .report-filter{flex-wrap:wrap;width:100%;margin-left:0;}
  .report-filter input[type=date]{flex:1;min-width:0;}
  .rpt-section{overflow-x:auto;padding:1rem .75rem;}
  .rpt-table{min-width:480px;}
  .rpt-chart-wrap{height:200px;}
  .rpt-section-title{font-size:.9rem;}
  .rpt-two-col{gap:.75rem;}
}
@media(max-width:480px){
  .report-filter{gap:.4rem;padding:.5rem .6rem;}
  .btn-rpt{padding:.4rem .75rem;font-size:.8rem;}
  .rpt-table{font-size:.8rem;min-width:400px;}
  .rpt-table th{font-size:.68rem;padding:.5rem .6rem;}
  .rpt-table td{padding:.5rem .6rem;}
  .rpt-section{padding:.75rem .6rem;}
}
</style>
@endsection

@section('content')

{{-- Date Filter --}}
<form method="GET" action="{{ route('admin.reports.inventory') }}" class="report-filter">
    <label>From</label>
    <input type="date" name="date_from" value="{{ $dateFrom }}">
    <label>To</label>
    <input type="date" name="date_to" value="{{ $dateTo }}">
    <button type="submit" class="btn-rpt btn-rpt-apply"><i class="fas fa-filter"></i> Apply</button>
    <a href="{{ route('admin.reports.inventory') }}" class="btn-rpt btn-rpt-reset"><i class="fas fa-undo"></i> Reset</a>
</form>

{{-- Stat Tiles --}}
<div class="status-stats">
    <div class="status-cards">
        <div class="status-card status-total">
            <div class="status-icon"><i class="fas fa-warehouse" style="font-size:1.4rem;"></i></div>
            <div class="status-content">
                <span class="status-name">Total Items</span>
                <div class="status-bottom"><span class="status-count">{{ $totalItems }}</span></div>
            </div>
        </div>
        <div class="status-card status-paid">
            <div class="status-icon"><i class="fas fa-check-circle" style="font-size:1.4rem;"></i></div>
            <div class="status-content">
                <span class="status-name">In Stock</span>
                <div class="status-bottom"><span class="status-count">{{ $inStock }}</span></div>
            </div>
        </div>
        <div class="status-card status-pending">
            <div class="status-icon"><i class="fas fa-exclamation-triangle" style="font-size:1.4rem;"></i></div>
            <div class="status-content">
                <span class="status-name">Low Stock</span>
                <div class="status-bottom"><span class="status-count">{{ $lowStock }}</span></div>
            </div>
        </div>
        <div class="status-card status-partial">
            <div class="status-icon"><i class="fas fa-clock" style="font-size:1.4rem;"></i></div>
            <div class="status-content">
                <span class="status-name">Expiring Soon</span>
                <div class="status-bottom"><span class="status-count">{{ $expiringSoon }}</span></div>
            </div>
        </div>
        <div class="status-card status-overdue">
            <div class="status-icon"><i class="fas fa-ban" style="font-size:1.4rem;"></i></div>
            <div class="status-content">
                <span class="status-name">Expired</span>
                <div class="status-bottom"><span class="status-count">{{ $expiredCount }}</span></div>
            </div>
        </div>
    </div>
</div>

{{-- Chart + Category Table --}}
<div class="rpt-two-col">
    <div class="rpt-section">
        <p class="rpt-section-title">Stock Status Breakdown</p>
        <div class="rpt-chart-wrap">
            <canvas id="stockChart"></canvas>
        </div>
    </div>
    <div class="rpt-section">
        <p class="rpt-section-title">Category Breakdown</p>
        @if($categoryBreakdown->isEmpty())
            <p style="color:#adb5bd;font-size:.875rem;">No data available.</p>
        @else
        <table class="rpt-table">
            <thead><tr><th>Category</th><th>Items</th><th>Total Qty</th></tr></thead>
            <tbody>
                @foreach($categoryBreakdown as $cat)
                <tr>
                    <td>{{ $cat->category ?? 'Uncategorized' }}</td>
                    <td>{{ $cat->count }}</td>
                    <td>{{ number_format($cat->total_qty) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

{{-- Expiring Items Table --}}
@if($expiringItems->isNotEmpty())
<div class="rpt-section">
    <p class="rpt-section-title">Expiring Within 30 Days</p>
    <table class="rpt-table">
        <thead><tr><th>#</th><th>Item</th><th>Category</th><th>Qty</th><th>Expires</th><th>Status</th></tr></thead>
        <tbody>
            @foreach($expiringItems as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->category ?? '—' }}</td>
                <td>{{ number_format($item->quantity) }}</td>
                <td>{{ \Carbon\Carbon::parse($item->expiration_date)->format('M d, Y') }}</td>
                <td><span class="badge badge-warning">Expiring Soon</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

{{-- Expired Items Table --}}
@if($expiredItems->isNotEmpty())
<div class="rpt-section">
    <p class="rpt-section-title">Expired Items</p>
    <table class="rpt-table">
        <thead><tr><th>#</th><th>Item</th><th>Category</th><th>Qty</th><th>Expired On</th><th>Status</th></tr></thead>
        <tbody>
            @foreach($expiredItems as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->item_name }}</td>
                <td>{{ $item->category ?? '—' }}</td>
                <td>{{ number_format($item->quantity) }}</td>
                <td>{{ \Carbon\Carbon::parse($item->expiration_date)->format('M d, Y') }}</td>
                <td><span class="badge badge-danger">Expired</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@endsection

@section('scripts')
<script>
(function () {
    const ctx = document.getElementById('stockChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['In Stock', 'Low Stock', 'Expiring Soon', 'Expired'],
            datasets: [{
                data: [{{ $inStock }}, {{ $lowStock }}, {{ $expiringSoon }}, {{ $expiredCount }}],
                backgroundColor: ['#27ae60', '#f39c12', '#e67e22', '#e74c3c'],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: 'Poppins', size: 12 }, padding: 16 } }
            }
        }
    });
})();
</script>
@endsection
