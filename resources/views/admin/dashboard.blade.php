@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')

@section('styles')
<link rel="stylesheet" href="/build/assets/billing-mM0IVGZh.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
.dash-container{padding:1rem;}
.status-stats .status-cards{grid-template-columns:repeat(5,1fr);}
.dash-two-col{display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.5rem;}
.dash-section{background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07);padding:1.5rem;}
.dash-section-title{font-size:1rem;font-weight:600;color:#2c3e50;margin:0 0 1.25rem;display:flex;align-items:center;gap:.5rem;}
.dash-table{width:100%;border-collapse:collapse;font-size:.875rem;}
.dash-table th{background:#f8f9fa;color:#7f8c8d;font-weight:600;font-size:.73rem;text-transform:uppercase;letter-spacing:.04em;padding:.65rem 1rem;border-bottom:1px solid #ecf0f1;text-align:left;}
.dash-table td{padding:.65rem 1rem;border-bottom:1px solid #f0f2f5;color:#2c3e50;}
.dash-table tr:last-child td{border-bottom:none;}
.dash-table tr:hover td{background:#fafbfc;}
.badge{display:inline-block;padding:.25em .65em;border-radius:999px;font-size:.73rem;font-weight:600;}
.badge-success{background:#d5f5e3;color:#1e8449;}
.badge-warning{background:#fef9e7;color:#b7950b;}
.badge-danger{background:#fde8e8;color:#c0392b;}
.badge-info{background:#d6eaf8;color:#1a5276;}
.badge-secondary{background:#f0f3f4;color:#717d7e;}
.badge-partial{background:#e8d5f5;color:#6c3483;}
.no-data{color:#adb5bd;font-size:.875rem;text-align:center;padding:1rem;}
@media(max-width:1200px){.status-stats .status-cards{grid-template-columns:repeat(3,1fr);}}
@media(max-width:768px){.status-stats .status-cards{grid-template-columns:repeat(2,1fr);}
.dash-two-col{grid-template-columns:1fr;}
.dash-section{overflow-x:auto;padding:1rem .75rem;}
.dash-table{min-width:480px;}}
</style>
@endsection

@section('content')
<div class="dash-container">

    {{-- Stat Tiles --}}
    <div class="status-stats">
        <div class="status-cards">
            <div class="status-card status-total">
                <div class="status-icon"><i class="fas fa-warehouse" style="font-size:1.4rem;"></i></div>
                <div class="status-content">
                    <span class="status-name">Storage Units</span>
                    <div class="status-bottom"><span class="status-count">{{ $totalStorageUnits }}</span></div>
                </div>
            </div>
            <div class="status-card status-paid">
                <div class="status-icon"><i class="fas fa-boxes" style="font-size:1.4rem;"></i></div>
                <div class="status-content">
                    <span class="status-name">Inventory Items</span>
                    <div class="status-bottom"><span class="status-count">{{ $totalInventoryItems }}</span></div>
                </div>
            </div>
            <div class="status-card status-revenue">
                <div class="status-icon"><i class="fas fa-users" style="font-size:1.4rem;"></i></div>
                <div class="status-content">
                    <span class="status-name">Total Employees</span>
                    <div class="status-bottom"><span class="status-count">{{ $totalEmployees }}</span></div>
                </div>
            </div>
            <div class="status-card status-pending">
                <div class="status-icon"><i class="fas fa-file-invoice" style="font-size:1.4rem;"></i></div>
                <div class="status-content">
                    <span class="status-name">Outstanding Invoices</span>
                    <div class="status-bottom"><span class="status-count">{{ $outstandingInvoices }}</span></div>
                </div>
            </div>
            <div class="status-card status-partial">
                <div class="status-icon"><i class="fas fa-exclamation-triangle" style="font-size:1.4rem;"></i></div>
                <div class="status-content">
                    <span class="status-name">Low Stock Items</span>
                    <div class="status-bottom"><span class="status-count">{{ $lowStockItems }}</span></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Alerts & Low Stock --}}
    <div class="dash-two-col">
        <div class="dash-section">
            <p class="dash-section-title">Recent Temperature Alerts</p>
            @if($recentTempAlerts->isEmpty())
                <p class="no-data">No recent alerts</p>
            @else
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Storage Unit</th>
                            <th>Temperature</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTempAlerts as $alert)
                        <tr>
                            <td>{{ $alert->storageUnit->name ?? 'N/A' }}</td>
                            <td><span class="badge badge-danger">{{ number_format($alert->temperature, 1) }}°C</span></td>
                            <td>{{ $alert->recorded_at->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="dash-section">
            <p class="dash-section-title">Low Stock & Expiring Items</p>
            @if($lowStockList->isEmpty())
                <p class="no-data">All items are in stock</p>
            @else
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStockList as $item)
                        <tr>
                            <td>{{ $item->product_name }}</td>
                            <td>{{ number_format($item->quantity, 0) }} {{ $item->unit }}</td>
                            <td>
                                @if($item->status === 'expired')
                                    <span class="badge badge-danger">Expired</span>
                                @elseif($item->status === 'expiring_soon')
                                    <span class="badge badge-warning">Expiring Soon</span>
                                @else
                                    <span class="badge badge-info">Low Stock</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- Recent Invoices & Payments --}}
    <div class="dash-two-col">
        <div class="dash-section">
            <p class="dash-section-title">Recent Invoices</p>
            @if($recentInvoices->isEmpty())
                <p class="no-data">No recent invoices</p>
            @else
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentInvoices as $invoice)
                        <tr>
                            <td>#{{ str_pad($invoice->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $invoice->customer->business_name ?? 'N/A' }}</td>
                            <td>₱{{ number_format($invoice->total_amount, 2) }}</td>
                            <td>
                                @if($invoice->status === 'paid')
                                    <span class="badge badge-success">Paid</span>
                                @elseif($invoice->status === 'partial')
                                    <span class="badge badge-partial">Partial</span>
                                @elseif($invoice->status === 'overdue')
                                    <span class="badge badge-danger">Overdue</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="dash-section">
            <p class="dash-section-title">Recent Payments</p>
            @if($recentPayments->isEmpty())
                <p class="no-data">No recent payments</p>
            @else
                <table class="dash-table">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentPayments as $payment)
                        <tr>
                            <td>#{{ str_pad($payment->invoice_id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $payment->invoice->customer->business_name ?? 'N/A' }}</td>
                            <td>₱{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

</div>
@endsection
