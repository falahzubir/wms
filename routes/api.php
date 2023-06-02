<?php

use App\Http\Controllers\api\OrderApiController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\BucketController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\api\ShippingApiController;
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

Route::get('buckets', [BucketController::class, 'list']);
Route::post('add-to-bucket', [BucketController::class, 'add_order']);

Route::get('dhl-store', [ShippingController::class, 'dhl_store']);
Route::post('request-cn', [ShippingController::class, 'request_cn']);
Route::post('reprint-cn', [ShippingController::class, 'reprint_cn']);
Route::post('check-cn-company', [ShippingController::class, 'check_cn_company']);
Route::post('download-consignment-note', [ShippingController::class, 'download_cn']);

Route::prefix('dashboard')->group(function () {
    Route::get('current-process', [DashboardController::class, 'current_process']);
    Route::post('statistics', [DashboardController::class, 'statistics']);
});
Route::prefix('buckets')->group(function () {
    Route::get('show/{id}', [BucketController::class, 'show']);
    Route::post('check-empty-batch', [BucketController::class, 'check_empty_batch']);
    Route::post('delete', [BucketController::class, 'delete']);
});

Route::prefix('orders')->group(function () {
    Route::patch('update-tracking', [ShippingController::class, 'update_tracking']);
    Route::post('reject', [OrderApiController::class, 'reject']);
    Route::post('scan-parcel', [OrderApiController::class, 'barcode_scan']);
    Route::post('split-parcels', [OrderApiController::class, 'get_order_split_parcels']);
    Route::post('approve-for-shipping', [OrderApiController::class, 'approve_for_shipping']);
    Route::post('set-order-completed', [OrderApiController::class, 'set_order_completed']);
});

Route::prefix('shippings')->group(function () {
    Route::post('check-multiple-parcels', [ShippingController::class, 'check_multiple_parcels']);
    Route::post('update-tracking', [ShippingController::class, 'update_bulk_tracking']);
    Route::post('first-milestone', [ShippingController::class, 'first_milestone']);
    Route::post('delivered-milestone', [ShippingController::class, 'delivered_milestone']);
    Route::post('return-ongoing-milestone', [ShippingController::class, 'return_ongoing_milestone']);
    Route::post('return-delivered-milestone', [ShippingController::class, 'return_delivered_milestone']);
    Route::post('update_shopee_tracking', [ShippingApiController::class, 'update_shopee_tracking']);
});

Route::post('bucket-batches/generate_cn', [BucketController::class, 'check_empty_bucket']);

Route::post('download-order-csv', [OrderController::class, 'download_order_csv']);

Route::get('get-couriers', [CourierController::class, 'list']);


Route::webhooks('webhook/sales');
