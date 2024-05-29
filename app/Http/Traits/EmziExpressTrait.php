<?php

namespace App\Http\Traits;

use App\Models\AccessToken;
use Illuminate\Support\Facades\Http;

Trait EmziExpressTrait
{

    public static function checkAccessToken($company)
    {
        $emziExpressUrlStaging = 'http://127.0.0.1:8000';
        $emziExpressUrlProduction = 'https://api.emziexpress.com/v1';

        $emziExpress = AccessToken::where('type', 'emzi-express')->where('company_id', $company)->first();
        $responseCheck = Http::withHeaders([
            'Authorization' => 'Bearer ' . $emziExpress->token,
        ])->post($emziExpressUrlStaging . '/api/check-authenticate');
        if ($responseCheck->status() == 401) {

            $responseGet = Http::withHeaders([
                'Accept' => 'application/json',
            ])->post($emziExpressUrlStaging . '/api/authenticate', [
                'email' => $emziExpress->additional_data->email,
                'password' => 'password'
            ]);

            if ($responseGet->status() == 200) {
                $emziExpress->token = $responseGet->json()['token'];
                $emziExpress->save();
            }
        }

        return $emziExpress;
    }

    public static function generateCN($token,$json)
    {
        $emziExpressUrlStaging = 'http://127.0.0.1:8000';
        $emziExpressUrlProduction = 'https://api.emziexpress.com/v1';

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])
        ->withBody($json, 'application/json')
        ->post($emziExpressUrlStaging . '/api/shipment/create');

        if($response->status() == 401){
            return response()->json()->pluck('message');
        }

        return $response->json();
    }
}
