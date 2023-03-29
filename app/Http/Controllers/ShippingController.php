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

    protected $dhl_access = 'https://api.dhlecommerce.dhl.com/rest/v1/OAuth/AccessToken';
    protected $dhl_label_url = 'https://api.dhlecommerce.dhl.com/rest/v2/Label';

    protected $dhl_access_test = "https://apitest.dhlecommerce.asia/rest/v1/OAuth/AccessToken";
    protected $dhl_label_url_test = "https://apitest.dhlecommerce.asia/rest/v2/Label";


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
     * Check if order has multiple packages / parcels
     * @param Request $request
     * @return response
     */
    public function check_multiple_parcels(Request $request)
    {
        $orders = Order::with(['items'])->whereIn('id', $request->order_ids)->get();

        $multiple_parcels = false;
        $order_id = 0;
        foreach ($orders as $order) {
            $total_qty = 0;
            foreach ($order->items as $item) {
                $total_qty += $item->quantity;
            }
            if ($total_qty > MAXIMUM_QUANTITY_PER_BOX) {
                $multiple_parcels = true;
                $order_id = $order->id;
                break;
            }
        }

        return response()->json(['multiple_parcels' => $multiple_parcels, 'order_id' => $order_id]);
    }

    /**
     * DHL label request
     * @param Request $request
     * @return response
     */
    public function dhl_label($order_ids)
    {

        if(config('app.env') == 'production'){
            $url = $this->dhl_label_url;
        }
        else{
            $url = $this->dhl_label_url_test;
        }

        // filter only selected order shipping not exists
        $orders_dhl = Order::doesntHave('shippings')->with([
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

        $count = 0;

        foreach ($access_tokens as $access_token) {
            $data = [];

            $data['labelRequest']['hdr'] = [
                "messageType" => "LABEL",
                "messageDateTime" => date('Y-m-d\TH:i:s') . '+08:00',
                "accessToken" => $access_token->token,
                "messageVersion" => "1.4",
                "messageLanguage" => "en"
            ];

            $data['labelRequest']['bd'] = [
                'pickupAccountId' => DHL_SOLD_PICKUP_ACCT[$access_token->company_id], //mandatory
                'soldToAccountId' => DHL_SOLD_PICKUP_ACCT[$access_token->company_id], //mandatory
                'inlineLabelReturn' => "Y", //mandatory
                'handoverMethod' => 2, //optional - 01 for drop off, 02 for pickup
                'pickupAddress' => [
                    'name' => $access_token->company->contact_person, //mandatory contact person name
                    'address1' => $access_token->company->address, //mandatory company name
                    'address2' => $access_token->company->address2 ?? null, //optional
                    'address3' => $access_token->company->address3 ?? null, //optional
                    'city' => $access_token->company->city ?? null, //mandatory for cross border, optional for domestic
                    'state' => $access_token->company->state ?? null, //optional, dont incude "|" character
                    'postCode' => $access_token->company->postcode ?? null, //optional
                    'country' => $access_token->company->country, //mandatory ISO 2-char country code
                    'phone' => $access_token->company->phone ?? null, //optional
                    'email' => $access_token->company->email ?? null, //optional
                    'district' => $access_token->company->district ?? null, //optional
                ],
                'label' => [
                    'pageSize' => "400x600",
                    'format' => "PDF",
                    'layout' => "1x1",
                ],
            ];



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

                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]] = [
                        'consigneeAddress' => [
                            'companyName' => get_shipping_remarks($order),
                            'name' => $order->customer->name,
                            'address1' => $order->customer->address,
                            'address2' => $order->customer->address2 ?? null,
                            'address3' => $order->customer->address3 ?? null,
                            'city' => $order->customer->city,
                            'state' => MY_STATES[$order->customer->state],
                            'country' => "MY",
                            'district' => $order->customer->district ?? null,
                            'postCode' => $order->customer->postcode,
                            'phone' => $order->customer->phone,
                            'email' => $order->customer->email,
                            'idNumber' => null,
                            'idType' => null,
                        ],
                        'shipmentID' => shipment_num_format($order, $access_token), //order_num_format($order); //must not repeated in 90 days, Accepted special characters : ~ _ \ .
                        'returnMode' => "02", //01: return to registered address, 02: return to pickup address (ad-hoc pickup only), 03: return to new address
                        'deliveryConfirmationNo' => null, //not used
                        'packageDesc' => substr($this->package_description($order), 0, 50), // required
                        'totalWeight' => get_order_weight($order), // mandatory, optional if product code is PDR
                        'totalWeightUOM' => "G",
                        'dimensionUOM' => "cm", //mandatory if height, length, width is not null
                        'height' => null, //mandatory if dimensionUOM is not null
                        'length' => null, //mandatory if dimensionUOM is not null
                        'width' => null, //mandatory if dimensionUOM is not null
                        'customerReference1' => null, //optional
                        'customerReference2' => null, //optional
                        'productCode' => "PDO", //PDO: Parcel Domestic, PDR: Parcel Domestic Return, PDD: Parcel Domestic Document, PDD: Parcel Domestic Document Return
                        'contentIndicator' => null, //mandatory if product include lithium battery
                        'codValue' => $order->purchase_type == 1 ? $order->total_price / 100 : null, //optional
                        'insuranceValue' => null, //optional
                        'freightCharge' => null, //optional
                        'totalValue' => null, //optional for domestic
                        'currency' => "MYR", //3-char currency code
                        'remarks' => get_shipping_remarks($order), //optional
                        'deliveryOption' => "C", //only C is supported
                        'isMult' => "false", //true: multiple pieces, false: single piece
                    ];
                }
                $order_count[$order->company_id]++;
            }

            $json = json_encode($data);
            logger($json);
            $response = Http::withBody($json, 'application/json')->post($url);
            $dhl_store = $this->dhl_store($orders_dhl, $response);

            if($dhl_store != null){
                return response([
                    "all_fail" => implode(" . ",collect($dhl_store)->pluck("messageDetail")->toArray())
                ]);
            }
        }

        if (($failer = Order::doesntHave('shippings')->with([
            'customer', 'items', 'items.product', 'company',
            'company.access_tokens' => function ($query) {
                $query->where('type', 'dhl');
            }
        ])->whereIn('id', $order_ids)
            ->where('courier_id', DHL_ID)
            ->count()) > 0) {
            return response([
                "error" => $failer . " order fail to generate cn.",
                "all_fail"=> $orders_dhl->count() == $failer
            ]);
        }

        return true;
    }

    /**
     * DHL Label Single Order
     * @request $order
     * @return $response
     */
    public function dhl_label_single(Request $request)
    {
        $order = Order::with([
            'customer', 'items', 'items.product', 'company', 'company.access_tokens'
        ])->where('id', $request->order_id)->where('courier_id', DHL_ID)->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found or courier is not DHL Ecommerce, please check with admin',
            ], 400);
        }

        if(config('app.env') == 'production'){
            $url = $this->dhl_label_url;
        }
        else{
            $url = $this->dhl_label_url_test;
        }

        $access_token = AccessToken::with(['company'])->where('company_id', $order->company_id)->where('type', 'dhl')->first();

        $data = [
            'labelRequest' => [
                'hdr' => [
                    "messageType" => "LABEL",
                    "messageDateTime" => date('Y-m-d\TH:i:s') . '+08:00',
                    "accessToken" => $access_token->token,
                    "messageVersion" => "1.4",
                    "messageLanguage" => "en"
                ],
                'bd' => [
                    'shipmentItems' =>  [
                        0 => [
                            'consigneeAddress' => [
                                'companyName' => get_shipping_remarks($order),
                                'name' => $order->customer->name,
                                'address1' => $order->customer->address,
                                'address2' => $order->customer->address2 ?? null,
                                'address3' => $order->customer->address3 ?? null,
                                'city' => $order->customer->city,
                                'state' => MY_STATES[$order->customer->state],
                                'country' => "MY",
                                'district' => $order->customer->district ?? null,
                                'postCode' => $order->customer->postcode,
                                'phone' => $order->customer->phone,
                                'email' => $order->customer->email,
                                'idNumber' => null,
                                'idType' => null,
                            ],
                            'shipmentID' => shipment_num_format($order, $access_token), //order_num_format($order); //must not repeated in 90 days, Accepted special characters : ~ _ \ .
                            'returnMode' => "02", //01: return to registered address, 02: return to pickup address (ad-hoc pickup only), 03: return to new address
                            'deliveryConfirmationNo' => null, //not used
                            'packageDesc' => substr($this->package_description($order), 0, 50), // required
                            'totalWeight' => get_order_weight($order), // mandatory, optional if product code is PDR
                            'totalWeightUOM' => "G",
                            'dimensionUOM' => "cm", //mandatory if height, length, width is not null
                            'height' => null, //mandatory if dimensionUOM is not null
                            'length' => null, //mandatory if dimensionUOM is not null
                            'width' => null, //mandatory if dimensionUOM is not null
                            'customerReference1' => null, //optional
                            'customerReference2' => null, //optional
                            'productCode' => "PDO", //PDO: Parcel Domestic, PDR: Parcel Domestic Return, PDD: Parcel Domestic Document, PDD: Parcel Domestic Document Return
                            'contentIndicator' => null, //mandatory if product include lithium battery
                            'codValue' => $order->purchase_type == 1 ? $order->total_price / 100 : null, //optional
                            'insuranceValue' => null, //optional
                            'freightCharge' => null, //optional
                            'totalValue' => null, //optional for domestic
                            'currency' => "MYR", //3-char currency code
                            'remarks' => get_shipping_remarks($order), //optional
                            'deliveryOption' => "C", //only C is supported
                            'isMult' => "true", //true: multiple pieces, false: single piece
                        ],
                    ],
                    'pickupAccountId' => DHL_SOLD_PICKUP_ACCT[$access_token->company_id], //mandatory
                    'soldToAccountId' => DHL_SOLD_PICKUP_ACCT[$access_token->company_id], //mandatory
                    'inlineLabelReturn' => "Y", //mandatory
                    'handoverMethod' => 2, //optional - 01 for drop off, 02 for pickup
                    'pickupAddress' => [
                        'name' => $access_token->company->contact_person, //mandatory contact person name
                        'address1' => $access_token->company->address, //mandatory company name
                        'address2' => $access_token->company->address2 ?? null, //optional
                        'address3' => $access_token->company->address3 ?? null, //optional
                        'city' => $access_token->company->city ?? null, //mandatory for cross border, optional for domestic
                        'state' => $access_token->company->state ?? null, //optional, dont incude "|" character
                        'postCode' => $access_token->company->postcode ?? null, //optional
                        'country' => $access_token->company->country, //mandatory ISO 2-char country code
                        'phone' => $access_token->company->phone ?? null, //optional
                        'email' => $access_token->company->email ?? null, //optional
                        'district' => $access_token->company->district ?? null, //optional
                    ],
                    'label' => [
                        'pageSize' => "400x600",
                        'format' => "PDF",
                        'layout' => "1x1",
                    ],
                ],
            ],
        ];

        // all parcel pieces
        $remainCodAmmount = $order->purchase_type == 1 ? $order->total_price / 100 : null;
        for ($i = 0; $i < $request->parcel_count; $i++) {
            $codAmmount = ($remainCodAmmount > MAX_DHL_COD_PER_PARCEL) ? MAX_DHL_COD_PER_PARCEL : $remainCodAmmount;
            $data['labelRequest']['bd']['shipmentItems'][0]['shipmentPieces'][$i] = [
                'pieceID' => $i + 1,
                'announcedWeight' => [
                    'weight' => round($request->parcel_weight * 1000),
                    'unit' => 'G'
                ],
                'codAmount' => $codAmmount == 0 ? null : $codAmmount,
            ];
            $remainCodAmmount -= $codAmmount;
        }

        $data = json_encode($data);

        $response = Http::withBody($data, 'application/json')->post($url);

        $this->dhl_store_single($order, $response);

        return true;
    }

    /**
     * Store the response from DHL
     *
     * @param  object $orders, $json
     * @return \Illuminate\Http\Response
     */

    public function dhl_store($orders, $json)
    {
        $data = [];
        $tracking_no[] = [];
        $json = json_decode($json);

        foreach ($json->labelResponse->bd->labels ?? [] as $label) {
            if(isset($label->responseStatus)){
                if(isset($label->responseStatus->message)){
                    if($label->responseStatus->message != "SUCCESS"){
                        if(isset($label->responseStatus->messageDetails)){
                            return $label->responseStatus->messageDetails;
                        }
                    }
                }
            }

            $shipment_id = $label->shipmentID;
            // logger($shipment_id);
            $tracking_no[$shipment_id] = $label->deliveryConfirmationNo;
            $content[$shipment_id] = $label->content;
        }

        // logger($tracking_no);

        foreach ($orders as $order) {
            if (!isset($tracking_no[shipment_num_format($order)])) {
                continue;
            }

            if ($content[shipment_num_format($order)] == null) {
                continue;
            }

            $data[$order->id]['tracking_number'] = $tracking_no[shipment_num_format($order)];
            //if empty tracking number, remove from array and skip
            if (empty($data[$order->id]['tracking_number'])) {
                unset($data[$order->id]);
                continue;
            }
            $data[$order->id]['order_id'] = $order->id;
            $data[$order->id]['courier'] = "dhl-ecommerce";
            $data[$order->id]['shipment_number'] = shipment_num_format($order);
            $data[$order->id]['created_by'] = auth()->user()->id ?? 1;
            $data[$order->id]['attachment'] = 'labels/' . str_replace("\\", "_", shipment_num_format($order) . '.pdf');
            //store label to storage
            Storage::put('public/labels/' . str_replace("\\", "_", shipment_num_format($order)) . '.pdf', base64_decode($content[shipment_num_format($order)]));
            set_order_status($order, ORDER_STATUS_PACKING, "Shipping label generated by DHL");
        }

        Shipping::upsert($data, ['order_id'], ['courier', 'shipment_number', 'created_by']);
    }

    /**
     * Store the response from DHL (single)
     *
     * @param  object $order, $json
     * @return \Illuminate\Http\Response
     */
    public function dhl_store_single($order, $json)
    {
        $json = json_decode($json);
        $shipment_num = $json->labelResponse->bd->labels[0]->shipmentID;
        $i = 0;
        foreach ($json->labelResponse->bd->labels[0]->pieces as $piece) {
            $data[$i]['tracking_number'] = $piece->deliveryConfirmationNo;
            $data[$i]['order_id'] = $order->id;
            $data[$i]['courier'] = "dhl-ecommerce";
            $data[$i]['shipment_number'] = $shipment_num . '-' . $piece->shipmentPieceID;
            $data[$i]['created_by'] = auth()->user()->id ?? 1;
            $data[$i]['attachment'] = 'labels/' . str_replace("\\", "_", $shipment_num . '-' . $piece->shipmentPieceID . '.pdf');
            // store label to storage
            Storage::put('public/labels/' . str_replace("\\", "_", $shipment_num . '-' . $piece->shipmentPieceID) . '.pdf', base64_decode($piece->content));
            $i++;
        }
        set_order_status($order, ORDER_STATUS_PACKING, "Shipping label generated by DHL, multiple parcel");

        Shipping::upsert($data, ['order_id'], ['courier', 'shipment_number', 'created_by']);
    }

    public function download_cn(Request $request)
    {
        $attachments = Shipping::select('attachment')->whereIn('order_id', $request->order_ids)->get();
        $attachments = $attachments->pluck('attachment')->toArray();

        $pdf = PDFMerger::init();

        foreach ($attachments as $attachment) {
            if (!file_exists(storage_path('app/public/' . $attachment))) {
                continue;
            }

            if (!is_file(storage_path('app/public/' . $attachment))) {
                continue;
            }

            if(file_get_contents(storage_path('app/public/' . $attachment)) == ""){
                continue;
            }

            $pdf->addPDF(storage_path('app/public/' . $attachment));
        }

        $filename = 'CN_' . date('Ymd_His') . '.pdf';
        $pdf->merge();
        $pdf->save(public_path('generated_labels/' . $filename), 'file');
        //download
        return response()->json(['download_url' => '/generated_labels/' . $filename]);
    }

    public function download_cn_bucket(Request $request)
    {
        $orders = Order::select('id')->where('bucket_id', $request->bucket_id)
            ->where('status', ORDER_STATUS_PROCESSING)->get();
        $order_list = $orders->pluck('id')->toArray();

        $this->dhl_label($order_list);
    }

    /**
     * Update tracking number to order.
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function upload_bulk_tracking(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
            'company' => 'required|exists:companies,id|integer',
        ]);


        Excel::import(new ShippingsImport($request->company), $request->file);

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

    /**
     * Package description
     * @param collection $order
     * @return \Illuminate\Http\Response
     */
    public function package_description($order, $mult_cn = [])
    {
        $package_description = "";
        foreach ($order->items as $items) {
            if(empty($mult_cn)){ //if product only have one CN include all product desc
                $package_description .= $items->product->name . ", ";
            }else{
                $quantity = collect($mult_cn)
                    ->whereIn('order_item_id', $items['id'])
                    ->pluck('quantity')
                    ->values()
                    ->implode(',');
                if($quantity > 0){ //check if product in the parcel more than 0 only then included in desc else excluded
                    $package_description .= $items->product->name . ", ";
                }
            }
        }
        $package_description = rtrim($package_description, ", ");

        return $package_description;
    }

    /**
     * Multiple order generate CN
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate_cn_multiple(Request $request)
    {
        $order_id = $request->validate([
            'order_id' => 'required',
        ]);

        $array_data = ($request->input('cn_data'));

        $orders_dhl = Order::doesntHave('shippings')->with([
            'customer', 'items', 'items.product', 'company',
            'company.access_tokens' => function ($query) {
                $query->where('type', 'dhl');
            }
        ])->whereIn('id', $order_id)->where('courier_id', DHL_ID)->get();

        return $this->dhl_label_mult_cn($order_id, $array_data); // for dhl orders
    }

    /**
     * DHL Multiple CN for Single Order
     * @request $order
     * @return $response
     */
    public function dhl_label_mult_cn($order_id, $array_data)
    {
        $order = Order::with([
            'customer', 'items', 'items.product', 'company', 'company.access_tokens'
        ])->where('id', $order_id)->where('courier_id', DHL_ID)->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found or courier is not DHL Ecommerce, please check with admin',
            ], 400);
        }

        if(config('app.env') == 'production'){
            $url = $this->dhl_label_url;
        }
        else{
            $url = $this->dhl_label_url_test;
        }

        $access_token = AccessToken::with(['company'])->where('company_id', $order->company_id)->where('type', 'dhl')->first();
        // $access_token = AccessToken::with(['company'])->where('company_id', 3)->where('type', 'dhl')->first(); //testing
        $remainCodAmmount = $order->purchase_type == 1 ? $order->total_price : null;
        $data = [];
        $dhl_store = [];


        foreach ($array_data as $key => $cn) {
            //calculate COD amount
            $codAmmount = ($remainCodAmmount > MAX_DHL_COD_PER_PARCEL) ? MAX_DHL_COD_PER_PARCEL : $remainCodAmmount;
            $remainCodAmmount -= $codAmmount;

            $data = [
                'labelRequest' => [
                    'hdr' => [
                        "messageType" => "LABEL",
                        "messageDateTime" => date('Y-m-d\TH:i:s') . '+08:00',
                        "accessToken" => $access_token->token,
                        "messageVersion" => "1.4",
                        "messageLanguage" => "en"
                    ],
                    'bd' => [
                        'shipmentItems' =>  [
                            0 => [
                                'consigneeAddress' => [
                                    'companyName' => get_shipping_remarks($order, $cn), //will return desc based on modal value inserted e.g NLC[40]SH FOC[1]
                                    'name' => $order->customer->name,
                                    'address1' => $order->customer->address,
                                    'address2' => $order->company_id == 2 ? "HQ NO: 60122843214" : "-",
                                    'address3' => $order->company_id == 2 ? "HQ NO: 60122843214" : $order->sold_by,
                                    'city' => $order->customer->city,
                                    'state' => MY_STATES[$order->customer->state],
                                    'country' => "MY",
                                    'district' => $order->customer->district ?? null,
                                    'postCode' => $order->customer->postcode,
                                    'phone' => $order->customer->phone,
                                    'email' => $order->customer->email,
                                    'idNumber' => null,
                                    'idType' => null,
                                ],
                                'shipmentID' => shipment_num_format_mult($order, $key, $access_token), //order_num_format($order); //must not repeated in 90 days, Accepted special characters : ~ _ \ .
                                'returnMode' => "02", //01: return to registered address, 02: return to pickup address (ad-hoc pickup only), 03: return to new address
                                'deliveryConfirmationNo' => null, //not used
                                'packageDesc' => substr($this->package_description($order, $cn), 0, 50), // required
                                'totalWeight' => get_order_weight($order, $cn), // mandatory, optional if product code is PDR
                                'totalWeightUOM' => "G",
                                'dimensionUOM' => "cm", //mandatory if height, length, width is not null
                                'height' => null, //mandatory if dimensionUOM is not null
                                'length' => null, //mandatory if dimensionUOM is not null
                                'width' => null, //mandatory if dimensionUOM is not null
                                'customerReference1' => null, //optional
                                'customerReference2' => null, //optional
                                'productCode' => "PDO", //PDO: Parcel Domestic, PDR: Parcel Domestic Return, PDD: Parcel Domestic Document, PDD: Parcel Domestic Document Return
                                'contentIndicator' => null, //mandatory if product include lithium battery
                                'codValue' =>$codAmmount == 0 ? null : $codAmmount/100, //optional
                                'insuranceValue' => null, //optional
                                'freightCharge' => null, //optional
                                'totalValue' => null, //optional for domestic
                                'currency' => "MYR", //3-char currency code
                                'remarks' => get_shipping_remarks($order, $cn), //optional
                                'deliveryOption' => "C", //only C is supported
                                'isMult' => "false", //true: multiple pieces, false: single piece
                            ],
                        ],
                        'pickupAccountId' => DHL_SOLD_PICKUP_ACCT[$access_token->company_id], //mandatory
                        'soldToAccountId' => DHL_SOLD_PICKUP_ACCT[$access_token->company_id], //mandatory
                        'inlineLabelReturn' => "Y", //mandatory
                        'handoverMethod' => 2, //optional - 01 for drop off, 02 for pickup
                        'pickupAddress' => [
                            'name' => $access_token->company->contact_person, //mandatory contact person name
                            'address1' => $access_token->company->address, //mandatory company name
                            'address2' => $access_token->company->address2 ?? null, //optional
                            'address3' => $access_token->company->address3 ?? null, //optional
                            'city' => $access_token->company->city ?? null, //mandatory for cross border, optional for domestic
                            'state' => $access_token->company->state ?? null, //optional, dont incude "|" character
                            'postCode' => $access_token->company->postcode ?? null, //optional
                            'country' => $access_token->company->country, //mandatory ISO 2-char country code
                            'phone' => $access_token->company->phone ?? null, //optional
                            'email' => $access_token->company->email ?? null, //optional
                            'district' => $access_token->company->district ?? null, //optional
                        ],
                        'label' => [
                            'pageSize' => "400x600",
                            'format' => "PDF",
                            'layout' => "1x1",
                        ],
                    ],
                ],
            ];

            $data = json_encode($data);
            logger($data);
            $response = Http::withBody($data, 'application/json')->post($url);
            // $dhl_store = ['test'];
            $dhl_store = $this->dhl_store_for_mult($order, $response, $key);
        }
        if(!empty($dhl_store)){
            return response()->json([
                'status' => 'error',
                'message' => 'Some CN cannot be generated.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'CN generated successfully.'
        ]);;
    }

    /**
     * Store the response from DHL
     *
     * @param  object $orders, $json
     * @return \Illuminate\Http\Response
     */

    public function dhl_store_for_mult($order, $json, $num_cn)
    {
        $data = [];
        $tracking_no[] = [];
        $json = json_decode($json);

        foreach ($json->labelResponse->bd->labels ?? [] as $label) {
            if(isset($label->responseStatus)){
                if(isset($label->responseStatus->message)){
                    if($label->responseStatus->message != "SUCCESS"){
                        if(isset($label->responseStatus->messageDetails)){
                            return $label->responseStatus->messageDetails;
                        }
                    }
                }
            }

            $shipment_id = $label->shipmentID;
            $tracking_no[$shipment_id] = $label->deliveryConfirmationNo;
            $content[$shipment_id] = $label->content;
        }

        $data[$order->id]['tracking_number'] = $tracking_no[shipment_num_format_mult($order, $num_cn)];

        //if empty tracking number, remove from array and skip
        if (empty($data[$order->id]['tracking_number'])) {
            unset($data[$order->id]);
        }
        $data[$order->id]['order_id'] = $order->id;
        $data[$order->id]['courier'] = "dhl-ecommerce";
        $data[$order->id]['shipment_number'] = shipment_num_format_mult($order, $num_cn);
        $data[$order->id]['created_by'] = auth()->user()->id ?? 1;
        $data[$order->id]['attachment'] = 'labels/' . str_replace("\\", "_", shipment_num_format_mult($order, $num_cn) . '.pdf');

        // store label to storage
        Storage::put('public/labels/' . str_replace("\\", "_", shipment_num_format_mult($order, $num_cn)) . '.pdf', base64_decode($content[shipment_num_format_mult($order, $num_cn)]));

        set_order_status($order, ORDER_STATUS_PACKING, "Shipping label generated by DHL");

        Shipping::upsert($data, ['order_id'], ['courier', 'shipment_number', 'created_by']);
    }

}
