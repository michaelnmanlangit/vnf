<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of employees with search and filter.
     */
    public function index(Request $request)
    {
        $query = Employee::query();

        // Search by name
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        // Filter by department
        if ($request->has('department') && !empty($request->department)) {
            $query->where('department', $request->department);
        }

        // Filter by employment status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('employment_status', $request->status);
        }

        // Sort by column
        $sortColumn = $request->get('sort_column', 'id');
        $sortDirection = $request->get('sort_direction', 'asc');

        // Allowed columns for sorting (prevent SQL injection)
        $allowedColumns = ['id', 'first_name', 'position', 'department', 'employment_status'];
        if (!in_array($sortColumn, $allowedColumns)) {
            $sortColumn = 'id';
        }

        // Validate sort direction
        if (!in_array(strtolower($sortDirection), ['asc', 'desc'])) {
            $sortDirection = 'asc';
        }

        // Apply sorting
        if ($sortColumn === 'first_name') {
            $query->orderBy('first_name', $sortDirection)->orderBy('last_name', $sortDirection);
        } else {
            $query->orderBy($sortColumn, $sortDirection);
        }

        $employees = $query->paginate(20);
        $departments = ['production', 'warehouse', 'delivery', 'administration', 'maintenance'];
        $statuses = ['active', 'inactive', 'on_leave'];

        return view('admin.employees.index', compact('employees', 'departments', 'statuses'));
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $salaryRanges = $this->generateSalaryRanges();
        return view('admin.employees.create', compact('salaryRanges'));
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'required|in:production,warehouse,delivery,administration,maintenance',
            'employment_status' => 'required|in:active,inactive,on_leave',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images/employees'), $imageName);
            $validated['image'] = 'images/employees/' . $imageName;
        }

        $employee = Employee::create($validated);

        // Auto-create user account if employee is warehouse or delivery staff
        $accountCreated = false;
        $accountDetails = null;
        
        if (in_array($validated['department'], ['warehouse', 'delivery'])) {
            $role = $this->getRole($validated['department'], $validated['position']);
            $defaultPassword = 'DefaultPass@2026';
            
            // Check if user account already exists
            if (!User::where('email', $validated['email'])->exists()) {
                User::create([
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($defaultPassword),
                    'role' => $role,
                ]);
                $accountCreated = true;
                $accountDetails = [
                    'email' => $validated['email'],
                    'password' => $defaultPassword,
                    'role' => $role,
                ];
            }
        }

        $successMessage = 'Employee created successfully!';
        if ($accountCreated) {
            $successMessage .= " Account created: {$accountDetails['email']} with role: {$accountDetails['role']}";
        }

        return redirect()->route('admin.employees.index')
            ->with('success', $successMessage)
            ->with('accountDetails', $accountDetails);
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        $salaryRanges = $this->generateSalaryRanges();
        return view('admin.employees.edit', compact('employee', 'salaryRanges'));
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'department' => 'required|in:production,warehouse,delivery,administration,maintenance',
            'employment_status' => 'required|in:active,inactive,on_leave',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'user_role' => 'nullable|in:admin,inventory_staff,temperature_staff,payment_staff,delivery_personnel',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($employee->image && file_exists(public_path($employee->image))) {
                unlink(public_path($employee->image));
            }
            $imageName = time() . '_' . $request->file('image')->getClientOriginalName();
            $request->file('image')->move(public_path('images/employees'), $imageName);
            $validated['image'] = 'images/employees/' . $imageName;
        }

        $oldDepartment = $employee->department;
        $employee->update($validated);

        // Handle account creation if department changed to warehouse or delivery
        $accountCreated = false;
        $accountDetails = null;
        
        if (in_array($validated['department'], ['warehouse', 'delivery'])) {
            $user = User::where('email', $validated['email'])->first();
            
            if (!$user) {
                // Account doesn't exist, create one
                $role = $this->getRole($validated['department'], $validated['position']);
                $defaultPassword = 'DefaultPass@2026';
                
                User::create([
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($defaultPassword),
                    'role' => $role,
                ]);
                $accountCreated = true;
                $accountDetails = [
                    'email' => $validated['email'],
                    'password' => $defaultPassword,
                    'role' => $role,
                ];
            } else {
                // Account exists, update role and name
                $role = $request->has('user_role') && !empty($request->user_role) 
                    ? $request->user_role 
                    : $this->getRole($validated['department'], $validated['position']);
                
                $user->update([
                    'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                    'role' => $role,
                ]);
            }
        } elseif ($request->has('user_role') && !empty($request->user_role)) {
            // Update user role if provided and employee is not warehouse/delivery
            $user = User::where('email', $employee->email)->first();
            if ($user) {
                $user->update(['role' => $request->user_role]);
            }
        }

        $successMessage = 'Employee updated successfully!';
        if ($accountCreated) {
            $successMessage .= " Account created: {$accountDetails['email']} with role: {$accountDetails['role']}";
        }

        return redirect()->route('admin.employees.index')
            ->with('success', $successMessage);
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee)
    {
        // Delete image if exists
        if ($employee->image && file_exists(public_path($employee->image))) {
            unlink(public_path($employee->image));
        }

        $employee->delete();

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee deleted successfully!');
    }

    /**
     * Generate salary ranges from 1k to 100k with 5k interval.
     */
    private function generateSalaryRanges()
    {
        $salaries = [];
        for ($i = 10000; $i <= 100000; $i += 5000) {
            $salaries[$i] = number_format($i, 0);
        }
        return $salaries;
    }

    /**
     * Get role based on department and position
     */
    private function getRole($department, $position)
    {
        if ($department === 'warehouse') {
            if (str_contains(strtolower($position), 'temperature')) {
                return 'temperature_staff';
            } elseif (str_contains(strtolower($position), 'payment')) {
                return 'payment_staff';
            } elseif (str_contains(strtolower($position), 'inventory')) {
                return 'inventory_staff';
            } else {
                return 'inventory_staff'; // Default warehouse role
            }
        } elseif ($department === 'delivery') {
            return 'delivery_personnel';
        }
        return 'user';
    }
}

