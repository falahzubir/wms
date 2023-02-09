<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderApiController extends Controller
{
    /*
    * Reject Order
    * @param Request $request
    * @return json
    */
    public function reject(Request $request)
    {

        $order = Order::find($request->order_id);
        $order->status = ORDER_STATUS_REJECTED;
        $order->save();

        set_order_status($order, ORDER_STATUS_REJECTED);

        return response()->json([
            'message' => 'Order rejected'
        ], 200);
    }

}
