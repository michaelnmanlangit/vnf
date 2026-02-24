<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventory with search and filter.
     */
    public function index(Request $request)
    {
        $query = Inventory::query();

        // Search by product name
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('supplier', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->has('category') && !empty($request->category)) {
            $query->where('category', $request->category);
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Sort by column
        $sortColumn = $request->get('sort_column', 'date_received');
        $sortDirection = $request->get('sort_direction', 'desc');

        // Allowed columns for sorting (prevent SQL injection)
        $allowedColumns = ['id', 'product_name', 'quantity', 'category', 'status', 'expiration_date', 'temperature_requirement', 'date_received'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'date_received';
        }

        // Validate sort direction
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        // Apply sorting
        $query->orderBy($sortColumn, $sortDirection);

        $inventory = $query->paginate(15);
        $categories = ['ice', 'meat', 'seafood', 'vegetables', 'fruits', 'beverages', 'dairy'];
        $statuses = ['in_stock', 'low_stock', 'expired', 'expiring_soon'];

        return view('admin.inventory.index', compact('inventory', 'categories', 'statuses'));
    }

    /**
     * Show the form for creating a new inventory item.
     */
    public function create()
    {
        $categories = ['ice', 'meat', 'seafood', 'vegetables', 'fruits', 'beverages', 'dairy'];
        $storageLocations = ['Unit A', 'Unit B', 'Unit C', 'Unit D', 'Unit E'];
        return view('admin.inventory.create', compact('categories', 'storageLocations'));
    }

    /**
     * Store a newly created inventory item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'category' => 'required|in:ice,meat,seafood,vegetables,fruits,beverages,dairy',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|in:kg,liter,pieces,boxes',
            'storage_location' => 'required|in:Unit A,Unit B,Unit C,Unit D,Unit E',
            'expiration_date' => 'required|date|after_or_equal:today',
            'date_received' => 'required|date|before_or_equal:today',
            'supplier' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        // Set default temperature requirement based on category
        $validated['temperature_requirement'] = $this->getDefaultTemperatureForCategory($validated['category']);

        // Automatically determine status based on quantity and expiration date
        $validated['status'] = $this->determineStatus($validated['quantity'], $validated['expiration_date']);

        Inventory::create($validated);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item added successfully!');
    }

    /**
     * Show the form for editing the specified inventory item.
     */
    public function edit(Inventory $inventory)
    {
        $categories = ['ice', 'meat', 'seafood', 'vegetables', 'fruits', 'beverages', 'dairy'];
        $storageLocations = ['Unit A', 'Unit B', 'Unit C', 'Unit D', 'Unit E'];
        return view('admin.inventory.edit', compact('inventory', 'categories', 'storageLocations'));
    }

    /**
     * Update the specified inventory item in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'category' => 'required|in:ice,meat,seafood,vegetables,fruits,beverages,dairy',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|in:kg,liter,pieces,boxes',
            'storage_location' => 'required|in:Unit A,Unit B,Unit C,Unit D,Unit E',
            'expiration_date' => 'required|date',
            'date_received' => 'required|date',
            'supplier' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        // Set default temperature requirement based on category
        $validated['temperature_requirement'] = $this->getDefaultTemperatureForCategory($validated['category']);

        // Automatically determine status based on quantity and expiration date
        $validated['status'] = $this->determineStatus($validated['quantity'], $validated['expiration_date']);

        $inventory->update($validated);

        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item updated successfully!');
    }

    /**
     * Remove the specified inventory item from storage.
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();
        return redirect()->route('admin.inventory.index')
            ->with('success', 'Inventory item deleted successfully!');
    }

    /**
     * Display inventory list for warehouse staff (read-only).
     */
    public function warehouseIndex(Request $request)
    {
        $query = Inventory::query();

        // Search by product name
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%");
            });
        }

        // Sort by column
        $sortColumn = $request->get('sort_column', 'id');
        $sortDirection = $request->get('sort_direction', 'asc');

        // Allowed columns for sorting
        $allowedColumns = ['id', 'product_name', 'quantity', 'location', 'status', 'updated_at'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'id';
        }

        // Validate sort direction
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        // Apply sorting
        $query->orderBy($sortColumn, $sortDirection);

        $inventory = $query->paginate(20);

        return view('warehouse.inventory.index', compact('inventory'));
    }

    /**
     * Display details of a specific inventory item for warehouse staff (read-only).
     */
    public function warehouseShow(Inventory $inventory)
    {
        return view('warehouse.inventory.show', compact('inventory'));
    }

    /**
     * Display temperature monitoring list for temperature staff (read-only).
     */
    public function warehouseTemperatureIndex(Request $request)
    {
        $query = Inventory::query();

        // Search by product name
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%");
            });
        }

        // Sort by column
        $sortColumn = $request->get('sort_column', 'id');
        $sortDirection = $request->get('sort_direction', 'asc');

        // Allowed columns for sorting
        $allowedColumns = ['id', 'product_name', 'quantity', 'location', 'updated_at'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'id';
        }

        // Validate sort direction
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        // Apply sorting
        $query->orderBy($sortColumn, $sortDirection);

        $inventory = $query->paginate(20);

        return view('warehouse.temperature.index', compact('inventory'));
    }

    /**
     * Display temperature details for a specific inventory item.
     */
    public function warehouseTemperatureShow(Inventory $inventory)
    {
        return view('warehouse.temperature.show', compact('inventory'));
    }

    /**
     * Display payment management list for payment staff (read-only).
     */
    public function warehousePaymentIndex(Request $request)
    {
        $query = Inventory::query();

        // Search by product name
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%");
            });
        }

        // Sort by column
        $sortColumn = $request->get('sort_column', 'id');
        $sortDirection = $request->get('sort_direction', 'asc');

        // Allowed columns for sorting
        $allowedColumns = ['id', 'product_name', 'quantity', 'unit_cost', 'updated_at'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'id';
        }

        // Validate sort direction
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        // Apply sorting
        $query->orderBy($sortColumn, $sortDirection);

        $inventory = $query->paginate(20);

        return view('warehouse.payment.index', compact('inventory'));
    }

    /**
     * Display payment details for a specific inventory item.
     */
    public function warehousePaymentShow(Inventory $inventory)
    {
        return view('warehouse.payment.show', compact('inventory'));
    }

    /**
     * Get default temperature requirement based on product category.
     */
    private function getDefaultTemperatureForCategory($category)
    {
        $temperatures = [
            'meat' => -18,
            'seafood' => -25,
            'vegetables' => -15,
            'fruits' => -18,
            'ice' => -10,
            'dairy' => -20,
            'beverages' => -15,
        ];

        return $temperatures[$category] ?? -18;
    }

    /**
     * Automatically determine inventory status based on quantity and expiration date.
     */
    private function determineStatus($quantity, $expirationDate)
    {
        $expirationDate = \Carbon\Carbon::parse($expirationDate);
        $daysToExpiration = now()->diffInDays($expirationDate, false);

        // Check if expired
        if ($daysToExpiration < 0) {
            return 'expired';
        }

        // Check if expiring soon (within 30 days)
        if ($daysToExpiration <= 30) {
            return 'expiring_soon';
        }

        // Check stock level (low stock if quantity <= 10)
        if ($quantity <= 10) {
            return 'low_stock';
        }

        // Default to in stock
        return 'in_stock';
    }
}
