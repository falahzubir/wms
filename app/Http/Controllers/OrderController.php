<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * List all active orders
     * @return view
     */
    public function index(Request $request)
    {
        $orders = Order::with(['customer', 'items', 'items.product', 'shipping'])
            ->where('is_active', IS_ACTIVE);

        if ($request->bucket) {
            $orders->where('bucket_id', $request->bucket);
        }

        return view('orders.overall', [
            'title' => 'List Orders',
            'orders' => $orders->paginate(PAGINATE_LIMIT),
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

    public function pending(Request $request)
    {
        return view('orders.pending', ['title' => 'Pending Orders']);
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
}
