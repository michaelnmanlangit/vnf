<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StorageUnit;
use App\Models\Inventory;
use App\Models\Employee;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\TemperatureLog;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function index()
    {
        // Stat Counts
        $totalStorageUnits = StorageUnit::count();
        $totalInventoryItems = Inventory::count();
        $totalEmployees = Employee::count();
        $outstandingInvoices = Invoice::whereIn('status', ['pending', 'partial'])->count();
        $lowStockItems = Inventory::where('status', 'low_stock')->count();
        $temperatureAlerts = TemperatureLog::whereIn('status', ['warning', 'critical'])
            ->where('recorded_at', '>=', Carbon::now()->subDays(7))
            ->count();

        // Recent Temperature Alerts (last 5)
        $recentTempAlerts = TemperatureLog::whereIn('status', ['warning', 'critical'])
            ->with('storageUnit')
            ->orderBy('recorded_at', 'desc')
            ->limit(5)
            ->get();

        // Low Stock & Expiring Items (5 each)
        $lowStockList = Inventory::whereIn('status', ['low_stock', 'expiring_soon', 'expired'])
            ->orderByRaw("FIELD(status, 'expired', 'expiring_soon', 'low_stock')")
            ->limit(5)
            ->get();

        // Recent Invoices (last 5)
        $recentInvoices = Invoice::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent Payments (last 5)
        $recentPayments = Payment::with('invoice.customer')
            ->orderBy('payment_date', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalStorageUnits',
            'totalInventoryItems',
            'totalEmployees',
            'outstandingInvoices',
            'lowStockItems',
            'temperatureAlerts',
            'recentTempAlerts',
            'lowStockList',
            'recentInvoices',
            'recentPayments'
        ));
    }
}
