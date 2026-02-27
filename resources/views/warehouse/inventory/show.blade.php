@extends('layouts.warehouse')

@section('title', 'Inventory Item Details')

@section('page-title', 'Inventory Details')

@section('styles')
<link rel="stylesheet" href="/build/assets/inventory-Wqoz_iPC.css">
@endsection

@section('content')
<div class="inventory-detail-container">
    <div class="detail-header">
        <h1>{{ $inventory->product_name }}</h1>
        <a href="{{ route('warehouse.inventory.index') }}" class="btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Inventory
        </a>
    </div>

    <div class="detail-content">
        <!-- Product Information Card -->
        <div class="detail-card">
            <div class="card-header">
                <h2>Product Information</h2>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-item">
                        <label>Product ID</label>
                        <p>{{ str_pad($inventory->id, 3, '0', STR_PAD_LEFT) }}</p>
                    </div>
                    <div class="info-item">
                        <label>Product Name</label>
                        <p>{{ $inventory->product_name }}</p>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-item">
                        <label>Description</label>
                        <p>{{ $inventory->description ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-item">
                        <label>Category</label>
                        <p>{{ $inventory->category ?? 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>SKU</label>
                        <p>{{ $inventory->sku ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quantity & Stock Card -->
        <div class="detail-card">
            <div class="card-header">
                <h2>Stock Information</h2>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-item">
                        <label>Current Quantity</label>
                        <p class="quantity-value">{{ $inventory->quantity }}</p>
                    </div>
                    <div class="info-item">
                        <label>Unit</label>
                        <p>{{ $inventory->unit }}</p>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-item">
                        <label>Minimum Stock Level</label>
                        <p>{{ $inventory->minimum_stock ?? 'N/A' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Maximum Stock Level</label>
                        <p>{{ $inventory->maximum_stock ?? 'N/A' }}</p>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-item">
                        <label>Status</label>
                        <p>
                            <span class="status-badge status-{{ strtolower($inventory->status) }}">
                                {{ ucfirst($inventory->status) }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location & Storage Card -->
        <div class="detail-card">
            <div class="card-header">
                <h2>Storage Location</h2>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-item">
                        <label>Storage Location</label>
                        <p>{{ $inventory->location }}</p>
                    </div>
                    <div class="info-item">
                        <label>Storage Temperature</label>
                        <p>{{ $inventory->storage_temperature ?? 'Room Temperature' }}</p>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-item">
                        <label>Special Requirements</label>
                        <p>{{ $inventory->special_requirements ?? 'None' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cost & Pricing Card -->
        <div class="detail-card">
            <div class="card-header">
                <h2>Cost & Pricing</h2>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-item">
                        <label>Unit Cost</label>
                        <p>₱{{ number_format($inventory->unit_cost ?? 0, 2) }}</p>
                    </div>
                    <div class="info-item">
                        <label>Total Cost</label>
                        <p>₱{{ number_format(($inventory->unit_cost ?? 0) * $inventory->quantity, 2) }}</p>
                    </div>
                </div>

                <div class="info-row">
                    <div class="info-item">
                        <label>Selling Price</label>
                        <p>₱{{ number_format($inventory->selling_price ?? 0, 2) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Time Tracking Card -->
        <div class="detail-card">
            <div class="card-header">
                <h2>Record Information</h2>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <div class="info-item">
                        <label>Date Added</label>
                        <p>{{ $inventory->created_at->format('M d, Y - h:i A') }}</p>
                    </div>
                    <div class="info-item">
                        <label>Last Updated</label>
                        <p>{{ $inventory->updated_at->format('M d, Y - h:i A') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="detail-footer">
        <a href="{{ route('warehouse.inventory.index') }}" class="btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Inventory
        </a>
    </div>
</div>

<link rel="stylesheet" href="/build/assets/inventory-Wqoz_iPC.css">
<style>
    .inventory-detail-container {
        max-width: 1000px;
        margin: 2rem auto;
        padding: 0 1.5rem;
    }

    .detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .detail-header h1 {
        font-size: 2rem;
        color: #2c3e50;
        margin: 0;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: #ecf0f1;
        color: #2c3e50;
        text-decoration: none;
        border-radius: 6px;
        transition: background 0.3s;
    }

    .btn-back:hover {
        background: #bdc3c7;
    }

    .detail-content {
        display: grid;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .detail-card {
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .card-header {
        padding: 1.5rem;
        background: #f8f9fa;
        border-bottom: 1px solid #ecf0f1;
    }

    .card-header h2 {
        font-size: 1.2rem;
        color: #2c3e50;
        margin: 0;
    }

    .card-body {
        padding: 1.5rem;
    }

    .info-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .info-row:last-child {
        margin-bottom: 0;
    }

    .info-item label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .info-item p {
        font-size: 1rem;
        color: #2c3e50;
        margin: 0;
        font-weight: 500;
    }

    .quantity-value {
        font-size: 1.5rem !important;
        color: #27ae60 !important;
    }

    .detail-footer {
        text-align: center;
        padding: 2rem 0;
    }

    .status-badge {
        display: inline-block;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .status-available {
        background: #d4edda;
        color: #155724;
    }

    .status-low {
        background: #fff3cd;
        color: #856404;
    }

    .status-out {
        background: #f8d7da;
        color: #721c24;
    }

    @media (max-width: 768px) {
        .detail-header {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
        }

        .detail-header h1 {
            font-size: 1.5rem;
        }

        .info-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection
