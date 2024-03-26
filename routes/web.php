<?php

use App\Http\Controllers\AlternativePostcodeController;
use App\Http\Controllers\AccessTokenController;
use App\Http\Controllers\BucketAutomationController;
use App\Http\Controllers\BucketBatchController;
use App\Http\Controllers\BucketController;
use App\Http\Controllers\ClaimController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OperationalModelController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TemplateSettingController;
use App\Http\Controllers\CustomTemplateController;
use App\Models\Company;
use App\Models\Setting;
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
Route::get('php', fn () => phpinfo());


Route::middleware(['auth'])->group(function() {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::group(['prefix' => 'orders'], function () {
        Route::get('overall', [OrderController::class, 'overall'])->name('orders.overall');
        Route::get('pending', [OrderController::class, 'pending'])->name('orders.pending');
        Route::get('processing', [OrderController::class, 'processing'])->name('orders.processing');
        Route::get('packing', [OrderController::class, 'packing'])->name('orders.packing');
        Route::get('ready-to-ship', [OrderController::class, 'ready_to_ship'])->name('orders.readyToShip');
        Route::get('shipping', [OrderController::class, 'shipping'])->name('orders.shipping');
        Route::get('delivered', [OrderController::class, 'delivered'])->name('orders.delivered');
        Route::get('returned', [OrderController::class, 'returned'])->name('orders.returned');
        Route::get('return-completed', [OrderController::class, 'return_completed'])->name('orders.return_completed');
        Route::get('scan', [OrderController::class, 'scan'])->name('orders.scan');
        Route::post('scan', [OrderController::class, 'scan_barcode'])->name('orders.scanned');
        Route::get('scan-setting', [OrderController::class, 'scan_setting'])->name('orders.scan_setting');
        Route::get('rejected', [OrderController::class, 'rejected'])->name('orders.rejected');
        Route::post('download-order-csv', [OrderController::class, 'download_order_csv']);
        Route::get('{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::get('change-postcode', [OrderController::class, 'change_postcode_view'])->name('orders.change_postcode_view');
        Route::post('change-postcode', [OrderController::class, 'change_postcode'])->name('orders.change_postcode');
        Route::get('bucket-batch/{batch}', [OrderController::class, 'bucket_batch'])->name('orders.bucket_batch');
        Route::get('/get_template_main', [OrderController::class, 'get_template_main']);
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
        Route::post('update-courier', [ShippingController::class, 'update_courier'])->name('shipping.update_courier');
        Route::post('update-tracking', [ShippingController::class, 'update_tracking'])->name('shipping.update_tracking');
        Route::post('upload-bulk-tracking', [ShippingController::class, 'upload_bulk_tracking'])->name('shipping.upload_bulk_tracking');
        Route::post('dhl-label-single', [ShippingController::class, 'dhl_label_single'])->name('shipping.dhl_label_single');
        Route::post('generate_cn_multiple', [ShippingController::class, 'generate_cn_multiple'])->name('shipping.generate_cn_multiple');
        Route::post('cancel_shipment', [ShippingController::class, 'cancel_shipment'])->name('shipping.cancel_shipment');

    });

    Route::group(['prefix' => 'companies'], function(){
        Route::get('/', [CompanyController::class, 'index'])->name('companies.index');
        Route::get('edit/{company}', [CompanyController::class, 'edit'])->name('companies.edit');
        Route::post('update/{company}', [CompanyController::class, 'update'])->name('companies.update');
        Route::get('add', [CompanyController::class, 'add'])->name('companies.add');
        Route::post('create', [CompanyController::class, 'create'])->name('companies.create');
    });

    Route::group(['prefix'=>'permissions', 'middleware' => ['role:IT_Admin']], function(){
        Route::get('/', [PermissionController::class, 'index'])->name('permissions.index');
        Route::get('create', [PermissionController::class, 'create'])->name('permissions.create');
        Route::post('store', [PermissionController::class, 'store'])->name('permissions.store');
        Route::get('edit/{role}', [PermissionController::class, 'edit'])->name('permissions.edit');
        Route::post('update/{role}', [PermissionController::class, 'update'])->name('permissions.update');
        // Route::get('delete/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    });

    // role group
    Route::group(['prefix'=>'roles', 'middleware' => ['role:IT_Admin']], function() {
        Route::get('/', [RoleController::class, 'index'])->name('roles.index');
        Route::get('create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('store', [RoleController::class, 'store'])->name('roles.store');
    });

    Route::group(['prefix'=>'products'], function (){
        Route::get('/', [ProductController::class, 'index'])->name('products.index');
        Route::post('update/{product}', [ProductController::class, 'update'])->name('products.update');
        Route::get('create', [ProductController::class, 'create'])->name('products.create');
        Route::get('show/{id}', [ProductController::class, 'show'])->name('products.show');
        Route::post('store', [ProductController::class, 'store'])->name('products.store');
        Route::get('get-product/{product}', [ProductController::class, 'get'])->name('products.get_product');
        Route::post('delete/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });

    Route::group(['prefix'=>'users'], function() {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::get('create', [UserController::class, 'create'])->name('users.create');
        Route::post('store', [UserController::class, 'store'])->name('users.store');
        Route::get('edit/{user}', [UserController::class, 'edit'])->name('users.edit');
        Route::post('update/{user}', [UserController::class, 'update'])->name('users.update');
        Route::get('delete/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
    Route::prefix('profile')->group(function () {
        Route::get('/', [UserController::class, 'profile'])->name('profile.index');
        Route::post('update', [UserController::class, 'profile_update'])->name('profile.update');
        Route::post('update-password', [UserController::class, 'profile_update_password'])->name('profile.update_password');
    });

    Route::prefix('access-tokens')->middleware('role:IT_Admin')->group(function () {
        Route::get('/{company_id}', [AccessTokenController::class, 'show'])->name('access-tokens.show');
        Route::post('/{company_id}', [AccessTokenController::class, 'update'])->name('access-tokens.update');
    });

    Route::prefix('operational_models')->group(function() {
        Route::get('/', [OperationalModelController::class, 'index'])->name('operational_model.index');
        Route::get('/{opmodel_id}', [OperationalModelController::class, 'show'])->name('operational_model.show');
        Route::post('/{opmodel_id}', [OperationalModelController::class, 'update'])->name('operational_model.update');
    });

    Route::prefix('reports')->group(function(){
        Route::get('/', [ReportController::class, 'index'])->name('reports.index');
        Route::get('service-level-agreement', [ReportController::class, 'sla'])->name('reports.sla');
        Route::get('outbound', [ReportController::class, 'outbound'])->name('reports.outbound');
        Route::get('order-matrix', [ReportController::class, 'order_matrix'])->name('reports.order_matrix');
        Route::get('pending-report', [ReportController::class, 'pending_report'])->name('reports.pending_report');
        Route::get('shipment', [ReportController::class, 'shipment'])->name('reports.shipment');
        Route::get('shipment/attempt', [ReportController::class, 'shipment_attempt'])->name('reports.shipment.attempt-list');
        Route::get('shipment/unattempt', [ReportController::class, 'shipment_unattempt'])->name('reports.shipment.unattempt-list');
        Route::get('shipment/problem', [ReportController::class, 'shipment_problem'])->name('reports.shipment.problematic-list');
    });

    Route::prefix('claims')->group(function() {
        Route::get('/product', [ClaimController::class, 'index_product'])->name('claims.product.index');
        Route::get('/courier', [ClaimController::class, 'index_courier'])->name('claims.courier.index');
    });

    Route::prefix('alternative_postcode')->group(function() {
        Route::get('/', [AlternativePostcodeController::class, 'index'])->name('alternative_postcode.index');
        Route::post('save', [AlternativePostcodeController::class, 'store'])->name('alternative_postcode.save');
        Route::post('update', [AlternativePostcodeController::class, 'update'])->name('alternative_postcode.update');
        Route::get('delete/{id}', [AlternativePostcodeController::class, 'destroy'])->name('alternative_postcode.delete');
        Route::get('/search', [AlternativePostcodeController::class, 'handleSearch'])->name('search');
    });


    Route::prefix('template_setting')->group(function() {
        Route::get('/', [TemplateSettingController::class, 'index'])->name('template_setting.index');
        Route::post('update', [TemplateSettingController::class, 'update'])->name('template_setting.update');
    });

    Route::prefix('custom_template_setting')->group(function() {
        Route::get('/', [CustomTemplateController::class, 'index'])->name('custom_template_setting.index');
        Route::post('save_template', [CustomTemplateController::class, 'saveTemplate'])->name('custom_template_setting.save');
        Route::get('/get_columns/{id}', [CustomTemplateController::class, 'getColumns']);
        Route::post('update_template', [CustomTemplateController::class, 'updateTemplate'])->name('custom_template_setting.update');
        Route::delete('delete_template', [CustomTemplateController::class, 'deleteTemplate'])->name('custom_template_setting.delete');
    });
    


    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'index'])->name('index');
        Route::put('/', [SettingsController::class, 'update'])->name('update');
        Route::get('bucket-category', [BucketController::class, 'bucket_category'])->name('bucket_category');
        Route::get('bucket-automation', [BucketAutomationController::class, 'automation'])->name('bucket_automation_setting');
        Route::post('bucket-automation', [BucketAutomationController::class, 'create'])->name('bucket_automation_setting.create');
        Route::get('bucket-automation-list', [BucketAutomationController::class, 'list'])->name('bucket_automation_setting.get');
        Route::delete('bucket-automation', [BucketAutomationController::class, 'delete'])->name('bucket_automation_setting.delete');
        Route::post('bucket-automation-update', [BucketAutomationController::class, 'update'])->name('bucket_automation_setting.update');
        Route::put('bucket-automation-status', [BucketAutomationController::class, 'update_status'])->name('bucket_automation_setting.update_status');
        Route::post('bucket-automation-update-priority', [BucketAutomationController::class, 'update_priority'])->name('bucket_automation_setting.update_priority');

        Route::get('/ship_doc_desc',[SettingsController::class,'view_shipping_doc_desc'])->name('view_shipping_doc_desc');
        Route::get('/ship_doc_desc/form',[SettingsController::class,'sdd_form'])->name('sdd_form');
        Route::get('/ship_doc_desc/form/{id}',[SettingsController::class,'sdd_form']);


        
    });

});

    Route::get("sdd_template",[SettingsController::class,'sdd_template_view']);

    Route::get('live', fn () => view('live')); // comment out suspect cause server issues timeout error

    Route::get('notifications', [NotificationController::class, 'list']);

    Route::get('dhl-access-token', [ShippingController::class, 'dhl_generate_access_token']);

    Auth::routes();

    Route::middleware(['auth', 'role:user'])->group(function() {
        Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    });

    Route::middleware(['auth', 'role:admin'])->group(function() {
        // Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    });

    //migration for dev purpose only
    Route::middleware(['auth', 'role:IT_Admin'])->group(function() {
        Route::get('run-migration', function () {
            // if(config('app.env')=="local"){
                Artisan::call('migrate', ['--force' => true]);
                return 'Migrations ran successfully!';
            // }
        });
        Route::get('rollback-migration', function () {
            if(config('app.env')!="production"){
                Artisan::call('migrate:rollback', ['--force' => true]);
                return 'Rollback ran successfully!';
            }
        });

        Route::get('seed/permission', function () {
            // if(config('app.env')=="local"){
                Artisan::call('db:seed', ['--class' => 'RolesAndPermissionsSeeder', '--force' => true]);
                return 'Seeds ran successfully!';
            // }
        });
    });

