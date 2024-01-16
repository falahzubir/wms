<?php

namespace App\Http\Controllers;

use App\Exports\OrderExport;
use App\Models\Company;
use App\Models\Courier;
use App\Models\Customer;
use App\Models\OperationalModel;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Shipping;
use App\Models\OrderLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Http\Traits\ApiTrait;
use App\Models\OrderEvent;
use App\Models\AlternativePostcode;
use App\Models\CategoryMain;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use App\Models\TemplateMain;

class OrderController extends Controller
{

    /**
     * Initiate orders list
     * @param  null
     * @return Order
     */
    public function index()
    {
        return Order::with([
            'customer', 'items', 'items.product', 'shippings', 'paymentType',
            'bucket', 'batch', 'company', 'courier', 'operationalModel',
            'logs' => function ($query) {
                $query->orderBy('id', 'desc');
            }
        ])
            ->where('is_active', IS_ACTIVE);
    }

    /**
     * List all filter data, exclude some data
     * @param  array $exclude
     * @return object
     */
    public function filter_data_exclude($exclude = [])
    {
        $filter_data = [];
        if (!in_array(ORDER_FILTER_COURIER, $exclude)) {
            $filter_data['couriers'] = Courier::where('status', IS_ACTIVE)->get();
        }
        if (!in_array(ORDER_FILTER_PRODUCT, $exclude)) {
            $filter_data['products'] = Product::where('is_active', IS_ACTIVE)->get();
        }
        if (!in_array(ORDER_FILTER_COMPANY, $exclude)) {
            $filter_data['companies'] = Company::get();
        }
        if (!in_array(ORDER_FILTER_PURCHASE_TYPE, $exclude)) {
            $filter_data['purchase_types'] = [
                PURCHASE_TYPE_COD => 'COD',
                PURCHASE_TYPE_PAID => 'Paid',
                PURCHASE_TYPE_INSTALLMENT => 'Installment'
            ];
        }
        if (!in_array(ORDER_FILTER_CUSTOMER_TYPE, $exclude)) {
            $filter_data['customer_types'] = [
                CUSTOMER_TYPE_LEAD => 'Lead',
                CUSTOMER_TYPE_PROSPECT => 'Prospect',
                CUSTOMER_TYPE_FOLLOWUP => 'Followup',
                CUSTOMER_TYPE_BUYER => 'Buyer',
                CUSTOMER_TYPE_DEBTOR => 'Debtor'
            ];
        }
        if (!in_array(ORDER_FILTER_SALES_EVENT, $exclude)) {
            $filter_data['sale_events'] = OrderEvent::get();
        }
        if (!in_array(ORDER_FILTER_TEAM, $exclude)) {
            $filter_data['teams'] = true; //http request
        }
        if (!in_array(ORDER_FILTER_OP_MODEL, $exclude)) {
            // $filter_data['operational_models'] = true; //http request
            $filter_data['operational_models'] = OperationalModel::get();
        }
        if (!in_array(ORDER_FILTER_STATE, $exclude)) {
            $filter_data['states'] = MY_STATES; //http request
        }

        if(!in_array(ORDER_FILTER_PLATFORM, $exclude)){
            $filter_data['platforms'] = [
                22 => 'Shopee',
                23 => 'TikTok',
            ];
        }

        if(!in_array(ORDER_FILTER_BUCKET_CATEGORY, $exclude)){
            $filter_data['bucket_categories'] = CategoryMain::where('category_status', IS_ACTIVE)->get();
        }

        if(!in_array(ORDER_FILTER_STATUS, $exclude)){

            //check if route is pending, then show only pending status
            if(Route::currentRouteName() == 'orders.pending'){
                $filter_data['statuses'] = [
                    ORDER_STATUS_PENDING => 'Pending',
                    ORDER_STATUS_PENDING_SHIPMENT => 'Pending Shipment',
                ];
            }else{
                $filter_data['statuses'] = [
                    ORDER_STATUS_PENDING => 'Pending',
                    ORDER_STATUS_PENDING_SHIPMENT => 'Pending Shipment',
                    ORDER_STATUS_PROCESSING => 'Processing',
                    ORDER_STATUS_READY_TO_SHIP => 'Ready To Ship',
                    ORDER_STATUS_PACKING => 'Packing',
                    ORDER_STATUS_SHIPPING => 'Shipping',
                    ORDER_STATUS_DELIVERED => 'Delivered',
                    ORDER_STATUS_RETURN_PENDING => 'Return Pending',
                    ORDER_STATUS_RETURN_SHIPPING => 'Return Shipping',
                    ORDER_STATUS_RETURNED => 'Returned',
                    ORDER_STATUS_RETURN_COMPLETED => 'Return Completed',
                    ORDER_STATUS_REJECTED => 'Rejected',
                ];
            }
        }

        return (object) $filter_data;
    }

    /**
     * List all active orders
     * @return view
     */
    public function overall(Request $request)
    {
        $orders = $this->index();

        $orders = $this->filter_order($request, $orders);

        return view('orders.index', [
            'title' => 'List Orders',
            'order_ids' => $orders->pluck('id')->toArray(),
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM]),
            'actions' => [ACTION_DOWNLOAD_CN, ACTION_DOWNLOAD_ORDER],
        ]);
    }

    /**
     * Load order table from ajax request
     *
     */
    public function load(Request $request)
    {
        $orders = Order::where('is_active', IS_ACTIVE)->paginate(PAGINATE_LIMIT);

        return view('orders.table', [
            'orders' => $orders,
        ]);
    }

    /**
     * Lists pending order
     * @param  Request $request
     * @return view
     */
    public function pending(Request $request)
    {
        $orders = $this->index()->whereIn('status', [ORDER_STATUS_PENDING, ORDER_STATUS_PENDING_SHIPMENT])
                ->whereDate('dt_request_shipping', '<=', date('Y-m-d'));
                // ->whereRaw('(payment_type IS NULL OR payment_type <> 22)'); //shopee order excluded

        $orders = $this->filter_order($request, $orders);

        return view('orders.index', [
            'title' => 'Pending Orders',
            'order_ids' => $orders->pluck('id')->toArray(),
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM]),
            'actions' => [ACTION_ADD_TO_BUCKET, ACTION_DOWNLOAD_ORDER,ACTION_ARRANGE_SHIPMENT],
        ]);
    }

    /**
     * Lists processing order
     * @param  Request $request
     * @return view
     */
    public function processing(Request $request)
    {
        $orders = $this->index()->whereIn('status', [ORDER_STATUS_PROCESSING]);

        $orders = $this->filter_order($request, $orders);
        return view('orders.index', [
            'title' => 'Processing Orders',
            'order_ids' => $orders->pluck('id')->toArray(),
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM]),
            'actions' => [ACTION_ADD_TO_BUCKET, ACTION_GENERATE_CN, ACTION_DOWNLOAD_CN, ACTION_DOWNLOAD_ORDER, ACTION_UPLOAD_TRACKING_BULK, ACTION_GENERATE_PICKING],
        ]);
    }

    /**
     * Lists Ready to Ship order
     * @param  Request $request
     * @return view
     */
    public function ready_to_ship(Request $request)
    {
        $orders = $this->index()->whereIn('status', [ORDER_STATUS_READY_TO_SHIP]);

        $orders = $this->filter_order($request, $orders);

        return view('orders.index', [
            'title' => 'Ready To Ship Orders',
            'order_ids' => $orders->pluck('id')->toArray(),
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM]),
            'actions' => [ACTION_APPROVE_AS_SHIPPED, ACTION_DOWNLOAD_ORDER],
        ]);
    }

    /**
     * Lists packing order
     * @param  Request $request
     * @return view
     */
    public function packing(Request $request)
    {
        $orders = $this->index()->where('status', ORDER_STATUS_PACKING);

        $orders = $this->filter_order($request, $orders);

        return view('orders.index', [
            'title' => 'Packing Orders',
            'order_ids' => $orders->pluck('id')->toArray(),
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM]),
            'actions' => [ACTION_DOWNLOAD_ORDER, ACTION_DOWNLOAD_CN],
        ]);
    }

    /**
     * Lists shipped order
     * @param  Request $request
     * @return view
     */
    public function shipping(Request $request)
    {
        $orders = $this->index()->whereIn('status', [
            ORDER_STATUS_SHIPPING, ORDER_STATUS_RETURN_PENDING, ORDER_STATUS_RETURN_SHIPPING
        ]);

        $orders = $this->filter_order($request, $orders);

        return view('orders.index', [
            'title' => 'Shipping Orders',
            'order_ids' => $orders->pluck('id')->toArray(),
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM]),
            'actions' => [ACTION_DOWNLOAD_ORDER],
        ]);
    }

    /**
     * Lists delivered order
     * @param  Request $request
     * @return view
     */
    public function delivered(Request $request)
    {
        $orders = $this->index()->where('status', ORDER_STATUS_DELIVERED);

        $orders = $this->filter_order($request, $orders);

        return view('orders.index', [
            'title' => 'Delivered Orders',
            'order_ids' => $orders->pluck('id')->toArray(),
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM]),
            'actions' => [ACTION_DOWNLOAD_ORDER],
        ]);
    }

    /**
     * Lists returned order
     * @param  Request $request
     * @return view
     */

    public function returned(Request $request)
    {
        $orders = $this->index()->where('status', ORDER_STATUS_RETURNED);

        $orders = $this->filter_order($request, $orders);

        return view('orders.index', [
            'title' => 'Returned Orders',
            'order_ids' => $orders->pluck('id')->toArray(),
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM]),
            'actions' => [ACTION_DOWNLOAD_ORDER],
        ]);
    }


    public function return_completed(Request $request)
    {
        $orders = $this->index()->where('status', ORDER_STATUS_RETURN_COMPLETED);

        $orders = $this->filter_order($request, $orders);

        return view('orders.index', [
            'title' => 'Return Completed Orders',
            'order_ids' => $orders->pluck('id')->toArray(),
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM, ORDER_FILTER_STATE]),
            'actions' => [ACTION_DOWNLOAD_ORDER],
        ]);

    }

    /**
     * Lists rejected order
     * @param  Request $request
     * @return view
     */
    public function rejected(Request $request)
    {
        $orders = $this->index()->where('status', ORDER_STATUS_REJECTED);

        $orders = $this->filter_order($request, $orders);

        return view('orders.index', [
            'title' => 'Rejected Orders',
            'order_ids' => $orders->pluck('id')->toArray(),
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM]),
            'actions' => [ACTION_DOWNLOAD_ORDER],
        ]);
    }

    /**
     * Listsorder in Bucket Batch
     * @param  Request $request
     * @return view
     */
    public function bucket_batch(Request $request)
    {
        $orders = $this->index()->where('bucket_batch_id', $request->batch);

        $orders = $this->filter_order($request, $orders);

        return view('orders.index', [
            'title' => 'Orders in Bucket Batch ' . get_picking_batch($orders->first()->bucket_batch_id),
            'order_ids' => $orders->pluck('id')->toArray(),
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM]),
            'actions' => [ACTION_DOWNLOAD_ORDER, ACTION_DOWNLOAD_CN],
        ]);
    }


    /**
     * Create a new order from webhook data.
     *
     * @param  array  $data
     * @return void
     */
    public function create($webhook)
    {
        // array of products
        //$get_product = Product::whereIn('code', array_column($webhook['product'], 'code'))
        //    ->whereIn('name', array_column($webhook['product'], 'name'))
        //    ->get();
        $get_product = Product::get();

        foreach ($get_product as $key => $value) {
            $products["$value->code"] = $value->id;
        }

        $company = Company::where('code', $webhook['company'])->first();
        $company_id = $company->id;
        // $company_id = Company::where('code', $webhook['company'])->first()->id;
        // $operational_model = OperationalModel::where('id', $webhook['operation_model_id'])->first();
        // if ($operational_model->default_company_id != null) {
        //     $company_id = $operational_model->default_company_id;
        // }

        // create order
        $ids['sales_id'] = $webhook['sales_id'];
        $ids['company_id'] = $company_id;
        $data['company_id'] = $company_id;
        $data['purchase_type'] = $webhook['purchase_type'];
        $data['total_price'] = $webhook['total_price'] * 100;
        $data['sold_by'] = $webhook['sold_by'];
        $data['event_id'] = $webhook['event_id'];
        $data['courier_id'] = $webhook['courier_id'] == 0 ? COURIER_OTHERS : $webhook['courier_id'];
        $data['customer_type'] = $webhook['customer_type'];
        $data['operational_model_id'] = $webhook['operation_model_id'];
        $data['team_id'] = $webhook['team_id'];
        $data['payment_refund'] = isset($webhook['payment_refund']) ? $webhook['payment_refund'] * 100 : 0;
        $data['sales_remarks'] = str_replace(array("\r", "\n"), '', $webhook['sales_remark'] ?? '');
        $data['dt_request_shipping'] = $webhook['dt_request_shipping'] ?? '';
        $data['payment_type'] = isset($webhook['payment_type']) ? $webhook['payment_type'] : null;
        $data['processed_at'] = $webhook['dt_processing'] ?? null;
        $data['third_party_sn'] = $webhook['third_party_sn'] ?? null;
        $data['is_active'] = IS_ACTIVE;

        $data_customer = $webhook['customer'];

        if($data_customer['country'] == 1 || $data_customer['country'] == 2){
            if(strlen($data_customer['postcode']) > 5 || strlen($data_customer['postcode']) < 5){
                throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'Postcode error ');
                return;
            }
        }elseif($data_customer['country'] == 3){
            if(strlen($data_customer['postcode']) != 6 && strlen($data_customer['postcode']) != 4){
                throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'Postcode error ');
                return;
            }
        }elseif($data_customer['country'] == 0){
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'Country error ');
            return;
        }

        if($data_customer['city'] == null){
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, 'City error');
            return;
        }

        // check and add product if not found
        $product_code_list = array_column($webhook['product'], 'code');
        $not_found = array_diff($product_code_list, array_keys($products));
        if(count($not_found) > 0){
            $import_prod = Http::post($company->url . '/api/get_products', [
                'codes' => $not_found,
            ])->json();

            if($import_prod['status'] != 'success'){
                throw new \Symfony\Component\HttpKernel\Exception\HttpException(403, $import_prod['message']);
                return;
            }

            $products = $import_prod['products'];

            foreach ($products as $key => $value) {
                $product = Product::updateOrCreate(['code' => $value['product_code']], [
                    'name' => $value['product_name'],
                    'price' => $value['product_price'] * 100,
                    'is_active' => IS_ACTIVE,
                    'weight' => $value['product_weight'] * 1000,
                    'is_foc' => $value['product_foc'],
                    'max_box' => 40,
                ]);
                $products["$value[product_code]"] = $product->id;
            }
        }

         // Check for alternative postcode
         $result = AlternativePostcode::where('actual_postcode', $data_customer['postcode'])->first();

         if ($result) {
             $data_customer['postcode'] = $result->alternative_postcode;
         }

        $customer = Customer::updateOrCreate($data_customer);
        $data['customer_id'] = $customer->id;

        $order = Order::updateOrCreate($ids, $data);
        $p_ids['order_id'] = $order->id;

        //create shipping for shopee and tiktok
        $sosMed = [22,23];
        if(in_array($order->payment_type, $sosMed))
        {
            Shipping::updateOrCreate(
            [
                'order_id' => $order->id,
            ],
            [
                'created_by' => auth()->user()->id ?? 1,
                'additional_data' => $webhook['additional_data'] ?? null,
            ]);
        }

        // create order item
        // group product by code
        $product_list = array_reduce($webhook['product'], function ($result, $item) {
            if (!isset($result[$item['code']])) {
                $result[$item['code']] = $item;
            } else {
                $result[$item['code']]['quantity'] += $item['quantity'];
                $result[$item['code']]['price'] += $item['price'];
            }
            return $result;
        }, array());
        OrderItem::where('order_id', $order->id)->update(['status' => 0]);

        foreach ($product_list as $product) {
            $p_ids['product_id'] = $products[$product['code']];
            $product_data['price'] = $product['price'] * 100;
            $product_data['quantity'] = $product['quantity'];
            $product_data['is_foc'] = $product['is_foc'];
            $product_data['status'] = 1;

            OrderItem::updateOrCreate($p_ids, $product_data);
        }
            if ($order->wasRecentlyCreated) {
                $this->check_duplicate($customer, $order);
                set_order_status($order, ORDER_STATUS_PENDING, 'Order created from webhook');
            } else {
                if($order->status == ORDER_STATUS_REJECTED){
                    set_order_status($order, ORDER_STATUS_PENDING, 'Order updated from webhook, previously rejected');
                    Shipping::where('order_id', $order->id)->update(['status' => 0]);
                } else{
                    set_order_status($order, $order->status, 'Order updated from webhook');
                }
            }


        return response()->json(['message' => 'Order created successfully'], 201);
    }

    /**
     * Check possible duplicate order
     * @param  object $cur_customer, object $cur_order
     * @return boolean
     */
    public function check_duplicate($cur_customer, $cur_order)
    {
        //return if duplicate detection is off
        if(config('settings.detect_by_phone') == 0 && config('settings.detect_by_address') == 0){
            return false;
        }

        $orders = Order::with('customer')
            ->where('processed_at', '>=', Carbon::now()->subSeconds(config('settings.detection_time')))
            ->whereNot('id', $cur_order->id);

        $duplicate_address = [];
        $duplicate_phone = [];
        $all_duplicate = [];

        //check if duplicate by address
        if(config('settings.detect_by_address') == 1){
            $addresses = $orders->get();
            if(count($addresses) > 0){
                foreach($addresses as $order){
                    similar_text(strtoupper($order->customer->address), strtoupper($cur_customer->address), $percent);
                    if($percent >= config('settings.detect_by_address_percentage')){
                        $duplicate_address[] = $order;
                    }
                }
            }
        }

        //check if duplicate by phone
        if(config('settings.detect_by_phone') == 1){
            //get all addresses from orders with address id as array index
            $phones = [];
            if($cur_customer->phone != null){
                $phones[] = $cur_customer->phone;
            }
            if($cur_customer->phone_2 != null){
                $phones[] = $cur_customer->phone_2;
            }
            $phone = $orders->whereHas('customer', function ($q) use ($phones) {
                $q->whereIn('phone', $phones)
                    ->orWhereIn('phone_2', $phones);
            })->get();
            if(count($phone) > 0){
                foreach($phone as $order){
                    $duplicate_phone[] = $order;
                }
            }
        }

        //return if not duplicate
        if(count($duplicate_address) == 0 && count($duplicate_phone) == 0){
            return false;
        }

        if(config('settings.detect_by_phone') == 1 && config('settings.detect_by_address') == 0){
            $array = collect($duplicate_phone)->pluck('id')->toArray();
            if(count($duplicate_phone) > 0){
                foreach($duplicate_phone as $order){
                    $order->duplicate_orders = implode(',', $array);
                    $order->save();
                }
                $cur_order->duplicate_orders = implode(',', $array);
                $cur_order->save();
                return true;
            }
            return false;
        }

        if(config('settings.detect_by_address') == 1 && config('settings.detect_by_phone') == 0){
            $array = collect($duplicate_address)->pluck('id')->toArray();
            if(count($duplicate_address) > 0){
                foreach($duplicate_address as $order){
                    $order->duplicate_orders = implode(',', $array);
                    $order->save();
                }
                $cur_order->duplicate_orders = implode(',', $array);
                $cur_order->save();
                return true;
            }
            return false;
        }

        if(config('settings.detect_operation_type') == 'OR'){
            if(config('settings.detect_by_address') == 1 || config('settings.detect_by_phone') == 1){
                if(count($duplicate_address) > 0 || count($duplicate_phone) > 0){
                    $all_duplicate = array_merge($duplicate_address, $duplicate_phone);
                    $array = array_unique(collect($all_duplicate)->pluck('id')->toArray());
                    foreach($all_duplicate as $order){
                        $order->duplicate_orders = implode(',', $array);
                        $order->save();
                    }
                    $cur_order->duplicate_orders = implode(',', $array);
                    $cur_order->save();
                    return true;
                }
                return false;
            }
        }
        else {
            if(config('settings.detect_by_address') == 1 && config('settings.detect_by_phone') == 1){
                if(count($duplicate_address) > 0 && count($duplicate_phone) > 0){
                    $all_duplicate = array_intersect($duplicate_address, $duplicate_phone);
                    $array = collect($all_duplicate)->pluck('id')->toArray();
                    foreach($all_duplicate as $order){
                        $order->duplicate_orders = implode(',', $array);
                        $order->save();
                    }
                    $cur_order->duplicate_orders = implode(',', $array);
                    $cur_order->save();
                    return true;
                }
                return false;
            }
        }

        return false;
    }

    /**
     * filter order by request
     *
     * @param  Request $request
     * @return object
     */
    public function filter_order($request, $orders)
    {
        $orders->when($request->filled('ids'), function ($query) use ($request) {
            $query->whereIn('id', explode(',', $request->ids));
        });
        $orders->when($request->filled('bucket_id'), function ($query) use ($request) {
            $query->where('bucket_id', $request->bucket_id);
        });
        $orders->when($request->filled('order_id'), function ($query) use ($request) {
            $query->where('id', $request->order_id);
        });
        $orders->when($request->has('search'), function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                $query->where('sales_id', 'LIKE', "%$request->search%")
                    ->orWhere('sales_remarks', 'LIKE', "%$request->search%")
                    ->orWhereHas('customer', function ($q) use ($request) {
                        $q->where('name', 'LIKE', "%$request->search%")
                            ->orWhere('phone', 'LIKE', "%$request->search%")
                            ->orWhere('address', 'LIKE', "%$request->search%");
                    })
                    ->orwhereHas('shippings', function ($q) use ($request) {
                        $q->where('tracking_number', 'LIKE', "%$request->search%");
                    });
            });
        });
        $orders->when($request->filled('companies'), function ($query) use ($request) {
            $query->whereIn('company_id', $request->companies);
        });
        $orders->when($request->filled('couriers'), function ($query) use ($request) {
            $query->whereIn('courier_id', $request->couriers);
        });
        $orders->when($request->filled('events'), function ($query) use ($request) {
            $events = $request->input('events');
            foreach ($events as $event) {
                $list = explode('|', $event);
                $event_id[] = $list[0];
                $company_id[] = $list[1];
            }
            $query->whereIn('event_id', $event_id);
            $query->whereIn('company_id', $company_id);
        });
        $orders->when($request->filled('op_models'), function ($query) use ($request) {
            $query->whereIn('operational_model_id', $request->op_models);
        });
        $orders->when($request->filled('teams'), function ($query) use ($request) {
            $query->whereIn('team_id', $request->teams);
        });
        $orders->when($request->filled('customer_types'), function ($query) use ($request) {
            $query->whereIn('customer_type', $request->customer_types);
        });
        $orders->when($request->filled('purchase_types'), function ($query) use ($request) {
            $query->whereIn('purchase_type', $request->purchase_types);
        });
        $orders->when($request->filled('products'), function ($query) use ($request) {
            if(count($request->products) == 1){
                $query->whereHas('items', function ($q) use ($request) {
                    $q->whereIn('product_id', $request->products);
                });
            }
            else {
                $query->whereHas('items', function ($q) use ($request) {
                    $q->whereIn('product_id', $request->products);
                }, '=', count($request->products));

                $query->whereDoesntHave('items', function ($q) use ($request) {
                    $q->whereNotIn('product_id', $request->products);
                });
            }
        });
        $orders->when($request->filled('not_products'), function ($query) use ($request) {
            $query->whereDoesntHave('items', function ($q) use ($request) {
                $q->whereIn('product_id', $request->not_products);
            });
        });
        $orders->when($request->filled('states'), function ($query) use ($request) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->whereIn('state', $request->states);
            });
        });

        $orders->when($request->filled('date_type'), function ($query) use ($request) {
            switch($request->date_type){
                case 1: //date order added
                    $request->date_from != null ? $query->where('created_at', '>=', date("Y-m-d H:i:s", strtotime($request->date_from))) : '';
                    $request->date_to != null ? $query->where('created_at', '<', date("Y-m-d 23:59:59", strtotime($request->date_to))) : '';
                    break;
                case 2: //date request shipping
                    $request->date_from != null ? $query->where('dt_request_shipping', '>=', date("Y-m-d H:i:s", strtotime($request->date_from))) : '';
                    $request->date_to != null ? $query->where('dt_request_shipping', '<', date("Y-m-d 23:59:59", strtotime($request->date_to))) : '';
                    break;
                case 3: //date scan parcel
                    $query->whereHas("shippings", function($q) use ($request){
                        $request->date_from != null ? $q->where('scanned_at', '>=', date("Y-m-d H:i:s", strtotime($request->date_from))) : '';
                        $request->date_to != null ? $q->where('scanned_at', '<', date("Y-m-d 23:59:59", strtotime($request->date_to))) : '';
                    });
                    break;
                default:
                    break;

            }
        });

        $orders->when($request->filled('status'), function ($query) use ($request) {
            $query->where('status', $request->status);
        });

        $orders->when($request->filled('platforms'), function ($query) use ($request) {
            $query->whereIn('payment_type', $request->platforms);
        });

        $orders->when($request->filled('statuses'), function ($query) use ($request) {
            $query->whereIn('status', $request->statuses);
        });

        return $orders;
    }

    /**
     * Scan barcode page
     * @param  null
     * @return view
     */
    public function scan()
    {
        return view('orders.scan', [
            'title' => 'Scan Barcode',
        ]);
    }

    /**
     * Scan barcode
     * @param  Request $request
     * @return view
     */
    public function scan_barcode(Request $request)
    {
        $request->validate([
            'code' => 'required',
        ]);

        //get shipping details
        $shipping = Shipping::with(['order', 'order.items', 'order.items.product', 'scannedBy'])
            ->where('tracking_number', $request->code)
            ->first();

        if (!$shipping) {
            return back()->with('error', 'Parcel Not Found')->with('order', $shipping);
        }

        //if not scanned, store scan time
        if ($shipping->scanned_at == null) {
            $shipping->scanned_at = $data['scanned_at'] = Carbon::now();
            $shipping->scanned_by = $data['scanned_by'] = auth()->user()->id ?? 1;

            if(config('settings.scan_multiple') == IS_ACTIVE){
                Shipping::where('tracking_number', $request->code)->update($data);
                $other_parcel = Shipping::where('order_id', $shipping->order_id)
                    ->where('status', IS_ACTIVE)
                    ->whereNull('scanned_at')
                    ->get();
                if(count($other_parcel) == 0){
                    set_order_status($shipping->order, ORDER_STATUS_READY_TO_SHIP, "Item Scanned by " . auth()->user()->name);
                }
                else{
                    set_order_status($shipping->order, ORDER_STATUS_PACKING, "Item Scanned by " . auth()->user()->name);
                }
            } else {
                Shipping::where('order_id', $shipping->order_id)->update($data);
                set_order_status($shipping->order, ORDER_STATUS_READY_TO_SHIP, "Item Scanned by " . auth()->user()->name);
            }

            return back()->with('success', 'Parcel Scanned Successfully')->with('shipping', $shipping);
        } else {
            //check if order return
            if($shipping->order->status == ORDER_STATUS_RETURN_SHIPPING){
                set_order_status($shipping->order, ORDER_STATUS_RETURNED, "Item Returned Scanned by " . auth()->user()->name);
                return back()->with('success', 'Return Parcel Scanned Successfully')->with('shipping', $shipping);
            }
            return back()->with('error', 'Parcel Already Scanned')->with('shipping', $shipping);
        }
    }

    /**
     * Download order csv
     * @param  Request $request
     * @return json
     */
    // public function download_order_csv(Request $request)
    // {
    //     // return $request;
    //     $fileName = date('Ymdhis') . '_list_of_orders.csv';
    //     $orders = $this->index();

    //     $orders->whereIn('id', $request->order_ids);

    //     $orders = $this->filter_order($request, $orders);

    //     $orders = $orders->get();

    //     Excel::store(new OrderExport($orders),"public/".$fileName);
    //     // \App\Jobs\DeleteTempExcelFileJob::dispatch("public/".$fileName)->delay(Carbon::now()->addMinute(2));

    //     return response([
    //         "file_name"=> $fileName
    //     ]);
    // }

    public function download_order_csv(Request $request)
    {
        $fileName = date('Ymdhis') . '_list_of_orders.csv';
        $orders = $this->index();
        $orders->whereIn('id', $request->order_ids);
        $orders = $this->filter_order($request, $orders);
        $orders = $orders->get();

        // Get headers from 
        $headers = $this->getHeader($request->template_id);

        $columnName = TemplateMain::join('template_columns', 'template_mains.id', '=', 'template_columns.template_main_id')
            ->join('column_mains', 'template_columns.column_main_id', '=', 'column_mains.id')
            ->select(
                'template_mains.*',
                'template_columns.*',
                'column_mains.*'
            )
            ->where('template_mains.delete_status', '!=', 1)
            ->whereIn('template_columns.template_main_id', function($query) use ($request) {
                $query->select('id')
                    ->from('template_mains')
                    ->where('id', $request->template_id);
            })
            ->get();

        Excel::store(new OrderExport($orders, $headers, $columnName), "public/" . $fileName);

        return response([
            "file_name" => $fileName,
        ]);
    }

    private function getHeader($templateId)
    {
        $template = TemplateMain::with('columns')->find($templateId);

        // Split the template_header string into an array
        $headers = explode(', ', $template->template_header);

        return $headers;
    }

    /** Change Postcode view
     *
     */
    public function change_postcode_view(){
        return view('orders.change_postcode', [
            'title' => 'Change Postcode',
            'companies' => Company::all(),
        ]);
    }
    /**
     * Change Postcode
     */
    public function change_postcode(Request $request)
    {
        $request->validate([
            'postcode' => 'required|digits:5',
            'sales_id' => 'required|exists:orders,sales_id',
            'company_id' => 'required|exists:companies,id',
        ]);

        $order = Order::with('customer')
            ->where('sales_id', $request->sales_id)
            ->where('company_id', $request->company_id)
            ->first();

        if (!$order) {
            return back()->with('error', 'Order Not Found');
        }
        $old_postcode = $order->customer->postcode;
        $order->customer->postcode = $request->postcode;
        $order->customer->save();

        OrderLog::create([
            'order_id' => $order->id,
            'order_status_id' => $order->status,
            'remarks' => 'Postcode Changed from '. $old_postcode .' to ' . $request->postcode,
            'created_by' => auth()->user()->id ?? 1,
        ]);

        if($request->redirect){
            return redirect($request->redirect)->with('success', 'Postcode Changed Successfully');
        }
        return back()->with('success', 'Postcode Changed Successfully');

    }

    public function scanned_parcel($year, $month, $day = null){

        // count scanned parcel by scanned by
        $parcel = Shipping::whereYear('scanned_at', $year)
            ->whereMonth('scanned_at', $month)
            ->where('status', IS_ACTIVE);

        $scanned_parcel = $parcel->get();

        // tracking number unique
        $scanned_parcel_count = $scanned_parcel->unique('tracking_number')
            ->groupBy('scanned_by')
            ->map(function ($item, $key) {
                return count($item);
            });

            //filter by today
            if($day != null){
                $scanned_parcel_day = $parcel->whereDay('scanned_at', $day)->get();
                $scanned_parcel_count_today = $scanned_parcel_day->unique('tracking_number')
                ->groupBy('scanned_by')
                ->map(function ($item, $key) {
                    return count($item);
                });
                $users_today = \App\Models\User::whereIn('id', $scanned_parcel_count_today->keys()->toArray())->get();
                $scans_today = [];
                foreach($users_today as $user){
                    $scans_today[] = [
                        'name' => $user->name,
                        'img' => $user->staff_id . '-test.jpeg',
                        'count' => $scanned_parcel_count_today[$user->id]
                    ];
                }
                $result['scans_today'] = $scans_today;
            }

            $users = \App\Models\User::whereIn('id', $scanned_parcel_count->keys()->toArray())->get();
            $scans = [];

        foreach($users as $user){
            $scans[] = [
                'name' => $user->name,
                'img' => $user->staff_id . '.jpeg',
                'count' => $scanned_parcel_count[$user->id]
            ];
        }

        $result['scans'] = $scans;

        $current_process = new DashboardController();
        $result['current_process'] = $current_process->current_process(true)->original['count'];

        return $result;
    }

    public function scan_setting(){
        $settings = Setting::haveParent()->where('type', SETTING_TYPE_SCAN)->get();
        return view('orders.scan_setting', [
            'title' => 'Scan Setting',
            'settings' => $settings,
        ]);
    }

    public function get_template_main(Request $request)
    {
        $status = $request->input('status');

        // Check if the status is 'pending'
        if ($status == 'pending') {
            $data = TemplateMain::where('delete_status', 0)
                ->where('template_type', 1)
                ->get();
        }elseif ($status == 'processing') {
            $data = TemplateMain::where('delete_status', 0)
                ->where('template_type', 2)
                ->get();
        }elseif ($status == 'packing') {
            $data = TemplateMain::where('delete_status', 0)
                ->where('template_type', 3)
                ->get();
        }elseif ($status == 'ready-to-ship') {
            $data = TemplateMain::where('delete_status', 0)
                ->where('template_type', 4)
                ->get();
        }elseif ($status == 'shipping') {
            $data = TemplateMain::where('delete_status', 0)
                ->where('template_type', 5)
                ->get();
        }elseif ($status == 'delivered') {
            $data = TemplateMain::where('delete_status', 0)
                ->where('template_type', 6)
                ->get();
        }elseif ($status == 'returned' || $status == 'return-completed') {
            $data = TemplateMain::where('delete_status', 0)
                ->where('template_type', 7)
                ->get();
        }elseif ($status == 'rejected') {
            $data = TemplateMain::where('delete_status', 0)
                ->where('template_type', 9)
                ->get();
        }else {
            
        }

        $templateMain = [];

        foreach ($data as $row) {
            $templateMain[] = [
                'value' => $row->id,
                'label' => $row->template_name
            ];
        }

        return response()->json($templateMain);
    }
}
