<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

    /*
    * Barcode Scan
    * @param Request $request
    * @return json
    */
    public function barcode_scan(Request $request)
    {
        $request->validate([
            'barcode' => 'required'
        ]);

        $order = Order::with(['shipping', 'items', 'items.product'])->where('is_active', IS_ACTIVE)
            ->whereHas('shipping', function ($query) use ($request) {
            $query->where('tracking_number', $request->barcode);
        })->first();

        if ($order->count() == 0) {
            return response()->json(['error' => 'Parcel not found']);
        }

        if ($order->shipping->scanned_at == null) {
            $order->shipping->scanned_at = Carbon::now();
            $order->shipping->scanned_by = auth()->user()->id ?? 1;
            $order->shipping->save();

            //update order status
            set_order_status($order, ORDER_STATUS_READY_TO_SHIP);

            return response()->json([
                'success' => 'ok',
                'message' => 'Parcel Scanned Successfully',
            ], 200);

        } else {
            return response()->json([
                'success' => 'ok',
                'message' => 'Parcel already scanned by '. $order->shipping->scanned_by,
            ], 200);
        }

    }
}
