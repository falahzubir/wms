<?php

namespace App\Http\Controllers;

use App\Models\AccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\api\ShippingApiController;
use App\Models\Company;

class AccessTokenController extends Controller
{

    /**
     * Show Access Token for company
     * @param Request $request
     */
    public function show(Request $request, $company_id)
    {
        $tokens = AccessToken::where('company_id', $company_id)->get()->toArray();
        return response()->json([
            'data' => $tokens,
            'company' => Company::find($company_id),
        ], 200);
    }

    /**
     * Update Access Token for company
     * @param Request $request
     */
    public function update(Request $request, $company_id)
    {
        $request->validate([
            'dhl_client_id' => 'nullable',
            'dhl_client_secret' => 'nullable',
            'posmalaysia_subscribtion_code' => 'regex:/^\S*$/u',
        ]);

        $data = [
            'client_id' => $request->dhl_client_id,
            'client_secret' => $request->dhl_client_secret
        ];

        AccessToken::where('company_id', $company_id)->where('type', 'dhl')->update($data);

        if($request->sync == "on"){
            $shipping_controller = new ShippingApiController;
            $shipping_controller->dhl_generate_access_token($company_id);
        }

        if($request->posmalaysia_subscribtion_code != ""){
           Company::where('id', $company_id)->update(['posmalaysia_subscribtion_code' => $request->posmalaysia_subscribtion_code]);
        }

        return response()->json([
            'message' => 'Courier setting for company updated successfully.'
        ], 200);
    }

}
