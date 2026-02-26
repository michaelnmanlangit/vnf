@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.warehouse')

@section('title', 'Invoice Details')

@section('page-title', 'Invoice Details')

@section('content')
<div class="billing-container">
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
            <button onclick="printThermalReceipt()" class="btn-primary">
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
                <h1>V&F Ice Plant and Cold Storage Inc</h1>
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
                        <strong>{{ number_format($item->quantity, 0) }}{{ $item->unit }} {{ $item->product_name }}</strong>
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
                @php $lastPayment = $invoice->payments->last(); @endphp
                @if($lastPayment && $lastPayment->tendered_amount && $lastPayment->change_amount > 0)
                <div class="totals-row">
                    <span>Cash Tendered:</span>
                    <span>₱{{ number_format($lastPayment->tendered_amount, 2) }}</span>
                </div>
                <div class="totals-row" style="font-weight:700; color:#27ae60;">
                    <span>Change:</span>
                    <span>₱{{ number_format($lastPayment->change_amount, 2) }}</span>
                </div>
                @endif
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

    <!-- Payment Recording Section (Not Printed) -->
    @if($invoice->status !== 'paid' && $invoice->status !== 'cancelled')
    <div class="payment-recording-section">
        <div class="payment-section-header">
            <h3>Record Payment</h3>
            <p class="balance-info">
                Outstanding Balance: <strong>₱{{ number_format($invoice->balance, 2) }}</strong>
            </p>
        </div>

        <form action="{{ route('admin.billing.payment.store', $invoice->id) }}" method="POST" class="payment-form">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label for="amount">Payment Amount *</label>
                    <input type="number" name="amount" id="amount" step="0.01" min="0.01" 
                           value="{{ $invoice->balance }}" required>
                </div>

                <div class="form-group">
                    <label for="payment_date">Payment Date *</label>
                    <input type="date" name="payment_date" id="payment_date" 
                           value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="payment_method">Payment Method *</label>
                    <select name="payment_method" id="payment_method" required>
                        <option value="">Select Method</option>
                        <option value="cash" selected>Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="check">Check</option>
                        <option value="online_payment">Online Payment</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="payment_reference">Reference Number</label>
                    <input type="text" name="payment_reference" id="payment_reference" 
                           placeholder="Optional reference number">
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea name="notes" id="notes" rows="2" placeholder="Optional payment notes"></textarea>
            </div>

            <!-- Live Change Calculator -->
            <div id="changeDisplay" style="display:none; background:#f0fdf4; border:1px solid #86efac; border-radius:8px; padding:0.75rem 1rem; margin-bottom:1rem;">
                <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#555;">
                    <span>Outstanding Balance:</span>
                    <strong>₱{{ number_format($invoice->balance, 2) }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:0.9rem; color:#555; margin-top:0.25rem;">
                    <span>Cash Tendered:</span>
                    <strong id="tenderedDisplay">₱0.00</strong>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:1rem; font-weight:700; color:#16a34a; margin-top:0.25rem; border-top:1px solid #86efac; padding-top:0.25rem;">
                    <span>Change:</span>
                    <span id="changeAmount">₱0.00</span>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">
                    Record Payment
                </button>
            </div>
        </form>
        <script>
            (function() {
                const amountInput = document.getElementById('amount');
                const balance = {{ $invoice->balance }};
                const changeDisplay = document.getElementById('changeDisplay');
                const tenderedDisplay = document.getElementById('tenderedDisplay');
                const changeAmount = document.getElementById('changeAmount');
                amountInput.addEventListener('input', function() {
                    const tendered = parseFloat(this.value) || 0;
                    const change = tendered - balance;
                    if (tendered > 0) {
                        changeDisplay.style.display = 'block';
                        tenderedDisplay.textContent = '₱' + tendered.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2});
                        changeAmount.textContent = change > 0
                            ? '₱' + change.toLocaleString('en-PH', {minimumFractionDigits:2, maximumFractionDigits:2})
                            : '₱0.00';
                    } else {
                        changeDisplay.style.display = 'none';
                    }
                });
            })();
        </script>
    </div>
    @endif

    <!-- Payment History Section -->
    @if($invoice->payments->count() > 0)
    <div class="payment-history-section">
        <h3>Payment History</h3>
        <div class="payment-history-table">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Reference</th>
                        <th>Method</th>
                        <th>Amount</th>
                        <th>Tendered</th>
                        <th>Change</th>
                        <th>Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoice->payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_date->format('M d, Y') }}</td>
                        <td>{{ $payment->payment_reference ?? '-' }}</td>
                        <td>
                            <span class="payment-method-badge">
                                {{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}
                            </span>
                        </td>
                        <td><strong>₱{{ number_format($payment->amount, 2) }}</strong></td>
                        <td>{{ $payment->tendered_amount ? '₱' . number_format($payment->tendered_amount, 2) : '-' }}</td>
                        <td>{{ ($payment->change_amount ?? 0) > 0 ? '₱' . number_format($payment->change_amount, 2) : '-' }}</td>
                        <td>{{ $payment->notes ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>




@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite(['resources/css/billing.css'])
@endsection

@section('scripts')
<script>
function printThermalReceipt() {
    var receiptContent = document.querySelector('.receipt-content').innerHTML;

    var css = `
        @page {
            size: 80mm auto;
            margin: 3mm 2mm;
        }
        * { box-sizing: border-box; }
        html, body {
            margin: 0 !important;
            padding: 0 !important;
            width: 80mm !important;
            max-width: 80mm !important;
            background: white;
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            color: #000;
            line-height: 1.3;
        }
        .company-header {
            text-align: center;
            margin-bottom: 0.5rem;
            padding-bottom: 0.4rem;
            border-bottom: 1px dashed #000;
        }
        .company-header h1 { font-size: 15px; font-weight: bold; margin: 0 0 2px 0; letter-spacing: 2px; }
        .company-tagline   { font-size: 9px; margin: 1px 0; display: block; }
        .company-contact span { font-size: 9px; display: block; margin: 1px 0; }
        .receipt-title {
            text-align: center; font-size: 12px; font-weight: bold;
            margin: 0.4rem 0; padding: 0.2rem 0; border-bottom: 1px dashed #000;
        }
        .invoice-title-section {
            text-align: center; margin-bottom: 0.4rem;
            border-bottom: 1px dashed #000; padding-bottom: 0.4rem;
        }
        .invoice-title-section h2 { font-size: 12px; font-weight: bold; margin: 0 0 3px 0; }
        .status-badge { font-size: 9px; padding: 1px 4px; font-weight: bold; text-transform: uppercase; }
        .status-paid    { background: #d4edda; color: #155724; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-overdue { background: #f8d7da; color: #721c24; }
        .receipt-details-grid {
            margin-bottom: 0.4rem; padding-bottom: 0.4rem; border-bottom: 1px dashed #000;
        }
        .bill-to-section h3 { font-size: 10px; margin: 0 0 2px 0; }
        .customer-info h4   { font-size: 10px; margin: 0; font-weight: bold; }
        .customer-info p    { font-size: 9px; margin: 1px 0; }
        .invoice-meta       { margin-top: 0.3rem; }
        .meta-row           { display: flex; justify-content: space-between; font-size: 9px; margin: 1px 0; }
        .receipt-items-header {
            display: flex; justify-content: space-between; font-size: 10px;
            font-weight: bold; border-bottom: 1px dashed #000; padding-bottom: 2px; margin-bottom: 3px;
        }
        .receipt-item-row { display: flex; justify-content: space-between; font-size: 10px; margin-bottom: 2px; }
        .items-sold-summary {
            text-align: center; font-size: 10px; font-weight: bold;
            margin: 0.3rem 0; border-bottom: 1px dashed #000; padding-bottom: 0.3rem;
        }
        .receipt-totals { border-top: 1px dashed #000; padding-top: 0.3rem; margin-top: 0; }
        .totals-row     { display: flex; justify-content: space-between; font-size: 10px; margin: 1px 0; }
        .total-line     { border-top: 1px dashed #000; margin-top: 3px; padding-top: 3px; font-size: 12px; font-weight: bold; }
        .receipt-notes  { margin: 0.4rem 0; }
        .receipt-notes h4 { font-size: 10px; margin: 0 0 2px 0; }
        .receipt-notes p  { font-size: 9px; margin: 0; }
        .receipt-footer {
            text-align: center; margin-top: 0.5rem;
            padding-top: 0.4rem; border-top: 1px dashed #000;
        }
        .receipt-thank-you       { font-size: 13px; font-weight: bold; letter-spacing: 3px; margin: 0.3rem 0; }
        .receipt-transaction-info { font-size: 8px; margin: 2px 0; }
    `;

    // Build a hidden iframe at exactly 80mm (302px) wide
    var iframe = document.createElement('iframe');
    iframe.style.cssText = 'position:fixed;top:0;left:-9999px;width:302px;height:1px;border:none;';
    document.body.appendChild(iframe);

    var doc = iframe.contentDocument || iframe.contentWindow.document;
    doc.open();
    doc.write('<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Receipt</title><style>' + css + '</style></head><body>' + receiptContent + '</body></html>');
    doc.close();

    // Wait for content to render, then print the iframe
    setTimeout(function() {
        iframe.contentWindow.focus();
        iframe.contentWindow.print();
        // Remove iframe after print dialog closes
        setTimeout(function() { document.body.removeChild(iframe); }, 2000);
    }, 400);
}
</script>
@endsection
