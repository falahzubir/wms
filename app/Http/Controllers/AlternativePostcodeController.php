<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\State;
use App\Models\AlternativePostcode;
use RealRashid\SweetAlert\Facades\Alert;

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
        try {
            $request->validate([
                'state' => 'required',
                'actual_postcode' => 'required',
                'actual_city' => 'required',
                'alternative_postcode' => 'required',
                'alternative_city' => 'required',
            ]);

            // Check if a record with the same actual_postcode already exists
            $existingRecord = AlternativePostcode::where('actual_postcode', $request->input('actual_postcode'))->where('delete_status', 0)->first();

            if ($existingRecord) {
                // Record already exists, throw a validation exception
                Alert::error('Error', 'The actual postcode already exists in the database.');
        
                // Redirect back
                return redirect()->back();
            }
    
            $model = new AlternativePostcode();
            $model->state = $request->input('state');
            $model->actual_postcode = $request->input('actual_postcode');
            $model->actual_city = $request->input('actual_city');
            $model->alternative_postcode = $request->input('alternative_postcode');
            $model->alternative_city = $request->input('alternative_city');
            $model->save();
    
            // Use the SweetAlert facade to show a success alert
            Alert::success('Success', 'Alternative Postcode Added Successfully!');
    
            // Redirect back
            return redirect()->back();

        } catch (\Exception $e) {

            // Handle the error, and display an error alert
            Alert::error('Error', $e->getMessage());
    
            // Redirect back
            return redirect()->back();
        }
    }

    // Edit Postcode
    public function update(Request $request)
    {
        try {
            $data = $request->validate([
                'state' => 'required',
                'actual_postcode' => 'required',
                'actual_city' => 'required',
                'alternative_postcode' => 'required',
                'alternative_city' => 'required',
            ]);

            // Alternative postcode id
            $id = $request->id;

            // Check if a record with the same actual_postcode already exists
            $existingRecord = AlternativePostcode::where('actual_postcode', $request->input('actual_postcode'))
                ->where('delete_status', 0)
                ->where('id', '!=', $id)
                ->first();

            if ($existingRecord) {
                // Record already exists, throw a validation exception
                Alert::error('Error', 'The actual postcode already exists in the database.');
        
                // Redirect back
                return redirect()->back();
            }
    
            $alternativePostcode = AlternativePostcode::findOrFail($id);
    
            $alternativePostcode->update($data);
    
            // Use the SweetAlert facade to show a success alert
            Alert::success('Success', 'Alternative Postcode Edited Successfully!');
    
            // Redirect back
            return redirect()->back();

        } catch (\Exception $e) {

            // Handle the error, and display an error alert
            Alert::error('Error', $e->getMessage());
    
            // Redirect back
            return redirect()->back();
        }
    }

    // Delete Postcode
    public function destroy($id)
    {
        try {
            $data = [
                'delete_status' => '1'
            ];
            
            $alternativePostcode = AlternativePostcode::findOrFail($id);
            
            $alternativePostcode->delete_status = $data['delete_status'];
            
            $alternativePostcode->save();
    
            // Use the SweetAlert facade to show a success alert
            Alert::success('Success', 'Alternative Postcode Deleted Successfully!');
    
            // Redirect back
            return redirect()->back();

        } catch (\Exception $e) {

            // Handle the error, and display an error alert
            Alert::error('Error', $e->getMessage());
    
            // Redirect back
            return redirect()->back();
        }
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

        $searchTerm = $request->input('search');
        $stateFilter = $request->input('filter_state');

        // Apply filters based on user input
        if ($request->filled('search')) {
            $query->where(function ($query) use ($searchTerm) {
                $query->where('alternative_postcode.actual_postcode', 'like', "%$searchTerm%")
                    ->orWhere('alternative_postcode.alternative_postcode', 'like', "%$searchTerm%")
                    ->orWhere('alternative_postcode.actual_city', 'like', "%$searchTerm%")
                    ->orWhere('alternative_postcode.alternative_city', 'like', "%$searchTerm%")
                    ->orWhere('states.name', 'like', "%$searchTerm%");
            });
        }

        // Filter State
        if ($request->filled('filter_state')) {
            $query->where('states.id', $stateFilter);
        }

        // Paginate the results
        $alternativePostcodes = $query->paginate(10);

        return view('alternative_postcode/index', [
            'title' => 'Alternative Postcode',
            'alternativePostcodes' => $alternativePostcodes,
            'states' => $states,
            'searchTerm' => $searchTerm,
            'stateFilter' => $stateFilter,
        ]);

    }
}
