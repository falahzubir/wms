<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;

class CurrencyListController extends Controller
{
    public function index(Request $request)
    {
        // Get the search query from the request
        $search = $request->input('search');

        // Initialize the query for Currency model
        $query = Currency::query();

        // If user searches something
        if ($search) {
            $query->where('currency', 'LIKE', '%' . $search . '%')
                ->orWhereHas('country', function($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%')
                    ->orWhere('code', 'LIKE', '%' . $search . '%');
                });
        }

        // Order by the related country name
        $query->join('countries', 'currencies.country_id', '=', 'countries.id')
            ->orderBy('countries.name', 'asc');

        // Paginate the results
        $currencies = $query->select('currencies.*')->paginate(10);

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

            // Check if country id already exists
            $existName = Currency::where('country_id', $request->input('add_country_name'))
                ->whereNull('deleted_at')
                ->first();

            if ($existName) {
                return response()->json(['success' => false, 'message' => '*The country name already exists. Try another.', 'field' => 'add_country_name'], 422);
            }

            // Check if currency code already exists
            $existCode = Currency::where('currency', $request->input('add_currency'))
                ->whereNull('deleted_at')
                ->first();

            if ($existCode) {
                return response()->json(['success' => false, 'message' => '*The currency already exists. Try another.', 'field' => 'add_currency'], 422);
            }

            // Save input into currencies table
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
        $currency = Currency::with('country')->find($id);

        // Check if the currency exists
        if (!$currency) {
            return response()->json([
                'message' => 'Currency not found'
            ], 404);
        }

        return response()->json([
            'data' => $currency,
            'message' => 'Retrieved successfully'
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'edit_country_name' => 'required',
            'edit_currency' => 'required|string|max:10',
        ]);

        try {
            // Find the currency by id
            $currency = Currency::find($id);

            // Check if country id exists (excluding the current country id)
            $countryNameExists = Currency::where('country_id', $request->input('edit_country_name'))
                ->where('id', '!=', $id)
                ->whereNull('deleted_at')
                ->exists();

            if ($countryNameExists) {
                return response()->json(['success' => false, 'message' => '*The country name already exists. Try another.', 'field' => 'edit_country_name'], 422);
            }

            // Check if currency exists (excluding the current currency)
            $countryCodeExists = Currency::where('currency', $request->input('edit_currency'))
                ->where('id', '!=', $id)
                ->whereNull('deleted_at')
                ->exists();

            if ($countryCodeExists) {
                return response()->json(['success' => false, 'message' => '*The currency already exists. Try another.', 'field' => 'edit_currency'], 422);
            }

            // Update the currency details
            $currency->country_id = $request->input('edit_country_name');
            $currency->currency = strtoupper($request->input('edit_currency'));

            if ($currency->save()) {
                return response()->json(['success' => true, 'message' => 'Currency updated successfully!']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to update currency']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update currency. Error: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            // Soft delete the currencies
            $currency = Currency::findOrFail($id);
            $currency->delete();

            // Soft delete the exchange_rates
            $exchangeRates = ExchangeRate::where('country_id', $currency->country_id);
            $exchangeRates->delete();

            return response()->json(['success' => true, 'message' => 'Currency deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete currency. Please try again. Error: ' . $e->getMessage()]);
        }
    }
}
