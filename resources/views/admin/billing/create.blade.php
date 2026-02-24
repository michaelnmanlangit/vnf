@extends('layouts.admin')

@section('title', 'Create Invoice')

@section('page-title', 'Create Invoice')
@section('styles')
@vite(['resources/css/billing.css'])
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection

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

    <form action="{{ route('admin.billing.store') }}" method="POST" id="invoiceForm">
        @csrf
        
        <div class="form-grid">
            <!-- Invoice Information -->
            <div class="form-section">
                <h3>Invoice Information</h3>
                
                <div class="form-group">
                    <label>Invoice Number <span class="required">*</span></label>
                    <input type="text" name="invoice_number" value="{{ $invoiceNumber }}" readonly class="form-control">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Invoice Date <span class="required">*</span></label>
                        <input type="date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required class="form-control">
                    </div>

                    <div class="form-group">
                        <label>Due Date <span class="required">*</span></label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}" required class="form-control">
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="form-section">
                <h3>Customer Information</h3>
                
                <div class="form-group">
                    <label>Select Customer <span class="required">*</span></label>
                    <select name="customer_id" required class="form-control" id="customerSelect">
                        <option value="">-- Select Customer --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                {{ $customer->business_name }} - {{ $customer->contact_person }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Invoice Items -->
        <div class="form-section">
            <div class="section-header">
                <h3>Invoice Items</h3>
                <button type="button" class="btn btn-sm btn-primary" onclick="addItem()">
                    Add Item
                </button>
            </div>

            <div class="items-table-container">
                <table class="items-table">
                    <thead>
                        <tr>
                            <th style="width: 30%;">Product Name</th>
                            <th style="width: 15%;">Quantity</th>
                            <th style="width: 12%;">Unit</th>
                            <th style="width: 18%;">Unit Price</th>
                            <th style="width: 20%;">Total</th>
                            <th style="width: 5%;"></th>
                        </tr>
                    </thead>
                    <tbody id="itemsContainer">
                        <!-- Items will be added here dynamically -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Totals -->
        <div class="totals-section">
            <div class="totals-grid">
                <div class="form-group">
                    <label>Notes (Optional)</label>
                    <textarea name="notes" rows="4" class="form-control" placeholder="Additional notes or terms...">{{ old('notes') }}</textarea>
                </div>

                <div class="totals-calculation">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <strong id="subtotalDisplay">₱0.00</strong>
                    </div>
                    <div class="total-row">
                        <span>Tax (12%):</span>
                        <strong id="taxDisplay">₱0.00</strong>
                    </div>
                    <div class="total-row total-final">
                        <span>Total Amount:</span>
                        <strong id="totalDisplay">₱0.00</strong>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="form-actions">
                <button type="submit" class="btn-submit">Create Invoice</button>
                <a href="{{ route('admin.billing.index') }}" class="btn-cancel">Cancel</a>
            </div>
        </div>
    </form>
</div>

<script>
let itemCount = 1;

// Inventory data from PHP
const inventoryItems = @json($inventory);

function addItem() {
    const container = document.getElementById('itemsContainer');
    const row = document.createElement('tr');
    row.id = `item-${itemCount}`;
    
    let inventoryOptions = '<option value="">-- Select Product --</option>';
    inventoryItems.forEach(item => {
        const statusText = item.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        const statusIndicator = item.status === 'low_stock' ? ' ⚠️' : item.status === 'expiring_soon' ? ' ⏰' : '';
        inventoryOptions += `<option value="${item.id}" data-unit="${item.unit}" data-name="${item.product_name}">${item.product_name} (${item.quantity} ${item.unit} available) - ${statusText}${statusIndicator}</option>`;
    });
    
    row.innerHTML = `
        <td>
            <select name="items[${itemCount}][product_name]" required class="form-control" onchange="selectProduct(${itemCount}, this)">
                ${inventoryOptions}
            </select>
            <input type="hidden" name="items[${itemCount}][inventory_id]" value="" id="inventory-id-${itemCount}">
        </td>
        <td>
            <input type="number" name="items[${itemCount}][quantity]" required class="form-control item-quantity" step="0.01" min="0.01" value="1" oninput="calculateItemTotal(${itemCount})">
        </td>
        <td>
            <input type="text" name="items[${itemCount}][unit]" required class="form-control" readonly id="unit-${itemCount}" placeholder="Unit" value="">
        </td>
        <td>
            <input type="number" name="items[${itemCount}][unit_price]" required class="form-control item-price" step="0.01" min="0" value="0" oninput="calculateItemTotal(${itemCount})">
        </td>
        <td>
            <strong class="item-total" id="item-total-${itemCount}">₱0.00</strong>
        </td>
        <td>
            <button type="button" class="btn-icon btn-delete" onclick="removeItem(${itemCount})" title="Remove">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    container.appendChild(row);
    itemCount++;
}
function selectProduct(itemIndex, selectElement) {
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    if (selectedOption.value) {
        // Set inventory ID
        document.getElementById(`inventory-id-${itemIndex}`).value = selectedOption.value;
        
        // Set the product name for form submission
        selectElement.setAttribute('data-product-name', selectedOption.getAttribute('data-name'));
        
        // Auto-fill unit field with the product's unit
        const unit = selectedOption.getAttribute('data-unit');
        if (unit) {
            const unitField = document.getElementById(`unit-${itemIndex}`);
            if (unitField) {
                unitField.value = unit;
            }
        }
    } else {
        // Clear fields if no product selected
        document.getElementById(`inventory-id-${itemIndex}`).value = '';
        const unitField = document.getElementById(`unit-${itemIndex}`);
        if (unitField) {
            unitField.value = '';
        }
    }
}
function removeItem(id) {
    const row = document.getElementById(`item-${id}`);
    if (row) {
        row.remove();
        calculateTotals();
    }
}

// Add first item automatically
document.addEventListener('DOMContentLoaded', function() {
    addItem();
});

// Format number with commas
function formatPeso(amount) {
    return '₱' + amount.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

function calculateItemTotal(id) {
    const row = document.getElementById(`item-${id}`);
    const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    const total = quantity * price;
    
    row.querySelector(`#item-total-${id}`).textContent = formatPeso(total);
    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;
    
    document.querySelectorAll('.item-total').forEach(element => {
        const amount = parseFloat(element.textContent.replace('₱', '').replace(/,/g, '')) || 0;
        subtotal += amount;
    });
    
    const tax = subtotal * 0.12;
    const total = subtotal + tax;
    
    document.getElementById('subtotalDisplay').textContent = formatPeso(subtotal);
    document.getElementById('taxDisplay').textContent = formatPeso(tax);
    document.getElementById('totalDisplay').textContent = formatPeso(total);
}
</script>
@endsection
