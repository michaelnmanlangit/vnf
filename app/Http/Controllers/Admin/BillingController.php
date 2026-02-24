<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    /**
     * Constructor - Auto-check for overdue invoices
     */
    public function __construct()
    {
        // Check for overdue invoices on every billing controller access
        $this->checkOverdueInvoices();
    }

    /**
     * Check and update overdue invoices
     */
    private function checkOverdueInvoices()
    {
        Invoice::where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);
    }

    // ============ INVOICES ============
    /**
     * Display a listing of invoices.
     */
    public function index(Request $request)
    {
        $query = Invoice::with('customer');

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('business_name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortColumn = $request->get('sort_column', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        $allowedColumns = ['customer', 'invoice_date', 'due_date', 'total_amount', 'status', 'created_at'];
        
        if (in_array($sortColumn, $allowedColumns)) {
            if ($sortColumn === 'customer') {
                // Join with customers table for sorting by customer name
                $query->join('customers', 'invoices.customer_id', '=', 'customers.id')
                      ->select('invoices.*')
                      ->orderBy('customers.business_name', $sortDirection);
            } else {
                $query->orderBy($sortColumn, $sortDirection);
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $invoices = $query->paginate(15);

        $stats = [
            'total' => Invoice::count(),
            'pending' => Invoice::where('status', 'pending')->count(),
            'paid' => Invoice::where('status', 'paid')->count(),
            'overdue' => Invoice::where('status', 'overdue')->count(),
            'total_amount' => Invoice::sum('total_amount')
        ];

        return view('admin.billing.index', compact('invoices', 'stats'));
    }

    /**
     * Show the form for creating a new invoice.
     */
    public function create()
    {
        $customers = Customer::where('status', 'active')->orderBy('business_name')->get();
        $inventory = Inventory::whereIn('status', ['in_stock', 'low_stock', 'expiring_soon'])
                            ->where('quantity', '>', 0)
                            ->orderBy('product_name')
                            ->get();
        
        // Generate invoice number
        $lastInvoice = Invoice::latest('id')->first();
        $invoiceNumber = 'INV-' . date('Ym') . '-' . str_pad(($lastInvoice ? $lastInvoice->id + 1 : 1), 5, '0', STR_PAD_LEFT);

        return view('admin.billing.create', compact('customers', 'inventory', 'invoiceNumber'));
    }

    /**
     * Store a newly created invoice in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_number' => 'required|unique:invoices',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit' => 'required|string',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.inventory_id' => 'required|exists:inventory,id',
        ]);

        DB::beginTransaction();
        try {
            // Calculate totals
            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }
            
            $tax = $subtotal * 0.12; // 12% VAT
            $total = $subtotal + $tax;

            // Create invoice
            $invoice = Invoice::create([
                'invoice_number' => $validated['invoice_number'],
                'customer_id' => $validated['customer_id'],
                'invoice_date' => $validated['invoice_date'],
                'due_date' => $validated['due_date'],
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total_amount' => $total,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create invoice items and update inventory
            foreach ($request->items as $item) {
                $inventoryItem = Inventory::find($item['inventory_id']);
                
                // Check if sufficient stock is available
                if ($inventoryItem && $inventoryItem->quantity >= $item['quantity']) {
                    $itemTotal = $item['quantity'] * $item['unit_price'];
                    
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'inventory_id' => $item['inventory_id'],
                        'product_name' => $inventoryItem->product_name,
                        'description' => null,
                        'quantity' => $item['quantity'],
                        'unit' => $item['unit'],
                        'unit_price' => $item['unit_price'],
                        'total' => $itemTotal,
                    ]);

                    // Update inventory quantity
                    $inventoryItem->quantity -= $item['quantity'];
                    $inventoryItem->save();
                } else {
                    throw new \Exception("Insufficient stock for {$inventoryItem->product_name}. Available: {$inventoryItem->quantity}, Requested: {$item['quantity']}");
                }
            }

            DB::commit();
            return redirect()->route('admin.billing.show', $invoice->id)
                ->with('success', 'Invoice created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating invoice: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified invoice.
     */
    public function show($id)
    {
        $invoice = Invoice::with(['customer', 'items', 'payments'])->findOrFail($id);
        return view('admin.billing.show', compact('invoice'));
    }

    /**
     * Show the form for editing the specified invoice.
     */
    public function edit($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);
        $customers = Customer::where('status', 'active')->orderBy('business_name')->get();
        $inventory = Inventory::orderBy('product_name')->get();

        return view('admin.billing.edit', compact('invoice', 'customers', 'inventory'));
    }

    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:invoice_date',
            'status' => 'required|in:pending,paid,partially_paid,overdue,cancelled',
            'notes' => 'nullable|string',
        ]);

        $invoice->update($validated);

        return redirect()->route('admin.billing.show', $invoice->id)
            ->with('success', 'Invoice updated successfully!');
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->delete();

        return redirect()->route('admin.billing.index')
            ->with('success', 'Invoice deleted successfully!');
    }

    // ============ CUSTOMERS ============
    /**
     * Display a listing of customers.
     */
    public function customers(Request $request)
    {
        $query = Customer::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('business_name', 'LIKE', "%{$search}%")
                  ->orWhere('contact_person', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortColumn = $request->get('sort_column', 'business_name');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        // Validate sort column to prevent SQL injection
        $allowedColumns = ['business_name', 'contact_person', 'customer_type', 'status'];
        if (in_array($sortColumn, $allowedColumns)) {
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('business_name', 'asc');
        }

        $customers = $query->paginate(15);

        return view('admin.billing.customers', compact('customers'));
    }

    /**
     * Store a new customer.
     */
    public function storeCustomer(Request $request)
    {
        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'required|string',
            'customer_type' => 'required|in:wet_market,restaurant,meat_supplier,fishery,grocery,distribution_company,other',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        Customer::create($validated);

        return back()->with('success', 'Customer added successfully!');
    }

    /**
     * Update the specified customer.
     */
    public function updateCustomer(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'business_name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'required|string',
            'customer_type' => 'required|in:wet_market,restaurant,meat_supplier,fishery,grocery,distribution_company,other',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $customer->update($validated);

        return back()->with('success', 'Customer updated successfully!');
    }

    /**
     * Delete a customer.
     */
    public function deleteCustomer($id)
    {
        $customer = Customer::findOrFail($id);
        
        if ($customer->invoices()->count() > 0) {
            return back()->with('error', 'Cannot delete customer with existing invoices.');
        }

        $customer->delete();
        return back()->with('success', 'Customer deleted successfully!');
    }

    // ============ PAYMENTS ============
    /**
     * Store a new payment for an invoice.
     */
    public function storePayment(Request $request, $invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        $validated = $request->validate([
            'payment_reference' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,check,online_payment,other',
            'notes' => 'nullable|string',
        ]);

        // Calculate balance and handle overpayment
        $balance = $invoice->total_amount - $invoice->total_paid;
        $paymentAmount = $validated['amount'];
        $actualPaymentAmount = min($paymentAmount, $balance); // Only record up to the balance
        $change = $paymentAmount > $balance ? $paymentAmount - $balance : 0;

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'payment_reference' => $validated['payment_reference'] ?? null,
            'amount' => $actualPaymentAmount, // Record only the amount that goes toward the invoice
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'notes' => $validated['notes'] ?? null,
        ]);

        // Update invoice status
        $totalPaid = $invoice->payments()->sum('amount');
        if ($totalPaid >= $invoice->total_amount) {
            $invoice->update(['status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => 'partially_paid']);
        }

        return back()->with('success', 'Payment recorded successfully!');
    }
}
