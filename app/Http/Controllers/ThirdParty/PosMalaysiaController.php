<?php

namespace App\Http\Controllers\ThirdParty;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ShippingController;
use App\Models\AccessToken;
use App\Models\Order;
use App\Models\Shipping;
use Database\Seeders\PosMalaysiaSettingSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

class PosMalaysiaController extends ShippingController
{

    public function __construct()
    {
        parent::__construct();

        if(config('settings.genconnote_application_code') == null){
            $seeder = new PosMalaysiaSettingSeeder();
            $seeder->run();
            Artisan::call('config:cache');
        }
    }

    public function generate_connote(Request $request)
    {
        $orders_pos = Order::doesntHave('shippings')->with([
            'company', 'operationalModel', 'batch',
        ])->whereIn('id', $request->order_ids)->where('courier_id', POSMALAYSIA_ID)->get();

        if (count($orders_pos) == 0) {
            return response([
                // 'status' => 'error',
                'message' => ['No orders found, please select correct courier'],
            ]);
        }

        $bearer = AccessToken::where('type', 'posmalaysia')->first();
        if($bearer->expires_at < Carbon::now()){
            Artisan::call('posmalaysia-token:cron');
        }

        $shipping = [];
        $error = [];
        foreach ($orders_pos as $order) {

            //check for COD with zero payment, change to Prefix PAID
            if($order->purchase_type == PURCHASE_TYPE_COD && $order->total_price == 0){
                $order->purchase_type = PURCHASE_TYPE_PAID;
            }
            $connote = Http::withToken($bearer->token, 'Bearer')->get($this->posmalaysia_generate_connote, [
                'numberOfItem' => 1,
                'Prefix' => $order->purchase_type == PURCHASE_TYPE_COD ? config('settings.genconnote_prefix_cod') : config('settings.genconnote_prefix_paid'),
                'ApplicationCode' => config('settings.genconnote_application_code'),
                'Secretid' => config('settings.genconnote_secret_id'),
                'Orderid' => shipment_num_format($order),
                'username' => config('settings.genconnote_username'),
            ])->json();

            switch ($connote['StatusCode']) {
                case '01':
                    $shipping[] = [
                        'order_id' => $order->id,
                        'tracking_number' => $connote['ConnoteNo'],
                        'shipment_number' => shipment_num_format($order),
                        'courier' => 'posmalaysia',
                        'created_by' => auth()->user()->id ?? 1,
                        'created_at' => Carbon::now(),
                    ];
                    break;
                case '02':
                    $error[] = order_num_format($order) . ' - ' . $connote['Message'];
                    break;
                case '03':
                    $error[] = order_num_format($order) . ' - ' . $connote['Message'];
                    break;
                default:
                    $error[] = order_num_format($order) . ' - ' . $connote['Message'];
                    break;
            }
        }

        if (count($shipping) > 0) {
            Shipping::insert($shipping);
        }

        if (count($error) > 0) {
            return response([
                'status' => 'error',
                'message' => $error,
                'data' => $shipping,
            ]);
        }

        return response([
            'status' => 'success',
            'message' => 'Connote generated successfully',
            'data' => $shipping,
        ]);
    }

    public function generate_pl9(Request $request)
    {
        $orders_pos = Order::with([
            'company', 'operationalModel', 'batch', 'shippings',
        ])->has('shippings')
            ->whereHas('shippings', function ($query) {
                $query->whereNull('additional_reference');
            })
            ->whereIn('id', $request->order_ids)->where('courier_id', POSMALAYSIA_ID)->get();

        if (count($orders_pos) == 0) {
            return response([
                'status' => 'error',
                'message' => ['No orders found'],
            ]);
        }

        $bearer = AccessToken::where('type', 'posmalaysia')->first();
        if($bearer->expires_at < Carbon::now()){
            Artisan::call('posmalaysia-token:cron');
        }
        $error = [];
        foreach ($orders_pos as $order) {
            $pl9_generate = Http::withToken($bearer->token, 'Bearer')->get($this->posmalaysia_generate_pl9, [
                'AccountNo' => config('settings.gen3pl_account_no'),
                'Secretid' => config('settings.gen3pl_secret_id'),
                'Orderid' => shipment_num_format($order),
                'username' => config('settings.gen3pl_username'),
                'ConnoteList' => implode('|', $order->shippings->pluck('tracking_number')->toArray()),
            ])->json();

            switch ($pl9_generate['StatusCode']) {
                case '01':
                    $order->shippings()->update(['additional_reference' => $pl9_generate['PL9No']]);
                    break;
                case '02':
                    $error[] = order_num_format($order) . ' - ' . $pl9_generate['Message'];
                    break;
                case '03':
                    $error[] = order_num_format($order) . ' - ' . $pl9_generate['Message'];
                    break;
                default:
                    $error[] = order_num_format($order) . ' - ' . $pl9_generate['Message'];
                    break;
            }
        }

        if(count($error) > 0){
            return response([
                'status' => 'error',
                'message' => $error,
            ]);
        }

        return response([
            'status' => 'success',
            'message' => 'PL9 generated successfully',
            'data' => $orders_pos,
        ]);
    }

    public function download_connote(Request $request)
    {
        $orders_pos = Order::has('shippings')->with([
            'customer', 'items', 'items.product', 'company', 'operationalModel', 'batch', 'shippings',
        ])->whereIn('id', $request->order_ids)->where('courier_id', POSMALAYSIA_ID)->get();

        if (count($orders_pos) == 0) {
            return response([
                'status' => 'error',
                'message' => ['No orders found'],
            ]);
        }

        $bearer = AccessToken::where('type', 'posmalaysia')->first();
        if($bearer->expires_at < Carbon::now()){
            Artisan::call('posmalaysia-token:cron');
        }
        $error = [];

        foreach ($orders_pos as $order) {
            //check for COD with zero payment, change to Prefix PAID
            if($order->purchase_type == PURCHASE_TYPE_COD && $order->total_price == 0){
                $order->purchase_type = PURCHASE_TYPE_PAID;
            }
            foreach($order->shippings as $shipping){
                $json_data = [
                    'subscriptionCode' => $order->company->posmalaysia_subscribtion_code,
                    'requireToPickup' => config('settings.genpreacceptedsingle_require_to_pickup'),
                    'requireWebHook' => config('settings.genpreacceptedsingle_require_web_hook'),
                    'accountNo' => config('settings.genpreacceptedsingle_account_no'),
                    'callerName' => config('settings.genpreacceptedsingle_caller_name'),
                    'callerPhone' => config('settings.genpreacceptedsingle_caller_phone'),
                    'pickupLocationID' => config('settings.genpreacceptedsingle_pickup_location_id'),
                    'pickupLocationName' => config('settings.genpreacceptedsingle_pickup_location_name'),
                    'contactPerson' => config('settings.genpreacceptedsingle_contact_person'),
                    'phoneNo' => config('settings.genpreacceptedsingle_phone_no'),
                    'pickupAddress' => config('settings.genpreacceptedsingle_pickup_address'),
                    'pickupDistrict' => config('settings.genpreacceptedsingle_pickup_district'),
                    'pickupProvince' => config('settings.genpreacceptedsingle_pickup_province'),
                    'pickupCountry' => config('settings.genpreacceptedsingle_pickup_country'),
                    'pickupLocation' => config('settings.genpreacceptedsingle_pickup_location'),
                    'pickupEmail' => config('settings.genpreacceptedsingle_pickup_email'),
                    'postCode' => config('settings.genpreacceptedsingle_post_code'),
                    'ItemType' => config('settings.genpreacceptedsingle_item_type'),
                    'totalQuantityToPickup' => 1, //to be confirmed
                    'totalWeight' => get_order_weight($order)/1000,
                    'ConsignmentNoteNumber' => $shipping->tracking_number,
                    'PaymentType' => $order->purchase_type == PURCHASE_TYPE_COD ? 0 : 2,
                    'Amount' => $order->total_price/100,
                    'readyToCollectAt' => date('h:i A', strtotime('+1 hour')),
                    'closeAt' => config('settings.genpreacceptedsingle_close_at'),
                    'receiverName' => $order->customer->name,
                    'receiverFname' => $order->customer->name,
                    'receiverLname' => $order->customer->name,
                    'receiverID' => '',
                    'receiverAddress' => $order->customer->address,
                    'receiverAddress2' => '',
                    'receiverDistrict' => '',
                    'receiverProvince' => MY_STATES[$order->customer->state],
                    'receiverCity' => $order->customer->city,
                    'receiverPostCode' => $order->customer->postcode,
                    'receiverCountry' => 'MY',
                    'receiverEmail' => '',
                    'receiverPhone01' => $order->customer->phone,
                    'receiverPhone02' => $order->customer->phone_2 ?? $order->customer->phone,
                    'sellerReferenceNo' => shipment_num_format($order),
                    'itemDescription' => get_shipping_remarks($order),
                    'sellerOrderNo' => $order->sales_id,
                    'comments' => $order->shipping_remarks,
                    'packDesc' => get_shipping_remarks($order),
                    'packVol' => '',
                    'packLeng' => '',
                    'packWidth' => '',
                    'packHeight' => '',
                    'packTotalitem' => '',
                    'orderDate' => date('Y-m-d'),
                    'packDeliveryType' => '',
                    'ShipmentName' => 'PosLaju',
                    'pickupProv' => '',
                    'deliveryProv' => '',
                    'postalCode' => '',
                    'currency' => 'MYR',
                    'countryCode' => 'MY',
                    'pickupDate' => '',
                ];

                $request = Http::withToken($bearer->token, 'Bearer')->post($this->posmalaysia_download_connote, $json_data)->json();

                $save = $this->save_connote($request, $order, $shipping);

                if($save !== true){
                    $error[] = $save;
                }
                // if(isset($request['pdf']) && $request['pdf'] != null){

                //     $context = stream_context_create([
                //         'http' => [
                //             'user_agent' => 'GuzzleHttp/7',
                //         ],
                //     ]);

                //     // URL of the file you want to download
                //     $fileUrl = $request['pdf'];

                //     // Get the file content
                //     $fileContent = file_get_contents($fileUrl, false, $context);

                //     if ($fileContent !== false) {
                //         // echo base64_encode($fileContent);
                //         // Specify the file name with the .pdf extension
                //         $fileName = shipment_num_format($order) . '.pdf';
                //         $filePath = storage_path('app/public/pos_labels/' . $fileName);

                //         // Set the appropriate headers for a PDF file
                //         header('Content-Type: application/pdf');
                //         header('Content-Disposition: attachment; filename="' . $fileName . '"');

                //         //create folder if not exist
                //         if (!file_exists(storage_path('app/public/pos_labels'))) {
                //             mkdir(storage_path('app/public/pos_labels'), 0777, true);
                //         }

                //         // Save the file
                //         file_put_contents($filePath, $fileContent);

                //         // save path to shipping
                //         $shipping->update(['attachment' => "pos_labels/".$fileName]);
                //         set_order_status($order, ORDER_STATUS_PACKING, 'Connote downloaded successfully', auth()->user()->id ?? 1);

                //     } else {
                //         // Handle the error, e.g., invalid base64 string
                //         $error[] = 'Error decoding the base64 content.';
                //     }
                // }
                // else{
                //     $error[] = 'Error downloading the connote.';
                // }
            }
        }

        if(count($error) > 0){
            return response([
                'status' => 'error',
                'message' => $error,
            ]);
        }

        set_order_status_bulk($orders_pos, ORDER_STATUS_PACKING, 'Connote downloaded successfully');

        return response([
            'status' => 'success',
            'message' => 'Connote downloaded successfully',
        ]);
    }

    public function generate_connote_multiple($order_id, $data)
    {
        $error = [];
        $order = Order::with([
            'customer', 'items', 'items.product', 'company', 'operationalModel', 'batch', 'shippings',
        ])->where('id', $order_id)->where('courier_id', POSMALAYSIA_ID)->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found',
            ], 400);
        }

        $bearer = AccessToken::where('type', 'posmalaysia')->first();
        if($bearer->expires_at < Carbon::now()){
            Artisan::call('posmalaysia-token:cron');
        }

        // $orders_pos = Order::doesntHave('shippings')->with([
        //     'company', 'operationalModel', 'batch',
        // ])->whereIn('id', $request->order_ids)->where('courier_id', POSMALAYSIA_ID)->get();

        if($order->shippings->count() == 0){
            $gen_connote = Http::withToken($bearer->token, 'Bearer')->get($this->posmalaysia_generate_connote, [
                    'numberOfItem' => count($data),
                    'Prefix' => $order->purchase_type == PURCHASE_TYPE_COD ? config('settings.genconnote_prefix_cod') : config('settings.genconnote_prefix_paid'),
                    'ApplicationCode' => config('settings.genconnote_application_code'),
                    'Secretid' => config('settings.genconnote_secret_id'),
                    'Orderid' => shipment_num_format($order),
                    'username' => config('settings.genconnote_username'),
                ])->json();

            if($gen_connote['StatusCode'] != '01'){
                return response()->json([
                    'status' => 'error',
                    'message' => $gen_connote['Message'],
                ], 400);
            }
        }

        if(isset($gen_connote)){
            $shippings = [];
            $connote_array = explode('|', $gen_connote['ConnoteNo']);

            foreach($data as $key => $value){
                $shippings[] = [
                    'order_id' => $order->id,
                    'tracking_number' => $connote_array[$key],
                    'shipment_number' => shipment_num_format_mult($order, $key),
                    'courier' => 'posmalaysia',
                    'created_by' => auth()->user()->id ?? 1,
                ];
            }

            Shipping::insert($shippings);
        }
        else{
            $connote_array = $order->shippings->pluck('tracking_number')->toArray();
        }

        if($order->shippings->whereNull('shippings.additional_reference')->count() == 0){
            $pl9_generate = Http::withToken($bearer->token, 'Bearer')->get($this->posmalaysia_generate_pl9, [
                'AccountNo' => config('settings.gen3pl_account_no'),
                'Secretid' => config('settings.gen3pl_secret_id'),
                'Orderid' => shipment_num_format($order),
                'username' => config('settings.gen3pl_username'),
                'ConnoteList' => implode('|', $connote_array),
            ])->json();

            if($pl9_generate['StatusCode'] != '01'){
                return response()->json([
                    'status' => 'error',
                    'message' => $pl9_generate['Message'],
                ], 400);
            }

            Shipping::whereIn('tracking_number', $connote_array)->update(['additional_reference' => $pl9_generate['PL9No']]);
        }

        foreach($data as $key => $cn){
            if(!isset($shipping)){
                $shipping = Shipping::where('tracking_number', $connote_array[$key])->first();
            }
            if($key == 0){ // parent json
                $json_data = [
                    'subscriptionCode' => $order->company->posmalaysia_subscribtion_code,
                    'requireToPickup' => config('settings.genpreacceptedsingle_require_to_pickup'),
                    'requireWebHook' => config('settings.genpreacceptedsingle_require_web_hook'),
                    'accountNo' => config('settings.genpreacceptedsingle_account_no'),
                    'callerName' => config('settings.genpreacceptedsingle_caller_name'),
                    'callerPhone' => config('settings.genpreacceptedsingle_caller_phone'),
                    'pickupLocationID' => config('settings.genpreacceptedsingle_pickup_location_id'),
                    'pickupLocationName' => config('settings.genpreacceptedsingle_pickup_location_name'),
                    'contactPerson' => config('settings.genpreacceptedsingle_contact_person'),
                    'phoneNo' => config('settings.genpreacceptedsingle_phone_no'),
                    'pickupAddress' => config('settings.genpreacceptedsingle_pickup_address'),
                    'pickupDistrict' => config('settings.genpreacceptedsingle_pickup_district'),
                    'pickupProvince' => config('settings.genpreacceptedsingle_pickup_province'),
                    'pickupCountry' => config('settings.genpreacceptedsingle_pickup_country'),
                    'pickupLocation' => config('settings.genpreacceptedsingle_pickup_location'),
                    'pickupEmail' => config('settings.genpreacceptedsingle_pickup_email'),
                    'postCode' => config('settings.genpreacceptedsingle_post_code'),
                    'ItemType' => config('settings.genpreacceptedsingle_item_type'),
                    'totalQuantityToPickup' => count($data),
                    'totalWeight' => get_order_weight($order, $cn)/1000, // KG
                    'ConsignmentNoteNumber' => $connote_array[$key], // parent connote, first child
                    'PaymentType' => $order->purchase_type == PURCHASE_TYPE_COD ? 0 : 2,
                    'Amount' => $order->total_price/100,
                    'readyToCollectAt' => date('h:i A', strtotime('+1 hour')),
                    'closeAt' => config('settings.genpreacceptedsingle_close_at'),
                    'receiverName' => $order->customer->name,
                    'receiverFname' => $order->customer->name,
                    'receiverLname' => $order->customer->name,
                    'receiverID' => '',
                    'receiverAddress' => $order->customer->address,
                    'receiverAddress2' => '',
                    'receiverDistrict' => '',
                    'receiverProvince' => MY_STATES[$order->customer->state],
                    'receiverCity' => $order->customer->city,
                    'receiverPostCode' => $order->customer->postcode,
                    'receiverCountry' => 'MY',
                    'receiverEmail' => '',
                    'receiverPhone01' => $order->customer->phone,
                    'receiverPhone02' => $order->customer->phone_2 ?? $order->customer->phone,
                    'sellerReferenceNo' => shipment_num_format_mult($order, $key),
                    'itemDescription' => $this->package_description($order, $cn),
                    'sellerOrderNo' => $order->sales_id,
                    'comments' => $order->shipping_remarks,
                    'packDesc' => get_shipping_remarks($order, $cn),
                    'packVol' => '',
                    'packLeng' => '',
                    'packWidth' => '',
                    'packHeight' => '',
                    'packTotalitem' => '',
                    'orderDate' => '', //to be confirmed
                    'packDeliveryType' => '',
                    'ShipmentName' => 'PosLaju',
                    'pickupProv' => '',
                    'deliveryProv' => '',
                    'postalCode' => '',
                    'currency' => 'MYR',
                    'countryCode' => 'MY',
                    'pickupDate' => date('Y-m-d'), //to be confirmed
                    'isMPS' => true,
                ];
            } else {
                $json_data['mps'][$key-1] = [
                    'consignmentNoteNumber' => $connote_array[$key],
                    'weight' => get_order_weight($order, $cn)/1000,
                    'length' => '0',
                    'width' => '0',
                    'height' => '0',
                    'details' => null,
                ];
            }
        }
        // dd($shipping);
        $request = Http::withToken($bearer->token, 'Bearer')->post($this->posmalaysia_download_connote, $json_data)->json();

        $save = $this->save_connote($request, $order, $shipping, true);

        if($save !== true){
            $error[] = $save;
        }
        if(count($error) > 0){
            return response()->json([
                'status' => 'error',
                'message' => $error,
            ], 400);
        }

        set_order_status($order, ORDER_STATUS_PACKING, 'Connote downloaded successfully', auth()->user()->id ?? 1);

        return response()->json([
            'status' => 'success',
            'message' => 'Connote downloaded successfully',
        ]);

    }

    private function save_connote($request, $order, $shipping, $mult = false)
    {
        $product_list = $this->generate_product_description($order->id);
        $error = [];
        if(isset($request['pdf']) && $request['pdf'] != null){

            $context = stream_context_create([
                'http' => [
                    'user_agent' => 'GuzzleHttp/7',
                ],
            ]);

            // URL of the file you want to download
            $fileUrl = $request['pdf'];

            // Get the file content
            $fileContent = file_get_contents($fileUrl, false, $context);
            if ($fileContent !== false) {
                // echo base64_encode($fileContent);
                // Specify the file name with the .pdf extension
                $fileName = ($mult == true ? shipment_num_format_mult($order, 1).'_original' : shipment_num_format($order)) . '.pdf';
                $filePath = storage_path('app/public/pos_labels/' . $fileName);

                // Set the appropriate headers for a PDF file
                header('Content-Type: application/pdf');
                header('Content-Disposition: attachment; filename="' . $fileName . '"');

                //create folder if not exist
                if (!file_exists(storage_path('app/public/pos_labels'))) {
                    mkdir(storage_path('app/public/pos_labels'), 0777, true);
                }

                //check if file exist, rename the original file
                if(file_exists($filePath)){
                    shell_exec('mv '.$filePath.' '.$filePath.'_deleted'.date('YmdHis'));
                }

                // Save the file
                if(file_put_contents($filePath, $fileContent)){
                    if($mult == true){ //split connote pdf
                        $new_file_path = storage_path('app/public/pos_labels/' . shipment_num_format_mult($order, 1) . '.pdf');
                        shell_exec('gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile='.$new_file_path.' '.$filePath);
                        unlink($filePath);
                        $fileName = shipment_num_format_mult($order, 1) . '.pdf';
                    }
                    else{
                        file_put_contents($filePath, $fileContent);
                    }
                }
                // save path to shipping
                $shipping->update(['attachment' => "pos_labels/".$fileName, 'packing_attachment' => $product_list]);

            } else {
                // Handle the error, e.g., invalid base64 string
                $error[] = 'Error decoding the base64 content.';
            }
        }
        else{
            $error[] = 'Error downloading the connote.';
        }

        if(count($error) > 0){
            return $error;
        }

        return true;
    }
}
