<?php

namespace App\Http\Traits;

use App\Models\AccessToken;
use Illuminate\Support\Facades\Http;

Trait EmziExpressTrait
{

    public static function checkAccessToken($company)
    {
        $emziExpressUrl = app()->environment() != 'production' ? 'https://emziexpress.groobok.com' : 'https://api.emziexpress.com/v1';
        $emziExpress = AccessToken::where('type', 'emzi-express')->where('company_id', $company)->first();
        $responseCheck = Http::withHeaders([
            'Authorization' => 'Bearer ' . $emziExpress->token,
        ])->post($emziExpressUrl . '/api/check-authenticate');
        if ($responseCheck->status() == 401) {

            $responseGet = Http::withHeaders([
                'Accept' => 'application/json',
            ])->post($emziExpressUrl . '/api/authenticate', [
                'email' => $emziExpress->additional_data->email,
                'password' => 'password'
            ]);

            if ($responseGet->status() == 200) {
                $emziExpress->token = $responseGet->json()['token'];
                $emziExpress->save();

                return $emziExpress;
            }
            else{
                return response()->json()->pluck('message');
            }
        }

        return $emziExpress;
    }

    public static function generateCN($token,$json)
    {
        $emziExpressUrl = app()->environment() != 'production' ? 'https://emziexpress.groobok.com' : 'https://api.emziexpress.com/v1';

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])
        ->withBody($json, 'application/json')
        ->post($emziExpressUrl . '/api/shipment/create');

        if($response->status() == 401){
            return response()->json()->pluck('message');
        }

        return $response->json();
    }
}
