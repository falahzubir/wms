<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Company;
use App\Models\OrderLog;
use App\Models\Shipping;
use App\Models\OrderItem;
use Illuminate\Log\Logger;
use App\Models\AccessToken;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Traits\ShopeeTrait;
use App\Http\Traits\TiktokTrait;
use App\Imports\ShippingsImport;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelPdf\Facades\Pdf;
use Illuminate\Support\Facades\Log;
// use Webklex\PDFMerger\Facades\PDFMergerFacade as PDFMerger;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use Monolog\Logger as MonologLogger;
use App\Http\Traits\EmziExpressTrait;
use Illuminate\Support\Facades\Storage;
use App\Models\ShippingDocumentTemplate;
use Karriere\PdfMerge\PdfMerge as PDFMerger;
use App\Http\Controllers\api\ShippingApiController;
use App\Models\Product;
use App\Models\WeightCategory;
use App\Models\GroupStateList;
use App\Models\ShippingCost;
use App\Models\ShippingProduct;

class ShippingController extends Controller
{

    protected $dhl_access;
    protected $dhl_label_url;
    protected $dhl_cancel_url;
    protected $dhl_reprint_url;
    protected $posmalaysia_access;
    protected $posmalaysia_generate_connote;
    protected $posmalaysia_generate_pl9;
    protected $posmalaysia_download_connote;

    /**
     * Constructor
     */
    public function __construct()
    {
        $dhl_access_live = 'https://api.dhlecommerce.dhl.com/rest/v1/OAuth/AccessToken';
        $dhl_label_url_live = 'https://api.dhlecommerce.dhl.com/rest/v2/Label';
        $dhl_cancel_url_live = 'https://api.dhlecommerce.dhl.com/rest/v2/Label/Delete';
        $dhl_reprint_url_live = 'https://api.dhlecommerce.dhl.com/rest/v2/Label/Reprint';
        $posmalaysia_generate_connote_live = 'https://gateway-usc.pos.com.my/as01/gen-connote/v1/api/GConnote';
        $posmalaysia_generate_pl9_live = 'https://gateway-usc.pos.com.my/as01/generate-pl9-with-connote/v1/api/GPL9C';
        $posmalaysia_download_connote_live = 'https://gateway-usc.pos.com.my/as2corporate/preacceptancessingle/v1/Tracking.PreAcceptance.WebApi/api/PreAcceptancesSingle';

        $dhl_access_test = "https://apitest.dhlecommerce.asia/rest/v1/OAuth/AccessToken";
        $dhl_label_url_test = "https://apitest.dhlecommerce.asia/rest/v2/Label";
        $dhl_cancel_url_test = "https://apitest.dhlecommerce.asia/rest/v2/Label/Delete";
        $dhl_reprint_url_test = "https://apitest.dhlecommerce.asia/rest/v2/Label/Reprint";
        $posmalaysia_generate_connote_test = 'https://gateway-usc.pos.com.my/staging/as01/gen-connote/v1/api/GConnote';
        $posmalaysia_generate_pl9_test = 'https://gateway-usc.pos.com.my/staging/as01/generate-pl9-with-connote/v1/api/GPL9C';
        $posmalaysia_download_connote_test = 'https://gateway-usc.pos.com.my/staging/as2corporate/preacceptancessingle/v1/Tracking.PreAcceptance.WebApi/api/PreAcceptancesSingle';

        $this->dhl_access = config('app.env') == 'production' ? $dhl_access_live : $dhl_access_test;
        $this->dhl_label_url = config('app.env') == 'production' ? $dhl_label_url_live : $dhl_label_url_test;
        $this->dhl_cancel_url = config('app.env') == 'production' ? $dhl_cancel_url_live : $dhl_cancel_url_test;
        $this->dhl_reprint_url = config('app.env') == 'production' ? $dhl_reprint_url_live : $dhl_reprint_url_test;

        $this->posmalaysia_access = 'https://gateway-usc.pos.com.my/security/connect/token'; // same for test and live
        $this->posmalaysia_generate_connote = config('app.env') == 'production' ? $posmalaysia_generate_connote_live : $posmalaysia_generate_connote_test;
        $this->posmalaysia_generate_pl9 = config('app.env') == 'production' ? $posmalaysia_generate_pl9_live : $posmalaysia_generate_pl9_test;
        $this->posmalaysia_download_connote = config('app.env') == 'production' ? $posmalaysia_download_connote_live : $posmalaysia_download_connote_test;
    }

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
        set_time_limit(180);

        $data = $request->validate([
            'order_ids' => 'required',
        ]);

        $data['created_by'] = auth()->user()->id ?? 1;

        switch ($request->input('type')):
            case ('dhl-ecommerce'):
                return $this->dhl_label($request->order_ids);
                break;
            case ('shopee'):
                return $this->generateShopeeCN($request->order_ids);
                break;
            case ('tiktok'):
                return $this->generateTiktokCN($request->order_ids);
                break;
            case ('posmalaysia'):
                return $this->posmalaysia_cn($request->order_ids);
                break;
            case ('ninjavan'):
                return $this->ninjavan_cn($request->order_ids);
                break;
            case ('emzi-express'):
                return $this->emzi_express_cn($request->order_ids);
                break;
            default:
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid courier type',
                ], 400);
        endswitch;
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

        $url = $this->dhl_label_url;

        // filter only selected order shipping not exists
        $orders_dhl = Order::doesntHave('shippings')->with([
            'customer',
            'items',
            'items.product',
            'company',
            'company.access_tokens' => function ($query) {
                $query->where('type', 'dhl');
            }
        ])->whereIn('id', $order_ids)->where('courier_id', DHL_ID)->get();
        if (count($orders_dhl) == 0) {
            return 0;
        }

        $companies = Order::select('company_id')->distinct()->whereIn('id', $order_ids)->get()->pluck('company_id'); //temporary, need to check if all orders are from same company

        //check first token expiry date
        $shipingApiController = new ShippingApiController();
        $access_tokens = $shipingApiController->checkExpiryTokenDHL($companies, true);
        // $access_tokens = AccessToken::with(['company'])->whereIn('company_id', $companies)->where('type', 'dhl')->get(); // DHL API access token, expires every 24 hours, could be refreshed every 12 hours
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
                'pickupAccountId' => $access_token->additional_data->dhl_pickup_account, //mandatory
                'soldToAccountId' => $access_token->additional_data->dhl_sold_to_account, //mandatory
                'inlineLabelReturn' => "Y", //mandatory
                'handoverMethod' => 1, //optional - 01 for drop off, 02 for pickup
                'pickupAddress' => [
                    'name' => substr($access_token->company->contact_person, 0, 50), //mandatory contact person name
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

                $total_price = $order->total_price > 0 ? $order->total_price / 100 : null;

                if ($order->company_id == $access_token->company_id) {

                    $second_phone_num = '';
                    //if ED
                    if ($order->company_id == 2) {
                        //If secondary phone number existed
                        if ($order->customer->phone_2 != null) {
                            $second_phone_num = $order->customer->phone_2 . " (HQ NO: 60122843214)";
                        }
                        //if not existed
                        else {
                            $second_phone_num = "(HQ NO: 60122843214)";
                        }
                    } else {//if EH
                        //If secondary phone number existed
                        if ($order->customer->phone_2 != null) {
                            $second_phone_num = $order->customer->phone_2 . ' (PIC: ' . $order->sold_by . ')';
                        }
                        //if not existed
                        else {
                            $second_phone_num = '(PIC: ' . $order->sold_by . ')';
                        }
                    }
                    $data['labelRequest']['bd']['shipmentItems'][$order_count[$order->company_id]] = [
                        'consigneeAddress' => [
                            // 'companyName' => get_shipping_remarks($order),
                            // 'name' => $order->customer->name,
                            'name' => substr($order->customer->name, 0, 30),
                            'address1' => $order->customer->address,
                            // 'address2' => $order->company_id == 2 ? "HQ NO: 60122843214" : "-",
                            'address2' => "-",
                            // 'address3' => $order->company_id == 2 ? "HQ NO: 60122843214" : $order->sold_by,
                            'address3' => $second_phone_num,
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
                        'returnMode' => "01", //01: return to registered address, 02: return to pickup address (ad-hoc pickup only), 03: return to new address
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
                        'codValue' => $order->purchase_type == 1 ? $total_price : null, //optional
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

            $response = Http::withBody($json, 'application/json')->post($url);

            $dhl_store = $this->dhl_store($orders_dhl, $response);

            if ($dhl_store != null) {

                $dhl_store_content = $dhl_store->getContent();
                $decode_store_content = json_decode($dhl_store_content, true);
                return response([
                    // "all_fail" => implode(" . ", collect($dhl_store)->pluck("messageDetail")->toArray())
                    "all_fail" => $decode_store_content
                ]);
            }
        }

        if (
            ($failer = Order::doesntHave('shippings')->with([
                'customer',
                'items',
                'items.product',
                'company',
                'company.access_tokens' => function ($query) {
                    $query->where('type', 'dhl');
                }
            ])->whereIn('id', $order_ids)
                ->where('courier_id', DHL_ID)
                ->count()) > 0
        ) {
            return response([
                "error" => $failer . " order fail to generate cn.",
                "all_fail" => $orders_dhl->count() == $failer
            ]);
        }

        return response([
            "success" => $orders_dhl->count() . " order(s) cn generated successfully."
        ]);
    }

    /**
     * DHL Label Single Order
     * @request $order
     * @return $response
     */
    public function dhl_label_single($order_id, $arr_data)
    {
        $order = Order::with([
            'customer',
            'items',
            'items.product',
            'company',
            'company.access_tokens'
        ])->where('id', $order_id)->where('courier_id', DHL_ID)->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found or courier is not DHL Ecommerce, please check with admin',
            ], 400);
        }

        $url = $this->dhl_label_url;

        $shipingApiController = new ShippingApiController();
        $access_token = $shipingApiController->checkExpiryTokenDHL([$order->company_id]);

        $remainCodAmmount = $order->purchase_type == 1 ? $order->total_price / 100 : null;

        if (count($arr_data) > 1) {
            $mult = "true";
        } else {
            $mult = "false";
        }

        $company_name = ($order->operational_model_id == OP_BLAST_ID && $mult) ? "EMZI BLAST" : $access_token->company->name;
        $pickup_account = ($order->operational_model_id == OP_BLAST_ID && $mult) ? $access_token->additional_data->dhl_pickup_account_blast : $access_token->additional_data->dhl_pickup_account;
        $soldto_account = ($order->operational_model_id == OP_BLAST_ID && $mult) ? $access_token->additional_data->dhl_sold_to_account_blast : $access_token->additional_data->dhl_sold_to_account;

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
                    'shipmentItems' => [
                        0 => [
                            'consigneeAddress' => [
                                // 'companyName' => get_shipping_remarks($order),
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
                            'shipmentID' => shipment_num_format($order), //order_num_format($order); //must not repeated in 90 days, Accepted special characters : ~ _ \ .
                            'returnMode' => "01", //01: return to registered address, 02: return to pickup address (ad-hoc pickup only), 03: return to new address
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
                            'codValue' => $codAmmount = $order->purchase_type == 1 ? $order->total_price / 100 : null, //optional
                            'insuranceValue' => null, //optional
                            'freightCharge' => null, //optional
                            'totalValue' => null, //optional for domestic
                            'currency' => "MYR", //3-char currency code
                            'remarks' => get_shipping_remarks($order), //optional
                            'deliveryOption' => "C", //only C is supported
                            'isMult' => $mult, //true: multiple pieces, false: single piece
                        ],
                    ],
                    'pickupAccountId' => $pickup_account, //mandatory
                    'soldToAccountId' => $soldto_account, //mandatory
                    'inlineLabelReturn' => "Y", //mandatory
                    'handoverMethod' => 1, //optional - 01 for drop off, 02 for pickup
                    'pickupAddress' => [
                        'name' => substr($company_name, 0, 50), //mandatory contact person name
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
        $remainCodAmmount = $order->purchase_type == 1 ? $order->total_price : null;
        foreach ($arr_data as $key => $cn) {
            $product_list[] = $this->generate_multiple_product_description($cn);
            $codAmmount = ($remainCodAmmount > MAX_DHL_COD_PER_PARCEL) ? MAX_DHL_COD_PER_PARCEL : $remainCodAmmount;
            $data['labelRequest']['bd']['shipmentItems'][0]['shipmentPieces'][$key] = [
                'pieceID' => $key + 1,
                'announcedWeight' => [
                    'weight' => get_order_weight($order, $cn),
                    'unit' => 'G'
                ],
                'codAmount' => $codAmmount == 0 ? null : $codAmmount / 100,
            ];
            $remainCodAmmount -= $codAmmount;
        }

        $data = json_encode($data);

        $response = Http::withBody($data, 'application/json')->post($url);
        $this->dhl_store_single($order, $response, $product_list);

        return response([
            'status' => 'success',
            'message' => 'CN generated successfully',
        ]);
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
            if (isset($label->responseStatus)) {
                if (isset($label->responseStatus->message)) {
                    if ($label->responseStatus->message != "SUCCESS") {
                        if (isset($label->responseStatus->messageDetails)) {

                            Log::error('DHL Error: ' . $label->shipmentID);
                            return response([
                                'success' => false,
                                'message' => $label->responseStatus->messageDetails[0]->messageDetail . '. Shipment ID: ' . $label->shipmentID
                            ]);
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

            // $product_list = $this->generate_product_description($order->id);

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
            $data[$order->id]['attachment'] = 'labels/' . shipment_num_format($order) . '.pdf';
            // $data[$order->id]['packing_attachment'] = $product_list;
            //store label to storage
            Storage::put('public/labels/' . shipment_num_format($order) . '.pdf', base64_decode($content[shipment_num_format($order)]));
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
    public function dhl_store_single($order, $json, $product_descs)
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
            $data[$i]['attachment'] = 'labels/' . $shipment_num . '-' . $piece->shipmentPieceID . '.pdf';
            $data[$i]['packing_attachment'] = $product_descs[$i];
            // store label to storage
            if (Storage::exists('public/labels/' . $shipment_num . '-' . $piece->shipmentPieceID . '.pdf')) {
                Storage::delete('public/labels/' . $shipment_num . '-' . $piece->shipmentPieceID . '.pdf');
            }
            Storage::put('public/labels/' . $shipment_num . '-' . $piece->shipmentPieceID . '.pdf', base64_decode($piece->content));
            $i++;
        }
        Shipping::upsert($data, ['order_id'], ['courier', 'shipment_number', 'created_by']);
        set_order_status($order, ORDER_STATUS_PACKING, "Shipping label generated by DHL, multiple parcel");
    }

    public function download_cn(Request $request)
    {
        $sorted_order_id = $this->sort_order_to_download($request->order_ids);

        $attachments = Shipping::select('attachment', 'packing_attachment', "order_id")->active()->whereIn('order_id', $sorted_order_id)->get()
            ->sortBy(function ($model) use ($sorted_order_id) {
                return array_search($model->order_id, $sorted_order_id);
            });

        if ($attachments->count() == 0) {
            return response()->json(['status' => false, 'error' => 'No attachment found']);
        }

        //remove array when attachment is null
        $attachments = $attachments->filter(function ($value) {
            return ($value->attachment != null) && ($value->attachment != "");
        });

        $pdf = new PDFMerger;

        foreach ($attachments as $rs) {

            //unset if attachment is null
            if ($rs->attachment == null) {
                continue;
            }

            $order_id = $rs->order_id;
            $attach = $rs->attachment;
            $packing_attach = $rs->packing_attachment;

            if (!file_exists(storage_path('app/public/' . $attach))) {
                continue;
            }

            if (!is_file(storage_path('app/public/' . $attach))) {
                continue;
            }

            if (file_get_contents(storage_path('app/public/' . $attach)) == "") {
                continue;
            }

            $pdf->add(storage_path('app/public/' . $attach));

            if ($request->inc_packing_list && !empty($packing_attach)) {
                if (!file_exists(storage_path('app/public/' . $packing_attach))) {
                    continue;
                }

                if (!is_file(storage_path('app/public/' . $packing_attach))) {
                    continue;
                }

                if (file_get_contents(storage_path('app/public/' . $packing_attach)) == "") {
                    continue;
                }

                $pdf->add(storage_path('app/public/' . $packing_attach));
            }
        }

        $filename = 'CN_' . date('Ymd_His') . '.pdf';
        $pdf->merge(public_path('generated_labels/' . $filename));

        if (!file_exists(public_path('generated_labels/' . $filename))) {
            return response()->json(['status' => false, 'error' => 'Error in generating PDF']);
        }

        return response()->json(['download_url' => '/generated_labels/' . $filename]);
    }

    public function checking_shipping_docs($order_id)
    {
        $order = Order::find($order_id);
        $operational_model_id = $order->operational_model_id;
        $platform_id = $order->payment_type;
        $shipping_docs = ShippingDocumentTemplate::where('start_date', '<=', Carbon::now())
            ->where('end_date', '>=', Carbon::now())
            ->when(!empty($operational_model_id), function ($query) use ($operational_model_id) {
                $query->where('operational_model_id', 'like', '%' . $operational_model_id . '%');
            })
            ->when(!empty($platform_id), function ($query) use ($platform_id) {
                $query->where('platform', 'like', '%' . $platform_id . '%');
            })
            ->first();

        if (!$shipping_docs) {
            $shipping_docs = ShippingDocumentTemplate::where('start_date', '<=', Carbon::now())
                ->where('end_date', '>=', Carbon::now())
                ->where(function ($query) {
                    $query->whereNull('platform')
                        ->orWhere('platform', '=', ''); // Check for empty string
                })
                ->where(function ($query) {
                    $query->whereNull('operational_model_id')
                        ->orWhere('operational_model_id', '=', ''); // Check for empty string
                })
                ->first();

            if (!$shipping_docs) {
                return null;
            }
        }

        return $shipping_docs;
    }

    public function generate_product_description($order_id)
    {
        $addon_url = '';
        $order = Order::with(['items', 'items.product', 'shippings', 'customer'])->find($order_id);
        $path = 'product_desc/' . Carbon::now()->format('Ymd_His') . '_' . $order_id . '_product_description.pdf';

        try {
            $ship_docs = $this->checking_shipping_docs($order_id);
            if ($ship_docs != null) {
                if ($ship_docs->additional_detail != null) {

                    $additional_details = json_decode($ship_docs->additional_detail, true);
                    switch ($additional_details) {
                        case in_array('1', $additional_details):
                            $addon_url .= '?order_id=' . $order->id . '&';
                            break;
                        case in_array('2', $additional_details):
                            $addon_url .= '?tracking_number=' . $order->shippings->first()->tracking_number . '&';
                            break;
                        case in_array('3', $additional_details):
                            $addon_url .= '?customer_tel=' . $order->customer->phone;
                            break;
                        default:
                            break;
                    }
                }
            }
            $pdf = PDF::view('pdf_template.shipping_description_document_template', compact('order', 'ship_docs', 'addon_url'));
            $pdf->format('a6')->save(storage_path('app/public/' . $path));

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }

        return $path;

    }

    public function generate_multiple_product_description($rs)
    {
        $addon_url = '';
        //remove array when quantity is 0
        $rs = array_filter($rs, function ($value) {
            return $value['quantity'] > 0;
        });

        $orderItems = array_column($rs, 'order_item_id');
        $quantity = array_column($rs, 'quantity');

        $items = OrderItem::with(['product'])->whereIn('id', $orderItems)->get();
        foreach ($items as $key => $value) {
            $total_price = $value->price / $value->quantity;
            $items[$key]->quantity = $quantity[$key];
            $items[$key]->price = $total_price * $quantity[$key];
        }

        $order_id = $items[0]->order_id;
        $order = Order::with(['items', 'items.product', 'shippings', 'customer'])->find($order_id);
        $ship_docs = $this->checking_shipping_docs($order_id);
        if ($ship_docs != null) {
            if ($ship_docs->additional_detail != null) {
                $additional_details = json_decode($ship_docs->additional_detail, true);
                switch ($additional_details) {
                    case in_array('1', $additional_details):
                        $addon_url .= '?order_id=' . $order->id . '&';
                        break;
                    case in_array('2', $additional_details):
                        $addon_url .= '?tracking_number=' . $order->shippings->first()->tracking_number . '&';
                        break;
                    case in_array('3', $additional_details):
                        $addon_url .= '?customer_tel=' . $order->customer->phone;
                        break;
                    default:
                        break;
                }
            }
        }

        $path = 'product_desc/' . Carbon::now()->format('Ymd_His') . '_' . $order_id . '_product_description.pdf';

        try {
            $pdf = PDF::view('pdf_template.shipping_description_document_template_multiple', compact('items', 'ship_docs', 'addon_url'));
            $pdf->format('a6')->save(storage_path('app/public/' . $path));

        } catch (\Exception $e) {
            return response()->json(['status' => false, 'error' => $e->getMessage()]);
        }

        return $path;
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
            'tracking_id' => 'required|exists:shippings,tracking_number',
        ]);

        $shipping = Shipping::with(['order'])->where('tracking_number', $request->tracking_id)->first();

        if (set_order_status($shipping->order, ORDER_STATUS_SHIPPING, "First Milestone from Phantom")) {
            return response()->json(['success' => 'ok']);
        } else {
            return response()->json(['error' => 'Failed to update order status'], 500);
        }
    }

    /**
     * Update shipping_events and order_logs.
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function attempt_order_milestone(Request $request)
    {
        $request->validate([
            'tracking_id' => 'required|exists:shippings,tracking_number',
            'attempt_status' => 'required|string',
            'description' => 'required|string',
            'attempt_time' => 'required|date',
        ]);

        $shipping = Shipping::with(['order'])->where('tracking_number', $request->tracking_id)->first();

        if ($shipping) {
            if (set_shipping_events($shipping->id, $request->attempt_status, $request->description, $request->attempt_time)) {
                if (set_order_status($shipping->order, ORDER_STATUS_SHIPPING, "Attempt Order List")) {
                    return response()->json(['success' => 'ok']);
                } else {
                    return response()->json(['error' => 'Failed to update order status'], 500);
                }
            } else {
                return response()->json(['error' => 'Failed to set shipping event'], 500);
            }
        } else {
            return response()->json(['error' => 'Shipping not found'], 404);
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
            'tracking_id' => 'required|exists:shippings,tracking_number',
        ]);

        $shipping = Shipping::with(['order'])->where('tracking_number', $request->tracking_id)->first();

        if (set_order_status($shipping->order, ORDER_STATUS_DELIVERED)) {
            return response()->json(['success' => 'ok']);
        } else {
            return response()->json(['error' => 'Failed to update order status'], 500);
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
            'tracking_id' => 'required|exists:shippings,tracking_number',
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
            'tracking_id' => 'required|exists:shippings,tracking_number',
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
            if (empty($mult_cn)) { //if product only have one CN include all product desc
                $package_description .= $items->product->name . ", ";
            } else {
                $quantity = collect($mult_cn)
                    ->whereIn('order_item_id', $items['id'])
                    ->pluck('quantity')
                    ->values()
                    ->implode(',');
                if ($quantity > 0) { //check if product in the parcel more than 0 only then included in desc else excluded
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
            'courier_id' => 'required',
        ]);

        $array_data = ($request->input('cn_data'));

        // for now support only dhl-ecommerce and posmalaysia
        if($request->input('courier_id') == DHL_ID){
            if(count($array_data) === 1)
            {
                return $this->dhl_label_mult_cn($order_id, $array_data); // for dhl orders
            }
            return $this->dhl_label_single($order_id, $array_data); // for dhl orders
        } elseif ($request->input('courier_id') == POSMALAYSIA_ID) {
            $posmalaysia = new \App\Http\Controllers\ThirdParty\PosMalaysiaController();
            return $posmalaysia->generate_connote_multiple($order_id, $array_data); // for posmalaysia orders
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Courier not supported yet, please check with admin',
        ], 400);
    }

    /**
     * DHL Multiple CN for Single Order
     * @request $order
     * @return $response
     */
    public function dhl_label_mult_cn($order_id, $array_data)
    {
        $order = Order::with([
            'customer',
            'items',
            'items.product',
            'company',
            'company.access_tokens'
        ])->where('id', $order_id)->where('courier_id', DHL_ID)->first();

        if (!$order) {
            return response()->json([
                'status' => 'error',
                'message' => 'Order not found or courier is not DHL Ecommerce, please check with admin',
            ], 400);
        }

        $url = $this->dhl_label_url;
        $shipingApiController = new ShippingApiController();
        $access_token = $shipingApiController->checkExpiryTokenDHL([$order->company_id]);

        // $access_token = AccessToken::with(['company'])->where('company_id', $order->company_id)->where('type', 'dhl')->first();
        // $access_token = AccessToken::with(['company'])->where('company_id', 3)->where('type', 'dhl')->first(); //testing
        $remainCodAmmount = $order->purchase_type == 1 ? $order->total_price : null;
        $data = [];
        $dhl_store = [];

        // $blast = false;
        // foreach ($order->items as $item) {
        //     if ($item->quantity > $item->product->max_box) {
        //         $blast = true;
        //     }
        // }
        // any order with more than 40 orders will be named as blast
        // if ($order->items->count() > 1 && $order->items->sum('quantity') > MAXIMUM_QUANTITY_PER_BOX) {
        //     $blast = true;
        // }
        // $company_name = ($order->operational_model_id == OP_BLAST_ID && $blast) ? "EMZI BLAST" : $access_token->company->name;

        if (count($array_data) > 1) {
            $mult = true;
        } else {
            $mult = false;
        }

        $company_name = ($order->operational_model_id == OP_BLAST_ID && $mult) ? "EMZI BLAST" : $access_token->company->name;
        $pickup_account = ($order->operational_model_id == OP_BLAST_ID && $mult) ? $access_token->additional_data->dhl_pickup_account_blast : $access_token->additional_data->dhl_pickup_account;
        $soldto_account = ($order->operational_model_id == OP_BLAST_ID && $mult) ? $access_token->additional_data->dhl_sold_to_account_blast : $access_token->additional_data->dhl_sold_to_account;

        foreach ($array_data as $key => $cn) {

            $product_list = $this->generate_multiple_product_description($cn);
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
                        'shipmentItems' => [
                            0 => [
                                'consigneeAddress' => [
                                    // 'companyName' => get_shipping_remarks($order, $cn), //will return desc based on modal value inserted e.g NLC[40]SH FOC[1]
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
                                'returnMode' => "01", //01: return to registered address, 02: return to pickup address (ad-hoc pickup only), 03: return to new address
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
                                'codValue' => $codAmmount == 0 ? null : $codAmmount / 100, //optional
                                'insuranceValue' => null, //optional
                                'freightCharge' => null, //optional
                                'totalValue' => null, //optional for domestic
                                'currency' => "MYR", //3-char currency code
                                'remarks' => get_shipping_remarks($order, $cn), //optional
                                'deliveryOption' => "C", //only C is supported
                                'isMult' => "false", //true: multiple pieces, false: single piece
                            ],
                        ],
                        'pickupAccountId' => $pickup_account, //mandatory
                        'soldToAccountId' => $soldto_account, //mandatory
                        'inlineLabelReturn' => "Y", //mandatory
                        'handoverMethod' => 1, //optional - 01 for drop off, 02 for pickup
                        'pickupAddress' => [
                            'name' => substr($company_name, 0, 50), // contact person, appears when DHL Scan, only on DHL site
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

            $response = Http::withBody($data, 'application/json')->post($url);
            // $dhl_store = ['test'];
            $dhl_store = $this->dhl_store_for_mult($order, $response, $key, $product_list);
        }
        if (!empty($dhl_store)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Some CN cannot be generated.'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'CN generated successfully.'
        ]);
        ;
    }

    /**
     * Store the response from DHL
     *
     * @param  object $orders, $json
     * @return \Illuminate\Http\Response
     */

    public function dhl_store_for_mult($order, $json, $num_cn, $product_list)
    {
        $data = [];
        $tracking_no[] = [];
        $json = json_decode($json);
        foreach ($json->labelResponse->bd->labels ?? [] as $label) {
            if (isset($label->responseStatus)) {
                if (isset($label->responseStatus->message)) {
                    if ($label->responseStatus->message != "SUCCESS") {
                        if (isset($label->responseStatus->messageDetails)) {
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
        $data[$order->id]['attachment'] = 'labels/' . shipment_num_format_mult($order, $num_cn) . '.pdf';
        $data[$order->id]['packing_attachment'] = $product_list;

        // store label to storage
        Storage::put('public/labels/' . shipment_num_format_mult($order, $num_cn) . '.pdf', base64_decode($content[shipment_num_format_mult($order, $num_cn)]));

        set_order_status($order, ORDER_STATUS_PACKING, "Shipping label generated by DHL");

        Shipping::upsert($data, ['order_id'], ['courier', 'shipment_number', 'created_by']);
    }

    /**
     * Cancel the shipment
     * @param Request $request
     * @return json
     */
    public function cancel_shipment(Request $request)
    {
        $order = Order::with(['shippings', 'company.access_tokens'])->find($request->order_id);

        if (AUTO_REJECT_DHL == true) {
            if ($order->shippings[0]->shipment_number != null) {
                $data = [
                    'deleteShipmentReq' => [
                        'hdr' => [
                            'messageType' => 'DELETESHIPMENT',
                            'messageDateTime' => date('Y-m-d\TH:i:s') . '+08:00',
                            'accessToken' => $order->company->access_tokens->where('type', 'dhl')->first()->token,
                            'messageVersion' => '1.0',
                            'messageLanguage' => 'en',
                        ],
                        'bd' => [
                            'pickupAccountId' => DHL_SOLD_PICKUP_ACCT[$order->company_id],
                            'soldToAccountId' => DHL_SOLD_PICKUP_ACCT[$order->company_id],
                        ]
                    ]
                ];
                foreach ($order->shippings as $shipping) {
                    $data['deleteShipmentReq']['bd']['shipmentItems'][] = [
                        'shipmentID' => $shipping->shipment_number,
                    ];
                }

                $res = Http::post($this->dhl_cancel_url, $data);
            }
        }
        //remove batch id and bucket id
        $order->bucket_id = null;
        $order->bucket_batch_id = null;
        $order->save();

        //delete all shipping and shipping products
        foreach ($order->shippings as $shipping) {
            $shipping->shipping_product()->delete();
        }
        $order->shippings()->delete();

        //set order status to pending
        set_order_status($order, ORDER_STATUS_PENDING, "Shipment cancelled by" . auth()->user()->name);

        return response()->json([
            'success' => 'ok',
            'message' => 'Shipment cancelled successfully.'
        ]);
    }

    /**
     * Reprint the shipment
     * @param Request $request
     * @return json
     */
    public function reprint_cn(Request $request)
    {
        $ids = $request->order_ids;
        $orders = Order::with([
            'customer',
            'items',
            'items.product',
            'company',
            'company.access_tokens' => function ($query) {
                $query->where('type', 'dhl');
            }
        ])->whereIn('id', $ids)->where('courier_id', DHL_ID)->get();

        // if company is different, return error
        if ($orders->pluck('company_id')->unique()->count() > 1) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cannot reprint CN from different company.'
            ]);
        }

        $data['labelReprintRequest']['hdr'] = [
            'messageType' => 'LABELREPRINT',
            'messageDateTime' => date('Y-m-d\TH:i:s') . '+08:00',
            'accessToken' => $orders[0]->company->access_tokens->where('type', 'dhl')->first()->token,
            'messageVersion' => '1.1',
            'messageLanguage' => 'en',
        ];
        $data['labelReprintRequest']['bd'] = [
            'pickupAccountId' => DHL_SOLD_PICKUP_ACCT[$orders[0]->company_id],
            'soldToAccountId' => DHL_SOLD_PICKUP_ACCT[$orders[0]->company_id],
            'consolidatedLabelRequired' => 'n',
        ];

        foreach ($orders as $order) {
            $data['labelReprintRequest']['bd']['shipmentItems'][] = [
                'shipmentID' => shipment_num_format($order),
            ];
        }

        $json = json_encode($data);
        $response = Http::withBody($json, 'application/json')->post($this->dhl_reprint_url);
        logger($response->body());
        $dhl_store = $this->dhl_store_reprint($orders, $response);

        if ($dhl_store != null) {
            return response([
                "all_fail" => implode(" . ", collect($dhl_store)->pluck("messageDetail")->toArray())
            ]);
        }

        if (
            ($failer = Order::doesntHave('shippings')->with([
                'customer',
                'items',
                'items.product',
                'company',
                'company.access_tokens' => function ($query) {
                    $query->where('type', 'dhl');
                }
            ])->whereIn('id', $ids)
                ->where('courier_id', DHL_ID)
                ->count()) > 0
        ) {
            return response([
                "error" => $failer . " order fail to generate cn.",
                "all_fail" => $orders->count() == $failer
            ]);
        }

        return response([
            "success" => "ok"
        ]);
    }

    public function dhl_store_reprint($orders, $json)
    {
        $data = [];
        $tracking_no[] = [];
        $json = json_decode($json);

        $sample_json = '{"labelReprintResponse":{"hdr":{"messageType":"LABELREPRINT","messageDateTime":"2023-06-02T10:07:58+08:00","accessToken":"9b2d434edcbf41c48a68d975eaa84975","messageVersion":"1.1","messageLanguage":"en"},"bd":{"shipmentItems":[{"shipmentID":"MYAAHQQA000525843-230601-013","deliveryConfirmationNo":"7022067161571123","content":"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2zCzBgOhYST2P3Et+ojJrmH+PMMFteLyC2L+M9QE3JUJDRNgzxaZLsfNBDN9RJVF4gpN5CmTLLeJtJsYXFcHmS/CinnoG4ZdMiEkWzGYCS6YqsoiQ3WQGBxDzIn5iLJA3sayrBlcYPNkcyNDFzjtIxPWjH9OTD+8YHCBOzHKMNh5SBDsMswzxAQJMbhAUDO4gLdz2RDDicF8dU4Mn1FpzLRJFlTuYc3gAjm/M4MDqSExOGkYVqWi0QWTwB0CGqhXR8w57e2pmGGT0UvmHKqrk+7MCGnWrcOaGa0wrGaG5q93z2kVQ9cevZUJ7sjkyKSm6cJ1V2KDUTWDC4zuxuDzGVUb3Ia5QgaXxdRJDL6zqv4rul7kVsyVZs0w0oQSNQTnFYMDcWLCJYMLDO/OULWpmNmSoXbnksEFtmk9r/YFEjB9Aaou1BcYasOMTBbAIloyAOeS79qzmRsmDEyDA8NznKyY4XmVOpcMLbh5z6bup1Hvy/TT5tRugRdOvcDzGPbTcBjBiJlWDYFpQInBBXJv215nzfg1M2AVQ71OVjF5YPqAosHILXqdQZNhNZOIisE+dCIqBiexxE+hweiN+9Dv18DDMY5xjGMc4xjHOMYxvwzzXt3+D8T/0+8KZW5kc3RyZWFtCmVuZG9iagoyIDAgb2JqCjw8L0ZpbHRlci9GbGF0ZURlY29kZS9MZW5ndGggNjM+PnN0cmVhbQp4nCvkKlQwsjDSMzJRMABCEyNjPWMzBWMgTM5V0M/MTTdQcMlXCOQK5CrkcgrhAslZmimEpHC5hgDFAKO9DU4KZW5kc3RyZWFtCmVuZG9iago0IDAgb2JqCjw8L0NvbnRlbnRzIDIgMCBSL1R5cGUvUGFnZS9SZXNvdXJjZXM8PC9Qcm9jU2V0IFsvUERGIC9UZXh0IC9JbWFnZUIgL0ltYWdlQyAvSW1hZ2VJXS9YT2JqZWN0PDwvaW1nMCAxIDAgUj4+Pj4vUGFyZW50IDMgMCBSL01lZGlhQm94WzAgMCAyODggNDMyXT4+CmVuZG9iagozIDAgb2JqCjw8L0tpZHNbNCAwIFJdL1R5cGUvUGFnZXMvQ291bnQgMS9JVFhUKDIuMS43KT4+CmVuZG9iago1IDAgb2JqCjw8L1R5cGUvQ2F0YWxvZy9QYWdlcyAzIDAgUj4+CmVuZG9iago2IDAgb2JqCjw8L01vZERhdGUoRDoyMDIzMDYwMjAyMDY1NVopL0NyZWF0aW9uRGF0ZShEOjIwMjMwNjAyMDIwNjU1WikvUHJvZHVjZXIoaVRleHQgMi4xLjcgYnkgMVQzWFQpPj4KZW5kb2JqCnhyZWYKMCA3CjAwMDAwMDAwMDAgNjU1MzUgZiAKMDAwMDAwMDAxNSAwMDAwMCBuIAowMDAwMDA4NTkxIDAwMDAwIG4gCjAwMDAwMDg4ODIgMDAwMDAgbiAKMDAwMDAwODcyMCAwMDAwMCBuIAowMDAwMDA4OTQ1IDAwMDAwIG4gCjAwMDAwMDg5OTAgMDAwMDAgbiAKdHJhaWxlcgo8PC9JbmZvIDYgMCBSL0lEIFs8MDJkYjliMjJiMTk1NDYxMDJiZmI2ZjMxYWQyY2NkMDc+PGQxYmM0ZTY3YjYwNWIyMmM5OTBkYjFmOGRmZmQ3ZjI0Pl0vUm9vdCA1IDAgUi9TaXplIDc+PgpzdGFydHhyZWYKOTEwMAolJUVPRgo=","responseStatus":{"code":"200","message":"SUCCESS","messageDetails":[{"messageDetail":"Successful label reprint."}]}}],"responseStatus":{"code":"200","message":"SUCCESS","messageDetails":[{"messageDetail":"All label reprints are successful."}]}}}}';
        foreach ($json->labelReprintResponse->bd->shipmentItems as $label) {
            if (isset($label->responseStatus)) {
                if (isset($label->responseStatus->message)) {
                    if ($label->responseStatus->message != "SUCCESS") {
                        if (isset($label->responseStatus->messageDetails)) {
                            return false;
                        }
                    }
                }
            }

            $shipment_id = $label->shipmentID;
            // logger($shipment_id);
            $tracking_no[$shipment_id] = $label->deliveryConfirmationNo;
            $content[$shipment_id] = $label->content;
        }

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
            $data[$order->id]['attachment'] = 'labels/' . shipment_num_format($order) . '.pdf';
            //store label to storage
            Storage::put('public/labels/' . shipment_num_format($order) . '.pdf', base64_decode($content[shipment_num_format($order)]));
            set_order_status($order, ORDER_STATUS_PACKING, "Shipping label generated by DHL");
        }

        Shipping::upsert($data, ['order_id'], ['courier', 'shipment_number', 'created_by']);

        return true;
    }


    public function sort_order_to_download($order_ids)
    {
        $newOrders = [];

        $orders = OrderItem::with(['order', 'product'])
            ->whereIn('order_id', $order_ids)
            ->where('status', IS_ACTIVE)
            ->where('is_foc', IS_INACTIVE)
            ->get();

        $product_counts = OrderItem::select('order_id', DB::raw('COUNT(DISTINCT product_id) as product_count'))
            ->whereIn('order_id', $order_ids)
            ->groupBy('order_id')
            ->pluck('product_count', 'order_id')->toArray();

        // Group by product name
        foreach ($orders->groupBy('product.name') as $product_name => $items) {
            // Sort the group by product count and quantity
            $sortedItems = $items->sortBy(function ($item) use ($product_counts) {
                return [$product_counts[$item->order_id], $item->quantity];
            });

            // Collect order IDs
            foreach ($sortedItems->pluck('order_id') as $order_id) {
                $newOrders[] = $order_id;
            }
        }

        // array diff newOrders and order_ids
        $diff = array_diff($order_ids, $newOrders);

        if (!empty($diff)) {
            foreach ($diff as $order_id) {
                $newOrders[] = (int)$order_id;
            }
        }

        return $newOrders;
    }


    // public function sort_order_to_download($order_ids)
    // {
    //     $newOrders = [];
    //     // $order_ids = [17849,17848,17842,17840,17839,17836,17835,17834,17833,17826];
    //     $orders = OrderItem::with(['order', 'product'])
    //         ->whereIn('order_id', $order_ids)->where('status', IS_ACTIVE)
    //         ->where('is_foc', IS_INACTIVE)
    //         ->get();

    //     // [x] GROUPING BY SINGLE OR MARRIED (IGNORE FOC)

    //     // [x] Single
    //     // [x] GROUP BY PRODUCT (IGNORE FOC)
    //     foreach ($orders->where("marital_status", "single")->groupBy("product.name") as $product_name => $item) {
    //         // [x] SORT BY QUANTITY ASC ^
    //         foreach ($item->sortBy("quantity")->pluck("order_id") as $order_id) {
    //             // logger($order_id->quantity);
    //             $newOrders[] = $order_id;
    //         }
    //     }

    //     // [x] Married
    //     // [x] GROUP BY PRODUCT (IGNORE FOC)
    //     foreach ($orders->where("marital_status", "married")->groupBy("product.name") as $product_name => $item) {
    //         // [x] SORT BY QUANTITY ASC ^
    //         foreach ($item->sortBy("quantity")->pluck("order_id") as $order_id) {
    //             $newOrders[] = $order_id;
    //         }
    //     }

    //     //  array diff newOrders and order_ids
    //     $diff = array_diff($order_ids, $newOrders);

    //     if (!empty($diff)) {
    //         foreach ($diff as $order_id) {
    //             $newOrders[] = (int)$order_id;
    //         }
    //     }

    //     return $newOrders;
    // }


    public function generateShopeeCN($orderIds)
    {
        $orders = Order::with(['shippings'])
            ->select('payment_type', 'id')
            ->whereIn('id', $orderIds)
            ->where('payment_type', 22)
            ->get();

        $order = [];
        $CNS = [];
        $message = '';

        foreach ($orders as $key => $value) {
            $product_list = $this->generate_product_description($value->id);
            $order[$key]['id'] = $value->id;

            if (isset($value->shippings) && !count($value->shippings) > 0 || empty($value->shippings->first()->tracking_number)) {
                $order[$key]['error']['type'][] = 'generateShopeeCN';
                $order[$key]['error']['message'][] = 'No Tracking Number Found';
                continue;
            }

            $order[$key]['additional_data'] = json_decode($value->shippings[0]->additional_data, true);

            ###### start get shipping document parameter ######
            $getShippingDocumentParameter = ShopeeTrait::getShippingDocumentParameter($order[$key]['additional_data']);
            $jsonGetShippingDocumentParameter = json_decode($getShippingDocumentParameter, true);

            if (empty($jsonGetShippingDocumentParameter['error'])) {
                $order[$key]['additional_data']['shipping_document_type'] = $jsonGetShippingDocumentParameter['response']['result_list'][0]['suggest_shipping_document_type'];

                ############################################
                ###### start create shipping document ######
                ############################################
                $getCreateShippingDocument = ShopeeTrait::createShippingDocument($order[$key]['additional_data']);
                $jsonGetCreateShippingDocument = json_decode($getCreateShippingDocument, true);

                if (empty($jsonGetCreateShippingDocument['error'])) {
                    ###############################################
                    ###### start get shipping document result #####
                    ###############################################
                    $getShippingDocument = ShopeeTrait::getShippingDocumentResult($order[$key]['additional_data']);
                    $jsonGetShippingDocument = json_decode($getShippingDocument, true);

                    if (empty($jsonGetShippingDocument['error'])) {
                        $order[$key]['additional_data']['shipping_document_status'] = $jsonGetShippingDocument['response']['result_list'][0]['status'];

                        #######################################
                        ###### start generate_cn ##############
                        #######################################
                        $getDownloadShippingDocument = ShopeeTrait::downloadShippingDocument($order[$key]['additional_data']);

                        if ($getDownloadShippingDocument) {
                            //save to shippings table
                            $shipping = Shipping::where('order_id', $value->id)->first();
                            $shipping->attachment = $getDownloadShippingDocument;
                            $shipping->packing_attachment = $product_list;
                            $shipping->save();

                            $order[$key]['attachment'] = $getDownloadShippingDocument;
                            $CNS['order_ids'][] = $value->id;
                            $CNS['attachment'][] = $getDownloadShippingDocument;
                        } else {
                            $order[$key]['error']['type'][] = 'downloadShippingDocument';
                            $order[$key]['error']['message'][] = 'Failed to download shipping document';
                        }
                        #####################################
                        ###### end generate_cn ##############
                        #####################################
                    } else {
                        $order[$key]['error']['type'][] = 'getShippingDocumentResult';
                        $order[$key]['error']['message'][] = $jsonGetShippingDocument['message'];
                    }
                    #############################################
                    ###### end get shipping document result ######
                    #############################################
                } else {
                    $order[$key]['error']['type'][] = 'createShippingDocument';
                    $order[$key]['error']['message'][] = $jsonGetCreateShippingDocument['response']['result_list'][0]['fail_message'];
                }
                ##########################################
                ###### end create shipping document ######
                ##########################################
            } else {
                $order[$key]['error']['type'][] = 'generateShopeeCN';
                $order[$key]['error']['message'][] = $jsonGetShippingDocumentParameter['message'];
            }
            #################################################
            ###### end get shipping document parameter ######
            #################################################
        }

        if (isset($CNS) && count($CNS) > 0) {
            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $CNS
            ], 200);
        }

        $message .= "Success: " . count($CNS) . " generated.<br>";

        if (isset($order) && count($order) > 0) {
            foreach ($order as $key => $value) {
                if (isset($value['error']['type']) && count($value['error']['type']) > 0) {
                    foreach ($value['error']['type'] as $k => $v) {
                        $message .= "Failed: Order ID " . $value['id'] . " - " . $value['error']['message'][$k] . "<br>";
                    }
                }
            }
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'all_fail' => ['message' => $message],
            'data' => $CNS ?? ''
        ], 200);

    }

    public function generateTiktokCN($orderIds)
    {
        $orders = Order::with(['shippings'])
            ->select('payment_type', 'id')
            ->whereIn('id', $orderIds)
            ->where('payment_type', 23)
            ->get();

        $order = [];
        $CNS = [];
        $message = '';

        foreach ($orders as $key => $value) {
            $product_list = $this->generate_product_description($value->id);
            $order[$key]['id'] = $value->id;
            if (isset($value->shippings) && !count($value->shippings) > 0 || empty($value->shippings[0]->tracking_number)) {
                $order[$key]['error']['type'][] = 'generateTiktokCN';
                $order[$key]['error']['message'][] = 'No Tracking Number Found';
                continue;
            }

            $order[$key]['additional_data'] = json_decode($value->shippings[0]->additional_data, true);

            ###### download shipping document ######
            $generateCN = TiktokTrait::generateCN($order[$key]['additional_data']);
            $generateCN = json_decode($generateCN, true);

            if ($generateCN['code'] != 0) {
                $order[$key]['error']['type'][] = 'generateCN';
                $order[$key]['error']['message'][] = $generateCN['message'];
                continue;
            }

            // save to shippings table
            $shipping = Shipping::where('order_id', $value->id)->first();
            $shipping->attachment = $generateCN['data']['file_name'];
            $shipping->packing_attachment = $product_list;
            $shipping->save();

            $order[$key]['attachment'] = $generateCN['data']['file_name'];
            $CNS['order_ids'][] = $value->id;
            $CNS['attachment'][] = $generateCN['data']['file_name'];
            #################################################
            ###### end get shipping document parameter ######
            #################################################
        }

        if (isset($CNS) && count($CNS) > 0) {
            $message .= "Success: " . count($CNS) . " generated.<br>";

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $CNS
            ], 200);
        }

        if (isset($order) && count($order) > 0) {
            foreach ($order as $key => $value) {
                if (isset($value['error']['type']) && count($value['error']['type']) > 0) {
                    foreach ($value['error']['type'] as $k => $v) {
                        $message .= "Failed: Order ID " . $value['id'] . " - " . $value['error']['message'][$k] . "<br>";
                    }
                }
            }
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $CNS ?? ''
        ], 200);
    }

    public function arrange_shipment(Request $request)
    {
        $responseSuccess = [];
        $responseProcessing = [];
        $responseFailed = [];
        $message = '';
        $data = $request->validate([
            'order_ids' => 'required',
            'platform' => 'required',
        ]);

        $platform = $request->platform == 'shopee' ? 22 : 23; #shopee = 22, tiktok = 23

        $data['created_by'] = auth()->user()->id ?? 1;

        $orders = Order::with(['shippings'])
            ->select('orders.payment_type', 'orders.id', 'orders.third_party_sn', 'couriers.code')
            ->whereIn('orders.id', $data['order_ids'])
            ->where('orders.payment_type', $platform)
            ->join('couriers', 'orders.courier_id', '=', 'couriers.id')
            ->get();

        if (!count($orders) > 0) {
            return response()->json([
                'success' => false,
                'message' => 'No order found',
                'data' => ''
            ], 200);
        }

        if ($platform == 22) #shopee only
        {
            foreach ($orders as $order) {
                //check third party sn
                if (empty($order->third_party_sn)) {
                    $responseFailed['order_id'][] = $order->id;
                    $responseFailed['message'][] = 'Third party sn not found';
                    continue;
                }

                //get order status
                $order_details = ShopeeTrait::getOrderDetail($order->third_party_sn);
                $detailsJson = json_decode($order_details, true);
                if (!empty($detailsJson['error'])) {
                    $responseFailed['order_id'][] = $order->id;
                    $responseFailed['message'][] = $detailsJson['message'];
                    continue;
                }
                $order_status = $detailsJson['response']['order_list'][0]['order_status'];

                //check order status to arrange shipment
                if ($order_status == 'READY_TO_SHIP') {
                    //get time slot
                    $timeslot = ShopeeTrait::getShippingParameter($order->third_party_sn);
                    $timeslot = json_decode($timeslot, true);
                    if (!empty($timeslot['error'])) {
                        $responseFailed['order_id'][] = $order->id;
                        $responseFailed['message'][] = $timeslot['message'];
                        continue;
                    }
                    //check if no slot time found
                    if (!isset($timeslot['response']['pickup']['address_list'][0])) {
                        $responseFailed['order_id'][] = $order->id;
                        $responseFailed['message'][] = 'No slot time found';
                        continue;
                    }
                    $timeslot = $timeslot['response']['pickup']['address_list'][0];
                    $timeslots = $timeslot['time_slot_list'];

                    //get time slot
                    $availablePickupTimes = [];
                    $now = Carbon::now();
                    foreach ($timeslots as $pickupTime) {
                        $pickupDate = Carbon::createFromTimestamp($pickupTime['date']);

                        if ($pickupDate->isAfter($now)) {
                            // Date is before now, add it to the available pickup times
                            $availablePickupTimes[] = $pickupTime;
                        }
                    }

                    $process = ShopeeTrait::shipOrder($order->third_party_sn, $availablePickupTimes[0]['pickup_time_id']);
                    $processJson = json_decode($process, true);
                    if (!empty($processJson['error'])) {
                        $responseFailed['order_id'][] = $order->id;
                        $responseFailed['message'][] = $processJson['message'];
                        continue;
                    }

                    //get tracking number
                    $tracking_number = ShopeeTrait::getTrackingNumber($order->third_party_sn);
                    $tracking_number = json_decode($tracking_number, true);
                    if (!empty($tracking_number['error'])) {
                        $responseFailed['order_id'][] = $order->id;
                        $responseFailed['message'][] = $tracking_number['message'];
                        continue;
                    }
                    $additional_data = json_encode([
                        'ordersn' => $order->third_party_sn,
                        'package_number' => $detailsJson['response']['order_list'][0]['package_list'][0]['package_number'],
                        'tracking_no' => $tracking_number['response']['tracking_number'],
                    ]);

                    Shipping::updateOrCreate(
                        [
                            'order_id' => $order->id
                        ],
                        [
                            'tracking_number' => $tracking_number['response']['tracking_number'],
                            'courier' => $order->code,
                            'created_by' => auth()->user()->id ?? 1,
                            'additional_data' => $additional_data,
                        ]
                    );

                    $responseSuccess[] = $order->id;
                    //update order status to pending shipment
                    set_order_status($order, ORDER_STATUS_PENDING_SHIPMENT, "Order arranged for shipment");
                } else {
                    //get tracking number
                    $tracking_number = ShopeeTrait::getTrackingNumber($order->third_party_sn);
                    $tracking_number = json_decode($tracking_number, true);
                    if (!empty($tracking_number['error'])) {
                        $responseFailed['order_id'][] = $order->id;
                        $responseFailed['message'][] = $tracking_number['message'];
                        continue;
                    }
                    $additional_data = json_encode([
                        'ordersn' => $order->third_party_sn,
                        'package_number' => $detailsJson['response']['order_list'][0]['package_list'][0]['package_number'],
                        'tracking_no' => $tracking_number['response']['tracking_number'],
                    ]);

                    Shipping::updateOrCreate(
                        [
                            'order_id' => $order->id
                        ],
                        [
                            'tracking_number' => $tracking_number['response']['tracking_number'],
                            'courier' => $order->code,
                            'created_by' => auth()->user()->id ?? 1,
                            'additional_data' => $additional_data,
                        ]
                    );

                    $responseProcessing[] = $order->id;
                    set_order_status($order, ORDER_STATUS_PENDING_SHIPMENT, "Order arranged for shipment");
                }
            }
        } elseif ($platform == 23) #tiktok only
        {
            foreach ($orders as $order) {
                //check third party sn
                if (empty($order->shippings->first()->additional_data)) {
                    $responseFailed['order_id'][] = $order->id;
                    $responseFailed['message'][] = 'Third party data not found';
                    continue;
                }
                //get order_status
                $additional_data = json_decode($order->shippings->first()->additional_data, true);
                $order_details = TiktokTrait::getOrderDetails($additional_data);
                $detailsJson = json_decode($order_details, true);
                if ($detailsJson['code'] != 0) {
                    $responseFailed['order_id'][] = $order->id;
                    $responseFailed['message'][] = $detailsJson['message'];
                    continue;
                }
                $order_status = $detailsJson['data']['order_list'][0]['order_status'];

                //check order status to arrange shipment
                if ($order_status == '111') {
                    $process = TikTokTrait::shipOrder($additional_data);
                    $processJson = json_decode($process, true);
                    if ($processJson['code'] != 0) {
                        $responseFailed['order_id'][] = $order->id;
                        $responseFailed['message'][] = $processJson['message'];
                        continue;
                    }

                    //run back to get tracking number
                    $order_details = TiktokTrait::getOrderDetails($additional_data);
                    $detailsJson = json_decode($order_details, true);

                    $additional_data = json_encode([
                        'order_id' => $order->third_party_sn,
                        'shop_id' => $additional_data['shop_id'],
                        'package_number' => $detailsJson['data']['order_list'][0]['package_list'][0]['package_id'],
                        'tracking_no' => $detailsJson['data']['order_list'][0]['order_line_list'][0]['tracking_number']
                    ]);

                    Shipping::updateOrCreate(
                        [
                            'order_id' => $order->id
                        ],
                        [
                            'tracking_number' => $detailsJson['data']['order_list'][0]['order_line_list'][0]['tracking_number'],
                            'courier' => $order->code,
                            'created_by' => auth()->user()->id ?? 1,
                            'additional_data' => $additional_data,
                        ]
                    );

                    $responseSuccess[] = $order->id;
                    //update order status to pending shipment
                    set_order_status($order, ORDER_STATUS_PENDING_SHIPMENT, "Order arranged for shipment");
                } else {
                    //run back to get tracking number
                    $order_details = TiktokTrait::getOrderDetails($additional_data);
                    $detailsJson = json_decode($order_details, true);

                    $additional_data = json_encode([
                        'order_id' => $order->third_party_sn,
                        'shop_id' => $additional_data['shop_id'],
                        'package_number' => $detailsJson['data']['order_list'][0]['package_list'][0]['package_id'],
                        'tracking_no' => $detailsJson['data']['order_list'][0]['order_line_list'][0]['tracking_number']
                    ]);

                    Shipping::updateOrCreate(
                        [
                            'order_id' => $order->id
                        ],
                        [
                            'tracking_number' => $detailsJson['data']['order_list'][0]['order_line_list'][0]['tracking_number'],
                            'courier' => $order->code,
                            'created_by' => auth()->user()->id ?? 1,
                            'additional_data' => $additional_data,
                        ]
                    );

                    $responseProcessing[] = $order->id;
                    set_order_status($order, ORDER_STATUS_PENDING_SHIPMENT, "Order arranged for shipment");
                }
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Platform not found',
                'data' => ''
            ], 200);
        }

        $message .= "Success: " . count($responseSuccess) . " order.<br>Already Processed/Shipped: " . count($responseProcessing) . " orders.<br>";

        if (isset($responseFailed['order_id']) && count($responseFailed['order_id']) > 0) {
            foreach ($responseFailed['order_id'] as $key => $value) {
                $message .= "Failed: Order ID " . $value . " - " . $responseFailed['message'][$key] . "<br>";
            }
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => ''
        ], 200);
    }

    public function emzi_express_cn($order_ids)
    {
        $order = [];
        $CNS = [];
        $message = '';
        $errors = [];

        // filter only selected order shipping not exists
        $order_ex = Order::with(['customer.states', 'items', 'items.product', 'company'])
            ->whereIn('id', $order_ids)
            ->whereDoesntHave('shippings', function ($query) {
                $query->where('courier', EMZIEXPRESS_ID);
            })
            ->get();
        if (count($order_ex) == 0) {
            return 0;
        }

        foreach ($order_ex as $key => $order) {
            $product_list = $this->generate_product_description($order->id);
            //check first access token
            $accessToken = EmziExpressTrait::checkAccessToken($order->company->id);

            // Create shipping first
            $shipping = new Shipping();
            $shipping->shipment_number = shipment_num_format($order);
            $shipping->order_id = $order->id;
            $shipping->courier = EMZIEXPRESS_ID;
            $shipping->created_by = auth()->user()->id ?? 1;
            $shipping->save();

            # Shipment details
            $shipmentID = $shipping->shipment_number ?? '';
            $shipmentRemarks = get_shipping_remarks($order) ?? '';
            $itemDescription = get_shipping_remarks($order) ?? '';
            $totalWeight = get_order_weight($order) / 1000 ?? '';
            $codValue = $order->purchase_type == PURCHASE_TYPE_PAID ? '0' : $order->total_price / 100 ?? '0';

            # Pickup address and return address (same)
            $pickupAddress = [
                // "companyName" => $order->company->name ?? '',
                // "name" => $order->company->contact_person ?? '',
                // "address1" => $order->company->address ?? '',
                // "address2" => $order->company->address2 ?? '',
                // "address3" => $order->company->address3 ?? '',
                // "city" => $order->company->city ?? '',
                // "state" => $order->company->state ?? '',
                // "country" => "MY",
                // "postCode" => $order->company->postcode ?? '',
                // "phone" => $order->company->phone ?? '',
                // "phone2" => $order->company->phone2 ?? '',
                // "email" => $order->company->email ?? ''
                "companyName" => "EMZI FULFILLMENT",
                "name" => "EMZI FULLFILLMENT",
                "address1" => "EMZI FULLFILLMENT, KOMPLEKS SP PLAZA, JALAN IBRAHIM, SUNGAI PETANI 08000 Sungai Petani, Kedah",
                "address2" => "",
                "address3" => "",
                "city" => "Sungai Petani",
                "state" => "Kedah",
                "country" => "MY",
                "postCode" => "08000",
                "phone" => "60195687313",
                "phone2" => "",
                "email" => "customerservice.elsb@emzi.com.my"
            ];

            # Receiver address
            $receiverAddress = [
                "companyName" => $pickupAddress['companyName'],
                "name" => $order->customer->name ?? '',
                "address1" => $order->customer->address ?? '',
                "address2" => $order->customer->address2 ?? '',
                "address3" => $order->customer->address3 ?? '',
                "city" => $order->customer->city ?? '',
                "state" => $order->customer->states->name ?? '',
                "country" => "MY",
                "postCode" => $order->customer->postcode ?? '',
                "phone" => $order->customer->phone ?? '',
                "phone2" => $order->customer->phone2 ?? '',
                "email" => $order->customer->email ?? ''
            ];

            # JSON structure
            $jsonArray = [
                "requestType" => "CN",
                "accountId" => $accessToken->client_id,
                "pickupAddress" => $pickupAddress,
                "shipmentItems" => [
                    [
                        "receiverAddress" => $receiverAddress,
                        "returnAddress" => $pickupAddress,
                        "shipmentID" => $shipmentID,
                        "shipmentRemarks" => $shipmentRemarks,
                        "itemDescription" => $itemDescription,
                        "totalWeight" => $totalWeight,
                        "height" => '',
                        "length" => '',
                        "width" => '',
                        "codValue" => $codValue,
                        "isMult" => ''
                    ]
                ]
            ];

            $jsonSend = json_encode($jsonArray, JSON_PRETTY_PRINT);

            $generateCN = EmziExpressTrait::generateCN($accessToken->token, $jsonSend);
            if (isset($generateCN['shipmentItems'][0]['trackingNumber']) && isset($generateCN['shipmentItems'][0]['labels'])) {
                $shipping->tracking_number = $generateCN['shipmentItems'][0]['trackingNumber'] ?? '';
                $shipping->attachment = 'labels/' . shipment_num_format($order) . '.pdf';
                $shipping->packing_attachment = $product_list;
                Storage::put('public/labels/' . shipment_num_format($order) . '.pdf', base64_decode($generateCN['shipmentItems'][0]['labels']));
                set_order_status($order, ORDER_STATUS_PACKING, "Shipping label generated by DHL");
                $shipping->save();

                $CNS['order_ids'][] = $order->id;
                $CNS['attachment'][] = $shipping->attachment;
            } else {
                $errors[] = $generateCN;
            }

        }

        if (count($CNS) > 0) {
            return response()->json([
                'success' => true,
                'message' => 'Success',
                'data' => $CNS
            ], 200);
        }

        $message .= "Success: " . count($CNS) . " generated.<br>";

        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $message .= "Failed: " . $error['message'] . "<br>";
            }
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $CNS ?? ''
        ], 200);
    }

    public function get_shipping_cost_id($order)
    {
        $courier_id = $order->courier_id;
        $state_id = $order->customer->state;
        $product_weight = 0;
        foreach($order->items as $item)
        {
            $weight = Product::where('id', $item['product_id'])->first()->weight;

            $product_weight += $item['quantity'] * $weight;
        }
        $weight_category = WeightCategory::where('min_weight', '<=', $product_weight)->where('max_weight', '>=', $product_weight)->first();
        $state_group = GroupStateList::where('state_id', $state_id)->first();

        //get shipping cost
        if ($weight_category && $courier_id && $state_group) {
            $shipping_cost = ShippingCost::where('weight_category_id', $weight_category->id)
            ->where('courier_id', $courier_id)
            ->where('state_group_id', $state_group->state_group_id)
            ->first();
        }

        if(isset($shipping_cost))
        {
            $shipping_cost_data['total_weight'] = $product_weight;
            $shipping_cost_data['shipping_cost_id'] = $shipping_cost->id;
            return $shipping_cost_data;
        }
        return false;

    }

    public function get_shipping_cost_multiple($order, $items)
    {
        $courier_id = $order->courier_id;
        $state_id = $order->customer->state;
        $product_weight = 0;
        foreach($items as $item)
        {
            $weight = OrderItem::with('product')->where('id', $item['order_item_id'])->first()->product->weight;
            $product_weight += $item['quantity'] * $weight;
        }

        $weight_category = WeightCategory::where('min_weight', '<=', $product_weight)->where('max_weight', '>=', $product_weight)->first();
        $state_group = GroupStateList::where('state_id', $state_id)->first();
        //get shipping cost
        if ($weight_category && $courier_id && $state_group) {
            $shipping_cost = ShippingCost::where('weight_category_id', $weight_category->id)
            ->where('courier_id', $courier_id)
            ->where('state_group_id', $state_group->state_group_id)
            ->first();
        }

        if(isset($shipping_cost))
        {
            $shipping_cost_data['total_weight'] = $product_weight;
            $shipping_cost_data['shipping_cost_id'] = $shipping_cost->id;
            return $shipping_cost_data;
        }
        return false;
    }

    public function store_shipping_products($orders)
    {
        $shipping_products = [];

        foreach($orders as $order)
        {
            //get shipping id
            $shipping_id = Shipping::where('order_id', $order->id)->first()->id;

            if(isset($shipping_id) && !empty($shipping_id))
            {
                //delete old shipping products
                ShippingProduct::where('shipping_id', $shipping_id)->delete();
                foreach($order->items as $item)
                {
                    $shipping_products[] = [
                        'shipping_id' => $shipping_id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            }
        }
        ShippingProduct::insert($shipping_products);
    }

    public function store_shipping_products_multiple($order, $items)
    {
        $shipping_products = [];

        $shipping_ids = Shipping::where('order_id', $order->id)->pluck('id');

        foreach($shipping_ids as $shipping_id)
        {
            foreach($items as $key => $item)
            {
                $shipping_products[$key] = [
                    'shipping_id' => $shipping_id,
                    'product_id' => OrderItem::with('product')->where('id', $item['order_item_id'])->first()->product->id,
                    'quantity' => $item['quantity'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ];
            }
        }
        ShippingProduct::insert($shipping_products);
    }

}
