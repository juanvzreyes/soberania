<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Security\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConsumerProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CooperativeProfileController;
use App\Http\Controllers\ProducerProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProducerLocationController;
use App\Http\Controllers\InventoryEntryController;
use App\Http\Controllers\InventoryExitController;
use Inertia\Inertia;
use App\Http\Controllers\PublicProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderManagementController;
use App\Http\Controllers\DeliveryManagementController;
use App\Http\Controllers\PaymentManagementController;
use App\Http\Controllers\PurchaseHistoryController;
use App\Http\Controllers\Web\ProducerReportController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::resource('producers/map', ProducerLocationController::class)->only('index')->names('producer.map');
Route::get('producers/report', [ProducerReportController::class, 'generateReport'])->name('producer.report');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('users', UserController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('inventoryEntry', InventoryEntryController::class);
    Route::resource('inventoryExit', InventoryExitController::class);
    Route::get('catalog', [PublicProductController::class, 'index'])->name('catalog.index');

    Route::get('photo/serve/{photo}', [FileController::class, 'servePhoto'])->name('photo.serve')->middleware('signed');

    // profiles
    Route::singleton('producers/profile', ProducerProfileController::class)->only(['show', 'update'])->names('profile.producer');
    Route::singleton('cooperatives/profile', CooperativeProfileController::class)->only(['show', 'update'])->names('profile.cooperative');
    Route::singleton('consumers/profile', ConsumerProfileController::class)->only(['show', 'update'])->names('profile.consumer');

    Route::prefix('cart')->name('cart.')->group(function () {
        Route::post('/add/{product}', [CartController::class, 'store'])->name('store');
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::patch('/{product}', [CartController::class, 'update'])->name('update');
        Route::delete('/clear', [CartController::class, 'clear'])->name('clear'); // ← primero
        Route::delete('/{product}', [CartController::class, 'destroy'])->name('destroy'); // ← después
    });

    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/', [CheckoutController::class, 'store'])->name('store');
    });

    Route::get('orders/{order}/confirmation', [CheckoutController::class, 'confirmation'])
        ->name('orders.confirmation');

    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read-all');

        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('unread-count');
    });
    Route::get('/orders', [OrderManagementController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderManagementController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [OrderManagementController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/cancel', [OrderManagementController::class, 'cancel'])->name('orders.cancel');

    Route::get('/deliveries', [DeliveryManagementController::class, 'index'])->name('deliveries.index');
    Route::get('/deliveries/{delivery}', [DeliveryManagementController::class, 'show'])->name('deliveries.show');
    Route::patch('/deliveries/{delivery}/status', [DeliveryManagementController::class, 'updateStatus'])->name('deliveries.update-status');
    Route::post('/deliveries/{delivery}/assign', [DeliveryManagementController::class, 'assignTransporter'])->name('deliveries.assign');
    Route::post('/deliveries/{delivery}/cancel', [DeliveryManagementController::class, 'cancel'])->name('deliveries.cancel');

    Route::get('/payments', [PaymentManagementController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [PaymentManagementController::class, 'show'])->name('payments.show');
    Route::post('/payments/{payment}/confirm', [PaymentManagementController::class, 'confirm'])->name('payments.confirm');
    Route::patch('/payments/{payment}/status', [PaymentManagementController::class, 'updateStatus'])->name('payments.update-status');
    Route::post('/payments/{payment}/revert', [PaymentManagementController::class, 'revert'])->name('payments.revert');
    Route::post('/payments/{payment}/upload-proof', [PaymentManagementController::class, 'uploadProof'])->name('payments.upload-proof');

    Route::prefix('purchase-history')->name('purchase-history.')->group(function () {
        Route::get('/', [PurchaseHistoryController::class, 'index'])->name('index');
        Route::get('/{order}', [PurchaseHistoryController::class, 'show'])->name('show');
        Route::post('/{order}/reorder', [PurchaseHistoryController::class, 'reorder'])->name('reorder');
    });

    Route::prefix('dashboard/export')->name('dashboard.export.')->group(function () {
        Route::get('excel', [DashboardController::class, 'exportExcel'])->name('excel');
        Route::get('pdf', [DashboardController::class, 'exportPdf'])->name('pdf');
    });
});

Route::get('orders/{order}/confirmation', [CheckoutController::class, 'confirmation'])
    ->name('orders.confirmation')
    ->middleware('auth');

require __DIR__ . '/auth.php';
