<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\ShopeeTrait;

class TestController extends Controller
{
    public function test()
    {
        $order_sn = '231025TES3JTW3';
        // $address_id = '200007694';
        // $pickup_time_id = '231024QX6B35H2';
        $res = ShopeeTrait::getOrderDetail($order_sn);
        // $res = ShopeeTrait::getShippingParameter($order_sn);
        // $res = ShopeeTrait::createShippingDocument($order_sn);
        // $res = ShopeeTrait::getShippingDocumentResult($order_sn);
        // $res = ShopeeTrait::getTrackingNumber($order_sn);
        dd(json_decode($res));
    }
}
