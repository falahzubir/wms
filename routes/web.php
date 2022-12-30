<?php

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

Route::get('/', function () {
    return view('welcome', [
        'title' => 'Dashboard'
    ]);
});
Route::get('login', fn () => view('login', ['title' => 'Login']));
Route::get('404', fn () => view('404'));
Route::get('blank', fn () => view('blank', ['title' => 'Blank']));

Route::get('orders', fn () => view('orders.index', ['title' => 'List Orders']));
Route::get('buckets', fn () => view('buckets.index', ['title' => 'List Buckets']));
