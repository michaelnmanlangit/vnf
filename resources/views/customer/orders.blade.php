@extends('layouts.customer')

@section('title', 'My Orders')

@section('styles')
<style>
    .orders-container {
        margin-top: 2rem;
    }

    .orders-header {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .orders-header h1 {
        color: #1a202c;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .orders-header p {
        color: #64748b;
    }

    .orders-list {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .order-card {
        border: 2px solid #e2e8f0;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    .order-card:hover {
        border-color: #4169E1;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .order-number {
        color: #1a202c;
        font-size: 1.2rem;
        font-weight: 600;
    }

    .order-date {
        color: #64748b;
        font-size: 0.9rem;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-confirmed {
        background: #cfe2ff;
        color: #084298;
    }

    .status-in_transit {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-out_for_delivery {
        background: #d1ecf1;
        color: #0c5460;
    }

    .status-delivered {
        background: #d4edda;
        color: #155724;
    }

    .status-cancelled {
        background: #f8d7da;
        color: #721c24;
    }

    .payment-badge {
        padding: 0.3rem 0.75rem;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .payment-paid {
        background: #d4edda;
        color: #155724;
    }

    .payment-unpaid {
        background: #fff3cd;
        color: #856404;
    }

    .order-items {
        margin-bottom: 1rem;
    }

    .order-item {
        padding: 0.5rem 0;
        color: #64748b;
        font-size: 0.95rem;
    }

    .order-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .order-total {
        color: #1a202c;
        font-size: 1.3rem;
        font-weight: 700;
    }

    .btn-view-order {
        padding: 0.75rem 1.5rem;
        background: linear-gradient(135deg, #1e3ba8, #4169E1);
        border: none;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        font-family: 'Poppins', sans-serif;
        display: inline-block;
    }

    .btn-view-order:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(65, 105, 225, 0.4);
    }

    .empty-orders {
        text-align: center;
        padding: 3rem;
        color: #64748b;
    }

    .empty-orders i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 2rem;
    }

    .pagination a, .pagination span {
        padding: 0.75rem 1rem;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        color: #1a202c;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .pagination a:hover {
        border-color: #4169E1;
        color: #4169E1;
    }

    .pagination .active {
        background: linear-gradient(135deg, #1e3ba8, #4169E1);
        color: white;
        border-color: transparent;
    }

    @media (max-width: 768px) {
        .order-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .order-footer {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="orders-container">
    <div class="orders-header">
        <h1><i class="fas fa-box"></i> My Orders</h1>
        <p>Track and manage your orders</p>
    </div>

    <div class="orders-list">
        @if($orders->count() > 0)
            @foreach($orders as $order)
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <div class="order-number">{{ $order->order_number }}</div>
                            <div class="order-date">Ordered on {{ $order->created_at->format('M d, Y') }}</div>
                        </div>
                        <div style="display:flex;flex-direction:column;align-items:flex-end;gap:0.4rem;">
                            @php
                                $statusLabel = match($order->status) {
                                    'out_for_delivery', 'in_transit' => 'In Transit',
                                    'confirmed', 'preparing', 'ready_for_delivery' => 'Pending',
                                    default => ucwords(str_replace('_', ' ', $order->status)),
                                };
                                $statusClass = in_array($order->status, ['out_for_delivery','in_transit']) ? 'status-in_transit' : 'status-' . $order->status;
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>
                            <span class="payment-badge payment-{{ $order->payment_status }}">
                                @if($order->payment_status === 'paid')
                                    <i class="fas fa-check-circle"></i>
                                    {{ $order->payment_method ? ucfirst($order->payment_method) . ' — Paid' : 'Paid' }}
                                @else
                                    <i class="fas fa-clock"></i> Unpaid
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="order-items">
                        @foreach($order->items->take(3) as $item)
                            <div class="order-item">
                                {{ $item->quantity }} {{ $item->unit }} - {{ $item->product_name }}
                            </div>
                        @endforeach
                        @if($order->items->count() > 3)
                            <div class="order-item">
                                <em>+{{ $order->items->count() - 3 }} more items</em>
                            </div>
                        @endif
                    </div>

                    <div class="order-footer">
                        <div class="order-total">Total: ₱{{ number_format($order->total, 2) }}</div>
                        <a href="{{ route('customer.order.detail', $order->id) }}" class="btn-view-order">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>
            @endforeach

            @if($orders->hasPages())
            <div class="d-flex justify-content-center">
                {{ $orders->appends(request()->query())->render('pagination.bootstrap-5') }}
            </div>
            @endif
        @else
            <div class="empty-orders">
                <i class="fas fa-box"></i>
                <h2>No orders yet</h2>
                <p>Start shopping to create your first order!</p>
                <a href="{{ route('customer.dashboard') }}" class="btn-view-order" style="margin-top: 1.5rem;">
                    <i class="fas fa-store"></i> Browse Products
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
