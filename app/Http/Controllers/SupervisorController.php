<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SupervisorController extends Controller
{
    /**
     * Display supervisor dashboard with team overview.
     */
    public function index()
    {
        $supervisor = Auth::user();
        
        // Get supervisor's assigned storage location
        $storageLocation = $supervisor->assignedStorageLocation;
        
        if (!$storageLocation) {
            return view('supervisor.dashboard')->with('error', 'You are not assigned to any storage location.');
        }

        // Get all production workers assigned to this storage unit
        $workers = Employee::where('assigned_storage_unit_id', $storageLocation->id)
            ->where('department', 'production')
            ->where('employment_status', 'active')
            ->with('todayAttendance')
            ->get();

        // Get today's attendance statistics
        $today = Carbon::today();
        $totalWorkers = $workers->count();
        $presentCount = Attendance::whereDate('date', $today)
            ->whereIn('employee_id', $workers->pluck('id'))
            ->where('status', 'present')
            ->count();
        $absentCount = Attendance::whereDate('date', $today)
            ->whereIn('employee_id', $workers->pluck('id'))
            ->where('status', 'absent')
            ->count();
        $lateCount = Attendance::whereDate('date', $today)
            ->whereIn('employee_id', $workers->pluck('id'))
            ->where('status', 'late')
            ->count();

        return view('supervisor.dashboard', compact(
            'supervisor',
            'storageLocation',
            'workers',
            'totalWorkers',
            'presentCount',
            'absentCount',
            'lateCount'
        ));
    }

    /**
     * Display attendance management page.
     */
    public function attendance(Request $request)
    {
        $supervisor = Auth::user();
        $storageLocation = $supervisor->assignedStorageLocation;

        if (!$storageLocation) {
            return redirect()->back()->with('error', 'You are not assigned to any storage location.');
        }

        $date = $request->input('date', Carbon::today()->toDateString());
        
        // Get all workers assigned to this supervisor's storage unit
        $workers = Employee::where('assigned_storage_unit_id', $storageLocation->id)
            ->where('department', 'production')
            ->where('employment_status', 'active')
            ->with(['attendance' => function ($query) use ($date) {
                $query->whereDate('date', $date);
            }])
            ->get();

        return view('supervisor.attendance', compact('workers', 'storageLocation', 'date'));
    }

    /**
     * Mark attendance for a worker.
     */
    public function markAttendance(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date' => 'required|date',
            'status' => 'required|in:present,absent,late,half_day,on_leave',
            'time_in' => 'nullable|date_format:H:i',
            'time_out' => 'nullable|date_format:H:i',
            'notes' => 'nullable|string|max:500',
        ]);

        $supervisor = Auth::user();
        
        // Verify the employee belongs to supervisor's storage unit
        $employee = Employee::find($validated['employee_id']);
        if ($employee->assigned_storage_unit_id !== $supervisor->assigned_storage_location_id) {
            return redirect()->back()->with('error', 'You can only mark attendance for your assigned workers.');
        }

        // Create or update attendance record
        Attendance::updateOrCreate(
            [
                'employee_id' => $validated['employee_id'],
                'date' => $validated['date'],
            ],
            [
                'status' => $validated['status'],
                'time_in' => $validated['time_in'],
                'time_out' => $validated['time_out'],
                'notes' => $validated['notes'],
                'marked_by' => $supervisor->id,
            ]
        );

        return redirect()->back()->with('success', 'Attendance marked successfully.');
    }

    /**
     * Bulk mark attendance for multiple workers.
     */
    public function bulkMarkAttendance(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'attendance' => 'required|array',
            'attendance.*.employee_id' => 'required|exists:employees,id',
            'attendance.*.status' => 'required|in:present,absent,late,half_day,on_leave',
            'attendance.*.time_in' => 'nullable|date_format:H:i',
            'attendance.*.time_out' => 'nullable|date_format:H:i',
        ]);

        $supervisor = Auth::user();
        $date = $validated['date'];

        foreach ($validated['attendance'] as $record) {
            // Verify the employee belongs to supervisor's storage unit
            $employee = Employee::find($record['employee_id']);
            if ($employee->assigned_storage_unit_id !== $supervisor->assigned_storage_location_id) {
                continue; // Skip if not supervisor's worker
            }

            Attendance::updateOrCreate(
                [
                    'employee_id' => $record['employee_id'],
                    'date' => $date,
                ],
                [
                    'status' => $record['status'],
                    'time_in' => $record['time_in'] ?? null,
                    'time_out' => $record['time_out'] ?? null,
                    'marked_by' => $supervisor->id,
                ]
            );
        }

        return redirect()->back()->with('success', 'Attendance marked successfully for all workers.');
    }

    /**
     * View attendance history for the supervisor's team.
     */
    public function attendanceHistory(Request $request)
    {
        $supervisor = Auth::user();
        $storageLocation = $supervisor->assignedStorageLocation;

        if (!$storageLocation) {
            return redirect()->back()->with('error', 'You are not assigned to any storage location.');
        }

        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->toDateString());

        $workers = Employee::where('assigned_storage_unit_id', $storageLocation->id)
            ->where('department', 'production')
            ->where('employment_status', 'active')
            ->with(['attendance' => function ($query) use ($startDate, $endDate) {
                $query->whereBetween('date', [$startDate, $endDate])
                    ->orderBy('date', 'desc');
            }])
            ->get();

        return view('supervisor.attendance-history', compact('workers', 'storageLocation', 'startDate', 'endDate'));
    }
}
