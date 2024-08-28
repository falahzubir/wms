<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\StateGroup;
use App\Models\GroupStateList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use App\Models\WeightCategory;
use App\Models\Courier;
use App\Models\ShippingCost;

class ShippingCostController extends Controller
{
    public function state_group()
    {
        $stateGroups = StateGroup::with('group_state_lists.states');

        $stateGroups = $stateGroups->when(request('search'), function ($query) {
            $query->where('name', 'like', '%' . request('search') . '%');
        });

        $stateGroups = $stateGroups->paginate(10);
        return view('state_group.list', [
            'title' => 'List of State Group',
            'states' => State::all(),
            'stateGroups' => $stateGroups
        ]);
    }

    public function store_state_group(Request $request)
    {
        $stateNames = State::pluck('name', 'id')->toArray();
        $customAttributes = [];
        foreach ($request->input('states') as $key => $stateId) {
            $customAttributes["states.$key"] = $stateNames[$stateId] ?? "State $key";
        }

        $request->validate([
            'name' => [
                'required',
                Rule::unique('state_groups', 'name')->whereNull('deleted_at')
            ],
            'states' => 'required|array|min:1',
            'states.*' => [
                'required',
                Rule::unique('group_state_lists', 'state_id')->whereNull('deleted_at')
            ],
        ]
        , [], $customAttributes);

        $stateGroup = StateGroup::create([
            'name' => $request->name
        ]);

        foreach ($request->states as $state) {
            GroupStateList::create([
                'state_group_id' => $stateGroup->id,
                'state_id' => $state
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'State Group created successfully'
        ]);
    }

    public function update_state_group(Request $request)
    {
        $stateNames = State::pluck('name', 'id')->toArray();
        $customAttributes = [];
        foreach ($request->input('states') as $key => $stateId) {
            $customAttributes["states.$key"] = $stateNames[$stateId] ?? "State $key";
        }

        $request->validate([
            'name' => [
                'required',
                Rule::unique('state_groups', 'name')->whereNull('deleted_at')->ignore($request->id)
            ],
            'states' => 'required|array|min:1',
            'states.*' => [
                'required',
                Rule::unique('group_state_lists', 'state_id')->whereNull('deleted_at')->ignore($request->id, 'state_group_id')
            ],
        ], [], $customAttributes);

        $stateGroup = StateGroup::find($request->id);
        $stateGroup->update([
            'name' => $request->name
        ]);

        $groupStateLists = GroupStateList::where('state_group_id', $request->id);
        $groupStateLists->delete();

        foreach ($request->states as $state) {
            GroupStateList::create([
                'state_group_id' => $stateGroup->id,
                'state_id' => $state
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'State Group updated successfully'
        ]);
    }

    public function delete_state_group($id)
    {
        $stateGroup = StateGroup::find($id);
        $stateGroup->delete();
        //delete group state list
        $groupStateLists = GroupStateList::where('state_group_id', $id);
        $groupStateLists->delete();
        //delete shipping cost
        $shippingCosts = ShippingCost::where('state_group_id', $id);
        $shippingCosts->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'State Group deleted successfully'
        ]);
    }

    public function shipping_cost_list()
    {
        $search = request('search');
        $shippingCosts = ShippingCost::with(['state_groups', 'couriers', 'weight_category']);

        $shippingCosts = $shippingCosts->when(isset($search), function ($query) use ($search) {
            $query->whereHas('weight_category', function (Builder $query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        })->when(isset($search), function ($query) {
            $query->orWhere('price', 'like', '%' . request('search') . '%');
        })->when(request('weight_category'), function ($query) {
            $query->whereIN('weight_category_id', request('weight_category'));
        })->when(request('courier'), function ($query) {
            $query->whereIN('courier_id', request('courier'));
        })->when(request('state_group_id'), function ($query) {
            $query->whereIN('state_group_id', request('state_group_id'));
        });

        $shippingCosts = $shippingCosts->paginate(10);

        return view('shipping_cost.index', [
            'title' => 'List of Shipping Cost',
            'shippingCosts' => $shippingCosts,
            'couriers' => Courier::all(),
            'stateGroups' => StateGroup::all(),
            'weightCategories' => WeightCategory::all(),

        ]);
    }

    public function store_shipping_cost_list(Request $request)
    {
        $request->validate([
            'weight_category_id' => [
                'required',
                Rule::unique('shipping_costs', 'weight_category_id')->where(function ($query) use ($request) {
                    return $query->where('courier_id', $request->courier_id)
                        ->where('state_group_id', $request->state_group_id);
                })
            ],
            'courier_id' => 'required',
            'state_group_id' => 'required',
            'price' => 'required|numeric|gt:0',
        ],
        [
            'weight_category_id.unique' => 'The courier or state group already exists for this Shipping cost'
        ]);
        $shippingCost = ShippingCost::create([
            'weight_category_id' => $request->weight_category_id,
            'courier_id' => $request->courier_id,
            'state_group_id' => $request->state_group_id,
            'price' => $request->price * 100
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Shipping cost created successfully'
        ]);
    }

    public function update_shipping_cost_list(Request $request)
    {
        $request->validate([
            'weight_category_id' => [
                'required',
                Rule::unique('shipping_costs', 'weight_category_id')->where(function ($query) use ($request) {
                    return $query->where('courier_id', $request->courier_id)
                        ->where('state_group_id', $request->state_group_id);
                })->ignore($request->id, 'id')
            ],
            'courier_id' => 'required',
            'state_group_id' => 'required',
            'price' => 'required|numeric|gt:0',
        ],
        [
            'weight_category_id.unique' => 'The courier or state group already exists for this Shipping cost'
        ]);

        $shippingCost = ShippingCost::find($request->id);
        $shippingCost->update([
            'weight_category_id' => $request->weight_category_id,
            'courier_id' => $request->courier_id,
            'state_group_id' => $request->state_group_id,
            'price' => $request->price * 100
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Shipping cost updated successfully'
        ]);
    }

    public function delete_shipping_cost_list($id)
    {
        $shippingCost = ShippingCost::find($id);
        $shippingCost->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Shipping cost deleted successfully'
        ]);
    }

    public function weight_category()
    {
        // Get the search keyword
        $search = request('search');

        // Query WeightCategory model
        $weightCategories = WeightCategory::query();

        // Apply search filter for category name
        $weightCategories = $weightCategories->when(isset($search), function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        });

        // Apply filters for weight_category_id (if applicable)
        $weightCategories = $weightCategories->when(request('weight_category'), function ($query) {
            $query->whereIn('id', request('weight_category'));
        });

        // Paginate the results
        $weightCategories = $weightCategories->paginate(10);

        // Return the view with filtered weight categories
        return view('weight_category.index', [
            'title' => 'List of Weight Category',
            'weightCategories' => $weightCategories,
        ]);
    }

    public function store_weight_category(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'category_name' => 'required',
            'min_weight' => 'required|numeric',
            'max_weight' => 'required|numeric',
        ]);

        // Check if the category name already exists
        if (WeightCategory::where('name', $request->category_name)->exists()) {
            return response()->json([
                'errors' => [
                    'category_name' => ['*The name already exists. Try another.']
                ]
            ], 422);
        }

        // Convert min and max weight to grams
        $minWeightInGrams = $request->min_weight * 1000;
        $maxWeightInGrams = $request->max_weight * 1000;

        // Create a new weight category
        $weightCategory = WeightCategory::create([
            'name' => $request->category_name,
            'min_weight' => $minWeightInGrams,
            'max_weight' => $maxWeightInGrams,
        ]);

        // Return a success response
        return response()->json([
            'status' => 'success',
            'data' => $weightCategory,
        ], 200);
    }

    public function update_weight_category(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'id' => 'required|exists:weight_categories,id',
            'category_name' => 'required',
            'min_weight' => 'required|numeric',
            'max_weight' => 'required|numeric',
        ]);

        // Find the existing weight category by ID
        $weightCategory = WeightCategory::find($request->id);

        // Check if the category name already exists, but exclude the current record's name
        if (WeightCategory::where('name', $request->category_name)->where('id', '!=', $weightCategory->id)->exists()) {
            return response()->json([
                'errors' => [
                    'category_name' => ['*The name already exists. Try another.']
                ]
            ], 422);
        }

        // Convert min and max weight to grams
        $minWeightInGrams = $request->min_weight * 1000;
        $maxWeightInGrams = $request->max_weight * 1000;

        // Update the weight category
        $weightCategory->update([
            'name' => $request->category_name,
            'min_weight' => $minWeightInGrams,
            'max_weight' => $maxWeightInGrams
        ]);

        // Return a success response
        return response()->json([
            'status' => 'success',
            'data' => $weightCategory,
        ], 200);
    }

    public function delete_weight_category($id)
    {
        // Find the existing weight category by ID
        $weightCategory = WeightCategory::find($id);

        if ($weightCategory) {
            // Perform soft delete on shipping costs with the given weight category ID
            ShippingCost::where('weight_category_id', $id)->delete();

            // Perform soft delete on the weight category itself
            $weightCategory->delete();

            // Return a success response
            return response()->json([
                'status' => 'success',
                'data' => $weightCategory,
            ], 200);
        }

        // If the weight category is not found, return a failure response
        return response()->json([
            'status' => 'failure',
            'message' => 'Weight category not found.',
        ], 404);
    }

    public function upload_bulk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bulk_upload_file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'code' => 'NO_FILE_CHOSEN',
                'message' => 'No file chosen! Please choose a file before uploading.'
            ], 422);
        }

        $file = $request->file('bulk_upload_file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));

        // Validate CSV headers
        $headers = array_shift($data);
        $expectedHeaders = ['Weight Category', 'Courier', 'State Group', 'Price(RM)'];
        if ($headers !== $expectedHeaders) {
            return response()->json([
                'status' => 'error',
                'code' => 'INVALID_DATA',
                'message' => 'Failed to upload CSV! Ensure all data matches the provided template.'
            ], 422);
        }

        // Validate all rows before making any changes to the database
        $valid_data = [];
        foreach ($data as $row) {
            $weight_category_name = $row[0];
            $courier_name = $row[1];
            $state_group_name = $row[2];
            $price = floatval($row[3]) * 100;

            $weight_category = WeightCategory::where('name', $weight_category_name)->first();
            $courier = Courier::where('name', $courier_name)->first();
            $state_group = StateGroup::where('name', $state_group_name)->first();

            if (!$weight_category || !$courier || !$state_group) {
                return response()->json([
                    'status' => 'error',
                    'code' => 'INVALID_DATA',
                    'message' => 'Failed to upload CSV! Ensure all data matches the provided template.'
                ], 422);
            }

            $valid_data[] = [
                'weight_category_id' => $weight_category->id,
                'courier_id' => $courier->id,
                'state_group_id' => $state_group->id,
                'price' => $price,
            ];
        }

        foreach ($valid_data as $row) {
            $shippingCost = ShippingCost::where('weight_category_id', $row['weight_category_id'])
                ->where('courier_id', $row['courier_id'])
                ->where('state_group_id', $row['state_group_id'])
                ->first();

            if ($shippingCost) {
                $shippingCost->update([
                    'price' => $row['price'],
                    'updated_at' => now(),
                ]);
            } else {
                ShippingCost::create([
                    'weight_category_id' => $row['weight_category_id'],
                    'courier_id' => $row['courier_id'],
                    'state_group_id' => $row['state_group_id'],
                    'price' => $row['price'],
                ]);
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Shipping Cost created successfully!'
        ]);
    }


    public function download_sample_csv()
    {
        $headers = [
            'Weight Category',
            'Courier',
            'State Group',
            'Price(RM)'
        ];

        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fclose($file);
        };

        $responseHeaders = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="list_of_shipping_cost.csv"',
        ];

        return Response::stream($callback, 200, $responseHeaders);
    }

}
