<?php

namespace App\Services;

use App\Models\TaskAssignment;
use App\Models\User;
use App\Models\Inventory;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AutoTaskAssignmentService
{
    /**
     * Automatically assign employees to storage locations based on inventory workload
     * 
     * @return array Statistics of assignments created
     */
    public function assignEmployeesToStorageLocations()
    {
        $assignments = [];
        $storageLocations = ['Unit A', 'Unit B', 'Unit C', 'Unit D', 'Unit E'];
        
        foreach ($storageLocations as $location) {
            // Count inventory items and calculate workload for this location
            $inventoryCount = Inventory::where('storage_location', $location)
                ->where('status', '!=', 'expired')
                ->count();
            
            // Determine how many employees needed (1 employee per 50 items, minimum 1)
            $employeesNeeded = max(1, ceil($inventoryCount / 50));
            
            // Get available inventory and temperature staff
            $availableStaff = User::whereIn('role', ['inventory_staff', 'temperature_staff'])
                ->get();
            
            if ($availableStaff->isEmpty()) {
                continue;
            }
            
            // Get staff who are not currently assigned to this location today
            $assignedToday = TaskAssignment::whereDate('created_at', today())
                ->where('task_type', 'storage_unit')
                ->where('title', 'LIKE', "%{$location}%")
                ->pluck('user_id')
                ->toArray();
            
            $unassignedStaff = $availableStaff->reject(function ($staff) use ($assignedToday) {
                return in_array($staff->id, $assignedToday);
            });
            
            // Assign employees
            $assigned = 0;
            foreach ($unassignedStaff->take($employeesNeeded) as $staff) {
                $task = TaskAssignment::create([
                    'user_id' => $staff->id,
                    'assigned_by' => auth()->id() ?? 1, // System auto-assignment
                    'task_type' => 'storage_unit',
                    'title' => "Monitor and Manage {$location}",
                    'description' => "Assigned to monitor inventory, check temperatures, and maintain {$location}. Current inventory items: {$inventoryCount}",
                    'priority' => $inventoryCount > 100 ? 'high' : ($inventoryCount > 50 ? 'medium' : 'low'),
                    'status' => 'pending',
                    'due_date' => today()->addDay(),
                    'notes' => 'Auto-assigned by system based on inventory workload'
                ]);
                
                $assignments[] = [
                    'location' => $location,
                    'employee' => $staff->name,
                    'inventory_count' => $inventoryCount,
                    'task_id' => $task->id
                ];
                
                $assigned++;
            }
        }
        
        return [
            'success' => true,
            'total_assignments' => count($assignments),
            'assignments' => $assignments
        ];
    }
    
    /**
     * Automatically assign delivery task when invoice is paid
     * 
     * @param Invoice $invoice
     * @return TaskAssignment|null
     */
    public function assignDeliveryForPaidInvoice(Invoice $invoice)
    {
        // Only assign if invoice is paid
        if ($invoice->status !== 'paid') {
            return null;
        }
        
        // Check if delivery task already exists for this invoice
        $existingTask = TaskAssignment::where('task_type', 'delivery')
            ->where('title', 'LIKE', "%{$invoice->invoice_number}%")
            ->first();
        
        if ($existingTask) {
            return $existingTask; // Already assigned
        }
        
        // Find available delivery personnel
        // Priority: those with fewer pending/in-progress deliveries
        $deliveryPersonnel = User::where('role', 'delivery_personnel')
            ->withCount(['taskAssignments as pending_deliveries' => function ($query) {
                $query->where('task_type', 'delivery')
                    ->whereIn('status', ['pending', 'in_progress']);
            }])
            ->orderBy('pending_deliveries', 'asc')
            ->first();
        
        if (!$deliveryPersonnel) {
            return null; // No delivery personnel available
        }
        
        // Get customer details
        $customer = $invoice->customer;
        $customerAddress = $customer ? $customer->address : 'Address not specified';
        $customerName = $customer ? $customer->name : 'Customer';
        
        // Create delivery task
        $task = TaskAssignment::create([
            'user_id' => $deliveryPersonnel->id,
            'assigned_by' => auth()->id() ?? 1,
            'task_type' => 'delivery',
            'title' => "Deliver Invoice #{$invoice->invoice_number}",
            'description' => "Deliver products to {$customerName}.\nAddress: {$customerAddress}\nTotal Amount: ₱" . number_format($invoice->total_amount, 2) . "\nStatus: Paid",
            'priority' => $invoice->total_amount > 10000 ? 'high' : 'medium',
            'status' => 'pending',
            'due_date' => $invoice->due_date ?? today()->addDays(3),
            'notes' => "Auto-assigned upon invoice payment. Invoice ID: {$invoice->id}"
        ]);
        
        return $task;
    }
    
    /**
     * Get statistics on current task assignments
     * 
     * @return array
     */
    public function getAssignmentStatistics()
    {
        $stats = [
            'storage_locations' => [],
            'delivery_personnel' => [],
            'recommendations' => []
        ];
        
        // Storage location coverage
        $storageLocations = ['Unit A', 'Unit B', 'Unit C', 'Unit D', 'Unit E'];
        foreach ($storageLocations as $location) {
            $inventoryCount = Inventory::where('storage_location', $location)
                ->where('status', '!=', 'expired')
                ->count();
            
            $assignedToday = TaskAssignment::whereDate('created_at', today())
                ->where('task_type', 'storage_unit')
                ->where('title', 'LIKE', "%{$location}%")
                ->where('status', '!=', 'completed')
                ->count();
            
            $recommended = max(1, ceil($inventoryCount / 50));
            
            $stats['storage_locations'][$location] = [
                'inventory_count' => $inventoryCount,
                'assigned_employees' => $assignedToday,
                'recommended_employees' => $recommended,
                'needs_more' => $assignedToday < $recommended
            ];
        }
        
        // Delivery personnel workload
        $deliveryStaff = User::where('role', 'delivery_personnel')
            ->withCount(['taskAssignments as pending_deliveries' => function ($query) {
                $query->where('task_type', 'delivery')
                    ->whereIn('status', ['pending', 'in_progress']);
            }])
            ->get();
        
        foreach ($deliveryStaff as $staff) {
            $stats['delivery_personnel'][$staff->name] = [
                'pending_deliveries' => $staff->pending_deliveries,
                'status' => $staff->pending_deliveries > 5 ? 'overloaded' : ($staff->pending_deliveries > 2 ? 'busy' : 'available')
            ];
        }
        
        // Generate recommendations
        foreach ($stats['storage_locations'] as $location => $data) {
            if ($data['needs_more']) {
                $stats['recommendations'][] = "Assign " . ($data['recommended_employees'] - $data['assigned_employees']) . " more employee(s) to {$location}";
            }
        }
        
        return $stats;
    }
}
