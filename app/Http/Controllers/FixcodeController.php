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
            'QITI' => array('url' => 'https://qastg.groobok.com', 'company_id' => 3),
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

        // Send orders to company
        $response = Http::post($companies[$company_id]['url'] . '/fix_code/fix_processing_date', [
            'sales_ids' => $orders->pluck('sales_id')->toArray(),
        ]);

        // Check for network errors
        if ($response->failed()) {
            return response()->json(['message' => 'Error: Network error'], 500);
        }

        $responseData = $response->json();
        // Check if the response is empty
        if (empty($responseData)) {
            return response()->json(['message' => 'Error: No data received from company'], 500);
        }

        // Check if the response status is success
        if ($responseData['status'] === 'success') {
            // Update orders with processing date
            foreach ($responseData['data'] as $order) {
                if (isset($order['sales_id'], $order['dt_processing'])) {
                    Order::where('sales_id', $order['sales_id'])->update([
                        'processed_at' => $order['dt_processing'],
                    ]);
                }
            }
            return response()->json(['message' => 'Success'], 200);
        } else {
            // Handle other types of failures
            return response()->json(['message' => 'Error: ' . $responseData['message']], 500);
        }

    }
}
