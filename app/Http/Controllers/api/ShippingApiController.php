<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ShippingController;
use App\Models\AccessToken;
use App\Models\Company;
use App\Models\Order;
use App\Models\Shipping;
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

    /**
     * Send shipping information back to BOS
     * @return void
     */
    public function send_shipping_info()
    {
        $orders = Order::with(['shippings', 'company'])
            ->whereHas('shippings', function ($query) {
                $query->where('status', 1)->whereNotNull('tracking_number')->where('is_send', 0);
            });

        if (config('app.env') == 'local') {
            $orders = $orders->where('company_id', 3);
        }
        $orders = $orders->get();

        $array_of_companies = $orders->pluck('company_id')->unique()->toArray();

        $companies = Company::whereIn('id', $array_of_companies)->get();

        foreach ($companies as $company) {
            $count[$company->id] = 0;
        }

        foreach ($orders as $order) {
            $data[$order->company_id]['trackings'][$count[$order->company_id]]['sales_id'] = $order->sales_id;
            $data[$order->company_id]['trackings'][$count[$order->company_id]]['tracking_number'] = $order->shippings->first()->tracking_number;
            $data[$order->company_id]['trackings'][$count[$order->company_id]]['courier_id'] = $order->courier_id;
            $data[$order->company_id]['trackings'][$count[$order->company_id]]['shipping_date'] = $order->shippings->first()->created_at->format('Y-m-d');

            $count[$order->company_id]++;
        }

        foreach ($companies as $company) {

            $res = Http::post($company->url . '/api/update_tracking', $data[$company->id]);

            if (json_decode($res)->status == "success") {

                $order_ids = $orders->where('company_id', $company->id)->pluck('id')->toArray();

                Shipping::whereIn('order_id', $order_ids)->update(['is_send' => 1]);
            }
        }
    }

    /*
    * Update Shopee Tracking
    * @param Request $request
    * @return json
    */
    public function update_shopee_tracking(Request $request)
    {
        $sales_id = $request->sales_id;
        $tracking_no = $request->tracking_number;
        $shipping_date = $request->shipping_date;

        $order = Order::select('orders.id', 'couriers.code')
            ->where('sales_id', $sales_id)
            ->where('company_id', 2)
            ->join('couriers', 'orders.courier_id', '=', 'couriers.id')
            ->first();

        Shipping::updateOrCreate([
            'order_id' => $order->id,
            'tracking_number' => $request->tracking_number,
            'courier' => $order->code,
            'created_by' => auth()->user()->id ?? 1,
            // 'ship_date' => $request->shipping_date,
        ]);

        set_order_status($order, ORDER_STATUS_PACKING);

        return response()->json([
            'message' => 'Tracking number updated'
        ], 200);
    }
}
