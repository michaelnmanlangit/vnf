@extends('layouts.customer')

@section('title', 'Shopping Cart')

@section('styles')
<style>
    .cart-container {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 2rem;
        margin-top: 2rem;
    }

    .cart-items {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .cart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .cart-header h1 {
        color: #1a202c;
        font-size: 2rem;
    }

    .empty-cart {
        text-align: center;
        padding: 3rem;
        color: #64748b;
    }

    .empty-cart i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.3;
    }

    .cart-item {
        display: flex;
        gap: 1.5rem;
        padding: 1.5rem;
        border: 2px solid #e2e8f0;
        border-radius: 15px;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }

    .cart-item:hover {
        border-color: #4169E1;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .item-image {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 10px;
        background: #f8f9fa;
    }

    .item-details {
        flex: 1;
    }

    .item-name {
        color: #1a202c;
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .item-price {
        color: #27ae60;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .qty-btn {
        width: 35px;
        height: 35px;
        border: 2px solid #e2e8f0;
        background: white;
        border-radius: 8px;
        color: #1a202c;
        font-size: 1.2rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .qty-btn:hover {
        border-color: #4169E1;
        color: #4169E1;
    }

    .qty-display {
        min-width: 60px;
        text-align: center;
        font-weight: 600;
        color: #1a202c;
        font-size: 1.1rem;
    }

    .remove-btn {
        background: none;
        border: none;
        color: #e74c3c;
        cursor: pointer;
        font-size: 1.5rem;
        transition: all 0.3s ease;
    }

    .remove-btn:hover {
        transform: scale(1.2);
    }

    .cart-summary {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        height: fit-content;
        position: sticky;
        top: 2rem;
    }

    .summary-title {
        color: #1a202c;
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #e2e8f0;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.75rem 0;
        color: #64748b;
    }

    .summary-row.total {
        border-top: 2px solid #e2e8f0;
        margin-top: 1rem;
        padding-top: 1rem;
        font-size: 1.3rem;
        font-weight: 700;
        color: #1a202c;
    }

    .btn-checkout {
        width: 100%;
        padding: 1.25rem;
        background: linear-gradient(135deg, #1e3ba8, #4169E1);
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

    .btn-checkout:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(65, 105, 225, 0.4);
    }

    .btn-continue {
        width: 100%;
        padding: 1rem;
        background: white;
        border: 2px solid #4169E1;
        border-radius: 10px;
        color: #4169E1;
        font-weight: 600;
        cursor: pointer;
        margin-top: 1rem;
        transition: all 0.3s ease;
        text-decoration: none;
        display: block;
        text-align: center;
        font-family: 'Poppins', sans-serif;
    }

    .btn-continue:hover {
        background: #4169E1;
        color: white;
    }

    @media (max-width: 968px) {
        .cart-container {
            grid-template-columns: 1fr;
        }

        .cart-summary {
            position: static;
        }
    }

    @media (max-width: 768px) {
        .cart-item {
            flex-direction: column;
        }

        .item-image {
            width: 100%;
            height: 200px;
        }
    }
</style>
@endsection

@section('content')
<div class="cart-container">
    <div class="cart-items">
        <div class="cart-header">
            <h1><i class="fas fa-shopping-cart"></i> Shopping Cart</h1>
        </div>

        @if(empty($cart))
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h2>Your cart is empty</h2>
                <p>Browse our products and add items to your cart</p>
                <a href="{{ route('customer.dashboard') }}" class="btn-continue" style="max-width: 300px; margin: 2rem auto;">
                    <i class="fas fa-store"></i> Continue Shopping
                </a>
            </div>
        @else
            @foreach($cart as $item)
                <div class="cart-item" data-product-id="{{ $item['id'] }}">
                    <img src="{{ $item['image'] ? (str_starts_with($item['image'], 'data:') ? $item['image'] : asset('storage/' . $item['image'])) : asset('images/default-product.png') }}" 
                         alt="{{ $item['name'] }}" 
                         class="item-image">
                    
                    <div class="item-details">
                        <h3 class="item-name">{{ $item['name'] }}</h3>
                        <div class="item-price">₱{{ number_format($item['price'], 2) }} / {{ $item['unit'] }}</div>
                        
                        <div class="quantity-controls">
                            <button type="button" class="qty-btn" onclick="updateQuantity({{ $item['id'] }}, -1)">-</button>
                            <span class="qty-display" id="qty-{{ $item['id'] }}">{{ $item['quantity'] }}</span>
                            <button type="button" class="qty-btn" onclick="updateQuantity({{ $item['id'] }}, 1)">+</button>
                        </div>
                    </div>

                    <button type="button" class="remove-btn" onclick="removeItem({{ $item['id'] }})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            @endforeach
        @endif
    </div>

    @if(!empty($cart))
        <div class="cart-summary">
            <h2 class="summary-title">Order Summary</h2>
            
            <div class="summary-row">
                <span>Subtotal:</span>
                <span id="subtotalDisplay">₱{{ number_format($subtotal, 2) }}</span>
            </div>
            
            <div class="summary-row total">
                <span>Total:</span>
                <span id="totalDisplay">₱{{ number_format($total, 2) }}</span>
            </div>

            <button type="button" class="btn-checkout" onclick="checkout()">
                <i class="fas fa-check-circle"></i> Proceed to Checkout
            </button>

            <a href="{{ route('customer.dashboard') }}" class="btn-continue">
                <i class="fas fa-arrow-left"></i> Continue Shopping
            </a>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    let quantities = @json(array_column($cart, 'quantity', 'id'));

    async function updateQuantity(productId, change) {
        const qtyDisplay = document.getElementById(`qty-${productId}`);
        let currentQty = quantities[productId];
        let newQty = currentQty + change;

        if (newQty < 1) return;

        try {
            const response = await fetch('{{ route("customer.cart.update") }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: newQty
                })
            });

            const data = await response.json();

            if (data.success) {
                quantities[productId] = newQty;
                qtyDisplay.textContent = newQty;
                document.getElementById('subtotalDisplay').textContent = '₱' + data.subtotal;
                document.getElementById('totalDisplay').textContent = '₱' + data.total;
            } else {
                alert(data.message);
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
        }
    }

    async function removeItem(productId) {
        if (!confirm('Remove this item from cart?')) return;

        try {
            const response = await fetch(`{{ url('customer/cart/remove') }}/${productId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (data.success) {
                document.querySelector(`[data-product-id="${productId}"]`).remove();
                
                // Check if cart is empty
                const remainingItems = document.querySelectorAll('.cart-item');
                if (remainingItems.length === 0) {
                    location.reload();
                }
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
        }
    }

    function checkout() {
        window.location.href = '{{ route("customer.checkout") }}';
    }
</script>
@endsection
