<?php

namespace App\Http\Controllers;

use App\Imports\ShippingsImport;
use App\Models\AccessToken;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Monolog\Logger as MonologLogger;
use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;

class ShippingController extends Controller
{

    protected $dhl_access = "https://apitest.dhlecommerce.asia/rest/v1/OAuth/AccessToken";
    protected $dhl_label_url = "https://apitest.dhlecommerce.asia/rest/v2/Label";


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

        return $this->dhl_label($request->order_ids); // for dhl orders
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
            set_order_status($order, ORDER_STATUS_PACKING);
        }

        Shipping::upsert($data, ['order_id'], ['courier', 'shipment_number', 'created_by']);

    }

    /**
     * DHL label request
     * @param Request $request
     * @return boolean
     */
    public function dhl_label($order_ids)
    {

        $url = $this->dhl_label_url;

        // filter only selected order shipping not exists
        $orders_dhl = Order::doesntHave('shipping')->with([
            'customer', 'items', 'items.product', 'company',
            'company.access_tokens' => function ($query) {
                $query->where('type', 'dhl');
            }
        ])->whereIn('id', $order_ids)->where('courier_id', DHL_ID)->get();

        if (count($orders_dhl) == 0) {
            return 0;
        }

        $companies = Order::select('company_id')->distinct()->whereIn('id', $order_ids)->get()->pluck('company_id'); //temporary, need to check if all orders are from same company

        $access_tokens = AccessToken::with(['company'])->whereIn('company_id', $companies)->where('type', 'dhl')->get(); // DHL API access token, expires every 24 hours, could be refreshed every 12 hours

        foreach ($access_tokens as $access_token) {
            $data = [];
            $data['labelRequest']['hdr']['messageType'] = "LABEL"; //mandatory
            $data['labelRequest']['hdr']['messageDateTime'] = date('Y-m-d\TH:i:s') . '+08:00'; //mandatory
            $data['labelRequest']['hdr']['accessToken'] = $access_token->token; //mandatory
            $data['labelRequest']['hdr']['messageVersion'] = "1.4"; //mandatory
            $data['labelRequest']['hdr']['messageLanguage'] = "en"; //optional

            $data['labelRequest']['bd']['pickupAccountId'] = DHL_SOLD_PICKUP_ACCT[$access_token->company_id]; //mandatory
            $data['labelRequest']['bd']['soldToAccountId'] = DHL_SOLD_PICKUP_ACCT[$access_token->company_id]; //mandatory
            $data['labelRequest']['bd']['inlineLabelReturn'] = "Y"; //mandatory
            $data['labelRequest']['bd']['handoverMethod'] = 2; //optional - 01 for drop off, 02 for pickup

            $data['labelRequest']['bd']['pickupAddress']['name'] = $access_token->company->contact_person; //mandatory contact person name
            $data['labelRequest']['bd']['pickupAddress']['address1'] = $access_token->company->address; //mandatory company name
            $data['labelRequest']['bd']['pickupAddress']['address2'] = $access_token->company->address2 ?? null; //optional
            $data['labelRequest']['bd']['pickupAddress']['address3'] = $access_token->company->address3 ?? null; //optional
            $data['labelRequest']['bd']['pickupAddress']['city'] = $access_token->company->city ?? null; //mandatory for cross border, optional for domestic
            $data['labelRequest']['bd']['pickupAddress']['state'] = $access_token->company->state ?? null; //optional, dont incude "|" character
            $data['labelRequest']['bd']['pickupAddress']['postCode'] = $access_token->company->postcode ?? null; //optional
            $data['labelRequest']['bd']['pickupAddress']['country'] = $access_token->company->country; //mandatory ISO 2-char country code
            $data['labelRequest']['bd']['pickupAddress']['phone'] = $access_token->company->phone ?? null; //optional
            $data['labelRequest']['bd']['pickupAddress']['email'] = $access_token->company->email ?? null; //optional
            $data['labelRequest']['bd']['pickupAddress']['district'] = $access_token->company->district ?? null; //optional


            foreach ($companies as $company_id) {
                $order_count[$company_id] = 0;
            }

            foreach ($orders_dhl as $order) {

                $package_description = "";
                foreach ($order->items as $items) {
                    $package_description .= $items->product->name . ", ";
                }
                $package_description = rtrim($package_description, ", ");

                if ($order->company_id == $access_token->company_id) {
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['consigneeAddress']['name'] = $order->customer->name;
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['consigneeAddress']['address1'] = $order->customer->address;
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['consigneeAddress']['address2'] = $order->customer->address2 ?? null;
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['consigneeAddress']['address3'] = $order->customer->address3 ?? null;
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['consigneeAddress']['city'] = $order->customer->city;
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['consigneeAddress']['state'] = MY_STATES[$order->customer->state];
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['consigneeAddress']['country'] = "MY";
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['consigneeAddress']['district'] = $order->customer->district ?? null;
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['consigneeAddress']['postCode'] = $order->customer->postcode;
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['consigneeAddress']['phone'] = $order->customer->phone;
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['consigneeAddress']['email'] = $order->customer->email;
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['consigneeAddress']['idNumber'] = null;
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['consigneeAddress']['idType'] = null;

                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['shipmentID'] = shipment_num_format($order, $access_token); //order_num_format($order); //must not repeated in 90 days, Accepted special characters : ~ _ \ .
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['returnMode'] = "02"; //01: return to registered address, 02: return to pickup address (ad-hoc pickup only), 03: return to new address
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['deliveryConfirmationNo'] = null; //not used
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['packageDesc'] = substr($package_description, 0, 50); // required
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['totalWeight'] = 3000; // mandatory, optional if product code is PDR
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['totalWeightUOM'] = "G";
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['dimensionUOM'] = "cm"; //mandatory if height, length, width is not null
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['height'] = null; //mandatory if dimensionUOM is not null
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['length'] = null; //mandatory if dimensionUOM is not null
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['width'] = null; //mandatory if dimensionUOM is not null
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['customerReference1'] = null; //optional
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['customerReference2'] = null; //optional
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['productCode'] = "PDO"; //PDO: Parcel Domestic, PDR: Parcel Domestic Return, PDD: Parcel Domestic Document, PDD: Parcel Domestic Document Return
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['contentIndicator'] = null; //mandatory if product include lithium battery
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['codValue'] = $order->purchase_type == 1 ? $order->total_price / 100 : null; //optional
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['insuranceValue'] = null; //optional
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['freightCharge'] = null; //optional
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['totalValue'] = null; //optional for domestic
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['currency'] = "MYR"; //3-char currency code
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['remarks'] = $order->shipping_remarks; //optional
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['isMult'] = "false"; //true: multiple pieces, false: single piece
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]]['deliveryOption'] = "C"; //only C is supported
                }
                $order_count[$order->company_id]++;
            }

            $data['labelRequest']['bd']['label']['pageSize'] = "400x600";
            $data['labelRequest']['bd']['label']['format'] = "PDF";
            $data['labelRequest']['bd']['label']['layout'] = "1x1";

            $json = json_encode($data);

            $response = Http::withBody($json, 'application/json')->post($url);

            $this->add_shipping_details($orders_dhl, "dhl-ecommerce");
            $this->dhl_store($response);

        }

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
            Storage::put('public/labels/' . str_replace("\\", "_", $label->shipmentID) . '.pdf', base64_decode($label->content));

            //update tracking number
            Shipping::where('shipment_number', $label->shipmentID)->update(['tracking_number' => $label->deliveryConfirmationNo, 'attachment' => 'labels/' . str_replace("\\", "_", $label->shipmentID) . '.pdf']);
        }
    }

    public function download_cn(Request $request)
    {
        // return $request;
        $attachments = Shipping::select('attachment')->whereIn('order_id', $request->order_ids)->get();
        $attachments = $attachments->pluck('attachment')->toArray();

        $pdf = PDFMerger::init();
        foreach ($attachments as $attachment) {
            $pdf->addPDF(storage_path('app/public/' . $attachment), 'all');
        }

        $filename = 'CN_' . date('Ymd_His') . '.pdf';
        $pdf->merge();
        $pdf->save(public_path('generated_labels/' . $filename), 'file');
        //download
        // return response()->download(public_path('generated_labels/' . $filename)); //return $pdf->download($filename);
        return response()->json(['download_url' => '/generated_labels/' . $filename]);
    }

    public function download_cn_bucket(Request $request)
    {
        $orders = Order::select('id')->where('bucket_id', $request->bucket_id)
            ->where('status', ORDER_STATUS_PROCESSING)->get();
        $order_list = $orders->pluck('id')->toArray();

        $this->dhl_label($order_list);

        // return $request;
        // $attachments = Shipping::select('attachment')->get();
        // $attachments = $attachments->pluck('attachment')->toArray();

        // $pdf = PDFMerger::init();
        // foreach ($attachments as $attachment) {
        //     $pdf->addPDF(storage_path('app/public/' . $attachment), 'all');
        // }

        // $filename = 'CN_' . auth()->user()->id ?? 1 . '_' . date('YmdHis') . '.pdf';
        // $pdf->merge();
        // $pdf->save(public_path('generated_labels/' . $filename), 'file');
        // return response()->json(['download_url' => '/generated_labels/' . $filename]);
    }

    /**
     * Update tracking number to order.
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function upload_bulk_tracking(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt'
        ]);


        Excel::import(new ShippingsImport, $request->file);

        return back()->with('success', 'Shipping Numbers Imported Successfully');
    }

    /**
     * Update tracking number to order.
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update_tracking(Request $request)
    {
        $request->validate([
            'tracking_number' => 'required',
            'courier' => 'required',
            'order_id' => 'required',
            'shipment_date' => 'date',
        ]);

        Shipping::create([
            'order_id' => $request->order_id,
            'tracking_number' => $request->tracking_number,
            'courier' => $request->courier,
            'created_by' => auth()->user()->id ?? 1,
            // 'shipment_date' => $request->shipment_date,
        ]);

        set_order_status(Order::find($request->order_id), ORDER_STATUS_PACKING);

        return back()->with('success', 'Tracking Number Updated Successfully');
    }

    /**
     * Update bulk tracking number to order.
     * @param \Illuminate\Http\Request  $request
     * @return json
     */
    public function update_bulk_tracking(Request $request)
    {
        $request->validate([
            'company_id' => 'required',
            'created_by' => 'required|int',
            'trackings.*.tracking_number' => 'required',
            'trackings.*.courier' => 'required',
            'trackings.*.sales_id' => 'required',
        ]);
return $request;
        $orders = Order::whereIn('id', $request->order_id)->get();

        Shipping::create([
            'order_id' => $orders->id->where('sales_id', $request->sales_id)->where('company_id', $request->company_id),
            'tracking_number' => $request->tracking_number,
            'courier' => $request->courier,
            'created_by' => auth()->user()->id ?? 1,
            // 'shipment_date' => $request->shipment_date,
        ]);

        set_order_status(Order::find($request->order_id), ORDER_STATUS_PACKING);

        return response()->json(['success' => 'ok']);
    }

    /**
     * Update order to shipping on first shipping milestone.
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function first_milestone(Request $request)
    {
        $request->validate([
            'tracking_id' => 'required|exists:table,column',
        ]);

        $shipping = Shipping::with(['order'])->where('tracking_number', $request->tracking_id)->first();

        if (set_order_status($shipping->order, ORDER_STATUS_SHIPPING, "First Milestone from Phantom")) {
            return response()->json(['success' => 'ok']);
        } else {
            return response()->json(['error' => 'error']);
        }
    }

    /**
     * Update order to shipping on delivered milestone.
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delivered_milestone(Request $request)
    {
        $request->validate([
            'tracking_id' => 'required|exists:table,column',
        ]);

        $shipping = Shipping::with(['order'])->where('tracking_number', $request->tracking_id)->first();

        if (set_order_status($shipping->order, ORDER_STATUS_DELIVERED)) {
            return response()->json(['success' => 'ok']);
        } else {
            return response()->json(['error' => 'error']);
        }
    }

    /**
     * Update order to shipping on ongoing return milestone.
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function return_ongoing_milestone(Request $request)
    {
        $request->validate([
            'tracking_id' => 'required|exists:table,column',
        ]);

        $shipping = Shipping::with(['order'])->where('tracking_number', $request->tracking_id)->first();

        if (set_order_status($shipping->order, ORDER_STATUS_RETURN_SHIPPING)) {
            return response()->json(['success' => 'ok']);
        } else {
            return response()->json(['error' => 'error']);
        }
    }

    /**
     * Update order to shipping on returned milestone.
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function return_delivered_milestone(Request $request)
    {
        $request->validate([
            'tracking_id' => 'required|exists:table,column',
        ]);

        $shipping = Shipping::with(['order'])->where('tracking_number', $request->tracking_id)->first();

        if (set_order_status($shipping->order, ORDER_STATUS_RETURNED)) {
            return response()->json(['success' => 'ok']);
        } else {
            return response()->json(['error' => 'error']);
        }
    }
}
