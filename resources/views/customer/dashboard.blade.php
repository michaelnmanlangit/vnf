@extends('layouts.customer')

@section('title', 'Shop')

@section('styles')
<style>
    .welcome-section {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .welcome-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .welcome-text h1 {
        color: #1a202c;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }

    .welcome-text p {
        color: #64748b;
        font-size: 1rem;
    }

    .cart-summary {
        text-align: right;
    }

    .cart-count {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #1e3ba8, #4169E1);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .cart-count:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(65, 105, 225, 0.4);
    }

    .cart-badge {
        background: rgba(255, 255, 255, 0.3);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.9rem;
    }

    .shop-section {
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
    }

    .section-header h2 {
        color: #1a202c;
        font-size: 1.8rem;
    }

    .category-filter {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .category-btn {
        padding: 0.5rem 1.25rem;
        border: 2px solid #e2e8f0;
        background: white;
        border-radius: 25px;
        color: #64748b;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Poppins', sans-serif;
        outline: none;
    }

    .category-btn:hover, .category-btn.active {
        background: linear-gradient(135deg, #1e3ba8, #4169E1);
        color: white;
        border-color: transparent;
        outline: none;
    }

    .category-btn:focus {
        outline: none;
    }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-top: 2rem;
    }

    .product-card {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        border-color: #4169E1;
    }

    .product-image-container {
        width: 100%;
        height: 200px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
    }

    .product-image-container::before {
        content: '\f03e';
        font-family: 'Font Awesome 5 Free';
        font-weight: 400;
        position: absolute;
        font-size: 3rem;
        color: #ddd;
        z-index: 0;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        position: relative;
        z-index: 1;
    }

    .product-details {
        padding: 1.25rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .product-category {
        display: inline-block;
        background: #eef1fc;
        color: #4169E1;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 0.75rem;
        width: auto;
        max-width: fit-content;
    }

    .product-name {
        color: #1a202c;
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .product-info {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        margin-top: auto;
    }

    .product-price {
        color: #27ae60;
        font-size: 1.5rem;
        font-weight: 700;
    }

    .product-unit {
        color: #64748b;
        font-size: 0.85rem;
    }

    .product-stock {
        color: #64748b;
        font-size: 0.85rem;
    }

    .product-actions {
        display: flex;
        gap: 0.75rem;
        align-items: center;
    }

    .quantity-selector {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex: 1;
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
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .qty-btn:hover {
        border-color: #4169E1;
        color: #4169E1;
    }

    .qty-input {
        width: 50px;
        height: 35px;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
        color: #1a202c;
        font-family: 'Poppins', sans-serif;
    }

    .add-to-cart-btn {
        flex: 1;
        padding: 0.75rem;
        background: linear-gradient(135deg, #1e3ba8, #4169E1);
        border: none;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        font-family: 'Poppins', sans-serif;
    }

    .add-to-cart-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(65, 105, 225, 0.4);
    }

    .add-to-cart-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    /* Toast Notification */
    .toast {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        gap: 1rem;
        transform: translateX(400px);
        transition: transform 0.3s ease, opacity 0.3s ease;
        opacity: 0;
        visibility: hidden;
        z-index: 1000;
    }

    .toast.show {
        transform: translateX(0);
        opacity: 1;
        visibility: visible;
    }

    .toast-success {
        border-left: 4px solid #27ae60;
    }

    .toast-error {
        border-left: 4px solid #e74c3c;
    }

    .toast-icon {
        font-size: 1.5rem;
    }

    .toast-success .toast-icon {
        color: #27ae60;
    }

    .toast-error .toast-icon {
        color: #e74c3c;
    }

    .toast-message {
        color: #1a202c;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .welcome-content {
            flex-direction: column;
            gap: 1.5rem;
            text-align: center;
        }

        .cart-summary {
            text-align: center;
        }

        .section-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .products-grid {
            grid-template-columns: 1fr;
        }

        .toast {
            bottom: 1rem;
            right: 1rem;
            left: 1rem;
        }
    }
</style>
@endsection

@section('content')
<div class="welcome-section">
    <div class="welcome-content">
        <div class="welcome-text">
            <h1>Welcome, {{ $customer->contact_person ?? Auth::user()->name }}!</h1>
            <p>Browse our fresh products and add them to your cart</p>
        </div>
        <div class="cart-summary">
            <a href="{{ route('customer.cart') }}" class="cart-count">
                <i class="fas fa-shopping-cart"></i>
                <span id="cartCountDisplay">{{ $cartCount }}</span> <span id="cartItemText">{{ $cartCount == 1 ? 'Item' : 'Items' }}</span>
            </a>
        </div>
    </div>
</div>

<div class="shop-section">
    <div class="section-header">
        <h2>Our Products</h2>
        <div class="category-filter">
            <button class="category-btn active" data-category="all">All Products</button>
            @foreach($categories as $category)
                <button class="category-btn" data-category="{{ $category }}">{{ $category }}</button>
            @endforeach
        </div>
    </div>

    <div class="products-grid" id="productsGrid">
        @foreach($products as $category => $categoryProducts)
            @foreach($categoryProducts as $product)
                <div class="product-card" data-category="{{ $product->category }}">
                    <div class="product-image-container">
                        <img src="{{ $product->product_image ? (str_starts_with($product->product_image, 'data:') ? $product->product_image : asset('storage/' . $product->product_image)) : asset('images/default-product.png') }}" 
                             alt="{{ $product->product_name }}" 
                             class="product-image"
                             onerror="this.style.display='none';">
                    </div>
                    
                    <div class="product-details">
                        <span class="product-category">{{ $product->category }}</span>
                        <h3 class="product-name">{{ $product->product_name }}</h3>
                        
                        <div class="product-info">
                            <div>
                                <div class="product-price">₱{{ number_format($product->price, 2) }}</div>
                                <div class="product-unit">per {{ $product->unit }}</div>
                            </div>
                            <div class="product-stock">
                                Stock: {{ number_format($product->quantity, 0) }} {{ $product->unit }}
                            </div>
                        </div>

                        <div class="product-actions">
                            <div class="quantity-selector">
                                <button type="button" class="qty-btn" onclick="decreaseQty(this)">-</button>
                                <input type="number" class="qty-input" value="1" min="1" max="{{ $product->quantity }}">
                                <button type="button" class="qty-btn" onclick="increaseQty(this)">+</button>
                            </div>
                            <button type="button" class="add-to-cart-btn" 
                                    onclick="addToCart({{ $product->id }}, this)"
                                    data-product-name="{{ $product->product_name }}">
                                <i class="fas fa-cart-plus"></i> Add
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        @endforeach
    </div>
</div>

<!-- Toast Notification -->
<div class="toast toast-success" id="toast">
    <div class="toast-icon">
        <i class="fas fa-check-circle"></i>
    </div>
    <div class="toast-message" id="toastMessage"></div>
</div>
@endsection

@section('scripts')
<script>
    // Category Filter
    const categoryBtns = document.querySelectorAll('.category-btn');
    const productCards = document.querySelectorAll('.product-card');

    categoryBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            categoryBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const category = btn.dataset.category;

            productCards.forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Quantity Controls
    function decreaseQty(btn) {
        const input = btn.nextElementSibling;
        if (input.value > 1) {
            input.value = parseInt(input.value) - 1;
        }
    }

    function increaseQty(btn) {
        const input = btn.previousElementSibling;
        const max = parseInt(input.max);
        if (parseInt(input.value) < max) {
            input.value = parseInt(input.value) + 1;
        }
    }

    // Add to Cart with AJAX
    async function addToCart(productId, btn) {
        const card = btn.closest('.product-card');
        const qtyInput = card.querySelector('.qty-input');
        const quantity = parseInt(qtyInput.value);
        const productName = btn.dataset.productName;

        // Disable button
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

        try {
            const response = await fetch('{{ route("customer.cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });

            const data = await response.json();

            if (data.success) {
                // Update cart count
                const cartCount = data.cart_count;
                document.getElementById('cartCountDisplay').textContent = cartCount;
                document.getElementById('cartItemText').textContent = cartCount == 1 ? 'Item' : 'Items';

                // Show success toast
                showToast(data.message, 'success');

                // Animate button
                btn.innerHTML = '<i class="fas fa-check"></i> Added!';
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-cart-plus"></i> Add';
                    btn.disabled = false;
                    qtyInput.value = 1;
                }, 1500);
            } else {
                showToast(data.message, 'error');
                btn.innerHTML = '<i class="fas fa-cart-plus"></i> Add';
                btn.disabled = false;
            }
        } catch (error) {
            showToast('An error occurred. Please try again.', 'error');
            btn.innerHTML = '<i class="fas fa-cart-plus"></i> Add';
            btn.disabled = false;
        }
    }

    // Show Toast Notification
    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        const toastMessage = document.getElementById('toastMessage');

        toast.className = `toast toast-${type}`;
        toastMessage.textContent = message;

        toast.classList.add('show');

        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }
</script>
@endsection
