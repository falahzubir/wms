<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FixcodeController extends Controller
{
    public function processing_date($company_id)
    {
        $companies = [
            'EH' => array('url' => 'https://bosemzi.com', 'company_id' => 1),
            'ED' => array('url' => 'https://aa.bosemzi.com', 'company_id' => 2),
            // 'QITI' => array('url' => 'https://qastg.groobok', 'company_id' => 3),
            'QITI' => array('url' => 'http://boslagi.zinx2yqtmb-ez94dnv9n3mr.p.temp-site.link', 'company_id' => 3),
            'INT' => array('url' => 'https://int.bosemzi.com', 'company_id' => 4),
        ];

        //check if company_id exists
        if (!array_key_exists($company_id, $companies)) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        //get orders from company
        $orders = Order::where('company_id', $companies[$company_id]['company_id'])->get();

        //dummy url
        $companies[$company_id]['url'] = 'http://localhost/bos';

        //send orders to company
        $response = Http::post($companies[$company_id]['url'] . '/fix_code/fix_processing_date', [
            'sales_ids' => $orders->pluck('sales_id')->toArray(),
        ]);

        if ($response->status() == 200) {

            $response = $response->json();

            //update orders with processing date
            foreach ($response as $order) {
                Order::where('sales_id', $order['sales_id'])->update([
                    'processed_at' => $order['dt_processing'],
                ]);
            }

            return response()->json(['message' => 'Success'], 200);

        } else {
            return response()->json(['message' => 'Failed'], 500);
        }
    }
}
