<?php

namespace App\Http\Traits;

use App\Models\AccessToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;


Trait NinjaVanInternationalTrait
{

    public static function checkAccessToken($company)
    {
        // Define the URL for the token request
        $ninjaVanTokenUrl = 'https://api-sandbox.ninjavan.co/sg/2.0/oauth/access_token';
        
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
            'client_id' => '03aa4db4a0404009a1141ce2658a1118',
            'client_secret' => '4f51570f6868451bb9f4f836177ffd8d',
            'grant_type' => 'client_credentials'
        ]);

        if ($responseGet->status() == 200) {
            $newTokenData = $responseGet->json();
            
            if (!$ninjaVanToken) {
                $ninjaVanToken = new AccessToken();
                $ninjaVanToken->type = 'nv-int';
                $ninjaVanToken->company_id = $company;
            }
            
            $ninjaVanToken->client_id = '03aa4db4a0404009a1141ce2658a1118';
            $ninjaVanToken->client_secret = '4f51570f6868451bb9f4f836177ffd8d';
            $ninjaVanToken->name = 'NinjaVan International Authentication';
            $ninjaVanToken->token = $newTokenData['access_token'];
            $ninjaVanToken->expires_at = Carbon::createFromTimestamp($newTokenData['expires'])->toDateTimeString(); // Convert to datetime format
            $ninjaVanToken->save();

            return $ninjaVanToken;
        } else {
            // Handle the error response
            return response()->json($responseGet->json())->pluck('message');
        }
    }

    public static function generateCN($json, $company)
    {
        // Retrieve the valid access token
        $accessToken = self::checkAccessToken($company);

        // $ninjaVanUrl = app()->environment() != 'production' ? 'https://api.ninjavan.co/MY/4.2/orders' : 'https://api-sandbox.ninjavan.co/SG/4.2/orders';
        $ninjaVanUrl = "https://api-sandbox.ninjavan.co/SG/4.2/orders";

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken->token
        ])
        ->withBody($json, 'application/json')
        ->post($ninjaVanUrl);

        if($response->status() == 401){
            return response()->json();
        }

        return $response->json();
    }

    public static function generateWayBill($trackingNumber, $company)
    {
        // Retrieve the valid access token
        $accessToken = self::checkAccessToken($company);

        // Define the URL for the waybill report request
        $ninjaVanUrl = "https://api-sandbox.ninjavan.co/sg/2.0/reports/waybill";

        // Make the API request with the valid token
        $response = Http::withHeaders([
            'Accept' => 'application/pdf',
            'Authorization' => 'Bearer ' . $accessToken->token
        ])->get($ninjaVanUrl, [
            'tid' => $trackingNumber,
            'hide_shipper_details' => 1
        ]);

        if ($response->status() == 401) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($response->status() != 200) {
            return response()->json(['error' => 'Failed to retrieve waybill'], $response->status());
        }

        return $response;
    }
}
