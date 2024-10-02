<?php

namespace App\Http\Traits;

use App\Models\AccessToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

Trait NinjaVanInternationalTrait
{
    public static function checkAccessToken($company)
    {
        // Define the URL for the token request
        $ninjaVanTokenUrl = app()->environment() == 'production' ? 'https://api.ninjavan.co/my/2.0/oauth/access_token' : 'https://api-sandbox.ninjavan.co/sg/2.0/oauth/access_token';

        // Retrieve the existing access token record from the database
        $ninjaVanToken = AccessToken::where('type', 'nv-int')
            ->where('company_id', $company)
            ->first();

        // Check if the token exists and is not expired
        if ($ninjaVanToken && Carbon::parse($ninjaVanToken->expires_at)->isFuture()) {
            return $ninjaVanToken;
        }

        // Request a new token since the current one is expired or doesn't exist
        $responseGet = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post($ninjaVanTokenUrl, [
            'client_id' => app()->environment() == 'production' ? 'f98f1ed2ce0c48a7bdbd7273e42c07fe' : 'b59d818fb3594a46bc6970da2f503a2f',
            'client_secret' => app()->environment() == 'production' ? 'b67e92e050834beca6d4814518eb3c13' : '115a4c5af7204f2692114e7499f4c46c',
            'grant_type' => 'client_credentials',
            'scope' => 'CORE_GET_AWB DASH_MANAGE_ORDER DASH_GET_ORDER ALL_ACCESS INTERNAL_SERVICE'
        ]);

        if ($responseGet->status() == 200) {
            $newTokenData = $responseGet->json();

            if (!$ninjaVanToken) {
                $ninjaVanToken = new AccessToken();
                $ninjaVanToken->type = 'nv-int';
                $ninjaVanToken->company_id = $company;
            }

            $ninjaVanToken->client_id = app()->environment() == 'production' ? 'f98f1ed2ce0c48a7bdbd7273e42c07fe' : 'b59d818fb3594a46bc6970da2f503a2f';
            $ninjaVanToken->client_secret = app()->environment() == 'production' ? 'b67e92e050834beca6d4814518eb3c13' : '115a4c5af7204f2692114e7499f4c46c';
            $ninjaVanToken->name = 'NinjaVan International Authentication';
            $ninjaVanToken->token = $newTokenData['access_token'];
            $ninjaVanToken->expires_at = Carbon::now()->addSeconds($newTokenData['expires_in'])->toDateTimeString(); // Convert to datetime format
            $ninjaVanToken->save();

            return $ninjaVanToken;
        } else {
            // Handle the error response by throwing an exception
            throw new \Exception('Failed to retrieve NinjaVan access token: ' . $responseGet->body());
        }
    }

    public static function createNinjaVanOrder($json, $accessToken)
    {
        $ninjaVanUrl = app()->environment() == 'production' ? 'https://api.ninjavan.co/my/4.2/orders' : 'https://api-sandbox.ninjavan.co/sg/4.2/orders';

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken->token
        ])
        ->withBody($json, 'application/json')
        ->post($ninjaVanUrl);

        if($response->status() == 401){
            throw new \Exception('Unauthorized: ' . $response->body());
        }

        return $response->json();
    }

    public static function generateWayBill($trackingNumber, $accessToken)
    {
        // Define the URL for the waybill report request
        $ninjaVanUrl = app()->environment() == 'production' ? 'https://api.ninjavan.co/my/2.0/reports/waybill' : 'https://api-sandbox.ninjavan.co/sg/2.0/reports/waybill';

        // Make the API request with the valid token
        $response = Http::withHeaders([
            'Accept' => 'application/pdf',
            'Authorization' => 'Bearer ' . $accessToken->token
        ])->get($ninjaVanUrl, [
            'tid' => $trackingNumber,
            'hide_shipper_details' => 1
        ]);

        if ($response->status() == 401) {
            throw new \Exception('Unauthorized: ' . $response->body());
        }

        if ($response->status() != 200) {
            throw new \Exception('Failed to retrieve waybill: ' . $response->body());
        }

        return $response;
    }
}
