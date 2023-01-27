<?php

use App\Http\Controllers\ShippingController;
use App\Http\Controllers\BucketController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Row;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('bucket/{id}', [BucketController::class, 'show']);
Route::get('buckets', [BucketController::class, 'list']);
Route::post('add-to-bucket', [BucketController::class, 'add_order']);

Route::get('dhl-store', [ShippingController::class, 'dhl_store']);
Route::post('request-cn', [ShippingController::class, 'request_cn']);
Route::post('check-cn-company', [ShippingController::class, 'check_cn_company']);
Route::post('download-consignment-note', [ShippingController::class, 'download_cn']);
Route::patch('orders/update-tracking', [ShippingController::class, 'update_tracking']);
Route::post('shipping/first-milestone', [ShippingController::class, 'first_milestone']);

Route::post('download-order-csv', [OrderController::class, 'download_order_csv']);

Route::get('get-couriers', [CourierController::class, 'list']);


Route::webhooks('webhook/sales');
