<?php

namespace App\Http\Controllers;

use App\Models\BucketBatch;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{

    /**
     * Display dashboard.
     *
     * @return view
     */
    public function index()
    {
        if(in_array('packer', auth()->user()->getRoleNames()->toArray())){
            return redirect()->route('orders.scan');
        }
        $batches = BucketBatch::with(['user','order'])->orderBy('created_at', 'desc')->limit(6)->get();

        return view('dashboard', [
            'title' => 'Dashboard',
            'batches' => $batches,
        ]);
    }

    /**
     * Display a current process count.
     *
     * @return json
     */
    public function current_process($live = false)
    {
        $status = [ORDER_STATUS_PENDING, ORDER_STATUS_PROCESSING, ORDER_STATUS_PACKING, ORDER_STATUS_READY_TO_SHIP, ORDER_STATUS_SHIPPING];
        $orders = Order::where('is_active', IS_ACTIVE)->whereIn('status', $status)->groupBy('status')->get([
            'status',
            DB::raw('count(*) as total')
        ])->pluck('total', 'status')->all();

        foreach ($status as $s) {
            if (!isset($orderCounts[$s])) {
                $orderCounts[$s] = 0;
            }
        }

        foreach ($orders as $status => $count) {
            $orderCounts[$status] = $count;
        }

        if($live){

            //order count for dhl orders only
            $dhlOrders = Order::with('shippings')->where('is_active', IS_ACTIVE)->where('status', ORDER_STATUS_SHIPPING)->groupBy('status')
                ->whereHas('shippings', function ($query) {
                    $query->where('courier', 'dhl-ecommerce');
                })->get([
                    'status',
                    DB::raw('count(*) as total')
                ])->pluck('total', 'status')->all();

            foreach ($dhlOrders as $status => $count) {
                $orderCounts['dhl'] = $count;
            }
        }

        return response()->json(['count' => $orderCounts], 200);
    }

    /**
     * Display a statistics count.
     *
     * @return json
     */
    public function statistics(Request $request)
    {
        $request->validate([
            'start' => 'required|date_format:Y-m-d H:i:s',
            'end' => 'required|date_format:Y-m-d H:i:s',
        ]);
        $status = [ORDER_STATUS_PENDING, ORDER_STATUS_PROCESSING, ORDER_STATUS_PACKING, ORDER_STATUS_READY_TO_SHIP, ORDER_STATUS_SHIPPING, ORDER_STATUS_DELIVERED, ORDER_STATUS_REJECTED];
return  $logs = OrderLog::where('status', IS_ACTIVE)->whereBetween('created_at', [$request->start, $request->end])
                    ->whereIn('order_status_id', $status)->groupBy('order_status_id')->get([
            'order_status_id',
            DB::raw('count(*) as total')
        ])->pluck('total', 'order_status_id')->all();

        foreach ($logs as $s) {
            if (!isset($orderCounts[$s])) {
                $orderCounts[$s] = 0;
            }
        }

        foreach ($logs as $status => $count) {
            $orderCounts[$status] = $count;
        }
        return response()->json(['count' => $orderCounts], 200);
    }
}
