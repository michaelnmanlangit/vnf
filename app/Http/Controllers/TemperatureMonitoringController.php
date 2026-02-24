<?php

namespace App\Http\Controllers;

use App\Models\StorageUnit;
use App\Models\TemperatureLog;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TemperatureMonitoringController extends Controller
{
    /**
     * Display the temperature monitoring dashboard
     */
    public function index()
    {
        // Mapping between storage unit codes and inventory storage locations
        $storageMapping = [
            'CS-001' => 'Unit A',
            'CS-002' => 'Unit B',
            'CS-003' => 'Unit C',
            'CS-004' => 'Unit D',
            'CS-005' => 'Unit E',
        ];
        
        // Get all storage units with their latest temperature logs
        $storageUnits = StorageUnit::with(['latestTemperatureLog'])
        ->where('type', 'cold_storage')
        ->where('status', 'active')
        ->get()
        ->map(function($unit) use ($storageMapping) {
            $latest = $unit->latestTemperatureLog;
            
            // Get inventory items for this unit using the mapping
            $inventoryLocation = $storageMapping[$unit->code] ?? null;
            $inventoryItems = collect();
            
            if ($inventoryLocation) {
                $inventoryItems = Inventory::where('storage_location', $inventoryLocation)
                    ->where('status', '!=', 'expired')
                    ->get();
            }
            
            return [
                'id' => $unit->id,
                'name' => $unit->name,
                'code' => $unit->code,
                'temperature_min' => $unit->temperature_min,
                'temperature_max' => $unit->temperature_max,
                'current_temperature' => $latest ? $latest->temperature : null,
                'current_humidity' => $latest ? $latest->humidity : null,
                'last_updated' => $latest ? $latest->recorded_at->diffForHumans() : 'No data',
                'last_updated_full' => $latest ? $latest->recorded_at->format('M d, Y h:i A') : null,
                'status' => $unit->getCurrentTemperatureStatus(),
                'products' => $inventoryItems->groupBy('category')->map(function($items, $category) {
                    return [
                        'category' => ucfirst($category),
                        'items' => $items->map(function($item) {
                            return [
                                'name' => $item->product_name,
                                'quantity' => $item->quantity . ' ' . $item->unit,
                            ];
                        })->values()
                    ];
                })->values(),
                'total_products' => $inventoryItems->count(),
                'capacity' => $unit->capacity,
            ];
        });

        return view('temperature.monitor', compact('storageUnits'));
    }

    /**
     * Show detailed view for a specific storage unit
     */
    public function show($id)
    {
        // Mapping between storage unit codes and inventory storage locations
        $storageMapping = [
            'CS-001' => 'Unit A',
            'CS-002' => 'Unit B',
            'CS-003' => 'Unit C',
            'CS-004' => 'Unit D',
            'CS-005' => 'Unit E',
        ];
        
        $unit = StorageUnit::with(['latestTemperatureLog'])->findOrFail($id);
        
        $latestTemp = $unit->latestTemperatureLog;
        $status = $unit->getCurrentTemperatureStatus();
        
        // Get inventory items for this unit using the mapping
        $inventoryLocation = $storageMapping[$unit->code] ?? null;
        $inventoryItems = collect();
        
        if ($inventoryLocation) {
            $inventoryItems = Inventory::where('storage_location', $inventoryLocation)
                ->where('status', '!=', 'expired')
                ->get();
        }
        
        $products = $inventoryItems->groupBy('category')->map(function($items, $category) {
            return [
                'category' => ucfirst($category),
                'items' => $items->map(function($item) {
                    return [
                        'name' => $item->product_name,
                        'quantity' => $item->quantity . ' ' . $item->unit,
                    ];
                })->values()
            ];
        })->values();
        
        $totalProducts = $inventoryItems->count();
        
        return view('temperature.detail', compact('unit', 'latestTemp', 'status', 'products', 'totalProducts'));
    }

    /**
     * Get temperature data for a specific unit (AJAX)
     */
    public function getUnitData($id)
    {
        // Mapping between storage unit codes and inventory storage locations
        $storageMapping = [
            'CS-001' => 'Unit A',
            'CS-002' => 'Unit B',
            'CS-003' => 'Unit C',
            'CS-004' => 'Unit D',
            'CS-005' => 'Unit E',
        ];
        
        $unit = StorageUnit::with(['latestTemperatureLog'])->findOrFail($id);

        $latest = $unit->latestTemperatureLog;
        
        // Get inventory items for this unit using the mapping
        $inventoryLocation = $storageMapping[$unit->code] ?? null;
        $inventoryItems = collect();
        
        if ($inventoryLocation) {
            $inventoryItems = Inventory::where('storage_location', $inventoryLocation)
                ->where('status', '!=', 'expired')
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $unit->id,
                'name' => $unit->name,
                'code' => $unit->code,
                'temperature_min' => $unit->temperature_min,
                'temperature_max' => $unit->temperature_max,
                'current_temperature' => $latest ? $latest->temperature : null,
                'current_humidity' => $latest ? $latest->humidity : null,
                'last_updated' => $latest ? $latest->recorded_at->diffForHumans() : 'No data',
                'status' => $unit->getCurrentTemperatureStatus(),
                'products' => $inventoryItems->groupBy('category')->map(function($items, $category) {
                    return [
                        'category' => ucfirst($category),
                        'items' => $items->map(function($item) {
                            return [
                                'name' => $item->product_name,
                                'quantity' => $item->quantity . ' ' . $item->unit,
                            ];
                        })
                    ];
                }),
            ]
        ]);
    }

    /**
     * Record a new temperature reading
     */
    public function recordTemperature(Request $request)
    {
        $validated = $request->validate([
            'storage_unit_id' => 'required|exists:storage_units,id',
            'temperature' => 'required|numeric|between:-50,50',
            'humidity' => 'nullable|numeric|between:0,100',
            'notes' => 'nullable|string|max:500',
        ]);

        $unit = StorageUnit::findOrFail($validated['storage_unit_id']);
        
        // Determine status based on temperature
        $status = TemperatureLog::determineStatus($validated['temperature'], $unit);

        $log = TemperatureLog::create([
            'storage_unit_id' => $validated['storage_unit_id'],
            'temperature' => $validated['temperature'],
            'humidity' => $validated['humidity'] ?? null,
            'status' => $status,
            'recorded_by' => auth()->id(),
            'notes' => $validated['notes'] ?? null,
            'recorded_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Temperature recorded successfully!');
    }

    /**
     * Get temperature chart data for all units
     */
    public function getChartData()
    {
        $units = StorageUnit::where('type', 'cold_storage')
            ->where('status', 'active')
            ->get();

        $data = [];
        foreach ($units as $unit) {
            $recentLogs = TemperatureLog::where('storage_unit_id', $unit->id)
                ->where('recorded_at', '>=', Carbon::now()->subHours(12))
                ->orderBy('recorded_at', 'asc')
                ->get();

            $data[] = [
                'unit' => $unit->name,
                'code' => $unit->code,
                'data' => $recentLogs->map(function($log) {
                    return [
                        'x' => $log->recorded_at->format('H:i'),
                        'y' => $log->temperature,
                    ];
                }),
                'min' => $unit->temperature_min,
                'max' => $unit->temperature_max,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Generate simulated temperature readings for testing
     */
    public function simulateReadings()
    {
        $units = StorageUnit::where('type', 'cold_storage')
            ->where('status', 'active')
            ->get();

        foreach ($units as $unit) {
            // Generate a temperature within or slightly outside the acceptable range
            $baseTemp = ($unit->temperature_min + $unit->temperature_max) / 2;
            $variation = rand(-30, 30) / 10; // -3 to +3 degrees variation
            $temperature = $baseTemp + $variation;

            $status = TemperatureLog::determineStatus($temperature, $unit);

            TemperatureLog::create([
                'storage_unit_id' => $unit->id,
                'temperature' => round($temperature, 2),
                'humidity' => rand(40, 80),
                'status' => $status,
                'recorded_by' => auth()->id(),
                'recorded_at' => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Simulated temperature readings generated for all units',
        ]);
    }

    /**
     * Get temperature history for a specific storage unit
     */
    public function getHistory($id, Request $request)
    {
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        $query = TemperatureLog::where('storage_unit_id', $id)
            ->with('recordedBy:id,name');
        
        if ($startDate) {
            $query->whereDate('recorded_at', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->whereDate('recorded_at', '<=', $endDate);
        }
        
        // If no dates provided, show last 30 days
        if (!$startDate && !$endDate) {
            $query->where('recorded_at', '>=', Carbon::now()->subDays(30));
        }
        
        $logs = $query->orderBy('recorded_at', 'desc')->get();
        
        return response()->json($logs);
    }
}
