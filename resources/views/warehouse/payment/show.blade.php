@extends('layouts.warehouse')

@section('title', 'Payment Details - ' . $inventory->product_name)

@section('page-title', 'Payment Details')

@section('styles')
<style>
    .payment-detail-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 20px;
    }

    .payment-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 2px solid #e0e0e0;
    }

    .payment-header h1 {
        font-size: 28px;
        color: #2c3e50;
        margin: 0;
    }

    .back-btn {
        background-color: #7f8c8d;
        color: white;
        padding: 8px 16px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 14px;
        transition: background-color 0.3s;
    }

    .back-btn:hover {
        background-color: #5a6c7d;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .detail-card {
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .detail-card.financial {
        border-left: 4px solid #27ae60;
        background: rgba(39, 174, 96, 0.02);
    }

    .detail-card.cost-highlight {
        border-left: 4px solid #e74c3c;
        background: rgba(231, 76, 60, 0.02);
    }

    .detail-label {
        font-size: 12px;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .detail-value {
        font-size: 24px;
        font-weight: 700;
        color: #2c3e50;
    }

    .detail-value.currency {
        color: #27ae60;
        font-size: 28px;
    }

    .detail-value.warning {
        color: #e74c3c;
    }

    .pricing-section {
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .pricing-section h2 {
        font-size: 18px;
        color: #2c3e50;
        margin: 0 0 20px 0;
        border-bottom: 2px solid #3498db;
        padding-bottom: 10px;
    }

    .pricing-row {
        display: flex;
        justify-content: space-between;
        padding: 15px 0;
        border-bottom: 1px solid #ecf0f1;
        align-items: center;
    }

    .pricing-row:last-child {
        border-bottom: none;
        padding-top: 20px;
        padding-bottom: 0;
        font-weight: 700;
        font-size: 16px;
        border-top: 2px solid #3498db;
    }

    .pricing-label {
        font-size: 14px;
        color: #7f8c8d;
    }

    .pricing-value {
        font-size: 18px;
        color: #27ae60;
        font-weight: 600;
    }

    .pricing-row:last-child .pricing-value {
        color: #e74c3c;
        font-size: 24px;
    }

    .inventory-section {
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .inventory-section h2 {
        font-size: 18px;
        color: #2c3e50;
        margin: 0 0 20px 0;
        border-bottom: 2px solid #3498db;
        padding-bottom: 10px;
    }

    .inventory-info {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .info-item {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 4px;
    }

    .info-item-label {
        font-size: 12px;
        color: #7f8c8d;
        text-transform: uppercase;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .info-item-value {
        font-size: 16px;
        color: #2c3e50;
        font-weight: 600;
    }

    .timestamp-section {
        background: #f8f9fa;
        border-radius: 4px;
        padding: 15px;
        font-size: 13px;
        color: #7f8c8d;
        margin-top: 20px;
    }

    @media (max-width: 768px) {
        .payment-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .pricing-row {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .inventory-info {
            grid-template-columns: 1fr;
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }

        .detail-value {
            font-size: 20px;
        }

        .detail-value.currency {
            font-size: 24px;
        }
    }
</style>
@endsection

@section('content')
<div class="payment-detail-container">
    <!-- Header -->
    <div class="payment-header">
        <h1>{{ $inventory->product_name }}</h1>
        <a href="{{ route('warehouse.payment.index') }}" class="back-btn">← Back to Payment List</a>
    </div>

    <!-- Key Financial Metrics -->
    <div class="detail-grid">
        <!-- Unit Cost -->
        <div class="detail-card financial">
            <div class="detail-label">Unit Cost</div>
            <div class="detail-value currency">₱{{ number_format($inventory->unit_cost ?? 0, 2) }}</div>
        </div>

        <!-- Selling Price -->
        <div class="detail-card financial">
            <div class="detail-label">Selling Price (Unit)</div>
            <div class="detail-value currency">₱{{ number_format($inventory->selling_price ?? 0, 2) }}</div>
        </div>

        <!-- Gross Profit Per Unit -->
        <div class="detail-card financial">
            <div class="detail-label">Profit Per Unit</div>
            <div class="detail-value currency">₱{{ number_format(($inventory->selling_price ?? 0) - ($inventory->unit_cost ?? 0), 2) }}</div>
        </div>

        <!-- Current Stock -->
        <div class="detail-card">
            <div class="detail-label">Current Stock</div>
            <div class="detail-value">{{ $inventory->quantity }} units</div>
        </div>

        <!-- Profit Margin -->
        <div class="detail-card financial">
            <div class="detail-label">Profit Margin</div>
            <div class="detail-value">
                @if($inventory->unit_cost > 0)
                    {{ number_format((($inventory->selling_price - $inventory->unit_cost) / $inventory->unit_cost) * 100, 2) }}%
                @else
                    N/A
                @endif
            </div>
        </div>

        <!-- Total Stock Value (Cost) -->
        <div class="detail-card cost-highlight">
            <div class="detail-label">Total Stock Cost</div>
            <div class="detail-value warning">₱{{ number_format(($inventory->unit_cost ?? 0) * $inventory->quantity, 2) }}</div>
        </div>
    </div>

    <!-- Detailed Pricing Section -->
    <div class="pricing-section">
        <h2>💰 Pricing Breakdown</h2>
        <div class="pricing-row">
            <span class="pricing-label">Unit Cost</span>
            <span class="pricing-value">₱{{ number_format($inventory->unit_cost ?? 0, 2) }}</span>
        </div>
        <div class="pricing-row">
            <span class="pricing-label">× Quantity in Stock</span>
            <span class="pricing-value">{{ $inventory->quantity }} units</span>
        </div>
        <div class="pricing-row">
            <span class="pricing-label">= Total Cost Value</span>
            <span class="pricing-value">₱{{ number_format(($inventory->unit_cost ?? 0) * $inventory->quantity, 2) }}</span>
        </div>
        <div class="pricing-row">
            <span class="pricing-label">Selling Price per Unit</span>
            <span class="pricing-value">₱{{ number_format($inventory->selling_price ?? 0, 2) }}</span>
        </div>
        <div class="pricing-row">
            <span class="pricing-label">× Quantity in Stock</span>
            <span class="pricing-value">{{ $inventory->quantity }} units</span>
        </div>
        <div class="pricing-row">
            <span class="pricing-label">= Total Revenue Value (if all sold)</span>
            <span class="pricing-value">₱{{ number_format(($inventory->selling_price ?? 0) * $inventory->quantity, 2) }}</span>
        </div>
        <div class="pricing-row">
            <span class="pricing-label">📊 Total Potential Profit</span>
            <span class="pricing-value" style="color: #27ae60; font-size: 20px;">₱{{ number_format((($inventory->selling_price ?? 0) - ($inventory->unit_cost ?? 0)) * $inventory->quantity, 2) }}</span>
        </div>
    </div>

    <!-- Inventory Status Section -->
    <div class="inventory-section">
        <h2>📦 Inventory Status</h2>
        <div class="inventory-info">
            <div class="info-item">
                <div class="info-item-label">Location</div>
                <div class="info-item-value">{{ $inventory->location ?? 'Not Specified' }}</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Current Quantity</div>
                <div class="info-item-value">{{ $inventory->quantity }} units</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Storage Temperature</div>
                <div class="info-item-value">{{ $inventory->storage_temperature ?? 'Room Temperature' }}</div>
            </div>
            <div class="info-item">
                <div class="info-item-label">Expiration Date</div>
                <div class="info-item-value">
                    @if($inventory->expiration_date)
                        {{ \Carbon\Carbon::parse($inventory->expiration_date)->format('M d, Y') }}
                    @else
                        Not Set
                    @endif
                </div>
            </div>
        </div>

        <!-- Timestamps -->
        <div class="timestamp-section">
            <strong>📅 Record Timestamps:</strong><br>
            Created: {{ $inventory->created_at->format('F d, Y \a\t h:i A') }}<br>
            Last Updated: {{ $inventory->updated_at->format('F d, Y \a\t h:i A') }}
        </div>
    </div>
</div>
@endsection
