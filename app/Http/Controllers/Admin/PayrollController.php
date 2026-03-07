<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    /**
     * Show payroll summary for a given pay period.
     * Default: current month (split into two semi-monthly periods if needed).
     */
    public function index(Request $request)
    {
        // Date range defaults to current month
        $from = $request->input('from', Carbon::now()->startOfMonth()->toDateString());
        $to   = $request->input('to',   Carbon::now()->endOfMonth()->toDateString());

        $employees = Employee::with(['attendance' => function ($q) use ($from, $to) {
            $q->whereBetween('date', [$from, $to]);
        }])->orderBy('last_name')->get();

        $payroll = $employees->map(function ($emp) use ($from, $to) {
            $records       = $emp->attendance;
            $daysPresent   = $records->whereIn('status', ['present', 'late', 'half_day'])->count();
            $daysAbsent    = $records->where('status', 'absent')->count();
            $daysLate      = $records->where('status', 'late')->count();
            $totalHours    = $records->sum(function ($rec) {
                if ($rec->time_in && $rec->time_out) {
                    $in  = Carbon::today()->setTimeFromTimeString($rec->time_in);
                    $out = Carbon::today()->setTimeFromTimeString($rec->time_out);
                    return abs(round($in->diffInMinutes($out) / 60, 2));
                }
                return max(0, (float) $rec->hours_worked);
            });

            // Daily rate = monthly salary / 26 working days
            $dailyRate     = $emp->salary > 0 ? round($emp->salary / 26, 2) : 0;
            $hourlyRate    = $dailyRate > 0 ? round($dailyRate / 8, 2) : 0;

            // Gross pay based on hours actually worked
            $grossPay      = round($totalHours * $hourlyRate, 2);

            // Late deduction: 30 min per late record
            $lateDeduction = round($daysLate * ($hourlyRate * 0.5), 2);

            $netPay        = max(0, $grossPay - $lateDeduction);

            return [
                'employee'      => $emp,
                'days_present'  => $daysPresent,
                'days_absent'   => $daysAbsent,
                'days_late'     => $daysLate,
                'total_hours'   => number_format($totalHours, 2),
                'daily_rate'    => number_format($dailyRate, 2),
                'hourly_rate'   => number_format($hourlyRate, 2),
                'gross_pay'     => number_format($grossPay, 2),
                'late_deduction'=> number_format($lateDeduction, 2),
                'net_pay'       => number_format($netPay, 2),
            ];
        });

        return view('admin.payroll.index', compact('payroll', 'from', 'to'));
    }

    /**
     * Show all attendance records for one employee in a date range.
     */
    public function employeeDetail(Request $request, Employee $employee)
    {
        $from    = $request->input('from', Carbon::now()->startOfMonth()->toDateString());
        $to      = $request->input('to',   Carbon::now()->endOfMonth()->toDateString());

        $records = Attendance::where('employee_id', $employee->id)
                             ->whereBetween('date', [$from, $to])
                             ->orderBy('date')
                             ->get();

        $totalHours  = $records->sum(function ($rec) {
            if ($rec->time_in && $rec->time_out) {
                $in  = Carbon::today()->setTimeFromTimeString($rec->time_in);
                $out = Carbon::today()->setTimeFromTimeString($rec->time_out);
                return abs(round($in->diffInMinutes($out) / 60, 2));
            }
            return max(0, (float) $rec->hours_worked);
        });
        $dailyRate   = $employee->salary > 0 ? round($employee->salary / 26, 2) : 0;
        $hourlyRate  = $dailyRate > 0 ? round($dailyRate / 8, 2) : 0;

        return view('admin.payroll.detail', compact('employee', 'records', 'from', 'to', 'totalHours', 'dailyRate', 'hourlyRate'));
    }
}
