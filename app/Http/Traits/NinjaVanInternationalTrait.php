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
        if ($ninjaVanToken && Carbon::createFromTimestamp($ninjaVanToken->expires_at)->isFuture()) {
            return $ninjaVanToken;
        }

        // Request a new token since the current one is expired or doesn't exist
        $responseGet = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post($ninjaVanTokenUrl, [
            'client_id' => '4c7b667e220b424bb684a3b91bad9ce4',
            'client_secret' => '5dbe0ca634264f07ae9de7fb1990467d',
            'grant_type' => 'client_credentials'
        ]);

        if ($responseGet->status() == 200) {
            $newTokenData = $responseGet->json();
            
            if (!$ninjaVanToken) {
                $ninjaVanToken = new AccessToken();
                $ninjaVanToken->type = 'nv-int';
                $ninjaVanToken->company_id = $company;
            }
            
            $ninjaVanToken->client_id = '5dbe0ca634264f07ae9de7fb1990467d';
            $ninjaVanToken->client_secret = '5dbe0ca634264f07ae9de7fb1990467d';
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

    public static function createNinjaVanOrder($json, $company)
    {
        // Retrieve the valid access token
        $accessToken = self::checkAccessToken($company);

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

    public static function generateWayBill($trackingNumber, $company)
    {
        // Retrieve the valid access token
        $accessToken = self::checkAccessToken($company);

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
