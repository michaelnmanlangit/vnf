@extends(auth()->user()->role === 'admin' ? 'layouts.admin' : 'layouts.warehouse')

@section('title', 'Edit Invoice')

@section('page-title', 'Edit Invoice')

@section('styles')
<link rel="stylesheet" href="/build/assets/billing-mM0IVGZh.css">
@endsection

@section('content')
<div class="billing-form-container">
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
                <h2>Invoice Information</h2>
                
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
                <h2>Customer Information</h2>
                
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
            <h2>Invoice Summary</h2>
            
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
                @php $lastPmt = $invoice->payments->last(); @endphp
                @if($lastPmt && ($lastPmt->change_amount ?? 0) > 0)
                <div class="summary-row">
                    <span>Last Cash Tendered:</span>
                    <strong>₱{{ number_format($lastPmt->tendered_amount, 2) }}</strong>
                </div>
                <div class="summary-row" style="color:#27ae60;">
                    <span>Last Change Given:</span>
                    <strong style="color:#27ae60;">₱{{ number_format($lastPmt->change_amount, 2) }}</strong>
                </div>
                @endif
            </div>

            <p class="help-text">
                <i class="fas fa-info-circle"></i> 
                To edit invoice items, please create a new invoice. This form only updates basic invoice information and status.
            </p>
        </div>

        <!-- Notes -->
        <div class="form-section">
            <h2>Notes</h2>
            <div class="form-group">
                <textarea name="notes" rows="4" class="form-control" placeholder="Additional notes or terms...">{{ $invoice->notes }}</textarea>
            </div>
        </div>

        <!-- Actions -->
        <div class="form-actions">
            <button type="button" class="btn-submit" onclick="showUpdateModal()">Update Invoice</button>
            <a href="{{ route('admin.billing.show', $invoice->id) }}" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>

<!-- Update Confirmation Modal -->
<div class="modal-overlay" id="updateModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirm Update</h3>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to update invoice <strong id="invoiceNumberToUpdate"></strong>?</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-modal btn-cancel" onclick="hideUpdateModal()">Cancel</button>
            <button type="button" class="btn-modal btn-confirm" style="background: #3498db;" onclick="confirmUpdate()">Update</button>
        </div>
    </div>
</div>

<script>
    let updateFormToSubmit = null;
    let hasChanges = false;
    let originalData = {};

    // Store original form data
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const formData = new FormData(form);
        
        for (let [key, value] of formData.entries()) {
            originalData[key] = value;
        }

        // Monitor changes
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('change', checkForChanges);
            input.addEventListener('input', checkForChanges);
        });
    });

    function checkForChanges() {
        const form = document.querySelector('form');
        const formData = new FormData(form);
        hasChanges = false;
        
        for (let [key, value] of formData.entries()) {
            if (originalData[key] !== value) {
                hasChanges = true;
                break;
            }
        }
        
        const submitBtn = document.querySelector('.btn-submit');
        if (hasChanges) {
            submitBtn.style.background = '#3498db';
            submitBtn.style.cursor = 'pointer';
        } else {
            submitBtn.style.background = '#bdc3c7';
            submitBtn.style.cursor = 'not-allowed';
        }
    }

    function showUpdateModal() {
        if (!hasChanges) {
            alert('No changes detected.');
            return;
        }
        
        const form = document.querySelector('form');
        const invoiceNumber = '{{ $invoice->invoice_number }}';
        
        document.getElementById('invoiceNumberToUpdate').textContent = invoiceNumber;
        document.getElementById('updateModal').style.display = 'flex';
        updateFormToSubmit = form;
    }

    function hideUpdateModal() {
        document.getElementById('updateModal').style.display = 'none';
        updateFormToSubmit = null;
    }

    function confirmUpdate() {
        if (updateFormToSubmit) {
            updateFormToSubmit.submit();
        }
    }

    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const updateModal = document.getElementById('updateModal');
        if (event.target === updateModal) {
            hideUpdateModal();
        }
    });
</script>
@endsection
