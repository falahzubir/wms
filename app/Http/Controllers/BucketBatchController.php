<?php

namespace App\Http\Controllers;

use App\Exports\PickingListExport;
use App\Models\BucketBatch;
use App\Models\Order;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BucketBatchController extends Controller
{
    /**
     * Generate Picking List for the orders in the bucket by batch
     * @param Obj $request bucket_id & order_ids (list of order ids)
     * @return \Illuminate\Http\Response
     */
    public function generate_pl(Request $request)
    {
        $prev_batch = BucketBatch::whereDate('created_at', date('Y-m-d'))->latest('created_at')->first()->batch_id ?? 0;
        // $prev_batch = BucketBatch::where('bucket_id', $request->bucket_id)->whereDate('created_at', Carbon::now()->month)->latest('created_at')->first()->batch_id ?? 0;

        $orders = Order::whereIn('id', $request->order_ids)->whereNull('bucket_batch_id')->get();
        if($orders->count() == 0){
            return response()->json([
                'message' => 'No orders or all orders already added to to picking list',
            ], 422);
        }
        $batch = BucketBatch::create([
            'batch_id' => $prev_batch + 1,
            'bucket_id' => $request->bucket_id,
        ]);

        $ids = $orders->pluck('id')->toArray();
        Order::whereIn('id', $ids)->update(['bucket_batch_id' => $batch->id]);

        return response()->json([
            'message' => 'Orders added to batch successfully',
            'batch_id' => $batch->id,
        ]);
    }

    /**
     * Download Picking List for the orders in the bucket by batch
     *
     * @return \Illuminate\Http\Response
     */
    public function download_pl(BucketBatch $batch)
    {
        $orders = Order::with(['items', 'items.product'])->where('bucket_batch_id', $batch->id)->get();

        $products = [];
        $total_products = ['loose' => 0, 'box' => 0];
        foreach($orders as $order){
            if($order->items->sum('quantity') < BOX_MINIMUM_QUANTITY){
                foreach ($order->items as $item) {
                    $products[$item->product->code] = [
                        'loose' => isset($products[$item->product->code]['loose']) ? $products[$item->product->code]['loose']+$item->quantity : $item->quantity,
                        'box' => isset($products[$item->product->code]['box']) ? $products[$item->product->code]['box'] : 0,
                    ];
                    $total_products['loose'] += $item->quantity;
                }
            }else{
                foreach ($order->items as $item) {
                    $products[$item->product->code] = [
                        'box' => isset($products[$item->product->code]['box']) ? $products[$item->product->code]['box']+$item->quantity : $item->quantity,
                        'loose' => isset($products[$item->product->code]['loose']) ? $products[$item->product->code]['loose'] : 0
                    ];
                    $total_products['box'] += $item->quantity;
                }
            }
        }

        $response = Excel::download(new PickingListExport($products, $total_products), 'picking_list_' . get_picking_batch($batch->id, '_') .'.csv', \Maatwebsite\Excel\Excel::CSV, [
            'Content-Type' => 'text/csv',
        ]);
        ob_end_clean();

        return $response;
    }

}
