<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ShippingController;
use App\Models\AccessToken;
use App\Models\Company;
use App\Models\Order;
use App\Models\Shipping;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ShippingApiController extends ShippingController
{
    /**
     * DHL access token request, response and save to database, CRON job to run every 20 hours
     * @return void
     */
    public function dhl_generate_access_token($company_id = null)
    {
        $url = $this->dhl_access;

        $dhl_tokens = AccessToken::where('type', 'dhl');
        if($company_id){
            $dhl_tokens = $dhl_tokens->where('company_id', $company_id);
        }
        $dhl_tokens = $dhl_tokens->get();
        foreach ($dhl_tokens as $token) {

            $response = Http::get($url . "?clientId=" . $token->client_id . "&password=" . $token->client_secret)->json();

            if ($response['accessTokenResponse']['responseStatus']['code'] == 100000) {
                $data['token'] = $response['accessTokenResponse']['token'];
                $data['expires_at'] = date('Y-m-d H:i:s', strtotime('+' . $response['accessTokenResponse']['expires_in_seconds'] . ' seconds'));
                $token->update($data);
            }
        }
    }

    public function posmalaysia_generate_access_token($company = null)
    {
        $url = $this->posmalaysia_access;

        $posmalaysia_tokens = AccessToken::where('type', 'posmalaysia');
        if($company){
            $posmalaysia_tokens = $posmalaysia_tokens->where('company_id', $company);
        }
        $posmalaysia_tokens = $posmalaysia_tokens->get();

        foreach ($posmalaysia_tokens as $token) {
            // <?phprequire_once 'HTTP/Request2.php'; $request = new HTTP_Request2();$request->setUrl('https://gateway-usc.pos.com.my/security/connect/token');$request->setMethod(HTTP_Request2::METHOD_POST);'follow_redirects' => TRUE));$request->setHeader(array('Content-Type' => 'application/x-www-form-urlencoded'));$request->addPostParameter(array('client_id' => '64dda61cfa1b1b000ed9fb30','client_secret' => 'cpy70tObJYUXa+67Wtw4+nQ44JCcCKkowXN5RV/sIgE=','grant_type' => 'client_credentials','scope' => 'as2corporate.v2trackntracewebapijson.all as2corporate.tracking-event-list.all as2corporate.tracking-office-list.all as2corporate.tracking-reason-list.all as2poslaju.poslaju-poscode-coverage.all as01.gen-connote.all as01.generate-pl9-with-connote.all as2corporate.preacceptancessingle.all'));try {$response = $request->send();if ($response->getStatus() == 200) {    echo $response->getBody();}else {    echo 'Unexpected HTTP status: ' . $response->getStatus() . ' ' .    $response->getReasonPhrase();}}catch(HTTP_Request2_Exception $e) {echo 'Error: ' . $e->getMessage();}
            try {
                $response = Http::asForm()
                    ->post('https://gateway-usc.pos.com.my/security/connect/token', [
                        'client_id' => '64dda61cfa1b1b000ed9fb30',
                        'client_secret' => 'cpy70tObJYUXa+67Wtw4+nQ44JCcCKkowXN5RV/sIgE=',
                        'grant_type' => 'client_credentials',
                        'scope' => 'as2corporate.v2trackntracewebapijson.all as2corporate.tracking-event-list.all as2corporate.tracking-office-list.all as2corporate.tracking-reason-list.all as2poslaju.poslaju-poscode-coverage.all as01.gen-connote.all as01.generate-pl9-with-connote.all as2corporate.preacceptancessingle.all',
                    ]);

                if ($response->successful()) {
                    $data['token'] = $response['access_token'];
                    $data['expires_at'] = date('Y-m-d H:i:s', strtotime('+' . $response['expires_in'] . ' seconds'));
                    $token->update($data);
                } else {
                    echo 'Unexpected HTTP status: ' . $response->status() . ' ' . $response->reason();
                }
            } catch (Exception $e) {
                echo 'Error: ' . $e->getMessage();
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
        $additional_data = $request->additional_data;

        $order = Order::select('orders.id', 'couriers.code')
            ->where('sales_id', $sales_id)
            ->where('payment_type',22)
            // ->where('company_id', 2)
            ->join('couriers', 'orders.courier_id', '=', 'couriers.id')
            ->first();

        Shipping::updateOrCreate([
            'order_id' => $order->id,
            'tracking_number' => $request->tracking_number,
            'courier' => $order->code,
            'created_by' => auth()->user()->id ?? 1,
            'additional_data' => $additional_data,
            // 'ship_date' => $request->shipping_date,
        ]);

        set_order_status($order, ORDER_STATUS_PENDING_SHIPMENT);

        return response()->json([
            'message' => 'Tracking number updated'
        ], 200);
    }
}
