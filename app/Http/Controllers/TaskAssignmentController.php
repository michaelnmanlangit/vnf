<?php

namespace App\Http\Controllers;

use App\Models\TaskAssignment;
use App\Models\User;
use App\Models\Employee;
use App\Models\StorageUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskAssignmentController extends Controller
{
    /**
     * Display a listing of task assignments
     */
    public function index(Request $request)
    {
        $query = Employee::with(['assignedStorageUnit', 'taskAssignments'])
            ->whereIn('department', ['production', 'administration', 'delivery', 'warehouse'])
            ->where('employment_status', 'active');

        // Filter by employee type/department
        if ($request->filled('employee_type')) {
            $query->where('department', $request->employee_type);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%');
            });
        }

        $tasks = $query->orderBy('first_name')->paginate(15);

        // Get storage units with worker counts (exclude maintenance/administration)
        $storageUnits = StorageUnit::withCount('assignedEmployees')
            ->withCount(['assignedEmployees as production_count' => function ($q) {
                $q->where('department', 'production');
            }])
            ->withCount(['assignedEmployees as warehouse_count' => function ($q) {
                $q->where('department', 'warehouse');
            }])
            ->where('status', '!=', 'maintenance')
            ->whereNotIn('type', ['maintenance', 'administration'])
            ->orderBy('name')
            ->get();

        // Get active employees for relocation modal
        $employees = Employee::whereIn('department', ['production', 'administration', 'delivery', 'warehouse'])
            ->where('employment_status', 'active')
            ->with('assignedStorageUnit')
            ->orderBy('first_name')
            ->get();

        $users = collect();
        $stats = [];

        return view('admin.tasks.index', compact('tasks', 'users', 'stats', 'storageUnits', 'employees'));
    }

    /**
     * Show the form for creating a new task assignment
     */
    public function create()
    {
        $employees = Employee::whereIn('department', ['production', 'administration'])
            ->where('employment_status', 'active')
            ->with('assignedStorageUnit')
            ->orderBy('first_name')
            ->get();

        $storageUnits = StorageUnit::orderBy('name')->get();

        return view('admin.tasks.create', compact('employees', 'storageUnits'));
    }

    /**
     * Store a newly created task assignment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id'                => 'required|exists:employees,id',
            'relocate_to_storage_unit_id' => 'required|exists:storage_units,id',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        $validated['task_type']  = 'relocation';
        $validated['title']      = 'Relocation: ' . $employee->full_name;
        $validated['assigned_by'] = Auth::id();
        $validated['status']     = 'pending';
        $validated['user_id']    = null;

        TaskAssignment::create($validated);

        // Update the employee's current storage unit
        $employee->update(['assigned_storage_unit_id' => $validated['relocate_to_storage_unit_id']]);

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Relocation work assigned successfully!');
    }

    /**
     * Display the specified task assignment
     */
    public function show(TaskAssignment $task)
    {
        $task->load(['user', 'assignedBy']);
        
        // Get employee info if available
        $employee = Employee::where('email', $task->user->email)->first();

        return view('admin.tasks.show', compact('task', 'employee'));
    }

    /**
     * Show the form for editing the specified task assignment
     */
    public function edit(TaskAssignment $task)
    {
        $employees = Employee::whereIn('department', ['production', 'administration'])
            ->where('employment_status', 'active')
            ->with('assignedStorageUnit')
            ->orderBy('first_name')
            ->get();

        $storageUnits = StorageUnit::orderBy('name')->get();

        return view('admin.tasks.edit', compact('task', 'employees', 'storageUnits'));
    }

    /**
     * Update the specified task assignment
     */
    public function update(Request $request, TaskAssignment $task)
    {
        $validated = $request->validate([
            'employee_id'                => 'required|exists:employees,id',
            'relocate_to_storage_unit_id' => 'required|exists:storage_units,id',
        ]);

        $employee = Employee::findOrFail($validated['employee_id']);

        $validated['task_type'] = 'relocation';
        $validated['title']     = 'Relocation: ' . $employee->full_name;
        $validated['user_id']   = null;

        $task->update($validated);

        // Update the employee's current storage unit
        $employee->update(['assigned_storage_unit_id' => $validated['relocate_to_storage_unit_id']]);

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Relocation task updated successfully!');
    }

    /**
     * Remove the specified task assignment
     */
    public function destroy(TaskAssignment $task)
    {
        $task->delete();

        return redirect()->route('admin.tasks.index')
            ->with('success', 'Task assignment deleted successfully!');
    }

    /**
     * AJAX: return employee info (current location) for the relocation form
     */
    public function getEmployeeInfo(Employee $employee)
    {
        $employee->load('assignedStorageUnit');

        return response()->json([
            'id'           => $employee->id,
            'name'         => $employee->full_name,
            'department'   => ucfirst($employee->department),
            'position'     => $employee->position,
            'current_unit' => $employee->assignedStorageUnit
                ? [
                    'id'   => $employee->assignedStorageUnit->id,
                    'name' => $employee->assignedStorageUnit->name,
                    'code' => $employee->assignedStorageUnit->code,
                  ]
                : null,
        ]);
    }

    /**
     * Quick status update
     */
    public function updateStatus(Request $request, TaskAssignment $task)
    {
        $validated = $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(TaskAssignment::STATUSES)),
        ]);

        if ($validated['status'] === 'completed') {
            $task->markCompleted();
        } else {
            $task->status = $validated['status'];
            $task->completed_at = null;
            $task->save();
        }

        return redirect()->back()->with('success', 'Task status updated!');
    }
}
