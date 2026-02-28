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
        $departments = ['production', 'warehouse', 'delivery', 'administration'];
        $statuses = ['active', 'inactive', 'on_leave'];

        // Calculate statistics
        $stats = [
            'total' => Employee::count(),
            'active' => Employee::where('employment_status', 'active')->count(),
            'inactive' => Employee::where('employment_status', 'inactive')->count(),
            'on_leave' => Employee::where('employment_status', 'on_leave')->count(),
            'by_department' => [
                'production' => Employee::where('department', 'production')->count(),
                'warehouse' => Employee::where('department', 'warehouse')->count(),
                'delivery' => Employee::where('department', 'delivery')->count(),
                'administration' => Employee::where('department', 'administration')->count(),
            ]
        ];

        return view('admin.employees.index', compact('employees', 'departments', 'statuses', 'stats'));
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
            'department' => 'required|in:production,warehouse,delivery,administration',
            'employment_status' => 'required|in:active,inactive,on_leave',
            'return_date' => 'nullable|date|after_or_equal:today|required_if:employment_status,on_leave',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Clear return_date if not on leave
        if ($validated['employment_status'] !== 'on_leave') {
            $validated['return_date'] = null;
        }

        // Handle image upload — store as base64 in DB for ephemeral filesystem compatibility
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $validated['image'] = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        }

        $employee = Employee::create($validated);

        // Auto-create user account if employee is warehouse or delivery staff
        $accountCreated = false;
        $accountDetails = null;
        
        if (in_array($validated['department'], ['warehouse', 'delivery'])) {
            $role = $validated['department'];
            $defaultPassword = $this->generatePassword($validated['first_name'], $validated['last_name'], $employee->id);
            
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
            $successMessage .= " Account created";
            return redirect()->route('admin.employees.edit', $employee)
                ->with('success', $successMessage)
                ->with('accountDetails', $accountDetails);
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
        $user = User::where('email', $employee->email)->first();
        $salaryRanges = $this->generateSalaryRanges();
        return view('admin.employees.edit', compact('employee', 'salaryRanges', 'user'));
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
            'department' => 'required|in:production,warehouse,delivery,administration',
            'employment_status' => 'required|in:active,inactive,on_leave',
            'return_date' => 'nullable|date|after_or_equal:today|required_if:employment_status,on_leave',
            'hire_date' => 'required|date',
            'salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'user_role' => 'nullable|in:admin,inventory_staff,temperature_staff,payment_staff,delivery_personnel',
        ]);

        // Clear return_date if not on leave
        if ($validated['employment_status'] !== 'on_leave') {
            $validated['return_date'] = null;
        }

        // Handle image upload — store as base64 in DB for ephemeral filesystem compatibility
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $validated['image'] = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        }

        $oldDepartment = $employee->department;
        $employee->update($validated);

        // Handle account removal if department changed from warehouse/delivery to non-account role
        $accountCreated = false;
        $accountDetails = null;
        
        if (!in_array($validated['department'], ['warehouse', 'delivery']) && 
            in_array($oldDepartment, ['warehouse', 'delivery'])) {
            // Department changed to non-account role, remove user account
            $user = User::where('email', $employee->email)->first();
            if ($user) {
                $user->delete();
            }
        } elseif (in_array($validated['department'], ['warehouse', 'delivery'])) {
            // Department is warehouse or delivery, create or update account
            $user = User::where('email', $validated['email'])->first();
            
            if (!$user) {
                // Account doesn't exist, create one
                $role = $this->getRole($validated['department'], $validated['position']);
                $defaultPassword = $this->generatePassword($validated['first_name'], $validated['last_name'], $employee->id);
                
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
            $successMessage .= " Account created";
            return redirect()->route('admin.employees.edit', $employee)
                ->with('success', $successMessage)
                ->with('accountDetails', $accountDetails);
        } elseif (!in_array($validated['department'], ['warehouse', 'delivery']) && 
                  in_array($oldDepartment, ['warehouse', 'delivery'])) {
            $successMessage .= " User account removed.";
        }

        return redirect()->route('admin.employees.index')
            ->with('success', $successMessage);
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee)
    {
        // Delete associated user account if exists
        $user = User::where('email', $employee->email)->first();
        if ($user) {
            $user->delete();
        }

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

    /**
     * Generate default password based on first name, last name, and employee id
     */
    private function generatePassword($firstName, $lastName, $employeeId)
    {
        $firstNamePart = ucfirst(strtolower(str_replace(' ', '', $firstName)));
        $lastNamePart = ucfirst(strtolower(str_replace(' ', '', $lastName)));
        return $firstNamePart . $lastNamePart . $employeeId;
    }
}

