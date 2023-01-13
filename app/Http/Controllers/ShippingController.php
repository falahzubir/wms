<?php

namespace App\Http\Controllers;

use App\Models\AccessToken;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class ShippingController extends Controller
{

    private $dhl_access = "https://apitest.dhlecommerce.asia/rest/v1/OAuth/AccessToken";
    private $dhl_label = "https://apitest.dhlecommerce.asia/rest/v2/Label";


    /**
     * Check CN Company same or not
     * @param Request $request
     * @return boolean
     */
    public function check_cn_company(Request $request)
    {
        $orders = Order::select('company_id')->distinct()->whereIn('id', $request->order_ids)->get();
        return count($orders);
    }

    /**
     * Request CN number
     * @param Request $request
     * @return json
     */
    public function request_cn(Request $request)
    {
        $data = $request->validate([
            'order_ids' => 'required',
        ]);

        $data['created_by'] = auth()->user()->id ?? 1;

        // OrderLog::create($data);

        return $this->dhl_label($request); // for dhl orders
    }

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
     * Add shipping details to order
     * @return void
     */
    public function add_shipping_details($orders, $courier)
    {
        foreach ($orders as $order) {
            $data[$order->id]['order_id'] = $order->id;
            $data[$order->id]['courier'] = $courier;
            $data[$order->id]['shipment_number'] = shipment_num_format($order);
            $data[$order->id]['created_by'] = auth()->user()->id ?? 1;
        }

        Shipping::upsert($data, ['order_id'], ['courier', 'shipment_number', 'created_by']);
    }

    /**
     * DHL label request
     * @param Request $request
     * @return boolean
     */
    public function dhl_label($request)
    {

        $url = $this->dhl_label;

        $orders = Order::with(['customer', 'items', 'items.product'])
            ->whereIn('id', $request->order_ids)->get();

        $company = Company::find($orders[0]->company_id); //temporary, need to check if all orders are from same company

        $access_token = AccessToken::where('company_id', $company->id)->where('type', 'dhl')->first(); // DHL API access token, expires every 24 hours, could be refreshed every 12 hours
        $dhl_pickup_account = "5264574522"; // DHL pickup account number
        $dhl_sold_to_account = "5264574522"; // DHL sold to account number

        $data['labelRequest']['hdr']['messageType'] = "LABEL"; //mandatory
        $data['labelRequest']['hdr']['messageDateTime'] = date('Y-m-d\TH:i:s') . '+08:00'; //mandatory
        $data['labelRequest']['hdr']['accessToken'] = $access_token->token; //mandatory
        $data['labelRequest']['hdr']['messageVersion'] = "1.4"; //mandatory
        $data['labelRequest']['hdr']['messageLanguage'] = "en"; //optional

        $data['labelRequest']['bd']['customerAccountId'] = null; //optional
        $data['labelRequest']['bd']['pickupAccountId'] = $dhl_pickup_account; //mandatory
        $data['labelRequest']['bd']['soldToAccountId'] = $dhl_sold_to_account; //mandatory
        $data['labelRequest']['bd']['pickupDateTime'] = date('Y-m-d\T16:i:s') . '+08:00'; //optional
        // $data['labelRequest']['bd']['consolidatedLabelRequired'] = "y"; //optional
        $data['labelRequest']['bd']['inlineLabelReturn'] = "Y"; //optional - Y for get shipping label, U for get shipping label url
        $data['labelRequest']['bd']['handoverMethod'] = 2; //optional - 01 for drop off, 02 for pickup

        // $data['labelRequest']['bd']['pickupAddress']['companyName'] = $company->name ?? null; //optional
        $data['labelRequest']['bd']['pickupAddress']['name'] = $company->contact_person; //mandatory contact person name
        $data['labelRequest']['bd']['pickupAddress']['address1'] = $company->address; //mandatory
        $data['labelRequest']['bd']['pickupAddress']['address2'] = $company->address2 ?? null; //optional
        $data['labelRequest']['bd']['pickupAddress']['address3'] = $company->address3 ?? null; //optional
        $data['labelRequest']['bd']['pickupAddress']['city'] = $company->city ?? null; //mandatory for cross border, optional for domestic
        $data['labelRequest']['bd']['pickupAddress']['state'] = $company->state ?? null; //optional, dont incude "|" character
        $data['labelRequest']['bd']['pickupAddress']['country'] = $company->country; //mandatory ISO 2-char country code
        $data['labelRequest']['bd']['pickupAddress']['postCode'] = $company->postcode ?? null; //optional
        $data['labelRequest']['bd']['pickupAddress']['phone'] = $company->phone ?? null; //optional
        $data['labelRequest']['bd']['pickupAddress']['email'] = $company->email ?? null; //optional
        $data['labelRequest']['bd']['pickupAddress']['district'] = $company->district ?? null; //optional

        $data['labelRequest']['bd']['shipperAddress']['name'] = "JEFFRY";
        $data['labelRequest']['bd']['shipperAddress']['address1'] = "DHL eCommerce";
        $data['labelRequest']['bd']['shipperAddress']['address2'] = "No 3 Jalan PPU 1";
        $data['labelRequest']['bd']['shipperAddress']['address3'] = "Taman Perindustrian Puchong Utama";
        $data['labelRequest']['bd']['shipperAddress']['city'] = "Puchong";
        $data['labelRequest']['bd']['shipperAddress']['state'] = "Selangor";
        $data['labelRequest']['bd']['shipperAddress']['country'] = "MY";
        $data['labelRequest']['bd']['shipperAddress']['postCode'] = "47100";
        $data['labelRequest']['bd']['shipperAddress']['phone'] = "0123456789";
        $data['labelRequest']['bd']['shipperAddress']['email'] = null;
        $data['labelRequest']['bd']['shipperAddress']['district'] = null;

        $order_count = 0;

        foreach ($orders as $order) {
            $package_description = "";
            foreach ($order->items as $items) {
                $package_description .= $items->product->name . ", ";
            }
            $package_description = rtrim($package_description, ", ");

            $data['labelRequest']['bd']['shipmentItems'][$order_count]['consigneeAddress']['name'] = $order->customer->name;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['consigneeAddress']['address1'] = $order->customer->address;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['consigneeAddress']['address2'] = $order->customer->address2 ?? null;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['consigneeAddress']['address3'] = $order->customer->address3 ?? null;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['consigneeAddress']['city'] = $order->customer->city;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['consigneeAddress']['state'] = MY_STATES[$order->customer->state];
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['consigneeAddress']['country'] = "MY";
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['consigneeAddress']['district'] = $order->customer->district ?? null;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['consigneeAddress']['postCode'] = $order->customer->postcode;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['consigneeAddress']['phone'] = $order->customer->phone;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['consigneeAddress']['email'] = $order->customer->email;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['consigneeAddress']['idNumber'] = null;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['consigneeAddress']['idType'] = null;

            //return address, only used if return mode 03

            $data['labelRequest']['bd']['shipmentItems'][$order_count]['returnAddress']['companyName'] = $company->name ?? null;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['returnAddress']['name'] = $company->contact_person;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['returnAddress']['address1'] = $company->address;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['returnAddress']['address2'] = $company->address2 ?? null;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['returnAddress']['address3'] = $company->address3 ?? null;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['returnAddress']['city'] = $company->city ?? null;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['returnAddress']['state'] = $company->state ?? null;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['returnAddress']['district'] = $company->district ?? null;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['returnAddress']['country'] = $company->country;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['returnAddress']['postCode'] = $company->postcode;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['returnAddress']['phone'] = $company->phone ?? null;
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['returnAddress']['email'] = $company->email ?? null;

            $data['labelRequest']['bd']['shipmentItems'][$order_count]['shipmentID'] = shipment_num_format($order); //order_num_format($order); //must not repeated in 90 days, Accepted special characters : ~ _ \ .
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['returnMode'] = "02"; //01: return to registered address, 02: return to pickup address (ad-hoc pickup only), 03: return to new address
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['deliveryConfirmationNo'] = null; //not used
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['packageDesc'] = substr($package_description, 0, 50); // required
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['totalWeight'] = 3000; // mandatory, optional if product code is PDR
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['totalWeightUOM'] = "G";
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['dimensionUOM'] = "cm"; //mandatory if height, length, width is not null
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['height'] = null; //mandatory if dimensionUOM is not null
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['length'] = null; //mandatory if dimensionUOM is not null
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['width'] = null; //mandatory if dimensionUOM is not null
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['customerReference1'] = null; //optional
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['customerReference2'] = null; //optional
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['productCode'] = "PDO"; //PDO: Parcel Domestic, PDR: Parcel Domestic Return, PDD: Parcel Domestic Document, PDD: Parcel Domestic Document Return
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['contentIndicator'] = null; //mandatory if product include lithium battery
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['codValue'] = $order->purchase_type == 1 ? $order->total_price/100 : null; //optional
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['insuranceValue'] = null; //optional
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['freightCharge'] = null; //optional
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['totalValue'] = null; //optional for domestic
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['currency'] = "MYR"; //3-char currency code
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['remarks'] = $order->shipping_remarks; //optional
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['isMult'] = "false"; //true: multiple pieces, false: single piece
            $data['labelRequest']['bd']['shipmentItems'][$order_count]['deliveryOption'] = "C"; //only C is supported

            //only for multiple pieces
            /*
            $i = 0;
            foreach ($order->items as $item) {
                // logger($item);
                $data['labelRequest']['bd']['shipmentItems'][$order_count]['shipmentPieces'][$i]['pieceID'] = $i+1; // cant have leading zero, will be appended after shipmentId
                $data['labelRequest']['bd']['shipmentItems'][$order_count]['shipmentPieces'][$i]['announcedWeight']['weight'] = $item->product->weight;
                $data['labelRequest']['bd']['shipmentItems'][$order_count]['shipmentPieces'][$i]['announcedWeight']['unit'] = "G";
                $data['labelRequest']['bd']['shipmentItems'][$order_count]['shipmentPieces'][$i]['codAmount'] = $item->total_price;
                $data['labelRequest']['bd']['shipmentItems'][$order_count]['shipmentPieces'][$i]['insuranceAmount'] = null;
                $data['labelRequest']['bd']['shipmentItems'][$order_count]['shipmentPieces'][$i]['billingReference1'] = null;
                $data['labelRequest']['bd']['shipmentItems'][$order_count]['shipmentPieces'][$i]['billingReference2'] = null;
                $data['labelRequest']['bd']['shipmentItems'][$order_count]['shipmentPieces'][$i]['pieceDescription'] = $item->product->name;

                $i++;
            }
            */
            $order_count++;
        }

        // logger($data['labelRequest']['bd']['shipmentItems'][$order_count]['shipmentPieces']);
        $data['labelRequest']['bd']['label']['pageSize'] = "400x600";
        $data['labelRequest']['bd']['label']['format'] = "PDF";
        $data['labelRequest']['bd']['label']['layout'] = "1x1";

        $json = json_encode($data);
        // logger($json);
        // return 1;
        $response = Http::withBody($json, 'application/json')->post($url);

        $this->add_shipping_details($orders, "dhl-ecommerce");
        $this->dhl_store($response);

        return true;
    }

    /**
     * Store the response from DHL
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function dhl_store($json)
    {

        $json = json_decode($json);
        foreach ($json->labelResponse->bd->labels as $label) {

            //store label to storage
            Storage::put('public/labels/' . $label->shipmentID . '.pdf', base64_decode($label->content));

            //update tracking number
            Shipping::where('shipment_number', $label->shipmentID)->update(['tracking_number' => $label->deliveryConfirmationNo, 'attachment' => 'storage/labels/' . $label->shipmentID . '.pdf']);
        }

    }
}
