@extends('layouts.admin')

@section('title', 'Reports – Temperature')
@section('page-title', 'Reports')

@section('styles')
<link rel="stylesheet" href="/build/assets/billing-mM0IVGZh.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
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
.rpt-chart-wrap{position:relative;height:260px;}
.rpt-table{width:100%;border-collapse:collapse;font-size:.875rem;}
.rpt-table th{background:#f8f9fa;color:#7f8c8d;font-weight:600;font-size:.73rem;text-transform:uppercase;letter-spacing:.04em;padding:.65rem 1rem;border-bottom:1px solid #ecf0f1;text-align:left;}
.rpt-table td{padding:.65rem 1rem;border-bottom:1px solid #f0f2f5;color:#2c3e50;}
.rpt-table tr:last-child td{border-bottom:none;}.rpt-table tr:hover td{background:#fafbfc;}
.badge{display:inline-block;padding:.25em .65em;border-radius:999px;font-size:.73rem;font-weight:600;}
.badge-success{background:#d5f5e3;color:#1e8449;}.badge-warning{background:#fef9e7;color:#b7950b;}
.badge-danger{background:#fde8e8;color:#c0392b;}.badge-info{background:#d6eaf8;color:#1a5276;}
/* Responsive */
@media(max-width:768px){
  .report-filter{flex-wrap:wrap;width:100%;margin-left:0;}
  .report-filter input[type=date]{flex:1;min-width:0;}
  .rpt-section{overflow-x:auto;padding:1rem .75rem;}
  .rpt-table{min-width:520px;}
  .rpt-chart-wrap{height:200px;}
  .rpt-section-title{font-size:.9rem;}
  .rpt-two-col{gap:.75rem;}
}
@media(max-width:480px){
  .report-filter{gap:.4rem;padding:.5rem .6rem;}
  .btn-rpt{padding:.4rem .75rem;font-size:.8rem;}
  .rpt-table{font-size:.8rem;min-width:440px;}
  .rpt-table th{font-size:.68rem;padding:.5rem .6rem;}
  .rpt-table td{padding:.5rem .6rem;}
  .rpt-section{padding:.75rem .6rem;}
}
</style>
@endsection

@section('content')

{{-- Date Filter --}}
<form method="GET" action="{{ route('admin.reports.temperature') }}" class="report-filter">
    <label>From</label>
    <input type="date" name="date_from" value="{{ $dateFrom }}">
    <label>To</label>
    <input type="date" name="date_to" value="{{ $dateTo }}">
    <button type="submit" class="btn-rpt btn-rpt-apply"><i class="fas fa-filter"></i> Apply</button>
    <a href="{{ route('admin.reports.temperature') }}" class="btn-rpt btn-rpt-reset"><i class="fas fa-undo"></i> Reset</a>
</form>

{{-- Stat Tiles --}}
<div class="status-stats">
    <div class="status-cards">
        <div class="status-card status-total">
            <div class="status-icon"><i class="fas fa-database" style="font-size:1.4rem;"></i></div>
            <div class="status-content">
                <span class="status-name">Total Logs</span>
                <div class="status-bottom"><span class="status-count">{{ $tempLogs->count() }}</span></div>
            </div>
        </div>
        <div class="status-card status-paid">
            <div class="status-icon"><i class="fas fa-check-circle" style="font-size:1.4rem;"></i></div>
            <div class="status-content">
                <span class="status-name">Normal</span>
                <div class="status-bottom"><span class="status-count">{{ $normalCount }}</span></div>
            </div>
        </div>
        <div class="status-card status-pending">
            <div class="status-icon"><i class="fas fa-exclamation-triangle" style="font-size:1.4rem;"></i></div>
            <div class="status-content">
                <span class="status-name">Warning</span>
                <div class="status-bottom"><span class="status-count">{{ $warningCount }}</span></div>
            </div>
        </div>
        <div class="status-card status-overdue">
            <div class="status-icon"><i class="fas fa-radiation" style="font-size:1.4rem;"></i></div>
            <div class="status-content">
                <span class="status-name">Critical</span>
                <div class="status-bottom"><span class="status-count">{{ $criticalCount }}</span></div>
            </div>
        </div>
    </div>
</div>

{{-- Chart + Unit Summary Table --}}
<div class="rpt-two-col">
    <div class="rpt-section">
        <p class="rpt-section-title">Status Breakdown</p>
        <div class="rpt-chart-wrap">
            <canvas id="tempChart"></canvas>
        </div>
    </div>
    <div class="rpt-section">
        <p class="rpt-section-title">Per Unit Summary</p>
        @if($unitSummary->isEmpty())
            <p style="color:#adb5bd;font-size:.875rem;">No logs for the selected period.</p>
        @else
        <table class="rpt-table">
            <thead>
                <tr>
                    <th>Unit</th>
                    <th>Logs</th>
                    <th>Avg °C</th>
                    <th>Min / Max</th>
                    <th>Alerts</th>
                </tr>
            </thead>
            <tbody>
                @foreach($unitSummary as $unit)
                <tr>
                    <td>{{ $unit->storageUnit->unit_name ?? 'Unit '.$unit->storage_unit_id }}</td>
                    <td>{{ $unit->log_count }}</td>
                    <td>{{ $unit->avg_temp }}°</td>
                    <td>{{ $unit->min_temp }}° / {{ $unit->max_temp }}°</td>
                    <td>
                        @if($unit->critical_count > 0)
                            <span class="badge badge-danger">{{ $unit->critical_count }} critical</span>
                        @endif
                        @if($unit->warning_count > 0)
                            <span class="badge badge-warning">{{ $unit->warning_count }} warn</span>
                        @endif
                        @if($unit->critical_count == 0 && $unit->warning_count == 0)
                            <span class="badge badge-success">OK</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

{{-- Recent Logs Table --}}
@if($tempLogs->isNotEmpty())
<div class="rpt-section">
    <p class="rpt-section-title">Temperature Logs</p>
    <table class="rpt-table">
        <thead>
            <tr><th>#</th><th>Unit</th><th>Temperature</th><th>Status</th><th>Recorded At</th></tr>
        </thead>
        <tbody>
            @foreach($tempLogs->take(50) as $i => $log)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $log->storageUnit->unit_name ?? 'Unit '.$log->storage_unit_id }}</td>
                <td>{{ $log->temperature }}°C</td>
                <td>
                    @if($log->status === 'critical')
                        <span class="badge badge-danger">Critical</span>
                    @elseif($log->status === 'warning')
                        <span class="badge badge-warning">Warning</span>
                    @else
                        <span class="badge badge-success">Normal</span>
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($log->recorded_at)->format('M d, Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($tempLogs->count() > 50)
        <p style="font-size:.8rem;color:#adb5bd;margin-top:.75rem;">Showing 50 of {{ $tempLogs->count() }} records.</p>
    @endif
</div>
@endif

@endsection

@section('scripts')
<script>
(function () {
    const ctx = document.getElementById('tempChart');
    if (!ctx) return;
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Normal', 'Warning', 'Critical'],
            datasets: [{
                label: 'Logs',
                data: [{{ $normalCount }}, {{ $warningCount }}, {{ $criticalCount }}],
                backgroundColor: ['#27ae60', '#f39c12', '#e74c3c'],
                borderRadius: 6,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, ticks: { font: { family: 'Poppins', size: 11 } }, grid: { color: '#f0f2f5' } },
                x: { ticks: { font: { family: 'Poppins', size: 11 } }, grid: { display: false } }
            }
        }
    });
})();
</script>
@endsection
