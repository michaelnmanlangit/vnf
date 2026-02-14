<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\WarehouseDashboardController;
use App\Http\Controllers\DeliveryDashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Admin\BillingController;

// Public routes
Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'admin' => redirect('/admin/dashboard'),
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
});

// Warehouse staff routes - All roles
Route::middleware(['auth', 'role:inventory_staff,temperature_staff,payment_staff'])->group(function () {
    Route::get('/warehouse/dashboard', [WarehouseDashboardController::class, 'index'])->name('warehouse.dashboard');
    
    // Inventory Staff routes
    Route::get('/warehouse/inventory', [InventoryController::class, 'warehouseIndex'])->name('warehouse.inventory.index');
    Route::get('/warehouse/inventory/{inventory}', [InventoryController::class, 'warehouseShow'])->name('warehouse.inventory.show');
    
    // Temperature Staff routes
    Route::get('/warehouse/temperature', [InventoryController::class, 'warehouseTemperatureIndex'])->name('warehouse.temperature.index');
    Route::get('/warehouse/temperature/{inventory}', [InventoryController::class, 'warehouseTemperatureShow'])->name('warehouse.temperature.show');
    
    // Payment Staff routes
    Route::get('/warehouse/payment', [InventoryController::class, 'warehousePaymentIndex'])->name('warehouse.payment.index');
    Route::get('/warehouse/payment/{inventory}', [InventoryController::class, 'warehousePaymentShow'])->name('warehouse.payment.show');
});

// Delivery personnel routes
Route::middleware(['auth', 'role:delivery_personnel'])->group(function () {
    Route::get('/delivery/dashboard', [DeliveryDashboardController::class, 'index'])->name('delivery.dashboard');
});
