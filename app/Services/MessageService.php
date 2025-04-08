<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MessageService
{
    public function send_tracking_data_to_qiscus($data)
    {
        $post_data = [
            "to" => $data['customer_tel'],
            "type" => "template",
            "template" => [
                "namespace" => "19a1824a_70c9_4025_be18_5c34a91a83",
                "name" => "onboarding_tracking_2",
                "language" => [
                    "policy" => "deterministic",
                    "code" => "ms"
                ],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => [
                            ["type" => "text", "text" => $data['customer_name']],
                            ["type" => "text", "text" => $data['product']],
                            ["type" => "text", "text" => $data['price']],
                            ["type" => "text", "text" => $data['tracking_number']],
                            ["type" => "text", "text" => "https://tracking.my/" . $data['courier_code'] . "/" . $data['tracking_number']],
                        ]
                    ]
                ]
            ]
        ];

        $url = 'https://omnichannel.qiscus.com/whatsapp/v1/jgmur-mcekka3f4629efg/6454/messages';

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Qiscus-App-Id: jgmur-mcekka3f4629efg',
            'Qiscus-Secret-Key: cfc1664d9133e55fc8c47a01a93af9a1'
        ]);

        $result = curl_exec($curl);

        if (curl_errno($curl)) {
            curl_close($curl);
            return ['status' => 'error', 'message' => curl_error($curl)];
        }

        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response = json_decode($result, true);
    }
}
