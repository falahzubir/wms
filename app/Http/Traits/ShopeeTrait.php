<?php

namespace App\Http\Traits;

use App\Models\AccessToken;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

trait ShopeeTrait
{
    public static function getAccessToken($company_id)
    {
        $date = date('Y-m-d H:i:s');

        $accessToken = AccessToken::where('type', 'shopee')->where('company_id', $company_id)->first();

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
                $new_token = self::refreshToken($company_id);
                return [
                    'token' => $new_token,
                    'shop_id' => $shop_id
                ];
            }
        }
    }

    public static function refreshToken($company_id)
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
        $access_token = AccessToken::where('type', 'shopee')->where('company_id', $company_id)->first();
        $access_token->token = $response['token']['token'];
        $access_token->expires_at = $response['token']['expired'];

        $access_token->save();

        return $response['token']['token'];

    }

    public static function getShopeeKey($company_id)
    {
        $shopee = AccessToken::where('type', 'shopee')->where('company_id', $company_id)->first();

        if($shopee){
            $shopee_partner_id = $shopee->client_id;
            $shopee_partner_key = $shopee->client_secret;

            return [
                'shopee_partner_id' => $shopee_partner_id,
                'shopee_partner_key' => $shopee_partner_key
            ];
        }
    }

    public static function get_sign($path, $partner_id, $timestamp, $access_token, $shop_id, $company_id)
    {
        $shopee = self::getShopeeKey($company_id);
        $partnerKey = $shopee['shopee_partner_key'];

        $tokenBaseString = $partner_id . $path . $timestamp . $access_token .  $shop_id;
        $sign = hash_hmac('sha256', $tokenBaseString, $partnerKey);

        return $sign;
    }

    public static function getOrderDetail($order_sn, $company_id)
    {
        $data = [
            'order_sn_list' => $order_sn,
            'company_id' => $company_id,
            'response_optional_fields' => "buyer_user_id,buyer_username,recipient_address,item_list,total_amount,shipping_carrier,estimated_shipping_fee,pay_time,package_list,fulfillment_flag",
        ];

        return self::sendRequest('get', '/api/v2/order/get_order_detail', $data);
    }

    public static function getShippingParameter($order_sn, $company_id)
    {
        $data = [
            'order_sn' => $order_sn,
            'company_id' => $company_id,
        ];

        return self::sendRequest('get', '/api/v2/logistics/get_shipping_parameter', $data);
    }

    public static function getTrackingNumber($order_sn, $company_id)
    {
        $data = [
            'order_sn' => $order_sn,
            'company_id' => $company_id,
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

    public static function shipOrder($order_sn, $pickup_time_id, $company_id)
    {
        $data = [
            'order_sn' => $order_sn,
            'company_id' => $company_id,
            'pickup' => [
                'address_id' => 200007694,
                'pickup_time_id' => $pickup_time_id,
                'tracking_number' => '',
            ],
        ];

        return self::sendRequest('post', '/api/v2/logistics/ship_order', $data);
    }

    public static function getShippingDocumentParameter($data, $company_id)
    {
        $orderList = [
            [
                'order_sn' => $data['ordersn'],
                'package_number' => $data['package_number'],
                'company_id' => $company_id,
            ],
        ];
        return self::sendRequest('post', '/api/v2/logistics/get_shipping_document_parameter', ['order_list' => $orderList]);
    }

    public static function createShippingDocument($data, $company_id)
    {
        $orderList = [
            [
                'order_sn' => $data['ordersn'],
                'package_number' => $data['package_number'],
                'shipping_document_type' => $data['shipping_document_type'],
                'tracking_number' => $data['tracking_no'],
                'company_id' => $company_id,
            ],
        ];

        return self::sendRequest('post', '/api/v2/logistics/create_shipping_document', ['order_list' => $orderList]);
    }

    public static function getShippingDocumentResult($data, $company_id)
    {
        $orderList = [
            [
                'order_sn' => $data['ordersn'],
                'package_number' => $data['package_number'],
                'shipping_document_type' => $data['shipping_document_type'],
                'company_id' => $company_id,
            ],
        ];

        return self::sendRequest('post', '/api/v2/logistics/get_shipping_document_result', ['order_list' => $orderList]);
    }

    public static function downloadShippingDocument($data,$company_id)
    {
        $fileContent = self::sendRequest('post', '/api/v2/logistics/download_shipping_document', [
            'shipping_document_type' => $data['shipping_document_type'],
            'order_list' => [
                [
                    'order_sn' => $data['ordersn'],
                    'package_number' => $data['package_number'],
                    'company_id' => $company_id,
                ],
            ],
        ]);

        try {
            //download file to storage
            $file_name = 'shopee/initial_'.Carbon::now()->format('YmdHis').'_'.$data['ordersn'].'.pdf';
            $file_path = storage_path('app/public/'.$file_name);
            // file_put_contents($file_path, $fileContent);
            Storage::put('public/'.$file_name, $fileContent);

            return $file_name;
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
        // Ensure `order_list` exists and has at least one element
        if (in_array($path,array('/api/v2/order/get_order_detail','/api/v2/logistics/get_tracking_number'))) {
            $company_id = $data['company_id'];
        } else if (isset($data['order_list'][0]['company_id'])) {
            $company_id = $data['order_list'][0]['company_id']; // Extract company_id
        } else {
            throw new \Exception('Company ID is missing or improperly structured.');
        }
        
        $accessToken = self::getAccessToken($company_id);
        $shopee = self::getShopeeKey($company_id);
        $partner_id = $shopee['shopee_partner_id'];
        $token = $accessToken['token'];
        $shop_id = $accessToken['shop_id'];
        $host = "https://partner.shopeemobile.com";
        $current_time = Carbon::now();
        $timestamp = $current_time->timestamp;

        $sign = self::get_sign($path, $partner_id, $timestamp, $token, $shop_id, $company_id);

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
