<?php

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

Route::get('/dashboard', fn () => view('dashboard', [ 'title' => 'Dashboard' ]));
Route::get('login', fn () => view('login', ['title' => 'Login']));
Route::get('404', fn () => view('404'));
Route::get('blank', fn () => view('blank', ['title' => 'Blank']));

Route::group(['prefix' => 'orders'], function () {
    Route::get('overall', [OrderController::class, 'overall'])->name('orders.overall');
    Route::get('pending', [OrderController::class, 'pending'])->name('orders.pending');
    Route::get('packing', [OrderController::class, 'packing'])->name('orders.packing');
    Route::get('shipping', [OrderController::class, 'shipping'])->name('orders.shipping');
    Route::get('delivered', [OrderController::class, 'delivered'])->name('orders.delivered');
    Route::get('returned', [OrderController::class, 'returned'])->name('orders.returned');
    Route::get('scan', [OrderController::class, 'scan'])->name('orders.scan');

    // Route::get('show/{id}', [OrderController::class, 'show'])->name('orders.show');
    // Route::get('load', [OrderController::class, 'load'])->name('orders.load');
});

// group routes for buckets
Route::group(['prefix' => 'buckets'], function () {
    Route::get('/', [BucketController::class, 'index'])->name('buckets.index');
    // Route::get('create', [BucketController::class, 'create'])->name('buckets.create');
    Route::post('store', [BucketController::class, 'store'])->name('buckets.store');
    Route::get('edit/{id}', [BucketController::class, 'edit'])->name('buckets.edit');
    Route::post('update/{id}', [BucketController::class, 'update'])->name('buckets.update');
    Route::get('delete/{id}', [BucketController::class, 'destroy'])->name('buckets.destroy');
});

Route::get('dhl-access-token', [ShippingController::class, 'dhl_generate_access_token']);

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
