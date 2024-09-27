<?php

namespace App\Http\Controllers;

use App\Models\Country;
use Illuminate\Http\Request;

class CountryListController extends Controller
{
    public function index(Request $request)
    {
        // Get the search query from the request
        $search = $request->input('search');

        // If a search term is provided, filter the countries; otherwise, get all countries
        if ($search) {
            $countries = Country::where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('code', 'LIKE', '%' . $search . '%')
                        ->orderBy('name', 'asc')
                        ->paginate(10);
        } else {
            $countries = Country::orderBy('name', 'asc')->paginate(10);
        }

        return view('country_list/index', [
            'title' => 'List of Country',
            'countries' => $countries,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'add_country_name' => 'required',
                'add_country_code' => 'required',
            ]);

            // Check if country name already exists
            $existName = Country::where('name', $request->input('add_country_name'))
                ->whereNull('deleted_at')
                ->first();

            if ($existName) {
                return response()->json(['success' => false, 'message' => '*The country name already exists. Try another.', 'field' => 'add_country_name'], 422);
            }

            // Check if country code already exists
            $existCode = Country::where('code', $request->input('add_country_code'))
                ->whereNull('deleted_at')
                ->first();

            if ($existCode) {
                return response()->json(['success' => false, 'message' => '*The country code already exists. Try another.', 'field' => 'add_country_code'], 422);
            }

            // Save input into countries table
            $country = new Country();
            $country->name = ucwords($request->input('add_country_name'));
            $country->code = strtoupper($request->input('add_country_code'));

            if ($country->save()) {
                return response()->json(['success' => true, 'message' => 'Country created successfully!']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to add country']);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add country. Error: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $country = Country::find($id);
        return response([
            'country' => $country,
            'message' => 'Retrieved successfully'
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'edit_country_name' => 'required|string|max:255',
            'edit_country_code' => 'required|string|max:10',
        ]);

        try {
            // Find the country by id
            $country = Country::find($id);

            // Check if country name exists (excluding the current country)
            $countryNameExists = Country::where('name', $request->input('edit_country_name'))
                ->where('id', '!=', $id) // Exclude the current country
                ->whereNull('deleted_at')
                ->exists();

            if ($countryNameExists) {
                return response()->json(['success' => false, 'message' => '*The country name already exists. Try another.', 'field' => 'edit_country_name'], 422);
            }

            // Check if country code exists (excluding the current country)
            $countryCodeExists = Country::where('code', $request->input('edit_country_code'))
                ->where('id', '!=', $id) // Exclude the current country
                ->whereNull('deleted_at')
                ->exists();

            if ($countryCodeExists) {
                return response()->json(['success' => false, 'message' => '*The country code already exists. Try another.', 'field' => 'edit_country_code'], 422);
            }

            // Update the country's details
            $country->name = ucwords($request->input('edit_country_name'));
            $country->code = strtoupper($request->input('edit_country_code'));

            if ($country->save()) {
                return response()->json(['success' => true, 'message' => 'Country updated successfully!']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to update country']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update country. Error: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            // Find the country by id
            $country = Country::findOrFail($id);

            // Soft delete the country
            $country->delete();

            return response()->json(['success' => true, 'message' => 'Country deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete country. Error: ' . $e->getMessage()]);
        }
    }
}
