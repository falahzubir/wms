<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class OrderApiController extends Controller
{
    /*
    * Reject Order
    * @param Request $request
    * @return json
    */
    public function reject(Request $request)
    {
        if (isset($request->sales_id)) { //from BOS
            $request->validate([
                'sales_id' => 'required|exists:orders,sales_id',
                'reason' => 'required'
            ]);
            $order = Order::with('company')->where('sales_id', $request->sales_id)
                ->whereHas('company', function ($query) use ($request) {
                    $query->where('code', $request->company);
                })
                ->first();
        } else {
            $request->validate([
                'order_id' => 'required|exists:orders,id',
                'reason' => 'required',
                'reject_reason' => 'required|in:1,2,3,4,5',
            ]);
            $order = Order::find($request->order_id);

            if (!empty($order) && empty($request->from)) {

                if (config('app.env') == 'production') {
                    $url = $order->company->url . "/api/reject_order";
                }
                else{
                    $url = "https://qastg.groobok.com/api/reject_order";
                }

                $json['from'] = "wms";
                $json['sales_id'] = $order->sales_id;
                $json['reason_reject'] = $request->input("reject_reason"); // 1-Phone, 2-Address, 3-Product(Qty), 4-Product(Other), 5-Change Purchase Type
                $json['approval_remark_textarea'] = $request->input("reason") . " - " . config("app.name");
                $json['data_to_be_send_to_websocket'] = $request->data_to_be_send_to_websocket;
                $response = Http::withHeaders([
                    "Signature" => hash_hmac('sha256', json_encode($json), env('WEBHOOK_CLIENT_SECRET')),
                    'Content-Type' => 'application/json'
                ])->post($url, $json);
            }
        }
        $order->status = ORDER_STATUS_REJECTED;
        $order->save();

        set_order_status($order, ORDER_STATUS_REJECTED, $request->input("reason"));

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

        $order = Order::with(['shippings', 'items', 'items.product'])->where('is_active', IS_ACTIVE)
            ->whereHas('shippings', function ($query) use ($request) {
                $query->where('tracking_number', $request->barcode);
            })->first();

        if ($order->count() == 0) {
            return response()->json(['error' => 'Parcel not found']);
        }

        if ($order->status == ORDER_STATUS_REJECTED) {
            return response()->json(['error' => 'Order rejected']);
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
            set_order_status($order, ORDER_STATUS_READY_TO_SHIP);
            return response()->json([
                'success' => 'ok',
                'message' => 'Parcel already scanned by ' . $order->shipping->scanned_by,
            ], 200);
        }
    }

    /*
    * Get Order for split parcels
    * @param Request $request
    * @return json
    */
    public function get_order_split_parcels(Request $request)
    {
        $request->validate([
            'order_id' => 'required'
        ]);

        $order = Order::with(['items', 'items.product'])->where('is_active', IS_ACTIVE)
            ->where('id', $request->order_id)->first();

        if ($order->count() == 0) {
            return response()->json(['error' => 'Order not found']);
        }

        return response()->json([
            'success' => 'ok',
            'count' => ceil($order->items->sum('quantity') / MAXIMUM_QUANTITY_PER_BOX),
            'weight' => get_order_weight($order),
            'order' => $order,
        ], 200);
    }

    /*
    * Approve Order for Shipping
    * @param Request $request
    * @return json
    */
    public function approve_for_shipping(Request $request)
    {
        $request->validate([
            'order_ids' => 'required',
            'user_id' => 'required',
        ]);

        $orders = Order::whereIn('id', $request->order_ids)->get();

        if (set_order_status_bulk($orders, ORDER_STATUS_DELIVERED, "Approved manually by {$request->user_id}")) {
            return response()->json(['success' => 'ok']);
        } else {
            return response()->json(['error' => 'error']);
        }
    }

    /*
    * Set order as completed
    * @param Request $request
    * @return json
    */
    public function set_order_completed(Request $request)
    {
        $request->validate([
            'tracking_numbers' => 'required', //sales id from BOS
        ]);

        $orders = Order::with('shippings')
            ->whereHas('shippings', function ($query) use ($request) {
                $query->whereIn('tracking_number', $request->tracking_numbers);
            })
            ->get();

        if (set_order_status_bulk($orders, ORDER_STATUS_DELIVERED, "Completed manually through API")) {
            return response()->json(['success' => 'ok']);
        } else {
            return response()->json(['error' => 'error']);
        }
    }

    public function getStatusWMS(Request $request)
    {
        $sales_id = $request->input("sales_id");

        $order = DB::table('orders')
        ->select('orders.sales_id','orders.status','shippings.tracking_number')
        ->leftJoin('shippings', function($join) {
            $join->on('orders.id', '=', 'shippings.order_id');
          })
        ->where("orders.sales_id", $sales_id)
        ->where("orders.is_active", IS_ACTIVE)
        ->where("orders.company_id",1)
        ->first();

        if($order){
            return response()->json([
            'success' => true,
            'message' => 'Order found',
            'data' => $order
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 200);
        }
    }

    public function getStatusWMSFilter(Request $request)
    {
        $sales_id = $request->input("sales_id");
        $status = $request->input("status");

        $order = Order::select('sales_id','status')
        ->whereIN("sales_id", $sales_id)
        ->where("is_active", IS_ACTIVE)
        ->where("company_id",3)
        ->where("status", $status)
        ->get();

        if($order){
            return response()->json([
            'success' => true,
            'message' => 'Order found',
            'data' => $order
            ], 200);
        }else{
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 200);
        }
    }

    public function scanParcelRanking(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
        ]);

        $today = $request->filled('date') ? Carbon::parse($request->date)->toDateString() :  Carbon::today()->toDateString();
        $startMonth = $request->filled('date') ? Carbon::parse($request->date)->startOfMonth()->toDateString() : Carbon::now()->startOfMonth()->toDateString();
        $endMonth = $request->filled('date') ? Carbon::parse($request->date)->endOfMonth()->toDateString() : Carbon::now()->endOfMonth()->toDateString();

        if($request->type == 'individual')
        {
            $parcels = Shipping::select(
                DB::raw('IFNULL(count(DISTINCT(order_id)),0) as total'),
                DB::raw('IFNULL(SUM(CASE WHEN DATE(scanned_at) = CURDATE() THEN 1 ELSE 0 END),0) AS daily')
            )
            // ->where('scanned_by', 6) // Assuming '6' is the scanned_by value
            ->where('scanned_by', $request->user_id)
            ->where('status',IS_ACTIVE)
            ->whereDate('scanned_at', '>=', $startMonth)
            ->whereDate('scanned_at', '<=', $endMonth);
        }
        else
        {
            $parcels = Shipping::select('scanned_by', DB::raw('count(DISTINCT(order_id)) as total'))
            ->with(['scannedBy'])
            ->where('status',IS_ACTIVE);
        }

        if($request->type == 'daily')
        {
            $parcels = $parcels->whereDate('scanned_at', $today);
        }

        //monthly
        if($request->type == 'monthly')
        {
            $parcels = $parcels->whereDate('scanned_at', '>=', $startMonth)
            ->whereDate('scanned_at', '<=', $endMonth);
        }

        //get
        if($request->type == 'individual'){

            $parcels = $parcels->first();
        }else{
            $parcels = $parcels->groupBy('scanned_by')
            ->orderBy('total', 'DESC')
            ->get();
        }
        $month_name = Carbon::parse($today)->format('F Y');

        return response()->json([
            'success' => true,
            'message' => 'Parcel found',
            'data' => $parcels,
            'month_name'=> $month_name
        ], 200);
    }

    /*
    * API to delete order
    * @param Request $request
    * @return json
    */
    public function delete(Request $request)
    {
        $request->validate([
            'sales_id' => 'required|exists:orders,sales_id',
            'company_code' => 'required|exists:companies,code',
        ]);

        $order = Order::where('sales_id', $request->sales_id)
            ->whereHas('company', function ($query) use ($request) {
                $query->where('code', $request->company_code);
            })->first();

        if (!$order) {
            return response()->json([
                'error' => 'Order not found'
            ], 404);
        }

        set_order_status($order, ORDER_STATUS_REJECTED, "Order deleted by API");

        $order->is_active = IS_INACTIVE;
        $order->save();

        return response()->json([
            'success' => 'ok',
            'message' => 'Order deleted successfully',
            'data' => $order
        ], 200);
    }
}
