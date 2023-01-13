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

Route::get('orders/overall', [OrderController::class, 'index'])->name('orders.index');
Route::get('orders/pending', [OrderController::class, 'pending'])->name('order.pending');

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
