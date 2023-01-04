<?php

use App\Http\Controllers\OrderController;
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

Route::get('/', fn () => view('dashboard', [ 'title' => 'Dashboard' ]));
Route::get('login', fn () => view('login', ['title' => 'Login']));
Route::get('404', fn () => view('404'));
Route::get('blank', fn () => view('blank', ['title' => 'Blank']));

Route::get('orders/overall', [OrderController::class, 'index'])->name('orders.index');
Route::get('orders/pending', [OrderController::class, 'pending'])->name('order.pending');

Route::get('buckets', fn () => view('buckets.index', ['title' => 'List Buckets']));
