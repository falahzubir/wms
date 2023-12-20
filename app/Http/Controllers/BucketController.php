<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Bucket;
use App\Models\Company;
use App\Models\OrderLog;
use App\Models\CategoryMain;
use App\Models\CategoryBucket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class BucketController extends Controller
{
    /**
     * List all buckets
     * @return view
     */
    public function index(Request $request)
    {
        $categories = CategoryMain::all();
        $buckets = Bucket::with(['categoryBuckets','categoryBuckets.categoryMain','processingOrders'])
        ->where('status', IS_ACTIVE)
        ->where(function ($q) use ($request) {
            if (!empty($request->search)) {
                $q->where('name', 'like', '%' . $request->search . '%');
            }
            if (!empty($request->category_id)) {
                $q->whereHas('categoryBuckets', function ($q) use ($request) {
                    $q->whereIn('category_id', $request->category_id);
                });
            }
        })
        ->get();

        return view('buckets.index', [
            'title' => 'List Buckets',
            'buckets' => $buckets,
            'categories' => $categories,
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
        $request->validate([
            'name' => [
                'required',
                Rule::unique('buckets')->where(function ($query) {
                    // Fetch the category by name and exclude soft-deleted records
                    $query->where('name', request('name'))->where('status', IS_ACTIVE);
                }),
            ],
            'description' => 'required',
            'category_id' => 'required|array',
        ], [
            'name.required' => 'The Bucket Name field is required.',
            'description.required' => 'The Bucket Description field is required.',
            'category_id.required' => 'The Bucket Category field is required.',
        ]);

        $bucket = Bucket::create([
            'name' => $request->name,
            'description' => $request->description,
            'created_by' => 1,
        ]);

        foreach ($request->category_id as $category_id) {
            $bucket->categoryBuckets()->create([
                'category_id' => $category_id,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Bucket created successfully.'
        ]);
    }

    /**
     * Edit bucket
     * @param Request $request, $id
     * @return view
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required',
                Rule::unique('buckets')->ignore($request->bucket_id)->where(function ($query) {
                    // Fetch the category by name and exclude soft-deleted records
                    $query->where('name', request('name'))->where('status', IS_ACTIVE);
                }),
            ],
            'description' => 'required',
            'category_id' => 'required|array',
        ], [
            'name.required' => 'The Bucket Name field is required.',
            'description.required' => 'The Bucket Description field is required.',
            'category_id.required' => 'The Bucket Category field is required.',
        ]);

        $bucket = Bucket::find($request->bucket_id);
        $bucket->name = $request->name;
        $bucket->description = $request->description;
        $bucket->save();

        $bucket->categoryBuckets()->delete();

        foreach ($request->category_id as $category_id) {
            $bucket->categoryBuckets()->create([
                'category_id' => $category_id,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Bucket edited successfully.'
        ]);
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

        $bucket->categoryBuckets()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Bucket deleted successfully.'
        ]);
    }

    public function bucket_category(Request $request)
    {
        $title = 'List of Bucket Category';
        $statuses = [
            '1' => 'Active',
            '2' => 'Inactive',
        ];
        $buckets = Bucket::where('status', IS_ACTIVE)->get();

        $categories = CategoryMain::with(['categoryBuckets', 'categoryBuckets.bucket'])
            ->where(function ($q) use ($request) {
                if (!empty($request->search)) {
                    $q->where('category_name', 'like', '%' . $request->search . '%');
                }
                if (!empty($request->status)) {
                    $q->where('category_status', $request->status);
                }
            })
            ->paginate(10);

        return view('buckets.category.index', compact('title', 'statuses', 'categories', 'buckets'));
    }

    public function add_category(Request $request)
    {
        $request->validate([
            // 'category_name' => 'required|string|max:255',
            'category_name' => [
                'required',
                Rule::unique('category_mains')->where(function ($query) {
                    // Fetch the category by name and exclude soft-deleted records
                    $query->where('category_name', request('category_name'))->whereNull('deleted_at');
                }),
            ],
            'category_status' => 'required|int',
            'category_bucket' => 'required|array',
        ]);

        $categoryMain = CategoryMain::create([
            'category_name' => $request->category_name,
            'category_status' => $request->category_status,
        ]);

        foreach ($request->category_bucket as $bucket_id) {
            $categoryMain->categoryBuckets()->create([
                'bucket_id' => $bucket_id,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Bucket Category added successfully.'
        ]);
    }

    public function edit_category(Request $request)
    {
        $request->validate([
            'category_id' => 'required|int',
            'category_name' => [
                'required',
                Rule::unique('category_mains')->ignore($request->category_id)->whereNull('deleted_at'),
            ],
            'category_status' => 'required|int',
            'category_bucket' => 'required|array',
        ]);

        $categoryMain = CategoryMain::find($request->category_id);
        $categoryMain->category_name = $request->category_name;
        $categoryMain->category_status = $request->category_status;
        $categoryMain->save();

        $categoryMain->categoryBuckets()->delete();

        foreach ($request->category_bucket as $bucket_id) {
            $categoryMain->categoryBuckets()->create([
                'bucket_id' => $bucket_id,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Bucket Category edited successfully.'
        ]);
    }

    public function delete_category(Request $request)
    {
        $request->validate([
            'category_id' => 'required|int',
        ]);

        $categoryMain = CategoryMain::find($request->category_id);
        $categoryMain->delete();

        $categoryMain->categoryBuckets()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Bucket Category deleted successfully.'
        ]);
    }

    public function get_bucket_by_category(Request $request)
    {
        $totalOrder = 0;
        $categoryBucket = CategoryBucket::with(['bucket'])->where('category_id', $request->category_id)->get();

        $countOrder = !empty($request->order_ids) ? count($request->order_ids) : Order::where('is_active', 1)
        ->whereIn('status', [ORDER_STATUS_PENDING, ORDER_STATUS_PENDING_SHIPMENT])
        ->whereDate('dt_request_shipping', '<=', date('Y-m-d'))
        ->count();

        $totalOrder = $countOrder;

        return response()->json([
            'status' => 'success',
            'categoryBucket' => $categoryBucket,
            'totalOrder' => $totalOrder,
        ]);
    }
}
