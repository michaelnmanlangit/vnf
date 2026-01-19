<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\WarehouseDashboardController;
use App\Http\Controllers\DeliveryDashboardController;

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
});

// Warehouse staff routes
Route::middleware(['auth', 'role:warehouse_staff'])->group(function () {
    Route::get('/warehouse/dashboard', [WarehouseDashboardController::class, 'index'])->name('warehouse.dashboard');
});

// Delivery personnel routes
Route::middleware(['auth', 'role:delivery_personnel'])->group(function () {
    Route::get('/delivery/dashboard', [DeliveryDashboardController::class, 'index'])->name('delivery.dashboard');
});
