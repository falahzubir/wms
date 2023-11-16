<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Traits\ShopeeTrait;
use App\Http\Traits\TiktokTrait;

class TestController extends Controller
{
    public function test()
    {
        // $order_sn = '{"ordersn":"2310309KKMWVGR","package_number":"OFG152330297317883","tracking_no":"SPE4701582423","shipping_document_type":"THERMAL_AIR_WAYBILL"}';
        // $order_sn = json_decode($order_sn,true);
        // $address_id = '200007694';
        // $pickup_time_id = '231024QX6B35H2';
        // $res = ShopeeTrait::getOrderDetail($order_sn);
        // $res = ShopeeTrait::getShippingParameter('2311097221EU5E');
        // $res = ShopeeTrait::createShippingDocument($order_sn);
        // $res = ShopeeTrait::getTrackingNumber($order_sn);
        // $res = ShopeeTrait::getTrackingNumber($order_sn);
        // dd(json_decode($res));

        $order_sn = '{"shop_id":"7495003145797929663","order_id":"576579459619456075","package_id":"1153050721332855883"}';
        $order_sn = json_decode($order_sn,true);

        // // $res = TiktokTrait::getOrderDetails($order_sn);
        // // $res = TiktokTrait::getPackageDetail($order_sn);
        // $res = ShopeeTrait::getOrderDetail('2311108MB65BNJ');
        // // $res = ShopeeTrait::getShippingParameter('2311108MB65BNJ');
        // // $res = ShopeeTrait::updateShipOrder('2311108MB65BNJ');
        // $res = TiktokTrait::shipOrder($order_sn);
        $res = TiktokTrait::getOrderDetails($order_sn);
        $resPonse = json_decode($res,true);
        $tracking_number = $resPonse['data']['order_list'][0]['order_line_list'][0]['tracking_number'];
        $package_number = $resPonse['data']['order_list'][0]['package_list'][0]['package_id'];
        dd($package_number);

        // $res = '{
        //     "package_list": [
        //       {
        //         "package_id": "1232131231243123213",
        //         "pick_up": {
        //           "pick_up_end_time": 1623812664,
        //           "pick_up_start_time": 1623812664
        //         },
        //         "pick_up_type": 1,
        //         "self_shipment": {
        //           "shipping_provider_id": "6617675021119438849",
        //           "tracking_number": "JX12345"
        //         }
        //       }
        //     ]
        //   }';

        // $res = json_decode($res,true);

        echo '<pre>';
        print_r($res);
    }
}
