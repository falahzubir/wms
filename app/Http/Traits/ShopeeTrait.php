<?php

namespace App\Http\Traits;

use App\Models\AccessToken;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

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
                    'shop_id' => $shop_id
                ];
            }
            else{
                $new_token = self::refreshToken();
                return [
                    'token' => $new_token,
                    'shop_id' => $shop_id
                ];
            }
        }
    }

    public static function refreshToken()
    {
        $url = app()->environment() == 'production' ? 'https://aa.bosemzi.com' : 'https://qastg.groobok.com';
        $json['from'] = 'wms';
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Signature' => hash_hmac('sha256', json_encode($json), env('WEBHOOK_CLIENT_SECRET')),
        ])
        ->post($url.'/api/get_tokenShopee', $json);

        $response = json_decode($response, true);
        //update access token
        $access_token = AccessToken::where('type', 'shopee')->first();
        $access_token->token = $response['token']['token'];
        $access_token->expires_at = $response['token']['expired'];

        $access_token->save();

        return $response['token']['token'];

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
        $data = [
            'order_sn_list' => $order_sn,
            'response_optional_fields' => "buyer_user_id,buyer_username,recipient_address,item_list,total_amount,shipping_carrier,estimated_shipping_fee,pay_time,package_list,fulfillment_flag",
        ];

        return self::sendRequest('get', '/api/v2/order/get_order_detail', $data);
    }

    public static function getShippingParameter($order_sn)
    {
        $data = [
            'order_sn' => $order_sn,
        ];

        return self::sendRequest('get', '/api/v2/logistics/get_shipping_parameter', $data);
    }

    public static function getTrackingNumber($order_sn)
    {
        $data = [
            'order_sn' => $order_sn,
            'package_number' => '-',
        ];

        return self::sendRequest('get', '/api/v2/logistics/get_tracking_number', $data);
    }

    public static function getTrackingInfo($order_sn)
    {
        $data = [
            'order_sn' => $order_sn,
            'package_number' => '-',
        ];

        return self::sendRequest('get', '/api/v2/logistics/get_tracking_info', $data);
    }

    public static function shipOrder($order_sn, $pickup_time_id)
    {
        $data = [
            'order_sn' => $order_sn,
            'pickup' => [
                'address_id' => 200007694,
                'pickup_time_id' => $pickup_time_id,
                'tracking_number' => '',
            ],
        ];

        return self::sendRequest('post', '/api/v2/logistics/ship_order', $data);
    }

    public static function getShippingDocumentParameter($data)
    {
        $orderList = [
            [
                'order_sn' => $data['ordersn'],
                'package_number' => $data['package_number'],
            ],
        ];
        return self::sendRequest('post', '/api/v2/logistics/get_shipping_document_parameter', ['order_list' => $orderList]);
    }

    public static function createShippingDocument($data)
    {
        $orderList = [
            [
                'order_sn' => $data['ordersn'],
                'package_number' => $data['package_number'],
                'shipping_document_type' => $data['shipping_document_type'],
                'tracking_number' => $data['tracking_no'],
            ],
        ];

        return self::sendRequest('post', '/api/v2/logistics/create_shipping_document', ['order_list' => $orderList]);
    }

    public static function getShippingDocumentResult($data)
    {
        $orderList = [
            [
                'order_sn' => $data['ordersn'],
                'package_number' => $data['package_number'],
                'shipping_document_type' => $data['shipping_document_type'],
            ],
        ];

        return self::sendRequest('post', '/api/v2/logistics/get_shipping_document_result', ['order_list' => $orderList]);
    }

    public static function downloadShippingDocument($data)
    {
        $fileContent = self::sendRequest('post', '/api/v2/logistics/download_shipping_document', [
            'shipping_document_type' => $data['shipping_document_type'],
            'order_list' => [
                [
                    'order_sn' => $data['ordersn'],
                    'package_number' => $data['package_number'],
                ],
            ],
        ]);

        try {
            //download file to storage
            $file_name = 'shopee/initial_'.Carbon::now()->format('YmdHis').'_'.$data['ordersn'].'.pdf';
            $file_path = storage_path('app/public/'.$file_name);
            file_put_contents($file_path, $fileContent);

            // * convert pdf version to 1.4 using ghostscript
            $new_file_name = 'shopee/'.Carbon::now()->format('YmdHis').'_'.$data['ordersn'].'.pdf';
            $new_file_path = storage_path('app/public/'.$new_file_name);
            $exec = 'gs -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dNOPAUSE -dQUIET -dBATCH -sOutputFile='.$new_file_path.' '.$file_path;
            shell_exec($exec);
            // ! delete initial file
            unlink($file_path);

            return $new_file_name;
        } catch (\Throwable $th) {

            return false;
        }
    }

    public static function downloadPDF($data)
    {
        $file = [
            'api_key' => '1234567890',
            'files' => array_filter($data),
        ];

        foreach ($file['files'] as $key => $value) {
            $file['files'][$key] = env('APP_URL') . '/storage/' . $value;
        }

        $response = Http::post('https://pdf-merger.groobok.com/api/pdf-merger', $file);

        $result = $response->json();

        if (isset($result['message']) && $result['message'] == 'success') {
            return $result['data']['file'];
        }

        return false;
    }

    private static function sendRequest($method, $path, $data = [])
    {
        $accessToken = self::getAccessToken();
        $partner_id = SHOPEE_LIVE_PARTNER_ID;
        $token = $accessToken['token'];
        $shop_id = $accessToken['shop_id'];
        $host = "https://partner.shopeemobile.com";
        $current_time = Carbon::now();
        $timestamp = $current_time->timestamp;

        $sign = self::get_sign($path, $partner_id, $timestamp, $token, $shop_id);

        $url = $host . $path . "?access_token=" . $token . "&partner_id=" . $partner_id . "&shop_id=" . $shop_id . "&sign=" . $sign . "&timestamp=" . $timestamp;

        $headers = [
            'Content-Type' => 'application/json',
        ];

        if($method == 'get') {
            $url = $host . $path;

            $headers = [
                'Content-Type' => 'application/json',
            ];

            $response = Http::withHeaders($headers)
                ->$method($url, array_merge([
                    'access_token' => $token,
                    'partner_id' => $partner_id,
                    'shop_id' => $shop_id,
                    'sign' => $sign,
                    'timestamp' => $timestamp,
                ], $data));

            return $response->body();
        }


        $response = Http::withHeaders($headers)->$method($url, $data);

        return $response->body();
    }

}
