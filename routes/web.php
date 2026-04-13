<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StatusController;
use App\Http\Controllers\UserController;

// Redirect ke login jika belum authenticated
Route::get('/', function () {
    if (auth()->check()) {
        // Redirect berdasarkan role user
        return redirect()->route('orderstatus.index');
    }
    return redirect()->route('login');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('authenticate');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::post('/test-order', [OrderController::class, 'store']);

Route::middleware(['auth'])->group(function () {

    /*
    =========================
    ORDER STATUS - SEMUA ROLE
    =========================
    */
    Route::get('/orderstatus', [StatusController::class, 'index'])->name('orderstatus.index');
    Route::get('/api/orders/{id}', [StatusController::class, 'getOrder'])->name('api.orders.get');
    Route::post('/orderstatus/{id}/void', [StatusController::class, 'voidOrder'])->name('orderstatus.void');
    Route::delete('/orderstatus/{id}', [StatusController::class, 'destroy'])->name('orderstatus.destroy');
    Route::get('/orders/{order}/receipt', [StatusController::class, 'receipt'])->name('orders.receipt');

    /*
    =========================
    PENERIMA KONSUMEN
    =========================
    */
    Route::middleware(['role:penerima'])->prefix('penerima')->name('penerima.')->group(function () {
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    });

    /*
    =========================
    KASIR
    =========================
    */
    Route::middleware(['role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/orders', [OrderController::class, 'unpaid'])->name('orders.index');
        Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');
        Route::post('/pay', [PaymentController::class, 'pay'])->name('pay');
        
        // DP Payment Routes
        Route::post('/payment/dp', [PaymentController::class, 'recordDP'])->name('payment.dp');
        Route::post('/payment/pelunasan', [PaymentController::class, 'recordPelunasan'])->name('payment.pelunasan');
        Route::get('/payment/{orderId}/detail', [PaymentController::class, 'getPaymentDetail'])->name('payment.detail');
        
        // Financial Reports (Moved to admin/reports with ARUS KAS tab)
        // Route::get('/reports/daily', [PaymentController::class, 'dailyReport'])->name('reports.daily');
        Route::get('/reports/payment-method', [PaymentController::class, 'paymentMethodReport'])->name('reports.method');
        Route::get('/reports/outstanding-dp', [PaymentController::class, 'outstandingDPReport'])->name('reports.outstanding');
        Route::get('/reports/dp-vs-pelunasan', [PaymentController::class, 'dpvsPelunasanReport'])->name('reports.dpvspelunasan');
    });

    /*
    =========================
    OPERATOR CETAK
    =========================
    */
    Route::middleware(['role:operator_cetak'])->prefix('operator')->name('operator.')->group(function () {
        Route::get('/orders', [OrderController::class, 'readyToPrint'])->name('orders.index');
        Route::post('/print/{id}', [StatusController::class, 'markPrinted'])->name('orders.print');
    });

    /*
    =========================
    PICKUP (BISA KASIR / OPERATOR)
    =========================
    */
    Route::post('/pickup/{id}', [StatusController::class, 'markTaken'])->name('pickup');
    Route::post('/orders/{id}/update-pickup-status', [StatusController::class, 'updatePickupStatus'])->name('orders.update-pickup-status');

    /*
    =========================
    ADMIN - AKSES SEMUA ROLE + MENU ADMIN
    =========================
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        // Penerima access
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        
        // Kasir access
        Route::get('/orders', [OrderController::class, 'unpaid'])->name('orders.index');
        Route::post('/pay', [PaymentController::class, 'pay'])->name('pay');
        
        // DP Payment Routes
        Route::post('/payment/dp', [PaymentController::class, 'recordDP'])->name('payment.dp');
        Route::post('/payment/pelunasan', [PaymentController::class, 'recordPelunasan'])->name('payment.pelunasan');
        Route::get('/payment/{orderId}/detail', [PaymentController::class, 'getPaymentDetail'])->name('payment.detail');
        
        // Financial Reports
        Route::get('/reports/daily', [PaymentController::class, 'dailyReport'])->name('reports.daily');
        Route::get('/reports/payment-method', [PaymentController::class, 'paymentMethodReport'])->name('reports.method');
        Route::get('/reports/outstanding-dp', [PaymentController::class, 'outstandingDPReport'])->name('reports.outstanding');
        Route::get('/reports/dp-vs-pelunasan', [PaymentController::class, 'dpvsPelunasanReport'])->name('reports.dpvspelunasan');
        
        // Operator access
        Route::post('/print/{id}', [StatusController::class, 'markPrinted'])->name('orders.print');
        
        // Admin specific
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/income', [ReportController::class, 'income'])->name('reports.income');
        Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');
        
        // User Management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

});