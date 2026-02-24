<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\WarehouseDashboardController;
use App\Http\Controllers\DeliveryDashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\TaskAssignmentController;
use App\Http\Controllers\StorageAssignmentController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\TemperatureMonitoringController;

// Public routes
Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'admin' => redirect('/admin/dashboard'),
            'storage_supervisor' => redirect('/supervisor/dashboard'),
            'inventory_staff', 'temperature_staff', 'payment_staff' => redirect('/warehouse/dashboard'),
            'delivery_personnel' => redirect('/delivery/dashboard'),
            default => redirect('/login'),
        };
    }
    return redirect('/login');
});

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Admin routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::resource('/admin/employees', EmployeeController::class)->names([
        'index' => 'admin.employees.index',
        'create' => 'admin.employees.create',
        'store' => 'admin.employees.store',
        'edit' => 'admin.employees.edit',
        'update' => 'admin.employees.update',
        'destroy' => 'admin.employees.destroy',
    ]);
    Route::resource('/admin/inventory', InventoryController::class)->names([
        'index' => 'admin.inventory.index',
        'create' => 'admin.inventory.create',
        'store' => 'admin.inventory.store',
        'edit' => 'admin.inventory.edit',
        'update' => 'admin.inventory.update',
        'destroy' => 'admin.inventory.destroy',
    ]);
    
    // Billing routes
    Route::prefix('admin/billing')->name('admin.billing.')->group(function () {
        // Invoices
        Route::get('/', [BillingController::class, 'index'])->name('index');
        Route::get('/create', [BillingController::class, 'create'])->name('create');
        Route::post('/', [BillingController::class, 'store'])->name('store');
        Route::get('/{id}', [BillingController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [BillingController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BillingController::class, 'update'])->name('update');
        Route::delete('/{id}', [BillingController::class, 'destroy'])->name('destroy');
        
        // Customers
        Route::get('/customers/list', [BillingController::class, 'customers'])->name('customers');
        Route::post('/customers', [BillingController::class, 'storeCustomer'])->name('customer.store');
        Route::put('/customer/{id}', [BillingController::class, 'updateCustomer'])->name('customer.update');
        Route::delete('/customer/{id}', [BillingController::class, 'deleteCustomer'])->name('customer.delete');
        
        // Payments
        Route::post('/{id}/payment', [BillingController::class, 'storePayment'])->name('payment.store');
    });
    
    // Task Assignment routes
    Route::resource('/admin/tasks', TaskAssignmentController::class)->names([
        'index' => 'admin.tasks.index',
        'create' => 'admin.tasks.create',
        'store' => 'admin.tasks.store',
        'show' => 'admin.tasks.show',
        'edit' => 'admin.tasks.edit',
        'update' => 'admin.tasks.update',
        'destroy' => 'admin.tasks.destroy',
    ]);
    Route::patch('/admin/tasks/{task}/status', [TaskAssignmentController::class, 'updateStatus'])->name('admin.tasks.updateStatus');
    Route::get('/admin/tasks/employee/{employee}/info', [TaskAssignmentController::class, 'getEmployeeInfo'])->name('admin.tasks.employeeInfo');
    
    // Storage Unit & Worker Assignment Management
    Route::prefix('admin/storage')->name('admin.storage.')->group(function () {
        Route::get('/', [StorageAssignmentController::class, 'index'])->name('index');
        Route::get('/{id}', [StorageAssignmentController::class, 'show'])->name('show');
        Route::post('/{id}/assign-supervisor', [StorageAssignmentController::class, 'assignSupervisor'])->name('assignSupervisor');
        Route::post('/{id}/remove-supervisor', [StorageAssignmentController::class, 'removeSupervisor'])->name('removeSupervisor');
        Route::post('/worker/reassign', [StorageAssignmentController::class, 'reassignWorker'])->name('reassignWorker');
        Route::post('/auto-redistribute', [StorageAssignmentController::class, 'autoRedistribute'])->name('autoRedistribute');
        Route::get('/workers/all', [StorageAssignmentController::class, 'workers'])->name('workers');
    });
    
    // Temperature Monitoring routes
    Route::prefix('admin/temperature')->name('admin.temperature.')->group(function () {
        Route::get('/', [TemperatureMonitoringController::class, 'index'])->name('index');
        Route::get('/{id}', [TemperatureMonitoringController::class, 'show'])->name('show');
        Route::get('/unit/{id}', [TemperatureMonitoringController::class, 'getUnitData'])->name('unit.data');
        Route::post('/record', [TemperatureMonitoringController::class, 'recordTemperature'])->name('record');
        Route::get('/history/{id}', [TemperatureMonitoringController::class, 'getHistory'])->name('history');
        Route::get('/chart-data', [TemperatureMonitoringController::class, 'getChartData'])->name('chartData');
        Route::post('/simulate', [TemperatureMonitoringController::class, 'simulateReadings'])->name('simulate');
    });
});

// Storage Supervisor routes
Route::middleware(['auth', 'role:storage_supervisor'])->group(function () {
    Route::get('/supervisor/dashboard', [SupervisorController::class, 'index'])->name('supervisor.dashboard');
    Route::get('/supervisor/attendance', [SupervisorController::class, 'attendance'])->name('supervisor.attendance');
    Route::post('/supervisor/attendance/mark', [SupervisorController::class, 'markAttendance'])->name('supervisor.markAttendance');
    Route::post('/supervisor/attendance/bulk', [SupervisorController::class, 'bulkMarkAttendance'])->name('supervisor.bulkMarkAttendance');
    Route::get('/supervisor/attendance/history', [SupervisorController::class, 'attendanceHistory'])->name('supervisor.attendanceHistory');
});

// Warehouse staff routes - All roles
Route::middleware(['auth', 'role:inventory_staff,temperature_staff,payment_staff'])->group(function () {
    Route::get('/warehouse/dashboard', [WarehouseDashboardController::class, 'index'])->name('warehouse.dashboard');
    
    // Inventory Staff routes
    Route::get('/warehouse/inventory', [InventoryController::class, 'warehouseIndex'])->name('warehouse.inventory.index');
    Route::get('/warehouse/inventory/{inventory}', [InventoryController::class, 'warehouseShow'])->name('warehouse.inventory.show');
    
    // Temperature Staff routes - Temperature Monitoring
    Route::prefix('warehouse/temperature')->name('warehouse.temperature.')->group(function () {
        Route::get('/', [TemperatureMonitoringController::class, 'index'])->name('index');
        Route::get('/{id}', [TemperatureMonitoringController::class, 'show'])->name('show');
        Route::get('/unit/{id}', [TemperatureMonitoringController::class, 'getUnitData'])->name('unit.data');
        Route::post('/record', [TemperatureMonitoringController::class, 'recordTemperature'])->name('record');
        Route::get('/history/{id}', [TemperatureMonitoringController::class, 'getHistory'])->name('history');
        Route::get('/chart-data', [TemperatureMonitoringController::class, 'getChartData'])->name('chartData');
        Route::post('/simulate', [TemperatureMonitoringController::class, 'simulateReadings'])->name('simulate');
    });
    
    // Payment Staff routes
    Route::get('/warehouse/payment', [InventoryController::class, 'warehousePaymentIndex'])->name('warehouse.payment.index');
    Route::get('/warehouse/payment/{inventory}', [InventoryController::class, 'warehousePaymentShow'])->name('warehouse.payment.show');
});

// Delivery personnel routes
Route::middleware(['auth', 'role:delivery_personnel'])->group(function () {
    Route::get('/delivery/dashboard', [DeliveryDashboardController::class, 'index'])->name('delivery.dashboard');
});
