<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\WarehouseDashboardController;
use App\Http\Controllers\DeliveryDashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InventoryController;

// Public routes
Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
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
});

// Warehouse staff routes
Route::middleware(['auth', 'role:warehouse_staff'])->group(function () {
    Route::get('/warehouse/dashboard', [WarehouseDashboardController::class, 'index'])->name('warehouse.dashboard');
});

// Delivery personnel routes
Route::middleware(['auth', 'role:delivery_personnel'])->group(function () {
    Route::get('/delivery/dashboard', [DeliveryDashboardController::class, 'index'])->name('delivery.dashboard');
});
