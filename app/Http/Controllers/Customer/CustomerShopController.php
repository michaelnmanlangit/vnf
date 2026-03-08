<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Delivery;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CustomerShopController extends Controller
{
    /**
     * Show customer dashboard/shop.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $customer = Customer::where('user_id', $user->id)->with('profile')->first();
        
        // Get all categories
        $categories = Inventory::whereIn('status', ['in_stock', 'low_stock'])
            ->where('quantity', '>', 0)
            ->distinct()
            ->pluck('category')
            ->filter()
            ->sort()
            ->values();

        // Get all products grouped by category
        $products = Inventory::whereIn('status', ['in_stock', 'low_stock'])
            ->where('quantity', '>', 0)
            ->orderBy('category')
            ->orderBy('product_name')
            ->get()
            ->groupBy('category');

        // Get cart count (number of distinct products)
        $cart = Session::get('cart', []);
        $cartCount = count($cart);

        return view('customer.dashboard', compact('customer', 'categories', 'products', 'cartCount'));
    }

    /**
     * Show shop page (same as dashboard).
     */
    public function shop()
    {
        return $this->dashboard();
    }

    /**
     * Get product details.
     */
    public function product($id)
    {
        $product = Inventory::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->product_name,
                'category' => $product->category,
                'price' => number_format($product->price, 2),
                'price_raw' => $product->price,
                'unit' => $product->unit,
                'quantity' => $product->quantity,
                'image' => $product->product_image ?? asset('images/default-product.png'),
                'status' => $product->status,
            ],
        ]);
    }

    /**
     * Add item to cart (AJAX).
     */
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:inventory,id',
            'quantity' => 'required|numeric|min:1',
        ]);

        $product = Inventory::findOrFail($validated['product_id']);

        // Check if sufficient stock
        if ($product->quantity < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Available: ' . $product->quantity . ' ' . $product->unit,
            ], 400);
        }

        // Get cart from session
        $cart = Session::get('cart', []);

        // Check if product already in cart
        if (isset($cart[$product->id])) {
            $cart[$product->id]['quantity'] += $validated['quantity'];
        } else {
            $cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->product_name,
                'price' => $product->price,
                'quantity' => $validated['quantity'],
                'unit' => $product->unit,
                'image' => $product->product_image,
            ];
        }

        // Save cart to session
        Session::put('cart', $cart);

        // Calculate cart totals (count distinct products, not total quantity)
        $cartCount = count($cart);
        $cartTotal = array_reduce($cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'cart_count' => $cartCount,
            'cart_total' => number_format($cartTotal, 2),
        ]);
    }

    /**
     * Show cart page.
     */
    public function cart()
    {
        $cart = Session::get('cart', []);
        $customer = Customer::where('user_id', Auth::id())->first();

        $subtotal = array_reduce($cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        $deliveryFee = 0;
        $total = $subtotal;

        return view('customer.cart', compact('cart', 'customer', 'subtotal', 'deliveryFee', 'total'));
    }

    /**
     * Update cart quantities (AJAX).
     */
    public function updateCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:inventory,id',
            'quantity' => 'required|numeric|min:1',
        ]);

        $cart = Session::get('cart', []);
        $product = Inventory::findOrFail($validated['product_id']);

        // Check if sufficient stock
        if ($product->quantity < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Available: ' . $product->quantity . ' ' . $product->unit,
            ], 400);
        }

        if (isset($cart[$validated['product_id']])) {
            $cart[$validated['product_id']]['quantity'] = $validated['quantity'];
            Session::put('cart', $cart);

            // Calculate new totals
            $subtotal = array_reduce($cart, function ($carry, $item) {
                return $carry + ($item['price'] * $item['quantity']);
            }, 0);
            $deliveryFee = 0;
            $total = $subtotal;

            return response()->json([
                'success' => true,
                'subtotal' => number_format($subtotal, 2),
                'total' => number_format($total, 2),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Product not found in cart.',
        ], 404);
    }

    /**
     * Remove item from cart.
     */
    public function removeFromCart($id)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            Session::put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart.',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item not found in cart.',
        ], 404);
    }

    /**
     * Show checkout page.
     */
    public function checkout()
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return redirect()->route('customer.shop')->with('error', 'Your cart is empty.');
        }

        $customer = Customer::where('user_id', Auth::id())->with('profile')->first();

        $subtotal = array_reduce($cart, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        $deliveryFee = 0;
        $tax = $subtotal * 0.12;
        $total = $subtotal + $tax;

        return view('customer.checkout', compact('cart', 'customer', 'subtotal', 'deliveryFee', 'tax', 'total'));
    }

    /**
     * Place order.
     */
    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'delivery_address' => 'required|string|max:500',
            'delivery_instructions' => 'nullable|string|max:500',
            'payment_method' => 'required|in:gcash,paymaya',
            'payment_reference' => 'required_if:payment_method,gcash,paymaya|nullable|string|max:255',
        ]);

        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return back()->with('error', 'Your cart is empty.');
        }

        DB::beginTransaction();
        try {
            $customer = Customer::where('user_id', Auth::id())->first();

            if (!$customer) {
                DB::rollBack();
                return back()->with('error', 'Customer profile not found. Please complete your profile first.');
            }

            // Calculate totals
            $subtotal = array_reduce($cart, function ($carry, $item) {
                return $carry + ($item['price'] * $item['quantity']);
            }, 0);
            $deliveryFee = 150;
            $total = $subtotal + $deliveryFee;

            // Generate order number
            $lastOrder = Order::latest('id')->first();
            $orderNumber = 'ORD-' . date('Ymd') . '-' . str_pad(($lastOrder ? $lastOrder->id + 1 : 1), 5, '0', STR_PAD_LEFT);

            // Generate invoice number
            $lastInvoice = Invoice::latest('id')->first();
            $invoiceNumber = 'INV-' . date('Ym') . '-' . str_pad(($lastInvoice ? $lastInvoice->id + 1 : 1), 5, '0', STR_PAD_LEFT);

            // Calculate invoice totals (12% VAT)
            $tax = $subtotal * 0.12;
            $invoiceTotal = $subtotal + $tax;

            // Prepare invoice notes
            $invoiceNotes = 'Order #' . $orderNumber;
            if (!empty($validated['payment_reference'])) {
                $invoiceNotes .= ' | Payment Ref: ' . $validated['payment_reference'];
            }

            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $customer->id,
                'invoice_date' => now(),
                'due_date' => now()->addDays(30), // 30 days payment term
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total_amount' => $invoiceTotal,
                'status' => 'pending',
                'notes' => $invoiceNotes,
            ]);

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'invoice_id' => $invoice->id,
                'customer_id' => Auth::id(),
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'payment_method' => $validated['payment_method'],
                'payment_reference' => $validated['payment_reference'] ?? null,
                'delivery_address' => $validated['delivery_address'],
                'delivery_instructions' => $validated['delivery_instructions'],
            ]);

            // Create order items, invoice items, and update inventory
            foreach ($cart as $item) {
                $product = Inventory::find($item['id']);
                
                if ($product && $product->quantity >= $item['quantity']) {
                    // Create order item
                    OrderItem::create([
                        'order_id' => $order->id,
                        'inventory_id' => $item['id'],
                        'product_name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'],
                        'price_per_unit' => $item['price'],
                        'subtotal' => $item['price'] * $item['quantity'],
                    ]);

                    // Create invoice item
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'inventory_id' => $item['id'],
                        'product_name' => $product->product_name,
                        'description' => null,
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'],
                        'unit_price' => $item['price'],
                        'total' => $item['price'] * $item['quantity'],
                    ]);

                    // Update inventory quantity
                    $product->quantity -= $item['quantity'];
                    
                    // Update inventory status based on new quantity
                    if ($product->quantity == 0) {
                        $product->status = 'out_of_stock';
                    } elseif ($product->quantity <= 10) {
                        $product->status = 'low_stock';
                    } else {
                        $product->status = 'in_stock';
                    }
                    
                    $product->save();
                } else {
                    throw new \Exception("Insufficient stock for {$item['name']}");
                }
            }

            // Create payment record if payment reference is provided (GCash/PayMaya)
            if (!empty($validated['payment_reference']) && in_array($validated['payment_method'], ['gcash', 'paymaya'])) {
                Payment::create([
                    'invoice_id' => $invoice->id,
                    'payment_reference' => $validated['payment_reference'],
                    'amount' => $invoiceTotal,
                    'tendered_amount' => $invoiceTotal,
                    'change_amount' => 0,
                    'payment_date' => now(),
                    'payment_method' => $validated['payment_method'],
                    'notes' => 'Online payment via ' . ucfirst($validated['payment_method']),
                ]);

                // Update invoice status to paid (InvoiceObserver will auto-create Delivery)
                $invoice->update(['status' => 'paid']);
                
                // Update order payment status
                $order->update(['payment_status' => 'paid']);
            }

            DB::commit();

            // Clear cart
            Session::forget('cart');

            return redirect()->route('customer.order.detail', $order->id)
                ->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to place order: ' . $e->getMessage());
        }
    }

    /**
     * Show order history.
     */
    public function orders()
    {
        $orders = Order::where('customer_id', Auth::id())
            ->with('items')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $customer = Customer::where('user_id', Auth::id())->first();

        return view('customer.orders', compact('orders', 'customer'));
    }

    /**
     * Show order detail with tracking.
     */
    public function orderDetail($id)
    {
        $order = Order::where('customer_id', Auth::id())
            ->with([
                'items.inventory', 
                'invoice.payments', 
                'invoice.delivery.latestLocation',
                'invoice.delivery.driver'
            ])
            ->findOrFail($id);

        $customer = Customer::where('user_id', Auth::id())->first();

        return view('customer.order-detail', compact('order', 'customer'));
    }

    /**
     * Get delivery location for live tracking (AJAX)
     */
    public function deliveryLocation($id)
    {
        $delivery = \App\Models\Delivery::with('latestLocation')->findOrFail($id);
        
        // Verify this delivery belongs to the authenticated customer
        if ($delivery->customer_id !== Auth::id()) {
            return response()->json(['found' => false], 403);
        }

        if (!$delivery->latestLocation) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'latitude' => $delivery->latestLocation->latitude,
            'longitude' => $delivery->latestLocation->longitude,
            'updated' => $delivery->latestLocation->created_at->diffForHumans(),
            'status' => $delivery->status,
        ]);
    }

    /**
     * Save or update the customer's FCM token for push notifications.
     */
    public function saveFcmToken(Request $request)
    {
        $request->validate(['token' => 'required|string']);

        Auth::user()->update(['fcm_token' => $request->token]);

        return response()->json(['success' => true]);
    }
}
