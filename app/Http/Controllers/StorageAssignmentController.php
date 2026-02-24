<?php

namespace App\Http\Controllers;

use App\Models\StorageUnit;
use App\Models\Employee;
use App\Models\User;
use App\Services\WorkerAutoAssignmentService;
use Illuminate\Http\Request;

class StorageAssignmentController extends Controller
{
    protected $autoAssignService;

    public function __construct(WorkerAutoAssignmentService $autoAssignService)
    {
        $this->autoAssignService = $autoAssignService;
    }

    /**
     * Display storage unit management dashboard.
     */
    public function index()
    {
        $storageUnits = StorageUnit::where('type', 'cold_storage')
            ->where('status', 'active')
            ->withCount(['assignedEmployees' => function ($query) {
                $query->where('department', 'production')->where('employment_status', 'active');
            }])
            ->with('supervisor')
            ->get();

        $stats = $this->autoAssignService->getDistributionStats();

        // Get available supervisors (users with storage_supervisor role but not assigned)
        $availableSupervisors = User::where('role', 'storage_supervisor')
            ->whereNull('assigned_storage_location_id')
            ->get();

        // Get all supervisors
        $allSupervisors = User::where('role', 'storage_supervisor')->get();

        return view('admin.storage.index', compact('storageUnits', 'stats', 'available Supervisors', 'allSupervisors'));
    }

    /**
     * Show worker distribution for a specific storage unit.
     */
    public function show($id)
    {
        $storageUnit = StorageUnit::with([
            'assignedEmployees' => function ($query) {
                $query->where('department', 'production')->where('employment_status', 'active');
            },
            'supervisor'
        ])->findOrFail($id);

        // Get unassigned production workers
        $unassignedWorkers = Employee::where('department', 'production')
            ->where('employment_status', 'active')
            ->whereNull('assigned_storage_unit_id')
            ->get();

        return view('admin.storage.show', compact('storageUnit', 'unassignedWorkers'));
    }

    /**
     * Assign a supervisor to a storage unit.
     */
    public function assignSupervisor(Request $request, $id)
    {
        $validated = $request->validate([
            'supervisor_id' => 'required|exists:users,id',
        ]);

        $storageUnit = StorageUnit::findOrFail($id);
        $supervisor = User::findOrFail($validated['supervisor_id']);

        // Verify user is a storage supervisor
        if ($supervisor->role !== 'storage_supervisor') {
            return redirect()->back()->with('error', 'Selected user is not a storage supervisor.');
        }

        // Remove previous assignment if supervisor was assigned elsewhere
        if ($supervisor->assigned_storage_location_id) {
            $supervisor->assigned_storage_location_id = null;
            $supervisor->save();
        }

        // Assign supervisor to this storage unit
        $supervisor->assigned_storage_location_id = $storageUnit->id;
        $supervisor->save();

        // Update all workers in this unit to have this supervisor
        Employee::where('assigned_storage_unit_id', $storageUnit->id)
            ->where('department', 'production')
            ->update(['supervisor_id' => $supervisor->id]);

        return redirect()->back()->with('success', "Supervisor {$supervisor->name} assigned to {$storageUnit->name}.");
    }

    /**
     * Remove supervisor from a storage unit.
     */
    public function removeSupervisor($id)
    {
        $storageUnit = StorageUnit::findOrFail($id);
        
        if ($storageUnit->supervisor) {
            $supervisor = $storageUnit->supervisor;
            $supervisor->assigned_storage_location_id = null;
            $supervisor->save();

            // Remove supervisor from workers
            Employee::where('supervisor_id', $supervisor->id)->update(['supervisor_id' => null]);

            return redirect()->back()->with('success', "Supervisor removed from {$storageUnit->name}.");
        }

        return redirect()->back()->with('error', 'No supervisor assigned to this unit.');
    }

    /**
     * Manually reassign a worker to a different storage unit.
     */
    public function reassignWorker(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'storage_unit_id' => 'required|exists:storage_units,id',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);
        $storageUnit = StorageUnit::findOrFail($validated['storage_unit_id']);

        $success = $this->autoAssignService->reassignWorker($employee, $storageUnit->id);

        // Update supervisor link
        if ($storageUnit->supervisor) {
            $employee->supervisor_id = $storageUnit->supervisor->id;
            $employee->save();
        }

        if ($success) {
            return redirect()->back()->with('success', "{$employee->full_name} reassigned to {$storageUnit->name}.");
        }

        return redirect()->back()->with('error', 'Failed to reassign worker.');
    }

    /**
     * Automatically redistribute all production workers evenly.
     */
    public function autoRedistribute()
    {
        $result = $this->autoAssignService->redistributeAllWorkers();

        if (isset($result['error'])) {
            return redirect()->back()->with('error', $result['error']);
        }

        // Update supervisor links for all workers
        $storageUnits = StorageUnit::with('supervisor', 'assignedEmployees')->get();
        foreach ($storageUnits as $unit) {
            if ($unit->supervisor) {
                Employee::where('assigned_storage_unit_id', $unit->id)
                    ->where('department', 'production')
                    ->update(['supervisor_id' => $unit->supervisor->id]);
            }
        }

        $message = "Workers redistributed successfully. {$result['total_workers']} workers across {$result['total_units']} units.";
        return redirect()->back()->with('success', $message)->with('stats', $result['distribution']);
    }

    /**
     * View all workers and their assignments.
     */
    public function workers()
    {
        $workers = Employee::where('department', 'production')
            ->with(['assignedStorageUnit', 'supervisor'])
            ->orderBy('assigned_storage_unit_id')
            ->orderBy('last_name')
            ->get();

        $storageUnits = StorageUnit::where('type', 'cold_storage')
            ->where('status', 'active')
            ->get();

        return view('admin.storage.workers', compact('workers', 'storageUnits'));
    }
}
