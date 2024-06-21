<?php

namespace App\Http\Controllers;

use App\Models\State;
use App\Models\StateGroup;
use App\Models\GroupStateList;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $request->validate([
            'name' => 'required',
            'states' => [
                'required',
                Rule::unique('group_state_lists', 'state_id')->whereNull('deleted_at')
            ],
        ]);

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
        $request->validate([
            'name' => 'required',
            'states' => [
                'required',
                Rule::unique('group_state_lists', 'state_id')->whereNull('deleted_at')->ignore($request->id)
            ],
        ]);

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
        // dd($id);
        //delete state group
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
}
