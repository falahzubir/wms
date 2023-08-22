<?php

namespace App\Http\Controllers;

use App\Models\Bucket;
use App\Models\Company;
use App\Models\Order;
use App\Models\OrderLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BucketController extends Controller
{
    /**
     * List all buckets
     * @return view
     */
    public function index()
    {
        $buckets = Bucket::with(['orders' => function($query){
            $query->where('status', ORDER_STATUS_PROCESSING);
        }])->where('status', IS_ACTIVE)->get();
        return view('buckets.index', [
            'title' => 'List Buckets',
            'buckets' => $buckets,
        ]);
    }

    /**
     * Show bucket detail
     * @param $id
     * @return json
     */
    public function show($id)
    {
        $bucket = Bucket::find($id);
        return response()->json($bucket);

    }

    /**
     * Create new bucket
     * @param Request $request
     */

    public function store(Request $request)
    {
        $bucket = $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $bucket['created_by'] = 1;

        Bucket::create($bucket);

        return redirect()->route('buckets.index')->with('success', 'Bucket created successfully.');
    }

    /**
     * Edit bucket
     * @param Request $request, $id
     * @return view
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        $bucket = Bucket::find($id);
        $bucket->name = $request->name;
        $bucket->description = $request->description;
        $bucket->save();

        return redirect()->route('buckets.index')->with('success', 'Bucket updated successfully.');
    }

    /**
     * Delete bucket
     * @param none
     * @return json
     */
    public function list()
    {
        $buckets = Bucket::where('status', IS_ACTIVE)->get();
        return response()->json($buckets);
    }

    /**
     * Add order to bucket
     * @param Request $request
     * @return json
     */
    public function add_order(Request $request)
    {

        $request->validate([
            'bucket_id' => 'required',
            'order_ids' => 'required',
        ]);


        $upd = Order::whereIn('id', $request->order_ids)->update([
            'bucket_batch_id' => null, // Reset batch id
            'bucket_id' => $request->bucket_id,
            'bucket_added_at' => Carbon::now(),
            'status' => ORDER_STATUS_PROCESSING,
        ]);

        if (!$upd) {
            return response()->json(['message' => 'Failed to add order to bucket.']);
        }

        // get orders
        $orders = Order::with(['company'])->whereIn('id', $request->order_ids)->get();

        //foreach company
        $orders_company = [];
        foreach ($orders as $order) {
            $orders_company[$order->company->id][] = $order;
        }

        //send api to company
        foreach ($orders_company as $orders) {
            $company_url = $orders[0]->company->url;

            if(!$company_url) continue;

            // get sales ids from orders array
            $orders = array_map(function ($order) {
                return $order->sales_id;
            }, $orders);

            $data = [
                'sales_ids' => $orders,
            ];

            Http::post($company_url . '/api/processed_order', $data);

        }

        foreach ($request->order_ids as $order_id) {
            OrderLog::create([
                'order_id' => $order_id,
                'order_status_id' => ORDER_STATUS_PROCESSING,
                'remarks' => 'Order added to bucket',
                'created_by' => 1,
            ]);
        }

        return response()->json(['message' => 'Order added to bucket successfully.']);
    }

    public function download_cn(Request $request)
    {
        $bucket = Bucket::find($request->bucket_id);
        $orders = $bucket->orders;
        $pdf = PDF::loadView('buckets.cn', compact('orders'));
        return $pdf->download('CN.pdf');
    }

    public function check_empty_batch(Request $request)
    {
        $orders = Order::where('bucket_id', $request->bucket_id)->whereNull('bucket_batch_id')->get();
        if (count($orders) == 0) {
            return response()->json(['message' => 'All orders are batched.', 'status' => 'proceed']);
        }
        return response()->json(['message' => 'There are orders not batched.', 'status' => 'stop']);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'bucket_id' => 'required',
        ]);

        $bucket = Bucket::find($request->bucket_id);
        $bucket->status = IS_INACTIVE;
        $bucket->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Bucket deleted successfully.'
        ]);
    }
}
