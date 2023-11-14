<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;
use App\Models\AlternativePostcode;

class AlternativePostcodeController extends Controller
{
    public function index() 
    {
        // Get list of state
        $states = State::all();

        // Join and get data from alternative_postcode and state table
        $alternativePostcodes = AlternativePostcode::join('states', 'alternative_postcode.state', '=', 'states.id')
            ->select('alternative_postcode.*', 'states.name as state_name')
            ->where('alternative_postcode.delete_status', '!=', 1)
            ->paginate(10);

        return view('alternative_postcode/index', [
            'title' => 'Alternative Postcode',
            'alternativePostcodes' => $alternativePostcodes,
            'states' => $states,
        ]);
    }

    // Add New Postcode
    public function store(Request $request)
    {
        $request->validate([
            'state' => 'required',
            'actual_postcode' => 'required',
            'actual_city' => 'required',
            'alternative_postcode' => 'required',
            'alternative_city' => 'required',
        ]);

        $model = new AlternativePostcode();
        $model->state = $request->input('state');
        $model->actual_postcode = $request->input('actual_postcode');
        $model->actual_city = $request->input('actual_city');
        $model->alternative_postcode = $request->input('alternative_postcode');
        $model->alternative_city = $request->input('alternative_city');
        $model->save();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Alternative Postcode Added Successfully!');
    }

    // Edit Postcode
    public function update(Request $request)
    {
        $data = $request->validate([
            'state' => 'required',
            'actual_postcode' => 'required',
            'actual_city' => 'required',
            'alternative_postcode' => 'required',
            'alternative_city' => 'required',
        ]);

        $id = $request->id;

        $alternativePostcode = AlternativePostcode::findOrFail($id);

        $alternativePostcode->update($data);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Alternative Postcode Edited Successfully!');
    }

    // Delete Postcode
    public function destroy($id)
    {
        $data = [
            'delete_status' => '1'
        ];
        
        $alternativePostcode = AlternativePostcode::findOrFail($id);
        
        $alternativePostcode->delete_status = $data['delete_status'];
        
        $alternativePostcode->save();

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Alternative Postcode Deleted Successfully!');
    }

    // For Search & Filters
    public function handleSearch(Request $request)
    {
        // Get list of states
        $states = State::all();

        // Define the initial query
        $query = AlternativePostcode::join('states', 'alternative_postcode.state', '=', 'states.id')
            ->select('alternative_postcode.*', 'states.name as state_name')
            ->where('alternative_postcode.delete_status', '!=', 1);

        // Apply filters based on user input
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($query) use ($searchTerm) {
                $query->where('alternative_postcode.actual_postcode', 'like', "%$searchTerm%")
                    ->orWhere('alternative_postcode.alternative_postcode', 'like', "%$searchTerm%")
                    ->orWhere('alternative_postcode.actual_city', 'like', "%$searchTerm%")
                    ->orWhere('alternative_postcode.alternative_city', 'like', "%$searchTerm%")
                    ->orWhere('states.name', 'like', "%$searchTerm%");
            });

            // Apply this when user select filter by
            if ($request->filled('filter_by')) {
                $filterBy = $request->input('filter_by');
                $query->where("alternative_postcode.$filterBy", $searchTerm);
            }
        }

        // Filter State
        if ($request->filled('filter_state')) {
            $stateFilter = $request->input('filter_state');
            $query->where('states.id', $stateFilter);
        }

        // Filter City
        if ($request->filled('filter_city')) {
            $cityFilter = $request->input('filter_city');
            $query->where('alternative_postcode.actual_city', $cityFilter);
        }

        // Paginate the results
        $alternativePostcodes = $query->paginate(10);

        return view('alternative_postcode/index', [
            'title' => 'Alternative Postcode',
            'alternativePostcodes' => $alternativePostcodes,
            'states' => $states,
        ]);

    }
}
