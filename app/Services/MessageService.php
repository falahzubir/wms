<?php

namespace App\Services;

use App\Models\ThirdPartyRequest;
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

        $this->sandboxAppId = "afcjg-kuq0gbmcmlqgsxh";
        $this->sandboxSecretKey = "97a4f88d37ba0f4b3e2f0dc4b0fd9866";

        $this->baseUrl = "https://omnichannel.qiscus.com/whatsapp/v1";
    }

    private function getUrl()
    {
        if (app()->environment(['local', 'development'])) {
            return "{$this->baseUrl}/{$this->sandboxAppId}/6526/messages";
        }

        return "{$this->baseUrl}/{$this->appId}/6454/messages";
    }

    /**
     * API Template For Qiscus 
     * Just provide the parameters that needs
     */
    protected function sendToQiscus($to, $templateName, $namespace, $parameters, $language = 'ms')
    {
        $url = $this->getUrl();

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

        if (app()->environment(['local', 'development'])) {
            $headers = [
                'Content-Type: application/json',
                'Qiscus-App-Id: ' . $this->sandboxAppId,
                'Qiscus-Secret-Key: ' . $this->sandboxSecretKey,
            ];
        } else {
            $headers = [
                'Content-Type: application/json',
                'Qiscus-App-Id: ' . $this->appId,
                'Qiscus-Secret-Key: ' . $this->secretKey,
            ];
        }

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

        // Save to third party request table
        ThirdPartyRequest::create([
            'parameters'   => json_encode($request),
            'response'     => $result,
            'status_code'  => $httpCode,
            'url'          => $url,
            'method'       => 'POST',
            'requested_at' => now(),
        ]);

        return json_decode($result, true);
    }

    /**
     * For sending tracking number message 
     */
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

        return $this->sendToQiscus($data['customer_tel'], 'onboarding_trackingnumber_2', '19a1824a_70c9_4025_be18_5cc34aa91a83', $parameters);
    }

    /**
     * For sending out for delivery message 
     */
    public function sendOutForDeliveryMessage($data)
    {
        $parameters = [
            $data['customer_name']
        ];

        if (app()->environment(['local', 'development'])) {
            return $this->sendToQiscus($data['customer_tel'], 'out_for_devlivery', '6616bb93_d895_41e7_8bbe_2c528219e56e', $parameters); // Sandbox
        } else {
            return $this->sendToQiscus($data['customer_tel'], 'onboarding_parcelofd_2', '19a1824a_70c9_4025_be18_5cc34aa91a83', $parameters); // Live
        }
    }

    /**
     * For sending delivered message 
     */
    public function sendDeliveredMessage($data)
    {
        $parameters = [
            $data['customer_name'] 
        ];

        if (app()->environment(['local', 'development'])) {
            return $this->sendToQiscus($data['customer_tel'], 'out_for_devlivery', '6616bb93_d895_41e7_8bbe_2c528219e56e', $parameters); // Sandbox
        } else {
            return $this->sendToQiscus($data['customer_tel'], 'onboarding_orderreceived_2', '19a1824a_70c9_4025_be18_5cc34aa91a83', $parameters); // Live
        }
    }

}
