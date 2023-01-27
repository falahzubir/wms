<?php

namespace App\Http\Controllers;

use App\Exports\OrderExport;
use App\Models\Company;
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
        return Order::with(['customer', 'items', 'items.product', 'shipping'])
            ->where('is_active', IS_ACTIVE);

        // return $orders;

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
            'actions' => [ACTION_ADD_TO_BUCKET, ACTION_GENERATE_CN, ACTION_DOWNLOAD_CN, ACTION_DOWNLOAD_ORDER, ACTION_UPLOAD_TRACKING_BULK],
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
        $orders = $this->index()->whereIn('status', [ORDER_STATUS_PENDING, ORDER_STATUS_PENDING_ON_BUCKET]);

        $orders = $this->filter_order($request, $orders);

        return view('orders.index', [
            'title' => 'Pending Orders',
            'orders' => $orders->paginate(PAGINATE_LIMIT),
            'actions' => [ACTION_ADD_TO_BUCKET, ACTION_GENERATE_CN, ACTION_DOWNLOAD_CN, ACTION_DOWNLOAD_ORDER, ACTION_UPLOAD_TRACKING_BULK],
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
            'actions' => [ACTION_ADD_TO_BUCKET, ACTION_GENERATE_CN, ACTION_DOWNLOAD_CN, ACTION_DOWNLOAD_ORDER, ACTION_UPLOAD_TRACKING_BULK],
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
            'actions' => [ACTION_DOWNLOAD_CN, ACTION_DOWNLOAD_ORDER],
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
            'actions' => [ACTION_DOWNLOAD_CN, ACTION_DOWNLOAD_ORDER],
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
            'actions' => [ACTION_DOWNLOAD_CN, ACTION_DOWNLOAD_ORDER],
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
            'actions' => [ACTION_DOWNLOAD_ORDER],
        ]);
    }

    /**
     * Lists completed order
     * @param  Request $request
     * @return view
     */

    public function completed(Request $request)
    {
        $orders = $this->index()->where('status', ORDER_STATUS_COMPLETED);

        $orders = $this->filter_order($request, $orders);

        return view('orders.index', [
            'title' => 'Completed Orders',
            'orders' => $orders->paginate(PAGINATE_LIMIT),
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
        $get_product = Product::whereIn('code', array_column($webhook['product'], 'code'))
            ->whereIn('name', array_column($webhook['product'], 'name'))
            ->get();

        foreach ($get_product as $key => $value) {
            $products[$value->code] = $value->id;
        }

        // create order
        $ids['sales_id'] = $webhook['sales_id'];
        $data['company_id'] = Company::where('code', $webhook['company'])->first()->id;
        $data['purchase_type'] = $webhook['purchase_type'];
        $data['total_price'] = $webhook['total_price'] * 100;
        $data['sold_by'] = $webhook['sold_by'];
        $data['event_id'] = $webhook['event_id'];

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
            return $query->where('bucket_id', $request->bucketId);
        });
        $orders->when($request->has('search'), function ($query) use ($request) {
            return $query->where('sales_id', 'LIKE', "%$request->search%")
                ->orWhereHas('customer', function ($q) use ($request) {
                    return $q->where('name', 'LIKE', "%$request->search%")
                        ->orWhere('phone', 'LIKE', "%$request->search%");
                })
                ->orwhereHas('shipping', function ($q) use ($request) {
                    return $q->where('tracking_number', 'LIKE', "%$request->search%");
                });
        });
        $orders->when($request->filled('date_from'), function ($query) use ($request) {
            return $query->where('created_at', '>=', date("Y-m-d H:i:s", strtotime($request->date_from)));
        });
        $orders->when($request->filled('date_to'), function ($query) use ($request) {
            return $query->where('created_at', '<', date("Y-m-d 23:59:59", strtotime($request->date_to)));
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

        return Excel::download(new OrderExport($orders), 'orders_'.date('Ymdhis').'.csv');
    }
}
