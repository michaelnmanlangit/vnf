@extends('layouts.admin')

@section('title', 'Edit Invoice')

@section('content')
<div class="billing-container">
    @if($errors->any())
        <div class="alert alert-error">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.billing.update', $invoice->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-grid">
            <!-- Invoice Information -->
            <div class="form-section">
                <h3>Invoice Information</h3>
                
                <div class="form-group">
                    <label>Invoice Number</label>
                    <input type="text" value="{{ $invoice->invoice_number }}" readonly class="form-control">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Invoice Date <span class="required">*</span></label>
                        <input type="date" name="invoice_date" value="{{ $invoice->invoice_date->format('Y-m-d') }}" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Due Date <span class="required">*</span></label>
                        <input type="date" name="due_date" value="{{ $invoice->due_date->format('Y-m-d') }}" required class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label>Status <span class="required">*</span></label>
                    <select name="status" required class="form-control">
                        <option value="pending" {{ $invoice->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ $invoice->status == 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="partially_paid" {{ $invoice->status == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                        <option value="overdue" {{ $invoice->status == 'overdue' ? 'selected' : '' }}>Overdue</option>
                        <option value="cancelled" {{ $invoice->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="form-section">
                <h3>Customer Information</h3>
                
                <div class="form-group">
                    <label>Select Customer <span class="required">*</span></label>
                    <select name="customer_id" required class="form-control">
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ $invoice->customer_id == $customer->id ? 'selected' : '' }}>
                                {{ $customer->business_name }} - {{ $customer->contact_person }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Invoice Summary -->
        <div class="form-section">
            <h3>Invoice Summary</h3>
            
            <div class="invoice-summary">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <strong>₱{{ number_format($invoice->subtotal, 2) }}</strong>
                </div>
                <div class="summary-row">
                    <span>Tax (12%):</span>
                    <strong>₱{{ number_format($invoice->tax, 2) }}</strong>
                </div>
                <div class="summary-row">
                    <span>Total Amount:</span>
                    <strong>₱{{ number_format($invoice->total_amount, 2) }}</strong>
                </div>
                <div class="summary-row">
                    <span>Amount Paid:</span>
                    <strong class="text-success">₱{{ number_format($invoice->total_paid, 2) }}</strong>
                </div>
                <div class="summary-row">
                    <span>Balance:</span>
                    <strong class="text-danger">₱{{ number_format($invoice->balance, 2) }}</strong>
                </div>
            </div>

            <p class="help-text">
                <i class="fas fa-info-circle"></i> 
                To edit invoice items, please create a new invoice. This form only updates basic invoice information and status.
            </p>
        </div>

        <!-- Notes -->
        <div class="form-section">
            <h3>Notes</h3>
            <div class="form-group">
                <textarea name="notes" rows="4" class="form-control" placeholder="Additional notes or terms...">{{ $invoice->notes }}</textarea>
            </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
            <button type="submit" class="btn-submit">Update Invoice</button>
            <a href="{{ route('admin.billing.show', $invoice->id) }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/billing.css'])
@endsection
