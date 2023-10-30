<?php

namespace App\Http\Traits;

use App\Models\AccessToken;
use Illuminate\Support\Carbon;

trait ShopeeTrait 
{
    public static function getAccessToken()
    {
        $date = date('Y-m-d H:i:s');
        
        $accessToken = AccessToken::where('type', 'shopee')->first();

        if($accessToken){
            $token = $accessToken->token;
            $expired = $accessToken->expires_at;
            $shop_id = $accessToken->additional_data->shop_id;

            if($date <= $expired){
                return [
                    'token' => $token,
                    'shop_id' => $shop_id];
            }
            else{
                $token = self::refreshToken($accessToken);
                return [
                    'token' => $token,
                    'shop_id' => $shop_id];
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Access token not found'
        ], 404);
    }

    public static function refreshToken($accessToken)
    {
        $host = "https://partner.shopeemobile.com";
        $path = "/api/v2/auth/access_token/get";
        $partner_id = SHOPEE_LIVE_PARTNER_ID;
        $refresh_token = $accessToken->additional_data['refresh_token'];
        $shop_id = $accessToken->additional_data['shop_id'];
        $current_time = date('Y-m-d H:i:s');
        $timestamp = strtotime($current_time);

        $sign = self::get_sign($path, $partner_id, $timestamp, null, null);
        
        $url= ($host.$path."?partner_id=".$partner_id."&timestamp=".$timestamp."&sign=".$sign);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{
            "refresh_token": "'.$refresh_token.'",
            "partner_id": '.$partner_id.',
            "shop_id": '.$shop_id.'
        }');
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);

        curl_close($ch);

        if(curl_error($ch)){
            echo 'Request Error:' . curl_error($ch);
        }
        else
        {
            $res = json_decode($response,true);
            dd($res);
            // return $token;
        }

    }

    public static function get_sign($path, $partner_id, $timestamp, $access_token, $shop_id)
    {
        $partnerKey = SHOPEE_LIVE_PARTNER_KEY;

        $tokenBaseString = $partner_id . $path . $timestamp . $access_token .  $shop_id;
        $sign = hash_hmac('sha256', $tokenBaseString, $partnerKey);

        return $sign;
    }

    public static function getOrderDetail($order_sn)
    {
        $accessToken = self::getAccessToken();
        $host = "https://partner.shopeemobile.com";
        $path = "/api/v2/order/get_order_detail";
        $partner_id = SHOPEE_LIVE_PARTNER_ID;
        $token = $accessToken['token'];
        $shop_id = $accessToken['shop_id'];
        $current_time = Carbon::now()->toDateTimeString();
        $timestamp = strtotime($current_time);

        $order_list = $order_sn;
        $response_optional_field = "buyer_user_id,buyer_username,recipient_address,item_list,total_amount,shipping_carrier,estimated_shipping_fee,pay_time,package_list,fulfillment_flag";

        $sign = self::get_sign($path, $partner_id, $timestamp, $token, $shop_id);
        
        $url= $host.$path."?access_token=".$token."&partner_id=".$partner_id."&shop_id=".$shop_id."&sign=".$sign."&timestamp=".$timestamp."&order_sn_list=".$order_list."&response_optional_fields=".$response_optional_field;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $response = curl_exec($ch);

        curl_close($ch);

        return $response;
    }

    public static function getShippingParameter($order_sn) #info_needed for ship order such as pickup_time_id, address_id
    {
        $accessToken = self::getAccessToken();
        $host = "https://partner.shopeemobile.com";
        $path = "/api/v2/logistics/get_shipping_parameter";
        $partner_id = SHOPEE_LIVE_PARTNER_ID;
        $token = $accessToken['token'];
        $shop_id = $accessToken['shop_id'];
        $current_time = Carbon::now()->toDateTimeString();
        $timestamp = strtotime($current_time);

        $sign = self::get_sign($path, $partner_id, $timestamp, $token, $shop_id);
        
        $url= $host.$path."?access_token=".$token."&partner_id=".$partner_id."&shop_id=".$shop_id."&sign=".$sign."&timestamp=".$timestamp."&order_sn=".$order_sn;

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
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

    public static function getTrackingNumber($order_sn) #get tracking number
    {
        $accessToken = $accessToken = self::getAccessToken();
        $host = "https://partner.shopeemobile.com";
        $path = "/api/v2/logistics/get_tracking_number";
        $partner_id = SHOPEE_LIVE_PARTNER_ID;
        $token = $accessToken['token'];
        $shop_id = $accessToken['shop_id'];
        $current_time = Carbon::now()->toDateTimeString();
        $timestamp = strtotime($current_time);

        $sign = self::get_sign($path, $partner_id, $timestamp, $token, $shop_id);
        
        $url= $host.$path."?access_token=".$token."&partner_id=".$partner_id."&shop_id=".$shop_id."&sign=".$sign."&timestamp=".$timestamp."&order_sn=".$order_sn."&package_number=-";

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
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

    public static function getTrackingInfo($order_sn) #somehow xleh guna
    {
        $accessToken = $accessToken = self::getAccessToken();
        $host = "https://partner.shopeemobile.com";
        $path = "/api/v2/logistics/get_tracking_info";
        $partner_id = SHOPEE_LIVE_PARTNER_ID;
        $token = $accessToken['token'];
        $shop_id = $accessToken['shop_id'];
        $current_time = Carbon::now()->toDateTimeString();
        $timestamp = strtotime($current_time);

        $sign = self::get_sign($path, $partner_id, $timestamp, $token, $shop_id);
        
        $url= $host.$path."?access_token=".$token."&partner_id=".$partner_id."&shop_id=".$shop_id."&sign=".$sign."&timestamp=".$timestamp."&order_sn=".$order_sn."&package_number=-";

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
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

    public static function shipOrder($order_sn)
    {
        $accessToken = $accessToken = self::getAccessToken();
        $host = "https://partner.shopeemobile.com";
        $path = "/api/v2/logistics/ship_order";
        $partner_id = SHOPEE_LIVE_PARTNER_ID;
        $token = $accessToken['token'];
        $shop_id = $accessToken['shop_id'];
        $current_time = Carbon::now()->toDateTimeString();
        $timestamp = strtotime($current_time);

        $address_id = 200007694;
        //check if time is before 11 am, then pickup time today else pickup time tomorrow
        $now = Carbon::now();
        if ($now->isWeekend()) 
        {
            // If today is Saturday or Sunday, set pickup time for Monday at 4:00 PM
            $pickup_time_id = $now->next(Carbon::MONDAY)->setTime(16, 0, 0)->timestamp;
        } 
        elseif ($now->hour < 11) 
        {
            // If it's a weekday and the current time is before 11 AM, set pickup time today at 4:00 PM
            $pickup_time_id = $now->setTime(16, 0, 0)->timestamp;
        } 
        else 
        {
            // If it's a weekday and the current time is 11 AM or later, set pickup time tomorrow at 4:00 PM
            $pickup_time_id = $now->addDay()->setTime(16, 0, 0)->timestamp;
        }
        
        $sign = self::get_sign($path, $partner_id, $timestamp, $token, $shop_id);
        
        $url= $host.$path."?access_token=".$token."&partner_id=".$partner_id."&shop_id=".$shop_id."&sign=".$sign."&timestamp=".$timestamp;

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => '{
            "order_sn": "'.$order_sn.'",
            "pickup": {
                "address_id": '.$address_id.',
                "pickup_time_id": "'.$pickup_time_id.'",
                "tracking_number": "-"
            }
        }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));
        
        $response = curl_exec($curl);
        
        return $response;
    }

    ########### START GENERATE CN FUNCTION ############

    public static function getShippingDocumentParameter($data) #get suggestion of shipping_document_type need for generate CN
    {
        $accessToken = $accessToken = self::getAccessToken();
        $host = "https://partner.shopeemobile.com";
        $path = "/api/v2/logistics/get_shipping_document_parameter";
        $partner_id = SHOPEE_LIVE_PARTNER_ID;
        $token = $accessToken['token'];
        $shop_id = $accessToken['shop_id'];
        $current_time = Carbon::now()->toDateTimeString();
        $timestamp = strtotime($current_time);

        $sign = self::get_sign($path, $partner_id, $timestamp, $token, $shop_id);
        
        $url= $host.$path."?access_token=".$token."&partner_id=".$partner_id."&shop_id=".$shop_id."&sign=".$sign."&timestamp=".$timestamp;

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
            "order_list": [
                {
                    "order_sn": "'.$data['ordersn'].'",
                    "package_number": "'.$data['package_number'].'"
                }
            ]
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        
        return $response;
    }

    public static function createShippingDocument($data) 
    {
        $accessToken = $accessToken = self::getAccessToken();
        $host = "https://partner.shopeemobile.com";
        $path = "/api/v2/logistics/create_shipping_document";
        $partner_id = SHOPEE_LIVE_PARTNER_ID;
        $token = $accessToken['token'];
        $shop_id = $accessToken['shop_id'];
        $current_time = Carbon::now()->toDateTimeString();
        $timestamp = strtotime($current_time);

        $sign = self::get_sign($path, $partner_id, $timestamp, $token, $shop_id);
        
        $url= $host.$path."?access_token=".$token."&partner_id=".$partner_id."&shop_id=".$shop_id."&sign=".$sign."&timestamp=".$timestamp;

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
            "order_list": [
                {
                    "order_sn": "'.$data['ordersn'].'",
                    "package_number": "'.$data['package_number'].'",
                    "shipping_document_type": "'.$data['shipping_document_type'].'",
                    "tracking_number": "'.$data['tracking_no'].'"
                }
            ]
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public static function getShippingDocumentResult($data) #to know if status READY then can download
    {
        $accessToken = $accessToken = self::getAccessToken();
        $host = "https://partner.shopeemobile.com";
        $path = "/api/v2/logistics/get_shipping_document_result";
        $partner_id = SHOPEE_LIVE_PARTNER_ID;
        $token = $accessToken['token'];
        $shop_id = $accessToken['shop_id'];
        $current_time = Carbon::now()->toDateTimeString();
        $timestamp = strtotime($current_time);

        $sign = self::get_sign($path, $partner_id, $timestamp, $token, $shop_id);
        
        $url= $host.$path."?access_token=".$token."&partner_id=".$partner_id."&shop_id=".$shop_id."&sign=".$sign."&timestamp=".$timestamp;

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
            "order_list": [
                {
                    "order_sn": "'.$data['ordersn'].'",
                    "package_number": "'.$data['package_number'].'",
                    "shipping_document_type": "'.$data['shipping_document_type'].'"
                }
            ]
        }',        
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }

    public static function downloadShippingDocument($data)
    {
        $accessToken = self::getAccessToken();
        $host = "https://partner.shopeemobile.com";
        $path = "/api/v2/logistics/download_shipping_document";
        $partner_id = SHOPEE_LIVE_PARTNER_ID;
        $token = $accessToken['token'];
        $shop_id = $accessToken['shop_id'];
        $current_time = Carbon::now()->toDateTimeString();
        $timestamp = strtotime($current_time);

        $sign = self::get_sign($path, $partner_id, $timestamp, $token, $shop_id);
        
        $url= $host.$path."?access_token=".$token."&partner_id=".$partner_id."&shop_id=".$shop_id."&sign=".$sign."&timestamp=".$timestamp;

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '        {
            "shipping_document_type": "'.$data['shipping_document_type'].'",
            "order_list": [
                {
                    "order_sn": "'.$data['ordersn'].'",
                    "package_number": "'.$data['package_number'].'"
                }
            ]
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        try {
            //download file to storage
            $file_name = 'shopee/'.Carbon::now()->format('YmdHis').'_'.$data['ordersn'].'.pdf';
            $file_path = storage_path('app/public/'.$file_name);
            file_put_contents($file_path, $response);

            return $file_name;
        } catch (\Throwable $th) {
            
            return false;
        }

        return false;
    }
}
