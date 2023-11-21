<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\ShopeeTrait;

class TestController extends Controller
{
    public function test()
    {
        $order_sn = '{"ordersn":"2310309KKMWVGR","package_number":"OFG152330297317883","tracking_no":"SPE4701582423","shipping_document_type":"THERMAL_AIR_WAYBILL"}';
        $order_sn = json_decode($order_sn,true);
        // $address_id = '200007694';
        // $pickup_time_id = '231024QX6B35H2';
        // $res = ShopeeTrait::getOrderDetail($order_sn);
        $res = ShopeeTrait::getShippingParameter('2311097221EU5E');
        // $res = ShopeeTrait::createShippingDocument($order_sn);
        // $res = ShopeeTrait::getTrackingNumber($order_sn);
        // $res = ShopeeTrait::getTrackingNumber($order_sn);
        dd(json_decode($res));
    }
}
