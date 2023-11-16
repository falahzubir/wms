<?php

namespace App\Http\Traits;

use App\Models\AccessToken;
use Illuminate\Support\Facades\Http;

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

        $curl = curl_init();

        //post curl
        curl_setopt_array($curl, array(
            CURLOPT_URL => $curl_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_POSTFIELDS => json_encode($order_id),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
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

        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => $curl_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        
        curl_close($curl);
        return $response;
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

        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => $curl_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);
        
        curl_close($curl);
        return $response;
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

        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => $curl_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_POSTFIELDS => json_encode($order_id),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        
        return $response;
        
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

        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => $curl_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_POSTFIELDS => json_encode($order_id),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        
        return $response;
        
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
            'document_type' => 1,
            'document_size' => 0,
            // Include all the necessary parameters for signing
        ];
        
        $sign = self::getSign($info_sign, TIKTOK_APP_SECRET, $action);
  
        $curl_url = $url.$action.'?'.'app_key='.TIKTOK_APP_KEY.'&access_token='.$token.'&sign='.$sign.'&timestamp='.$timestamp.'&shop_id='.$shop_id.'&package_id='.$params['package_id'].'&document_type=1&document_size=0';

        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => $curl_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);
        
        $response = json_decode($response,true);
        
        return $response;
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

        $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => $curl_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);
        
        $response = json_decode($response,true);
    
        return $response;
    }
}