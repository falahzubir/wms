<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\StateGroup;
use App\Models\GroupStateList;
use Illuminate\Http\Request;
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
            'name' => 'required',
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
            'name' => 'required',
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

        return response()->json([
            'status' => 'success',
            'message' => 'State Group deleted successfully'
        ]);
    }

    public function weight_category()
    {
        $search = request('search');
        $shippingCosts = ShippingCost::with(['state_groups', 'couriers', 'weight_category']);

        $shippingCosts = $shippingCosts->when(isset($search), function ($query) use ($search) {
            $query->whereHas('weight_category', function (Builder $query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            });
        })->when(request('courier'), function ($query) {
            $query->whereIN('courier_id', request('courier'));
        })->when(request('state_group_id'), function ($query) {
            $query->whereIN('state_group_id', request('state_group_id'));
        });

        $shippingCosts = $shippingCosts->paginate(10);

        return view('weight_category.index', [
            'title' => 'List of Weight Category',
            'shippingCosts' => $shippingCosts,
            'couriers' => Courier::all(),
            'stateGroups' => StateGroup::all(),
            'weightCategories' => WeightCategory::all(),

        ]);
    }

    public function store_weight_category(Request $request)
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
            'weight_category_id.unique' => 'The courier or state group already exists for this weight category'
        ]);
        $shippingCost = ShippingCost::create([
            'weight_category_id' => $request->weight_category_id,
            'courier_id' => $request->courier_id,
            'state_group_id' => $request->state_group_id,
            'price' => $request->price * 1000
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Weight Category created successfully'
        ]);
    }

    public function update_weight_category(Request $request)
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
            'weight_category_id.unique' => 'The courier or state group already exists for this weight category'
        ]);

        $shippingCost = ShippingCost::find($request->id);
        $shippingCost->update([
            'weight_category_id' => $request->weight_category_id,
            'courier_id' => $request->courier_id,
            'state_group_id' => $request->state_group_id,
            'price' => $request->price * 1000
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Weight Category updated successfully'
        ]);
    }

    public function delete_weight_category($id)
    {
        $shippingCost = ShippingCost::find($id);
        $shippingCost->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Weight Category deleted successfully'
        ]);
    }
}
