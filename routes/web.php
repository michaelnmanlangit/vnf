<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DeliveryDashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\DeliveryController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\TaskAssignmentController;
use App\Http\Controllers\StorageAssignmentController;
use App\Http\Controllers\SupervisorController;
use App\Http\Controllers\TemperatureMonitoringController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicAttendanceController;
use App\Http\Controllers\Admin\PayrollController;

// Public routes
Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'admin' => redirect('/admin/dashboard'),
            'storage_supervisor' => redirect('/supervisor/dashboard'),
            'inventory_staff' => redirect('/inventory'),
            'temperature_staff' => redirect('/warehouse/temperature'),
            'payment_staff' => redirect('/admin/billing'),
            'delivery_personnel' => redirect('/delivery/dashboard'),
            'customer' => redirect('/customer/shop'),
            default => redirect('/login'),
        };
    }
    $products = \App\Models\Inventory::whereIn('status', ['in_stock','low_stock','expiring_soon'])
        ->where('quantity', '>', 0)
        ->orderBy('product_name')
        ->get();
    return view('landing', compact('products'));
})->name('landing');

// Public attendance portal (no auth required)
Route::get('/attendance', [PublicAttendanceController::class, 'index'])->name('attendance');
Route::post('/attendance/clock', [PublicAttendanceController::class, 'clock'])->name('attendance.clock');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Profile routes (authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// Admin-only routes
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    // Payroll
    Route::get('/admin/payroll', [PayrollController::class, 'index'])->name('admin.payroll.index');
    Route::get('/admin/payroll/{employee}', [PayrollController::class, 'employeeDetail'])->name('admin.payroll.detail');
    Route::resource('/admin/employees', EmployeeController::class)->names([
        'index' => 'admin.employees.index',
        'create' => 'admin.employees.create',
        'store' => 'admin.employees.store',
        'show' => 'admin.employees.show',
        'edit' => 'admin.employees.edit',
        'update' => 'admin.employees.update',
        'destroy' => 'admin.employees.destroy',
    ]);

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
    
    // Reports routes
    Route::get('/admin/reports', fn() => redirect()->route('admin.reports.inventory'));
    Route::get('/admin/reports/inventory',   [ReportsController::class, 'inventory'])->name('admin.reports.inventory');
    Route::get('/admin/reports/temperature', [ReportsController::class, 'temperature'])->name('admin.reports.temperature');
    Route::get('/admin/reports/financial',   [ReportsController::class, 'financial'])->name('admin.reports.financial');

    // Delivery & GPS Tracking routes
    Route::prefix('admin/deliveries')->name('admin.deliveries.')->group(function () {
        Route::get('/',           [DeliveryController::class, 'index'])->name('index');
        Route::get('/{delivery}', [DeliveryController::class, 'show'])->name('show');
        Route::get('/{delivery}/location', [DeliveryController::class, 'location'])->name('location');
        Route::get('/{delivery}/refresh',  [DeliveryController::class, 'refresh'])->name('refresh');
        Route::patch('/{delivery}/cancel',   [DeliveryController::class, 'cancel'])->name('cancel');
        Route::patch('/{delivery}/reassign', [DeliveryController::class, 'reassign'])->name('reassign');
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

// Inventory routes - accessible by admin only (staff uses /inventory below)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('/admin/inventory', InventoryController::class)->names([
        'index' => 'admin.inventory.index',
        'create' => 'admin.inventory.create',
        'store' => 'admin.inventory.store',
        'show' => 'admin.inventory.show',
        'edit' => 'admin.inventory.edit',
        'update' => 'admin.inventory.update',
        'destroy' => 'admin.inventory.destroy',
    ]);
});

// Inventory routes - clean URL for inventory_staff (no /admin/ prefix)
Route::middleware(['auth', 'role:inventory_staff'])->group(function () {
    Route::resource('/inventory', InventoryController::class)->names([
        'index' => 'inventory.index',
        'create' => 'inventory.create',
        'store' => 'inventory.store',
        'show' => 'inventory.show',
        'edit' => 'inventory.edit',
        'update' => 'inventory.update',
        'destroy' => 'inventory.destroy',
    ]);
});

// Billing routes - accessible by admin and payment_staff
Route::middleware(['auth', 'role:admin,payment_staff'])->group(function () {
    Route::prefix('admin/billing')->name('admin.billing.')->group(function () {
        Route::get('/', [BillingController::class, 'index'])->name('index');
        Route::get('/create', [BillingController::class, 'create'])->name('create');
        Route::post('/', [BillingController::class, 'store'])->name('store');
        Route::get('/{id}', [BillingController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [BillingController::class, 'edit'])->name('edit');
        Route::put('/{id}', [BillingController::class, 'update'])->name('update');
        Route::delete('/{id}', [BillingController::class, 'destroy'])->name('destroy');
        Route::get('/customers/list', [BillingController::class, 'customers'])->name('customers');
        Route::put('/customer/{id}', [BillingController::class, 'updateCustomer'])->name('customer.update');
        Route::delete('/customer/{id}', [BillingController::class, 'deleteCustomer'])->name('customer.delete');
        Route::post('/{id}/payment', [BillingController::class, 'storePayment'])->name('payment.store');
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

// Warehouse staff routes - temperature_staff only
Route::middleware(['auth', 'role:temperature_staff'])->group(function () {
    Route::prefix('warehouse/temperature')->name('warehouse.temperature.')->group(function () {
        Route::get('/', [TemperatureMonitoringController::class, 'index'])->name('index');
        Route::get('/{id}', [TemperatureMonitoringController::class, 'show'])->name('show');
        Route::get('/unit/{id}', [TemperatureMonitoringController::class, 'getUnitData'])->name('unit.data');
        Route::post('/record', [TemperatureMonitoringController::class, 'recordTemperature'])->name('record');
        Route::get('/history/{id}', [TemperatureMonitoringController::class, 'getHistory'])->name('history');
        Route::get('/chart-data', [TemperatureMonitoringController::class, 'getChartData'])->name('chartData');
        Route::post('/simulate', [TemperatureMonitoringController::class, 'simulateReadings'])->name('simulate');
    });
});

// Delivery personnel routes
Route::middleware(['auth', 'role:delivery_personnel'])->group(function () {
    Route::get('/delivery/dashboard',              [DeliveryDashboardController::class, 'index'])->name('delivery.dashboard');
    Route::post('/delivery/{delivery}/start',      [DeliveryDashboardController::class, 'start'])->name('delivery.start');
    Route::post('/delivery/{delivery}/complete',   [DeliveryDashboardController::class, 'complete'])->name('delivery.complete');
    Route::post('/delivery/{delivery}/collect-cod',[DeliveryDashboardController::class, 'collectCod'])->name('delivery.collect_cod');
    Route::post('/delivery/location',              [DeliveryDashboardController::class, 'updateLocation'])->name('delivery.location');
});

// Customer routes
use App\Http\Controllers\Customer\CustomerAuthController;
use App\Http\Controllers\Customer\CustomerProfileController;
use App\Http\Controllers\Customer\CustomerShopController;
use App\Http\Controllers\Customer\OTPController;

// Customer registration (POST only - form is in main login page)
Route::post('/customer/register', [CustomerAuthController::class, 'register'])->name('customer.register.store')->middleware('guest');

// OTP Verification routes (accessible to guests with session)
Route::middleware('guest')->prefix('customer/otp')->name('customer.otp.')->group(function () {
    Route::get('/verify', [OTPController::class, 'showVerificationForm'])->name('verify.form');
    Route::post('/verify', [OTPController::class, 'verify'])->name('verify');
    Route::post('/resend', [OTPController::class, 'resend'])->name('resend');
    Route::get('/status', [OTPController::class, 'status'])->name('status');
});

// Authenticated customer routes
Route::middleware(['auth', 'role:customer', 'profile.complete'])->prefix('customer')->name('customer.')->group(function () {
    // Dashboard redirects to shop
    Route::get('/dashboard', function() {
        return redirect()->route('customer.shop');
    })->name('dashboard');
    
    Route::get('/profile', [CustomerProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/complete', [CustomerProfileController::class, 'complete'])->name('profile.complete');
    Route::post('/profile/store', [CustomerProfileController::class, 'store'])->name('profile.store');
    Route::get('/profile/edit', [CustomerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [CustomerProfileController::class, 'update'])->name('profile.update');
    
    // Shopping
    Route::get('/shop', [CustomerShopController::class, 'shop'])->name('shop');
    Route::get('/product/{id}', [CustomerShopController::class, 'product'])->name('product');
    Route::post('/cart/add', [CustomerShopController::class, 'addToCart'])->name('cart.add');
    Route::get('/cart', [CustomerShopController::class, 'cart'])->name('cart');
    Route::put('/cart/update', [CustomerShopController::class, 'updateCart'])->name('cart.update');
    Route::delete('/cart/remove/{id}', [CustomerShopController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/checkout', [CustomerShopController::class, 'checkout'])->name('checkout');
    Route::post('/order', [CustomerShopController::class, 'placeOrder'])->name('order.place');
    Route::get('/orders', [CustomerShopController::class, 'orders'])->name('orders');
    Route::get('/order/{id}', [CustomerShopController::class, 'orderDetail'])->name('order.detail');
    Route::get('/delivery/{id}/location', [CustomerShopController::class, 'deliveryLocation'])->name('delivery.location');
    Route::post('/fcm-token', [CustomerShopController::class, 'saveFcmToken'])->name('fcm.token.save');
});
