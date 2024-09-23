<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Currency;
use Illuminate\Http\Request;

class CurrencyListController extends Controller
{
    public function index(Request $request)
    {
        // Get the search query from the request
        $search = $request->input('search');

        // Initialize the query for Currency model
        $query = Currency::query();

        // If user search something
        if ($search) {
            $query->where('currency', 'LIKE', '%' . $search . '%')
                ->orWhereHas('country', function($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('code', 'LIKE', '%' . $search . '%');
                });
        }

        // Paginate the results
        $currencies = $query->paginate(10);

        $countries = Country::all();

        // Return the view with the currency data
        return view('currency_list/index', [
            'title' => 'List of Currency',
            'currencies' => $currencies,
            'countries' => $countries,

        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'add_country_name' => 'required',
                'add_currency' => 'required',
            ]);

            // Check if country name already exists
            $existName = Currency::where('country_id', $request->input('add_country_name'))
                ->whereNull('deleted_at')
                ->first();

            if ($existName) {
                return response()->json(['success' => false, 'message' => '*The country name already exists. Try another.', 'field' => 'add_country_name'], 422);
            }

            // Check if country code already exists
            $existCode = Currency::where('currency', $request->input('add_currency'))
                ->whereNull('deleted_at')
                ->first();

            if ($existCode) {
                return response()->json(['success' => false, 'message' => '*The currency already exists. Try another.', 'field' => 'add_currency'], 422);
            }

            // Save input into countries table
            $currency = new Currency();
            $currency->country_id = $request->input('add_country_name');
            $currency->currency = strtoupper($request->input('add_currency'));

            if ($currency->save()) {
                return response()->json(['success' => true, 'message' => 'Currency created successfully!']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to add currency']);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add currency. Error: ' . $e->getMessage()], 500);
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
                ->exists();

            if ($countryNameExists) {
                return response()->json(['success' => false, 'message' => '*The country name already exists. Try another.', 'field' => 'edit_country_name'], 422);
            }

            // Check if country code exists (excluding the current country)
            $countryCodeExists = Country::where('code', $request->input('edit_country_code'))
                ->where('id', '!=', $id) // Exclude the current country
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
            $currency = Currency::findOrFail($id);

            // Soft delete the country
            $currency->delete();

            return response()->json(['success' => true, 'message' => 'Currency deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete country. Error: ' . $e->getMessage()]);
        }
    }
}
