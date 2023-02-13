<?php

namespace App\Http\Controllers;

use App\Exports\OrderExport;
use App\Models\Company;
use App\Models\Courier;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{

    /**
     * Initiate orders list
     * @param  null
     * @return Order
     */
    public function index()
    {
        return Order::with(['customer', 'items', 'items.product', 'shipping', 'bucket', 'batch', 'company', 'courier'])
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
            $filter_data['sale_events'] = true; // http request
        }
        if (!in_array(ORDER_FILTER_TEAM, $exclude)) {
            $filter_data['teams'] = true; //http request
        }
        if (!in_array(ORDER_FILTER_OP_MODEL, $exclude)) {
            $filter_data['operational_models'] = true; //http request
        }
        if (!in_array(ORDER_FILTER_STATE, $exclude)) {
            $filter_data['states'] = MY_STATES; //http request
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
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM, ORDER_FILTER_OP_MODEL]),
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
        $orders = $this->index()->whereIn('status', [ORDER_STATUS_PENDING]);

        $orders = $this->filter_order($request, $orders);

        return view('orders.index', [
            'title' => 'Pending Orders',
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM, ORDER_FILTER_OP_MODEL]),
            'actions' => [ACTION_ADD_TO_BUCKET, ACTION_DOWNLOAD_ORDER],
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
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM, ORDER_FILTER_OP_MODEL]),
            'actions' => [ACTION_ADD_TO_BUCKET, ACTION_DOWNLOAD_CN, ACTION_DOWNLOAD_ORDER, ACTION_UPLOAD_TRACKING_BULK, ACTION_GENERATE_PICKING],
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
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM, ORDER_FILTER_OP_MODEL]),
            'actions' => [ACTION_DOWNLOAD_ORDER],
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
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM, ORDER_FILTER_OP_MODEL]),
            'actions' => [ACTION_DOWNLOAD_ORDER],
        ]);
    }

    /**
     * Lists shipped order
     * @param  Request $request
     * @return view
     */
    public function shipping(Request $request)
    {
        $orders = $this->index()->where('status', ORDER_STATUS_SHIPPING);

        $orders = $this->filter_order($request, $orders);

        return view('orders.index', [
            'title' => 'Shipping Orders',
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM, ORDER_FILTER_OP_MODEL]),
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
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM, ORDER_FILTER_OP_MODEL]),
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
            'title' => 'Rejected Orders',
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM, ORDER_FILTER_OP_MODEL]),
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
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'filter_data' => $this->filter_data_exclude([ORDER_FILTER_CUSTOMER_TYPE, ORDER_FILTER_TEAM, ORDER_FILTER_OP_MODEL]),
            'actions' => [ACTION_DOWNLOAD_ORDER],
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

        $company_id = Company::where('code', $webhook['company'])->first()->id;

        // create order
        $ids['sales_id'] = $webhook['sales_id'];
        $ids['company_id'] = $company_id;
        $data['company_id'] = $company_id;
        $data['purchase_type'] = $webhook['purchase_type'];
        $data['total_price'] = $webhook['total_price'] * 100;
        $data['sold_by'] = $webhook['sold_by'];
        $data['event_id'] = $webhook['event_id'];
        $data['courier_id'] = $webhook['courier_id'];
        $data['customer_type'] = $webhook['customer_type'];
        $data['operational_model_id'] = $webhook['operation_model_id'];
        $data['team_id'] = $webhook['team_id'];
        $data['payment_refund'] = isset($webhook['payment_refund']) ? $webhook['payment_refund'] * 100 : 0;

        $customer = Customer::updateorCreate($webhook['customer']);
        $data['customer_id'] = $customer->id;

        $order = Order::updateOrCreate($ids, $data);
        $p_ids['order_id'] = $order->id;

        // create order items
        foreach ($webhook['product'] as $product) {
            $p_ids['product_id'] = $products[$product['code']];
            $product_data['price'] = $product['price'] * 100;
            $product_data['quantity'] = $product['quantity'];
            $product_data['is_foc'] = $product['is_foc'];

            OrderItem::updateOrCreate($p_ids, $product_data);
        }

        set_order_status($order, ORDER_STATUS_PENDING, 'Order created from webhook');

        return response()->json(['message' => 'Order created successfully'], 201);
    }

    /**
     * filter order by request
     *
     * @param  Request $request
     * @return object
     */
    public function filter_order($request, $orders)
    {
        $orders->when($request->filled('bucket_id'), function ($query) use ($request) {
            $query->where('bucket_id', $request->bucket_id);
        });
        $orders->when($request->has('search'), function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                $query->where('sales_id', 'LIKE', "%$request->search%")
                    ->orWhereHas('customer', function ($q) use ($request) {
                        $q->where('name', 'LIKE', "%$request->search%")
                            ->orWhere('phone', 'LIKE', "%$request->search%");
                    })
                    ->orwhereHas('shipping', function ($q) use ($request) {
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
            $query->whereIn('event_id', $request->events);
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
            $query->whereHas('items', function ($q) use ($request) {
                $q->whereIn('product_id', $request->products);
            });
        });
        $orders->when($request->filled('states'), function ($query) use ($request) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->whereIn('state', $request->states);
            });
        });

        $orders->when($request->filled('date_from'), function ($query) use ($request) {
            $query->where('created_at', '>=', date("Y-m-d H:i:s", strtotime($request->date_from)));
        });
        $orders->when($request->filled('date_to'), function ($query) use ($request) {
            $query->where('created_at', '<', date("Y-m-d 23:59:59", strtotime($request->date_to)));
        });
        $orders->when($request->filled('status'), function ($query) use ($request) {
            $query->where('status', $request->status);
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

        $order = Order::with(['shipping', 'items', 'items.product'])->where('is_active', IS_ACTIVE);

        //filter tracking number on shipping table
        $order->whereHas('shipping', function ($query) use ($request) {
            $query->where('tracking_number', $request->code);
        });

        $order = $order->first();

        if ($order->count() == 0) {
            return back()->with('error', 'Parcel Not Found')->with('order', $order);
        }

        //if not scanned, store scan time
        if ($order->shipping->scanned_at == null) {
            $order->shipping->scanned_at = Carbon::now();
            $order->shipping->scanned_by = auth()->user()->id ?? 1;
            $order->shipping->save();

            //update order status
            set_order_status($order, ORDER_STATUS_READY_TO_SHIP);

            return back()->with('success', 'Parcel Scanned Successfully')->with('order', $order);
        } else {
            return back()->with('error', 'Parcel Already Scanned')->with('order', $order);
        }

        // if ($order) {
        //     return back()->with('error', 'This Parcel was Already Scanned')->with('order', $order);
        // } else {
        //     return back()->with('success', 'Parcel Scan Successful');
        // }

    }

    /**
     * Download order csv
     * @param  Request $request
     * @return json
     */
    public function download_order_csv(Request $request)
    {
        return $request;
        $orders = $this->index();

        $orders->whereIn('id', $request->order_ids);

        $orders = $this->filter_order($request, $orders);

        $orders = $orders->get();

        $response =  Excel::download(new OrderExport($orders), 'orders_' . date('Ymdhis') . '.csv');

        ob_end_clean();

        return $response;

    }
}
