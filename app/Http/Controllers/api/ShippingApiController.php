<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ShippingController;
use App\Models\AccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShippingApiController extends ShippingController
{
    /**
     * DHL access token request, response and save to database, CRON job to run every 20 hours
     * @return void
     */
    public function dhl_generate_access_token()
    {
        $url = $this->dhl_access;
        $dhl_tokens = AccessToken::where('type', 'dhl')->get();

        foreach ($dhl_tokens as $token) {

            $response = Http::get($url . "?clientId=" . $token->client_id . "&password=" . $token->client_secret)->json();

            if ($response['accessTokenResponse']['responseStatus']['code'] == 100000) {
                $data['token'] = $response['accessTokenResponse']['token'];
                $data['expires_at'] = date('Y-m-d H:i:s', strtotime('+' . $response['accessTokenResponse']['expires_in_seconds'] . ' seconds'));
                $token->update($data);
            }
        }
    }
}
