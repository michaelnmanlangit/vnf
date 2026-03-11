<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    /**
     * Display all attendance records for admin.
     */
    public function index(Request $request)
    {
        $date = $request->get('date', Carbon::today()->toDateString());
        $selectedDate = Carbon::parse($date);

        // Fetch all employees with their attendance for the selected date
        $employees = Employee::with(['attendance' => function ($q) use ($date) {
                $q->whereDate('date', $date);
            }])
            ->orderBy('first_name')
            ->get();

        // Stats for the selected date
        $totalEmployees = $employees->count();
        $presentCount   = Attendance::whereDate('date', $date)->where('status', 'present')->count();
        $absentCount    = Attendance::whereDate('date', $date)->where('status', 'absent')->count();
        $lateCount      = Attendance::whereDate('date', $date)->where('status', 'late')->count();
        $notMarkedCount = $employees->filter(fn($e) => $e->attendance->isEmpty())->count();

        return view('admin.attendance.index', compact(
            'employees', 'date', 'selectedDate',
            'totalEmployees', 'presentCount', 'absentCount', 'lateCount', 'notMarkedCount'
        ));
    }

    /**
     * Manually mark or update an employee's attendance.
     */
    public function mark(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'date'        => 'required|date',
            'status'      => 'required|in:present,absent,late,half_day,on_leave',
            'time_in'     => 'nullable|date_format:H:i',
            'time_out'    => 'nullable|date_format:H:i|after:time_in',
            'notes'       => 'nullable|string|max:500',
        ]);

        $hoursWorked = null;
        if (!empty($validated['time_in']) && !empty($validated['time_out'])) {
            $in  = Carbon::today()->setTimeFromTimeString($validated['time_in']);
            $out = Carbon::today()->setTimeFromTimeString($validated['time_out']);
            $hoursWorked = round(abs($in->diffInMinutes($out)) / 60, 2);
        }

        Attendance::updateOrCreate(
            ['employee_id' => $validated['employee_id'], 'date' => $validated['date']],
            [
                'marked_by'    => auth()->id(),
                'status'       => $validated['status'],
                'time_in'      => $validated['time_in'] ?? null,
                'time_out'     => $validated['time_out'] ?? null,
                'hours_worked' => $hoursWorked,
                'notes'        => $validated['notes'] ?? null,
            ]
        );

        return redirect()->route('admin.attendance.index', ['date' => $validated['date']])
            ->with('success', 'Attendance record saved.');
    }
}
