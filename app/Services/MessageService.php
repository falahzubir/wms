<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MessageService
{
    protected $appId;
    protected $secretKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->appId = "jgmur-mcekka3f4629efg";
        $this->secretKey = "cfc1664d9133e55fc8c47a01a93af9a1";
        $this->baseUrl = "https://omnichannel.qiscus.com/whatsapp/v1";
    }

    protected function sendToQiscus($to, $templateName, $namespace, $parameters, $language = 'ms')
    {
        $url = "{$this->baseUrl}/{$this->appId}/6454/messages";

        $request = [
            "to" => $to,
            "type" => "template",
            "template" => [
                "namespace" => $namespace,
                "name" => $templateName,
                "language" => [
                    "policy" => "deterministic",
                    "code" => $language,
                ],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => array_map(function ($param) {
                            return [
                                "type" => "text",
                                "text" => $param
                            ];
                        }, $parameters)
                    ]
                ]
            ]
        ];

        $headers = [
            'Content-Type: application/json',
            'Qiscus-App-Id: ' . $this->appId,
            'Qiscus-Secret-Key: ' . $this->secretKey,
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => json_encode($request),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_TIMEOUT => 30,
        ]);

        $result = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return json_decode($result, true);
    }

    public function sendTrackingMessage($data)
    {
        $tracking_url = "https://tracking.my/" . $data['courier_code'] . "/" . $data['tracking_number'];

        $parameters = [
            $data['customer_name'],
            $data['product'],
            $data['price'],
            $data['tracking_number'],
            $tracking_url,
        ];

        return $this->sendToQiscus($data['customer_tel'], 'onboarding_tracking_2', '19a1824a_70c9_4025_be18_5c34a91a83', $parameters);
    }

    public function sendDeliveredMessage($data)
    {
        $parameters = [
            $data['customer_name']
        ];

        return $this->sendToQiscus($data['customer_tel'], 'order_receive_onboarding_1', '191824а_70c9_4025_be18_5cc34a91a83', $parameters);
    }
    
}
