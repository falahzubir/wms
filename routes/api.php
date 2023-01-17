<?php

use App\Http\Controllers\ShippingController;
use App\Http\Controllers\BucketController;
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

Route::post('request-cn', [ShippingController::class, 'request_cn']);
Route::post('check-cn-company', [ShippingController::class, 'check_cn_company']);
Route::post('download-consignment-note', [ShippingController::class, 'download_cn']);

Route::get('dhl-store', [ShippingController::class, 'dhl_store']);

Route::webhooks('webhook/sales');
