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
        $statuses = ['in_stock', 'low_stock'];
        return view('admin.inventory.create', compact('categories', 'storageLocations', 'statuses'));
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
            'temperature_requirement' => 'required|numeric|min:-50|max:0',
            'expiration_date' => 'required|date|after_or_equal:today',
            'date_received' => 'required|date|before_or_equal:today',
            'supplier' => 'required|string|max:255',
            'status' => 'required|in:in_stock,low_stock',
            'notes' => 'nullable|string|max:500',
        ]);

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
        $statuses = ['in_stock', 'low_stock', 'expired', 'expiring_soon'];
        return view('admin.inventory.edit', compact('inventory', 'categories', 'storageLocations', 'statuses'));
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
            'temperature_requirement' => 'required|numeric|min:-50|max:0',
            'expiration_date' => 'required|date',
            'date_received' => 'required|date',
            'supplier' => 'required|string|max:255',
            'status' => 'required|in:in_stock,low_stock,expired,expiring_soon',
            'notes' => 'nullable|string|max:500',
        ]);

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
}
