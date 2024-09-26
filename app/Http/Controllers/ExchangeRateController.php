<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;

class ExchangeRateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Get the search query from the request
        $search = $request->input('search');

        // If a search term is provided, filter the countries; otherwise, get all countries
        if ($search) {
            $exchangeRate = ExchangeRate::where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('code', 'LIKE', '%' . $search . '%')
                        ->orderBy('start_date', 'desc')
                        ->paginate(10);
        } else {
            $exchangeRate = ExchangeRate::orderBy('start_date', 'desc')->paginate(10);
        }

        $currencies = Currency::all();

        return view('exchange_rate/index', [
            'title' => 'List of Exchange Rate',
            'exchangeRate' => $exchangeRate,
            'currencies' => $currencies,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'add_start_date' => 'required',
                'add_end_date' => 'required',
                'add_currency' => 'required',
                'add_rate' => 'required',
            ]);

            // Check if country id already exists
            $exists = ExchangeRate::where('currency', $request->input('add_currency'))
                ->where(function($query) use ($request) {
                    $query->where(function($q) use ($request) {
                        // Check if the input start date falls within an existing date range
                        $q->where('start_date', '<=', $request->input('add_start_date'))
                        ->where('end_date', '>=', $request->input('add_start_date'));
                    })
                    ->orWhere(function($q) use ($request) {
                        // Check if the input end date falls within an existing date range
                        $q->where('start_date', '<=', $request->input('add_end_date'))
                        ->where('end_date', '>=', $request->input('add_end_date'));
                    })
                    ->orWhere(function($q) use ($request) {
                        // Check if an existing date range is fully inside the input range
                        $q->where('start_date', '>=', $request->input('add_start_date'))
                        ->where('end_date', '<=', $request->input('add_end_date'));
                    });
                })
                ->whereNull('deleted_at') // Ignore soft-deleted entries
                ->first();

            if ($exists) {
                return response()->json(['success' => false, 'message' => 'The exchange rate for this currency already exists within the selected date range.'], 422);
            }

            // Save input into currencies table
            $exchangeRate = new ExchangeRate();
            $exchangeRate->start_date = $request->input('add_start_date');
            $exchangeRate->end_date = $request->input('add_end_date');
            $exchangeRate->currency = $request->input('add_currency');
            $exchangeRate->rate = $request->input('add_rate');

            if ($exchangeRate->save()) {
                return response()->json(['success' => true, 'message' => 'Exchange rate created successfully!']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to add exchange rate']);
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to add exchange rate. Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Find the exchange rate and load the related currency
        $exchangeRate = ExchangeRate::with('currencies')->find($id);

        if (!$exchangeRate) {
            return response([
                'message' => 'Exchange rate not found.'
            ], 404); // Return a 404 if the exchange rate is not found
        }

        return response([
            'data' => $exchangeRate,
            'message' => 'Retrieved successfully'
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
