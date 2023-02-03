<?php

use App\Http\Controllers\BucketBatchController;
use App\Http\Controllers\BucketController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShippingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', fn() => redirect()->route('dashboard'));
Route::get('login', fn () => view('login', ['title' => 'Login']));
Route::get('404', fn () => view('404'));
// Route::get('blank', fn () => view('blank', ['title' => 'Blank']));

Route::middleware(['auth'])->group(function() {
    Route::get('dashboard', fn () => view('dashboard', [ 'title' => 'Dashboard' ]))->name('dashboard');

    Route::group(['prefix' => 'orders'], function () {
        Route::get('overall', [OrderController::class, 'overall'])->name('orders.overall');
        Route::get('pending', [OrderController::class, 'pending'])->name('orders.pending');
        Route::get('processing', [OrderController::class, 'processing'])->name('orders.processing');
        Route::get('packing', [OrderController::class, 'packing'])->name('orders.packing');
        Route::get('ready-to-ship', [OrderController::class, 'ready_to_ship'])->name('orders.readyToShip');
        Route::get('shipping', [OrderController::class, 'shipping'])->name('orders.shipping');
        Route::get('delivered', [OrderController::class, 'delivered'])->name('orders.delivered');
        Route::get('returned', [OrderController::class, 'returned'])->name('orders.returned');
        Route::get('scan', [OrderController::class, 'scan'])->name('orders.scan');
        Route::post('scan', [OrderController::class, 'scan_barcode'])->name('orders.scanned');
    });

    // group routes for buckets
    Route::group(['prefix' => 'buckets'], function () {
        Route::get('/', [BucketController::class, 'index'])->name('buckets.index');
        // Route::get('create', [BucketController::class, 'create'])->name('buckets.create');
        Route::post('store', [BucketController::class, 'store'])->name('buckets.store');
        Route::get('edit/{id}', [BucketController::class, 'edit'])->name('buckets.edit');
        Route::post('update/{id}', [BucketController::class, 'update'])->name('buckets.update');
        Route::get('delete/{id}', [BucketController::class, 'destroy'])->name('buckets.destroy');
        Route::post('download_cn', [ShippingController::class, 'download_cn_bucket'])->name('buckets.download_cn');
    });

    // group routes for bucket batches
    Route::group(['prefix' => 'bucket-batches'], function () {
        Route::post('generate_pl', [BucketBatchController::class, 'generate_pl'])->name('buckets.generate_pl');
        Route::get('download_pl/{batch}', [BucketBatchController::class, 'download_pl'])->name('bucket-batches.download_pl');
    });

    Route::group(['prefix' => 'shipping'], function () {
        // Route::get('request-cn', [ShippingController::class, 'request_cn'])->name('shipping.request_cn');
        // Route::get('check-cn-company', [ShippingController::class, 'check_cn_company'])->name('shipping.check_cn_company');
        // Route::get('download-consignment-note', [ShippingController::class, 'download_cn'])->name('shipping.download_cn');
        Route::post('update-tracking', [ShippingController::class, 'update_tracking'])->name('shipping.update_tracking');
        Route::post('upload-bulk-tracking', [ShippingController::class, 'upload_bulk_tracking'])->name('shipping.upload_bulk_tracking');
    });
});

Route::get('dhl-access-token', [ShippingController::class, 'dhl_generate_access_token']);

Auth::routes();

Route::middleware(['auth', 'role:user'])->group(function() {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});

Route::middleware(['auth', 'role:admin'])->group(function() {
    // Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
});
