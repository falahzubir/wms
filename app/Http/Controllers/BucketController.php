<?php

namespace App\Http\Controllers;

use App\Models\Bucket;
use App\Models\Order;
use App\Models\OrderLog;
use Illuminate\Http\Request;

class BucketController extends Controller
{
    /**
     * List all buckets
     * @return view
     */
    public function index()
    {
        $buckets = Bucket::where('status', IS_ACTIVE)->get();
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

        Order::whereIn('id', $request->order_ids)->update([
            'bucket_id' => $request->bucket_id
        ]);


        foreach ($request->order_ids as $order_id) {
            OrderLog::create([
                'order_id' => $order_id,
                'order_status_id' => ORDER_STATUS_PENDING_ON_BUCKET,
                'remarks' => 'Order added to bucket',
                'created_by' => 1,
            ]);
        }

        return response()->json(['message' => 'Order added to bucket successfully.']);
    }
}
