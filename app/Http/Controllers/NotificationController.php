<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function list()
    {
        $notifications = [];

        //need to approve for shipping
        //laravel permission check
        if(Auth::user()->can('order.approve_for_shipping')){
            $orders = Order::where('status', ORDER_STATUS_READY_TO_SHIP)
            ->whereNotIn('courier_id', AUTO_SHIPPING_COURIER)
            ->get();

            $manual_shipping_couriers = Courier::whereNotIn('id', AUTO_SHIPPING_COURIER)->where('status', true)->get('id')->pluck('id')->toArray();
            logger($manual_shipping_couriers);
            //append notification
            if($orders->count() > 0){
                array_push($notifications, [
                    'message' => "There are {$orders->count()} orders need to approve for shipping",
                    'count' => $orders->count(),
                    'url' => route('orders.readyToShip', ['couriers' => $manual_shipping_couriers]),
                ]);
            }
        }

        return response()->json($notifications);
    }
}
