@extends('layouts.admin')

@section('title', 'Invoice Details')

@section('page-title', 'Invoice Details')

@section('content')
<div class="billing-container">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <!-- Header Navigation -->
    <div class="receipt-page-header">
        <a href="{{ route('admin.billing.index') }}" class="manage-customers-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Invoices
        </a>
        
        <div class="receipt-page-actions">
            <a href="{{ route('admin.billing.edit', $invoice->id) }}" class="btn-submit">
                <i class="fas fa-edit"></i> Edit
            </a>
            <button onclick="window.print()" class="btn-primary">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <!-- Receipt Container -->
    <div class="receipt-container">
        <!-- Receipt Content -->
        <div class="receipt-content">
            <!-- Company Header -->
            <div class="company-header">
                <h1>V&F ICE PLANT</h1>
                <p class="company-tagline">ICE PRODUCTION & DISTRIBUTION</p>
                <div class="company-contact">
                    <span>Contact: (123) 456-7890</span>
                    <span>Email: info@vficeplant.com</span>
                </div>
            </div>

            <!-- Receipt Title -->
            <div class="receipt-title">
                SALES RECEIPT
            </div>

            <!-- Invoice Title & Status -->
            <div class="invoice-title-section">
                <h2>Invoice #{{ $invoice->invoice_number }}</h2>
                <span class="status-badge status-{{ $invoice->status }}">
                    {{ ucwords(str_replace('_', ' ', $invoice->status)) }}
                </span>
            </div>

            <!-- Customer & Invoice Info -->
            <div class="receipt-details-grid">
                <div class="bill-to-section">
                    <h3>Bill To:</h3>
                    <div class="customer-info">
                        <h4>{{ $invoice->customer->business_name }}</h4>
                        <p>Contact: {{ $invoice->customer->contact_person }}</p>
                        @if($invoice->customer->email)
                            <p>Email: {{ $invoice->customer->email }}</p>
                        @endif
                        <p>Phone: {{ $invoice->customer->phone }}</p>
                    </div>
                </div>
                
                <div class="invoice-meta">
                    <div class="meta-row">
                        <span>Date:</span>
                        <strong>{{ $invoice->invoice_date->format('d/m/Y') }}</strong>
                    </div>
                    <div class="meta-row">
                        <span>Due Date:</span>
                        <strong>{{ $invoice->due_date->format('d/m/Y') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Items Header -->
            <div class="receipt-items-header">
                <span>Qty Item Description</span>
                <span>Price</span>
            </div>

            <!-- Items List -->
            @foreach($invoice->items as $index => $item)
                <div class="receipt-item-row" style="display: flex; justify-content: space-between; margin-bottom: 0.2rem;">
                    <div style="width: 75%;">
                        <strong>{{ number_format($item->quantity, 0) }}x {{ $item->product_name }}</strong>
                    </div>
                    <div style="width: 25%; text-align: right;">
                        <strong>₱{{ number_format($item->total, 2) }}</strong>
                    </div>
                </div>
            @endforeach

            <!-- Items Sold Summary -->
            <div class="items-sold-summary">
                {{ $invoice->items->count() }}x Items Sold
            </div>

            <!-- Totals Section -->
            <div class="receipt-totals">
                <div class="totals-row">
                    <span>Sub Total:</span>
                    <span>₱{{ number_format($invoice->subtotal, 2) }}</span>
                </div>
                <div class="totals-row">
                    <span>Tax (12%):</span>
                    <span>₱{{ number_format($invoice->tax, 2) }}</span>
                </div>
                <div class="totals-row total-line">
                    <span>Total:</span>
                    <strong>₱{{ number_format($invoice->total_amount, 2) }}</strong>
                </div>
                <div class="totals-row">
                    <span>Amount Paid:</span>
                    <span>₱{{ number_format($invoice->total_paid, 2) }}</span>
                </div>
                <div class="totals-row">
                    <span>Change:</span>
                    <span>₱{{ number_format($invoice->total_paid - $invoice->total_amount, 2) }}</span>
                </div>
            </div>

            @if($invoice->notes)
                <div class="receipt-notes">
                    <h4>Notes:</h4>
                    <p>{{ $invoice->notes }}</p>
                </div>
            @endif

            <!-- Receipt Footer -->
            <div class="receipt-footer">
                <div class="receipt-thank-you">
                    THANK YOU
                </div>
                <div class="receipt-transaction-info">
                    {{ $invoice->invoice_number }} - {{ $invoice->invoice_date->format('d/m/Y') }} {{ $invoice->created_at->format('H:i') }} - Admin
                </div>
            </div>
        </div>
    </div>


</div>


@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/billing.css'])
@endsection
