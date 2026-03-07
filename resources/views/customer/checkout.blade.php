@extends('layouts.customer')

@section('title', 'Checkout')

@section('styles')
<style>
    .checkout-container {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 2rem;
        margin-top: 2rem;
    }

    .checkout-form {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .form-header {
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #ecf0f1;
    }

    .form-header h1 {
        color: #2c3e50;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .form-header p {
        color: #7f8c8d;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .section-title {
        color: #2c3e50;
        font-size: 1.3rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .section-icon {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #3498db, #2c3e50);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .form-group label.required::after {
        content: ' *';
        color: #e74c3c;
    }

    .form-control {
        width: 100%;
        padding: 0.875rem 1rem;
        border: 2px solid #ecf0f1;
        border-radius: 10px;
        font-size: 1rem;
        font-family: 'Poppins', sans-serif;
        transition: all 0.3s ease;
        color: #2c3e50;
    }

    .form-control:focus {
        outline: none;
        border-color: #3498db;
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }

    .payment-methods {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }

    .payment-option {
        position: relative;
    }

    .payment-option input[type="radio"] {
        position: absolute;
        opacity: 0;
    }

    .payment-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 1.25rem;
        height: 100px;
        border: 2px solid #ecf0f1;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }

    .payment-option input:checked + .payment-label {
        border-color: #3498db;
        background: #e8f4fd;
        color: #3498db;
    }

    .payment-label i {
        font-size: 2rem;
    }

    .order-summary {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        height: fit-content;
        position: sticky;
        top: 2rem;
    }

    .summary-title {
        color: #2c3e50;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #ecf0f1;
    }

    .order-items {
        max-height: 300px;
        overflow-y: auto;
        margin-bottom: 1.5rem;
    }

    .order-item {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #ecf0f1;
    }

    .item-info {
        flex: 1;
    }

    .item-name {
        color: #2c3e50;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .item-qty {
        color: #7f8c8d;
        font-size: 0.85rem;
    }

    .item-price {
        color: #27ae60;
        font-weight: 600;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        color: #7f8c8d;
    }

    .summary-row.total {
        border-top: 2px solid #ecf0f1;
        margin-top: 1rem;
        padding-top: 1rem;
        font-size: 1.3rem;
        font-weight: 700;
        color: #2c3e50;
    }

    .btn-place-order {
        width: 100%;
        padding: 1.25rem;
        background: linear-gradient(135deg, #27ae60, #229954);
        border: none;
        border-radius: 10px;
        color: white;
        font-size: 1.1rem;
        font-weight: 600;
        cursor: pointer;
        margin-top: 1.5rem;
        transition: all 0.3s ease;
        font-family: 'Poppins', sans-serif;
    }

    .btn-place-order:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1.5rem;
        border-left: 4px solid #e74c3c;
    }

    .qr-code-section {
        display: none;
        background: #f8f9fa;
        border-radius: 15px;
        padding: 1.5rem;
        margin-top: 1.5rem;
        text-align: center;
        border: 2px solid #3498db;
    }

    .qr-code-section.active {
        display: block;
    }

    .qr-code-title {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 1rem;
        font-size: 1.1rem;
    }

    .qr-code-image {
        width: 250px;
        height: 250px;
        margin: 0 auto 1rem;
        background: white;
        padding: 1rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .qr-code-image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .qr-instructions {
        color: #7f8c8d;
        font-size: 0.9rem;
        margin-bottom: 1rem;
        line-height: 1.6;
    }

    @media (max-width: 968px) {
        .checkout-container {
            grid-template-columns: 1fr;
        }

        .order-summary {
            position: static;
        }

        .payment-methods {
            grid-template-columns: 1fr;
        }

        .qr-code-image {
            width: 200px;
            height: 200px;
        }
    }
</style>
@endsection

@section('content')
<div class="checkout-container">
    <div class="checkout-form">
        <div class="form-header">
            <h1><i class="fas fa-shopping-bag"></i> Checkout</h1>
            <p>Complete your order information</p>
        </div>

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert-error" style="background:#f8d7da;color:#721c24;padding:1rem;border-radius:10px;margin-bottom:1.5rem;border-left:4px solid #e74c3c;">
                <ul style="margin:0;padding-left:1.2rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('customer.order.place') }}" method="POST" id="checkout-form">
            @csrf

            <div class="form-section">
                <h3 class="section-title">
                    <span class="section-icon"><i class="fas fa-map-marker-alt"></i></span>
                    Delivery Information
                </h3>

                <div class="form-group">
                    <label for="delivery_address" class="required">Delivery Address</label>
                    <textarea class="form-control {{ $errors->has('delivery_address') ? 'border-danger' : '' }}" id="delivery_address" name="delivery_address" 
                              rows="3" required>{{ old('delivery_address', $customer->profile->address ?? '') }}</textarea>
                    @error('delivery_address')<small style="color:#e74c3c;">{{ $message }}</small>@enderror
                </div>

                <div class="form-group">
                    <label for="delivery_instructions">Delivery Instructions (Optional)</label>
                    <textarea class="form-control" id="delivery_instructions" name="delivery_instructions" 
                              rows="2" placeholder="e.g., Call upon arrival, Ring doorbell, etc.">{{ old('delivery_instructions') }}</textarea>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">
                    <span class="section-icon"><i class="fas fa-credit-card"></i></span>
                    Payment Method
                </h3>

                <div class="payment-methods">
                    <div class="payment-option">
                        <input type="radio" id="gcash" name="payment_method" value="gcash" {{ old('payment_method', 'gcash') === 'gcash' ? 'checked' : '' }}>
                        <label for="gcash" class="payment-label">
                            <span style="font-size: 2rem; font-weight: bold; color: inherit;">G</span>
                            <span>GCash</span>
                        </label>
                    </div>

                    <div class="payment-option">
                        <input type="radio" id="paymaya" name="payment_method" value="paymaya" {{ old('payment_method') === 'paymaya' ? 'checked' : '' }}>
                        <label for="paymaya" class="payment-label">
                            <span style="font-size: 2rem; font-weight: bold; color: inherit;">M</span>
                            <span>PayMaya</span>
                        </label>
                    </div>
                </div>

                <!-- GCash QR Code Section -->
                <div class="qr-code-section" id="gcash-qr">
                    <div class="qr-code-title">
                        <i class="fas fa-qrcode"></i> Scan GCash QR Code
                    </div>
                    <div class="qr-code-image">
                        <img src="{{ asset('images/qrcode-gcash.png') }}" alt="GCash QR Code">
                    </div>
                    <div class="qr-instructions">
                        <p><strong>Instructions:</strong></p>
                        <p>1. Open your GCash app<br>
                        2. Scan the QR code above<br>
                        3. Enter the total amount: <strong>₱{{ number_format($total, 2) }}</strong><br>
                        4. Complete the payment and enter the reference number below</p>
                    </div>
                </div>

                <!-- PayMaya QR Code Section -->
                <div class="qr-code-section" id="paymaya-qr">
                    <div class="qr-code-title">
                        <i class="fas fa-qrcode"></i> Scan PayMaya QR Code
                    </div>
                    <div class="qr-code-image">
                        <img src="{{ asset('images/qrcode-maya.png') }}" alt="PayMaya QR Code">
                    </div>
                    <div class="qr-instructions">
                        <p><strong>Instructions:</strong></p>
                        <p>1. Open your PayMaya app<br>
                        2. Scan the QR code above<br>
                        3. Enter the total amount: <strong>₱{{ number_format($total, 2) }}</strong><br>
                        4. Complete the payment and enter the reference number below</p>
                    </div>
                </div>

                <!-- Reference Number Field (shown for online payments) -->
                <div class="form-group" id="reference-field" style="display: {{ in_array(old('payment_method', 'gcash'), ['gcash','paymaya']) ? 'block' : 'none' }}; margin-top: 1.5rem;">
                    <label for="payment_reference" class="required">Payment Reference Number</label>
                    <input type="text" class="form-control {{ $errors->has('payment_reference') ? 'border-danger' : '' }}" id="payment_reference" name="payment_reference" 
                           value="{{ old('payment_reference') }}"
                           placeholder="Enter your transaction reference number">
                    @error('payment_reference')<small style="color:#e74c3c;">{{ $message }}</small>@enderror
                    <small style="color: #7f8c8d; margin-top: 0.5rem; display: block;">
                        Please enter the reference number from your payment confirmation.
                    </small>
                </div>
            </div>
        </form>
    </div>

    <div class="order-summary">
        <h2 class="summary-title">Order Summary</h2>

        <div class="order-items">
            @foreach($cart as $item)
                <div class="order-item">
                    <div class="item-info">
                        <div class="item-name">{{ $item['name'] }}</div>
                        <div class="item-qty">{{ $item['quantity'] }} {{ $item['unit'] }} × ₱{{ number_format($item['price'], 2) }}</div>
                    </div>
                    <div class="item-price">₱{{ number_format($item['price'] * $item['quantity'], 2) }}</div>
                </div>
            @endforeach
        </div>

        <div class="summary-row">
            <span>Subtotal:</span>
            <span>₱{{ number_format($subtotal, 2) }}</span>
        </div>

        <div class="summary-row total">
            <span>Total:</span>
            <span>₱{{ number_format($total, 2) }}</span>
        </div>

        <button type="submit" form="checkout-form" class="btn-place-order">
            <i class="fas fa-check-circle"></i> Place Order
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const gcashQR = document.getElementById('gcash-qr');
    const paymayaQR = document.getElementById('paymaya-qr');
    const referenceField = document.getElementById('reference-field');
    const referenceInput = document.getElementById('payment_reference');

    function updatePaymentUI(method) {
        gcashQR.classList.remove('active');
        paymayaQR.classList.remove('active');
        referenceField.style.display = 'none';
        referenceInput.removeAttribute('required');

        if (method === 'gcash') {
            gcashQR.classList.add('active');
            referenceField.style.display = 'block';
            referenceInput.setAttribute('required', 'required');
        } else if (method === 'paymaya') {
            paymayaQR.classList.add('active');
            referenceField.style.display = 'block';
            referenceInput.setAttribute('required', 'required');
        }
    }

    // Restore state on page load (e.g. after validation failure)
    const checkedMethod = document.querySelector('input[name="payment_method"]:checked');
    if (checkedMethod) {
        updatePaymentUI(checkedMethod.value);
    }

    paymentMethods.forEach(radio => {
        radio.addEventListener('change', function() {
            updatePaymentUI(this.value);
        });
    });

    // Form validation
    const form = document.getElementById('checkout-form');
    form.addEventListener('submit', function(e) {
        const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
        const reference = referenceInput.value.trim();

        if ((selectedMethod === 'gcash' || selectedMethod === 'paymaya') && !reference) {
            e.preventDefault();
            referenceField.style.display = 'block';
            referenceInput.focus();
            referenceInput.style.borderColor = '#e74c3c';
            referenceInput.setAttribute('required', 'required');
            return false;
        }
    });
});
</script>
@endsection
