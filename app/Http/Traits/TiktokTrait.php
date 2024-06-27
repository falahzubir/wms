<?php

namespace App\Http\Traits;

use App\Models\AccessToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

### Tiktok Order Status
// - UNPAID = 100;
// - ON_HOLD = 105;
// - AWAITING_SHIPMENT = 111;
// - AWAITING_COLLECTION = 112;
// - PARTIALLY_SHIPPING = 114;
// - IN_TRANSIT = 121;
// - DELIVERED = 122;
// - COMPLETED = 130;
// - CANCELLED = 140;
Trait TiktokTrait
{
    public static function getAccessToken($shop_id)
    {
        $date = date('Y-m-d H:i:s');

        $accessToken = AccessToken::where('type', 'tiktok')->where('additional_data->shop_id',$shop_id)->first();
        if($accessToken){
            $token = $accessToken->token;
            $expired = $accessToken->expires_at;
            $shop_id = $accessToken->additional_data->shop_id;

            if($date <= $expired){
                return [
                    'token' => $token,
                    'shop_id' => $shop_id
                ];
            }
            else{
                $new_token = self::refreshToken($shop_id);
                return [
                    'token' => $new_token,
                    'shop_id' => $shop_id
                ];
            }
        }
    }

    public static function refreshToken($shop_id)
    {
        $url = app()->environment() == 'production' ? 'https://aa.bosemzi.com' : 'https://qastg.groobok.com';
        // $url = 'https://aa.bosemzi.com'; # FOR TESTING
        $json['from'] = 'wms';
        $json['shop_id'] = $shop_id;
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Signature' => hash_hmac('sha256', json_encode($json), env('WEBHOOK_CLIENT_SECRET')),
        ])
        ->post($url.'/api/get_tokenTiktok', $json);

        $response = json_decode($response, true);

        if(isset($response['token'])){
            //update access token
            $access_token = AccessToken::where('type', 'tiktok')->where('additional_data->shop_id',$shop_id)->first();
            $access_token->token = $response['token']['token'];
            $access_token->expires_at = $response['token']['expired'];

            $access_token->save();

            return $response['token']['token'];
        }

        return false;

    }

    public static function getSign($data,$app_secret,$action)
    {
        $sign = '';

        // Sort the data alphabetically by parameter names
        ksort($data);

        //concate all the parameters
        foreach ($data as $key => $value) {
            $sign .= $key . $value;
        }

        //Append the request path to the beginning
        $sign = $action . $sign;

        //Wrap string generated in step 3 with app_secret.
        $sign = $app_secret . $sign . $app_secret;

        //hmac-sha256
        $sign = hash_hmac('sha256', $sign, $app_secret);

        return $sign;
    }

    public static function getOrderDetails($params)
    {
        $url = 'https://open-api.tiktokglobalshop.com';
        $action = '/api/orders/detail/query';
        $access_token = self::getAccessToken($params['shop_id']);
        $token = $access_token['token'];
        $sign = '';
        $timestamp = time();

        // Data to be signed
        $info_sign = [
            'app_key' => TIKTOK_APP_KEY,
            'timestamp' => $timestamp,
            // Include all the necessary parameters for signing
        ];

        $order_id['order_id_list'][] = $params['order_id'];

        $sign = self::getSign($info_sign, TIKTOK_APP_SECRET, $action);

        $curl_url = $url.$action.'?'.'app_key='.TIKTOK_APP_KEY.'&access_token='.$token.'&sign='.$sign.'&timestamp='.$timestamp;

        $response = Http::post("$curl_url", [
            'app_key' => TIKTOK_APP_KEY,
            'access_token' => $token,
            'sign' => $sign,
            'timestamp' => $timestamp,
            'order_id_list' => $order_id['order_id_list'],
        ]);

        return $response->body();
    }

    public static function getPackageDetail($params)
    {
        $url = 'https://open-api.tiktokglobalshop.com';
        $action = '/api/fulfillment/detail';
        $access_token = self::getAccessToken($params['shop_id']);
        $token = $access_token['token'];
        $shop_id = $access_token['shop_id'];
        $sign = '';
        $timestamp = time();

        // Data to be signed
        $info_sign = [
            'app_key' => TIKTOK_APP_KEY,
            'timestamp' => $timestamp,
            'shop_id' => $shop_id,
            'package_id' => $params['package_id'],
            // Include all the necessary parameters for signing
        ];

        $order_id['package_id'] = $params['package_id'];
        $order_id['pick_up_type'] = 1;

        $sign = self::getSign($info_sign, TIKTOK_APP_SECRET, $action);

        $curl_url = $url.$action.'?'.'app_key='.TIKTOK_APP_KEY.'&access_token='.$token.'&sign='.$sign.'&timestamp='.$timestamp.'&shop_id='.$shop_id.'&package_id='.$params['package_id'];

        $response = Http::get("$curl_url");

        return $response->body();
    }

    public static function getConfigPickup($params)
    {
        $url = 'https://open-api.tiktokglobalshop.com';
        $action = '/api/fulfillment/package_pickup_config/list';
        $access_token = self::getAccessToken($params['shop_id']);
        $token = $access_token['token'];
        $shop_id = $access_token['shop_id'];
        $sign = '';
        $timestamp = time();

        // Data to be signed
        $info_sign = [
            'app_key' => TIKTOK_APP_KEY,
            'timestamp' => $timestamp,
            'shop_id' => $shop_id,
            'package_id' => $params['package_id'],
            // Include all the necessary parameters for signing
        ];

        $order_id['package_id'] = $params['package_id'];
        $order_id['pick_up_type'] = 1;

        $sign = self::getSign($info_sign, TIKTOK_APP_SECRET, $action);

        $curl_url = $url.$action.'?'.'app_key='.TIKTOK_APP_KEY.'&access_token='.$token.'&sign='.$sign.'&timestamp='.$timestamp.'&shop_id='.$shop_id.'&package_id='.$params['package_id'];

        $response = Http::get("$curl_url");

        return $response->body();
    }

    public static function shipOrder($params)
    {
        $url = 'https://open-api.tiktokglobalshop.com';
        $action = '/api/fulfillment/rts';
        $access_token = self::getAccessToken($params['shop_id']);
        $token = $access_token['token'];
        $shop_id = $access_token['shop_id'];
        $sign = '';
        $timestamp = time();

        // Data to be signed
        $info_sign = [
            'app_key' => TIKTOK_APP_KEY,
            'timestamp' => $timestamp,
            'shop_id' => $shop_id,
            'package_id' => $params['package_id'],
            'pick_up_type' => 1,
            // Include all the necessary parameters for signing
        ];

        $order_id['package_id'] = $params['package_id'];
        $order_id['pick_up_type'] = 1;

        $sign = self::getSign($info_sign, TIKTOK_APP_SECRET, $action);

        $curl_url = $url.$action.'?'.'app_key='.TIKTOK_APP_KEY.'&access_token='.$token.'&sign='.$sign.'&timestamp='.$timestamp.'&shop_id='.$shop_id.'&package_id='.$params['package_id'].'&pick_up_type=1';

        $response = Http::post("$curl_url", [
            'app_key' => TIKTOK_APP_KEY,
            'access_token' => $token,
            'sign' => $sign,
            'timestamp' => $timestamp,
            'shop_id' => $shop_id,
            'package_id' => $params['package_id'],
            'pick_up_type' => 1,
        ]);

        return $response->body();

    }

    public static function batchShipOrder($params)
    {
        $url = 'https://open-api.tiktokglobalshop.com';
        $action = '/api/fulfillment/batch_rts';
        $access_token = self::getAccessToken($params['shop_id']);
        $token = $access_token['token'];
        $shop_id = $access_token['shop_id'];
        $sign = '';
        $timestamp = time();

        // Data to be signed
        $info_sign = [
            'app_key' => TIKTOK_APP_KEY,
            'timestamp' => $timestamp,
            'shop_id' => $shop_id,
            // Include all the necessary parameters for signing
        ];

        $order_id['package_list'][] = $params['package_id'];

        $sign = self::getSign($info_sign, TIKTOK_APP_SECRET, $action);

        $curl_url = $url.$action.'?'.'app_key='.TIKTOK_APP_KEY.'&access_token='.$token.'&sign='.$sign.'&timestamp='.$timestamp;

        $response = Http::post("$curl_url", [
            'app_key' => TIKTOK_APP_KEY,
            'access_token' => $token,
            'sign' => $sign,
            'timestamp' => $timestamp,
            'shop_id' => $shop_id,
            'package_list' => $order_id['package_list'],
        ]);

        return $response->body();

    }

    public static function generateCNJugak($params)
    {
        $url = 'https://open-api.tiktokglobalshop.com';
        $action = '/api/fulfillment/shipping_document';
        $access_token = self::getAccessToken($params['shop_id']);
        $token = $access_token['token'];
        $shop_id = $access_token['shop_id'];
        $sign = '';
        $timestamp = time();

        // Data to be signed
        $info_sign = [
            'app_key' => TIKTOK_APP_KEY,
            'timestamp' => $timestamp,
            'shop_id' => $shop_id,
            'package_id' => $params['package_id'],
            'document_type' => 3,
            'document_size' => 0,
            // Include all the necessary parameters for signing
        ];

        $sign = self::getSign($info_sign, TIKTOK_APP_SECRET, $action);

        $curl_url = $url.$action.'?'.'app_key='.TIKTOK_APP_KEY.'&access_token='.$token.'&sign='.$sign.'&timestamp='.$timestamp.'&shop_id='.$shop_id.'&package_id='.$params['package_id'].'&document_type=1&document_size=0';

        $response = Http::get("$curl_url");

        $response = json_decode($response->body(),true);

        try {

            if ($response['code'] != 0) {
                return json_encode([
                    'code' => $response['code'],
                    'message' => $response['message']
                ]);
            }

            // Download file from the provided URL
            $fileUrl = $response['data']['doc_url'];
            $fileContent = Http::get($fileUrl)->body();

            // Save the file to storage
            $file_name = 'tiktok/initial_' . Carbon::now()->format('YmdHis') . '_' . $params['order_id'] . '.pdf';
            $file_path = storage_path('app/public/' . $file_name);

            // file_put_contents($file_path, $fileContent);
            Storage::put('public/'.$file_name, $fileContent);

            // * convert pdf version to 1.4 using ghostscript
            $new_file_name = 'tiktok/'.Carbon::now()->format('YmdHis').'_'.$params['order_id'].'.pdf';
            $new_file_path = storage_path('app/public/'.$new_file_name);
            $exec = 'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile='.$new_file_path.' '.$file_path;
            shell_exec($exec);
            // ! delete initial file
            // unlink($file_path);

            return json_encode([
                'code' => 0,
                'message' => 'Success',
                'data' => [
                    'file_name' => $new_file_name
                ]
            ]);

        } catch (\Throwable $th) {

            return json_encode([
                'code' => 500,
                'message' => 'Failed to download file (2)'
            ]);
        }

        return json_encode([
            'code' => 500,
            'message' => 'Failed to download file (3)'
        ]);
    }

    public static function generateCN($params)
    {
        $url = 'https://open-api.tiktokglobalshop.com';
        $action = '/api/logistics/shipping_document';
        $access_token = self::getAccessToken($params['shop_id']);
        $token = $access_token['token'];
        $shop_id = $access_token['shop_id'];
        $sign = '';
        $timestamp = time();

        // Data to be signed
        $info_sign = [
            'app_key' => TIKTOK_APP_KEY,
            'timestamp' => $timestamp,
            'shop_id' => $shop_id,
            'order_id' => $params['order_id'],
            'document_type' => 'SHIPPING_LABEL'
            // Include all the necessary parameters for signing
        ];

        $sign = self::getSign($info_sign, TIKTOK_APP_SECRET, $action);

        $curl_url = $url.$action.'?'.'app_key='.TIKTOK_APP_KEY.'&access_token='.$token.'&sign='.$sign.'&timestamp='.$timestamp.'&shop_id='.$shop_id.'&order_id='.$params['order_id'].'&document_type=SHIPPING_LABEL';

        $response = Http::get("$curl_url");

        $response = json_decode($response->body(),true);

        try {

            if ($response['code'] != 0) {
                return json_encode([
                    'code' => $response['code'],
                    'message' => $response['message']
                ]);
            }

            $context = stream_context_create([
                'http' => [
                    'user_agent' => 'GuzzleHttp/7',
                ],
            ]);
            // URL of the file you want to download
            $fileUrl = $response['data']['doc_url'];
            // Get the file content
            $fileContent = file_get_contents($fileUrl, false, $context);

            // Save the file to storage
            $file_name = 'tiktok/initial_' . Carbon::now()->format('YmdHis') . '_' . $params['order_id'] . '.pdf';
            $file_path = storage_path('app/public/' . $file_name);

            // Set the appropriate headers for a PDF file
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $file_name . '"');

            Storage::put('public/'.$file_name, $fileContent);

            return json_encode([
                'code' => 0,
                'message' => 'Success',
                'data' => [
                    'file_name' => $file_name
                ]
            ]);

        } catch (\Throwable $th) {

            return json_encode([
                'code' => 500,
                'message' => json_encode($th)
            ]);
        }

        return json_encode([
            'code' => 500,
            'message' => 'Failed to download file (3)'
        ]);
    }
}
