<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return view('orders.overall', ['title' => 'List Orders']);
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
        $h = `{
            "sale_id":"01234",
            "company":"EH",
            "customer":{
               "name":"Muhamad Iqbal",
               "phone":"60199928102",
               "Address":"Test Address",
               "postcode":"01000",
               "state":"Perlis"
            },
            "product":[
               {
                  "code":"KPK",
                  "price":"30.00",
                  "quantity":"5",
                  "is_foc":"no"
               },
               {
                  "code":"SHK",
                  "price":"0.00",
                  "quantity":"1",
                  "is_foc":"yes"
               }
            ],
            "order_price":"135.00",
            "shipping_price":"5.70",
            "total_price":"140.70",
            "sold_by":"392",
            "event_id":"1"
         }`;

         logger($h);
        // create order
        $ids['sales_id'] = $webhook['sales_id'];
        $data['company'] = $webhook['company'];
        $data['order_price'] = $webhook['order_price'];
        $data['shipping_price'] = $webhook['shipping_price'];
        $data['total_price'] = $webhook['total_price'];
        $data['sold_by'] = $webhook['sold_by'];
        $data['event_id'] = $webhook['event_id'];
        $data['customer_data'] = json_encode($webhook['customer']);

        Order::updateOrCreate($ids, $data);

        return response()->json(['message' => 'Order created successfully'], 201);
    }
}
