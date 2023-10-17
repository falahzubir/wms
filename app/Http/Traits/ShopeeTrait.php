<?php

namespace App\Http\Traits;

use App\Models\AccessToken;
use Illuminate\Support\Carbon;

trait ShopeeTrait 
{
    public function getAccessToken()
    {
        $date = date('Y-m-d H:i:s');
        
        $accessToken = AccessToken::where('type', 'shopee')->first();

        if($accessToken){
            $token = $accessToken->token;
            $expired = $accessToken->expires_at;

            if($date <= $expired){
                return $token;   
            }
            else{
                $token = $this->refreshToken($accessToken);
                return $token;
            }
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Access token not found'
        ], 404);
    }

    public function refreshToken($accessToken)
    {
        $host = "https://partner.shopeemobile.com";
        $path = "/api/v2/auth/access_token/get";
        $partner_id = SHOPEE_LIVE_PARTNER_ID;
        $refresh_token = $accessToken->additional_data['refresh_token'];
        $shop_id = $accessToken->additional_data['shop_id'];
        $current_time = date('Y-m-d H:i:s');
        $timestamp = strtotime($current_time);

        $sign = $this->get_sign($path, $partner_id, $timestamp, null, null);
        
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

    public static function getShippingDocument()
    {
        $accessToken = AccessToken::where('type', 'shopee')->first();
        $host = "https://partner.shopeemobile.com";
        $path = "/api/v2/logistics/create_shipping_document";
        $partner_id = SHOPEE_LIVE_PARTNER_ID;
        $token = '544d48696a747759736f7042427a6c6d';
        $refresh_token = $accessToken->additional_data->refresh_token;
        $shop_id = $accessToken->additional_data->shop_id;
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
                    "order_sn": "2310163Y3XTRCK",
                    "package_number": "OFG151136499204599",
                    "shipping_document_type": "THERMAL_AIR_WAYBILL",
                    "tracking_number": "SPE8865095826"
                }
            ]
        }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        if(curl_error($curl)){
            echo 'Request Error:' . curl_error($curl);
        }
        echo $response;
        die;
        // dd($response);
    }

    public static function getShippingDocumentResult()
    {
        $accessToken = AccessToken::where('type', 'shopee')->first();
        $host = "https://partner.shopeemobile.com";
        $path = "/api/v2/logistics/get_shipping_document_result";
        $partner_id = SHOPEE_LIVE_PARTNER_ID;
        $token = '544d48696a747759736f7042427a6c6d';
        $refresh_token = $accessToken->additional_data->refresh_token;
        $shop_id = $accessToken->additional_data->shop_id;
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
                    "order_sn": "2310163Y3XTRCK",
                    "package_number": "OFG151136499204599",
                    "shipping_document_type": "THERMAL_AIR_WAYBILL"
                }
            ]
        }',        
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        if(curl_error($curl)){
            echo 'Request Error:' . curl_error($curl);
        }
        echo $response;
        die;
        // dd($response);
    }
}
