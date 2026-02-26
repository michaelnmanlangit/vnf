<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\TemperatureLog;
use Illuminate\Http\Request;

class ReportsController extends Controller
{
    /* ── Inventory Report ───────────────────────────────────────── */
    public function inventory(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->get('date_to',   now()->toDateString());

        $totalItems    = Inventory::count();
        $inStock       = Inventory::where('status', 'in_stock')->count();
        $lowStock      = Inventory::where('status', 'low_stock')->count();
        $expiredCount  = Inventory::where('status', 'expired')->count();
        $expiringSoon  = Inventory::where('status', 'expiring_soon')->count();

        $categoryBreakdown = Inventory::selectRaw('category, COUNT(*) as count, SUM(quantity) as total_qty')
            ->groupBy('category')
            ->orderByDesc('total_qty')
            ->get();

        $expiringItems = Inventory::where('expiration_date', '<=', now()->addDays(30))
            ->where('expiration_date', '>=', now())
            ->orderBy('expiration_date')
            ->get();

        $expiredItems = Inventory::where('expiration_date', '<', now())
            ->orderBy('expiration_date')
            ->get();

        return view('admin.reports.inventory', compact(
            'dateFrom', 'dateTo',
            'totalItems', 'inStock', 'lowStock', 'expiredCount', 'expiringSoon',
            'categoryBreakdown', 'expiringItems', 'expiredItems'
        ));
    }

    /* ── Temperature Report ─────────────────────────────────────── */
    public function temperature(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->get('date_to',   now()->toDateString());

        $tempLogs = TemperatureLog::with('storageUnit')
            ->whereBetween('recorded_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->orderBy('recorded_at', 'desc')
            ->get();

        $normalCount   = $tempLogs->where('status', 'normal')->count();
        $warningCount  = $tempLogs->where('status', 'warning')->count();
        $criticalCount = $tempLogs->where('status', 'critical')->count();

        $unitSummary = TemperatureLog::selectRaw(
                'storage_unit_id,
                 COUNT(*) as log_count,
                 ROUND(AVG(temperature), 2) as avg_temp,
                 MIN(temperature) as min_temp,
                 MAX(temperature) as max_temp,
                 SUM(CASE WHEN status = "critical" THEN 1 ELSE 0 END) as critical_count,
                 SUM(CASE WHEN status = "warning"  THEN 1 ELSE 0 END) as warning_count'
            )
            ->whereBetween('recorded_at', [$dateFrom . ' 00:00:00', $dateTo . ' 23:59:59'])
            ->groupBy('storage_unit_id')
            ->with('storageUnit')
            ->get();

        return view('admin.reports.temperature', compact(
            'dateFrom', 'dateTo',
            'tempLogs', 'normalCount', 'warningCount', 'criticalCount',
            'unitSummary'
        ));
    }

    /* ── Financial Report ───────────────────────────────────────── */
    public function financial(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->startOfMonth()->toDateString());
        $dateTo   = $request->get('date_to',   now()->toDateString());

        $invoices = Invoice::with('customer', 'payments')
            ->whereBetween('invoice_date', [$dateFrom, $dateTo])
            ->orderBy('invoice_date', 'desc')
            ->get();

        $payments = Payment::whereBetween('payment_date', [$dateFrom, $dateTo])->get();

        $totalBilled      = $invoices->sum('total_amount');
        $totalCollected   = $payments->sum('amount');
        $totalOutstanding = Invoice::whereIn('status', ['pending', 'overdue', 'partial'])->sum('total_amount');

        $paidCount    = $invoices->where('status', 'paid')->count();
        $pendingCount = $invoices->where('status', 'pending')->count();
        $overdueCount = $invoices->where('status', 'overdue')->count();
        $partialCount = $invoices->where('status', 'partial')->count();

        $topCustomers = Invoice::selectRaw(
                'customer_id, SUM(total_amount) as total_billed, COUNT(*) as invoice_count'
            )
            ->with('customer')
            ->whereBetween('invoice_date', [$dateFrom, $dateTo])
            ->groupBy('customer_id')
            ->orderByDesc('total_billed')
            ->limit(5)
            ->get();

        return view('admin.reports.financial', compact(
            'dateFrom', 'dateTo',
            'invoices', 'payments',
            'totalBilled', 'totalCollected', 'totalOutstanding',
            'paidCount', 'pendingCount', 'overdueCount', 'partialCount',
            'topCustomers'
        ));
    }
}
