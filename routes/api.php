<?php

use Maatwebsite\Excel\Row;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BucketController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CourierController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\api\WebhookController;
use App\Http\Controllers\api\OrderApiController;
use App\Http\Controllers\api\ShippingApiController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;
use App\Http\Controllers\ThirdParty\PosMalaysiaController;
use App\Http\Controllers\CourierServiceLevelAgreementController;
use App\Http\Controllers\FixcodeController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShippingCostController;

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
Route::post('check-cn-company', [ShippingController::class, 'check_cn_company']);
Route::post('download-consignment-note', [ShippingController::class, 'download_cn']);
Route::post('arrange-shipment', [ShippingController::class, 'arrange_shipment']);

Route::prefix('dashboard')->group(function () {
    Route::get('current-process', [DashboardController::class, 'current_process']);
    Route::post('statistics', [DashboardController::class, 'statistics']);
});
Route::prefix('buckets')->group(function () {
    Route::get('show/{id}', [BucketController::class, 'show']);
    Route::post('check-empty-batch', [BucketController::class, 'check_empty_batch']);
    Route::post('delete', [BucketController::class, 'delete']);
    Route::post('add-category', [BucketController::class, 'add_category']);
    Route::post('edit-category', [BucketController::class, 'edit_category']);
    Route::post('delete-category', [BucketController::class, 'delete_category']);
    Route::post('store', [BucketController::class, 'store']);
    Route::post('update', [BucketController::class, 'update']);
    Route::post('get-bucket-by-category', [BucketController::class, 'get_bucket_by_category']);
    Route::post('add-to-bucket', [BucketController::class, 'add_to_bucket']);
});

Route::prefix('orders')->group(function () {
    Route::patch('update-tracking', [ShippingController::class, 'update_tracking']);
    Route::post('reject', [OrderApiController::class, 'reject']);
    Route::post('scan-parcel', [OrderApiController::class, 'barcode_scan']);
    Route::post('split-parcels', [OrderApiController::class, 'get_order_split_parcels']);
    Route::post('approve-for-shipping', [OrderApiController::class, 'approve_for_shipping']);
    Route::post('set-order-completed', [OrderApiController::class, 'set_order_completed']);
    Route::match(array('GET','POST'),'getStatusWMS', [OrderApiController::class, 'getStatusWMS']);
    Route::match(array('GET','POST'),'getStatusWMSFilter', [OrderApiController::class, 'getStatusWMSFilter']);
    Route::match(array('GET','POST'),'parcels', [OrderApiController::class, 'scanParcelRanking']);
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

Route::prefix('claims')->group(function () {
    Route::post('create', [ClaimController::class, 'create']);
    Route::post('upload-credit-note', [ClaimController::class, 'upload_cn']);
    Route::delete('delete', [ClaimController::class, 'delete']);
});

Route::prefix('reports')->group(function() {
    // Route::get('sla', [ReportController::class, 'sla']);
    Route::get('outbound', [ReportController::class, 'get_outbound']);
    Route::get('order-matrix/extract', [ReportController::class, 'get_order_matrix_extract']);
    Route::get('order-matrix/pack', [ReportController::class, 'get_order_matrix_pack']);
    Route::get('order-matrix/pickup', [ReportController::class, 'get_order_matrix_pickup']);
    Route::get('order-matrix/comparison', [ReportController::class, 'get_order_matrix']);
    Route::get('pending-report', [ReportController::class, 'get_pending_report']);
    // Route::get('shipment', [ReportController::class, 'shipment']);
});

Route::prefix('couriers')->group(function(){
    Route::post('/listCourier', [CourierController::class, 'listCourier']);
    Route::post('/addCourier', [CourierController::class, 'addCourier']);
    Route::post('/deleteCourier', [CourierController::class, 'deleteCourier']);
    Route::post('/listCoverage', [CourierController::class, 'listCoverage']);
    Route::post('/addCoverage', [CourierController::class, 'addCoverage']);
    Route::post('/listSelectedCoverage', [CourierController::class, 'listSelectedCoverage']);
    Route::post('/defaultCoverageState', [CourierController::class, 'defaultCoverageState']);
    Route::put('/defaultCoverageState', [CourierController::class, 'updateDefaultCoverageState']);
    Route::post('/exceptionalCoverage', [CourierController::class, 'exceptionalCoverage']);
    Route::put('/exceptionalCoverage', [CourierController::class, 'updateExceptionalCoverage']);
    Route::post('/uploadExceptionalCoverage', [CourierController::class, 'uploadExceptionalCoverage']);
    Route::delete('/exceptionalCoverage', [CourierController::class, 'deleteExceptionalCoverage']);
    Route::post('/addExceptionalCoverage', [CourierController::class, 'addExceptionalCoverage']);
    Route::post('/updateGeneralSettings', [CourierController::class, 'updateGeneralSettings']);
});

Route::prefix('sla')->group(function(){
    Route::get('list/{courier}', [CourierServiceLevelAgreementController::class, 'list']);
    Route::get('show/{id}', [CourierServiceLevelAgreementController::class, 'show']);
    Route::post('add/{courier}', [CourierServiceLevelAgreementController::class, 'create']);
    Route::post('update/{sla}', [CourierServiceLevelAgreementController::class, 'update']);
    Route::delete('/', [CourierServiceLevelAgreementController::class, 'delete']);
    Route::post('check-duplicate/{courier}/{id?}', [CourierServiceLevelAgreementController::class, 'check_duplicate']);
});

Route::prefix('pos')->group(function(){
    Route::post('generate-connote', [PosMalaysiaController::class, 'generate_connote']);
    Route::post('generate-pl9', [PosMalaysiaController::class, 'generate_pl9']);
    Route::post('download-connote', [PosMalaysiaController::class, 'download_connote']);
});

Route::get('scanned-parcel/{year}/{month}/{day?}', [OrderController::class, 'scanned_parcel']);

Route::post('bucket-batches/generate_cn', [BucketController::class, 'check_empty_bucket']);

Route::post('download-order-csv', [OrderController::class, 'download_order_csv']);
Route::post('download-claim-csv', [ClaimController::class, 'download_claim_csv']);

Route::get('get-couriers', [CourierController::class, 'list']);

Route::get('get-failed-order/{date}', [WebhookController::class, 'fail_insert']);

Route::webhooks('webhook/sales');

Route::get('test',[TestController::class, 'test']);

Route::prefix('settings')->group(function() {
    // Route::get('sla', [ReportController::class, 'sla']);
    Route::get('/init_shipping_doc_desc_data', [SettingsController::class,'init_shipping_doc_desc_page_data']);
    Route::get('/init_sdd_table', [SettingsController::class,'init_sdd_table']);
    Route::post('/shipping_doc_desc/form/add',[SettingsController::class,'add_sdd']); //add
    Route::delete('/init_sdd_table/{template}', [SettingsController::class,'delete_sdd_table']); //delete
    Route::get('/edit_sdd/form/{templateId}', [SettingsController::class,'edit_sdd_table']); //edit
    Route::post('/shipping_doc_desc/form/update/{form_id}', [SettingsController::class,'update_sdd']); //update
    // Route::get('shipment', [ReportController::class, 'shipment']);
});
Route::post('sign-in', [LoginController::class, 'login_through_api']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('sign-out', [LoginController::class, 'logout_through_api']);

    Route::prefix('fixcode')->group(function () {
    Route::get('processing-date/{company_id}', [FixcodeController::class, 'processing_date']);
    });
});

Route::prefix('state-group')->group(function() {
    Route::post('store', [ShippingCostController::class, 'store_state_group'])->middleware('can:state_group.create');
    Route::post('update', [ShippingCostController::class, 'update_state_group'])->middleware('can:state_group.edit');
    Route::post('delete/{id}', [ShippingCostController::class, 'delete_state_group'])->middleware('can:state_group.delete');
});
