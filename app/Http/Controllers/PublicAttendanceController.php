<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PublicAttendanceController extends Controller
{
    /**
     * Show the public clock-in / clock-out page.
     */
    public function index()
    {
        return view('admin.attendance.index');
    }

    /**
     * Handle the employee ID lookup + clock action.
     */
    public function clock(Request $request)
    {
        $request->validate([
            'employee_id' => ['required', 'digits_between:1,10'],
        ], [
            'employee_id.required' => 'Please enter your Employee ID.',
            'employee_id.digits_between' => 'Employee ID must be a valid number.',
        ]);

        $employee = Employee::find($request->employee_id);

        if (!$employee) {
            return back()->withErrors(['employee_id' => 'Employee ID not found. Please check and try again.'])->withInput();
        }

        $today     = Carbon::today();
        $now       = Carbon::now();
        $record    = Attendance::where('employee_id', $employee->id)
                               ->whereDate('date', $today)
                               ->first();

        // ── CLOCK-IN ──────────────────────────────────────────────────────────
        if (!$record) {
            Attendance::create([
                'employee_id' => $employee->id,
                'marked_by'   => null,
                'date'        => $today,
                'status'      => 'present',
                'time_in'     => $now->format('H:i:s'),
                'time_out'    => null,
                'hours_worked'=> null,
                'notes'       => 'Self clock-in via attendance portal.',
            ]);

            return back()->with([
                'action'         => 'in',
                'employee_name'  => $employee->full_name,
                'employee_image' => $employee->image,
                'time'           => $now->format('h:i A'),
                'date'           => $today->format('F d, Y'),
            ]);
        }

        // ── ALREADY CLOCKED OUT ───────────────────────────────────────────────
        if ($record->time_out) {
            $recTimeIn  = Carbon::today()->setTimeFromTimeString($record->time_in);
            $recTimeOut = Carbon::today()->setTimeFromTimeString($record->time_out);
            $recHours   = number_format(round($recTimeIn->diffInMinutes($recTimeOut) / 60, 2), 2);
            return back()->with([
                'action'         => 'done',
                'employee_name'  => $employee->full_name,
                'employee_image' => $employee->image,
                'time_in'        => $recTimeIn->format('h:i A'),
                'time'           => $recTimeOut->format('h:i A'),
                'date'           => $today->format('F d, Y'),
                'hours_worked'   => $recHours,
            ]);
        }

        // ── CLOCK-OUT ─────────────────────────────────────────────────────────
        $timeIn      = Carbon::today()->setTimeFromTimeString($record->time_in);
        $minutesIn   = $timeIn->diffInMinutes($now);
        $hoursWorked = round($minutesIn / 60, 2);

        $record->update([
            'time_out'     => $now->format('H:i:s'),
            'hours_worked' => abs($hoursWorked),
        ]);

        return back()->with([
            'action'         => 'out',
            'employee_name'  => $employee->full_name,
            'employee_image' => $employee->image,
            'time_in'        => $timeIn->format('h:i A'),
            'time'           => $now->format('h:i A'),
            'date'           => $today->format('F d, Y'),
            'hours_worked'   => number_format($hoursWorked, 2),
        ]);
    }
}
