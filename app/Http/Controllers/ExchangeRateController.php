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
        // Fetch filter inputs from the request
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $currency = $request->input('currency');

        // Initialize query builder
        $query = ExchangeRate::with(['currencies.country']);

        // Search by country name (relationship with Country model)
        if ($search) {
            $query->whereHas('currencies.country', function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%');
            });
        }

        // Filter by multiple currencies if provided
        if ($currency && is_array($currency)) {
            $query->whereIn('currency', $currency);
        }

        // Filter by date range if dateFrom or dateTo is provided
        if ($dateFrom || $dateTo) {
            $query->where(function($q) use ($dateFrom, $dateTo) {
                // If only dateFrom is provided
                if ($dateFrom && !$dateTo) {
                    $q->where(function($subQuery) use ($dateFrom) {
                        $subQuery->where('start_date', '>=', $dateFrom)
                                ->orWhere('end_date', '>=', $dateFrom);
                    });
                }

                // If only dateTo is provided
                if (!$dateFrom && $dateTo) {
                    $q->where(function($subQuery) use ($dateTo) {
                        $subQuery->where('start_date', '<=', $dateTo)
                                ->orWhere('end_date', '<=', $dateTo);
                    });
                }

                // If both dateFrom and dateTo are provided
                if ($dateFrom && $dateTo) {
                    $q->whereBetween('start_date', [$dateFrom, $dateTo])
                    ->orWhereBetween('end_date', [$dateFrom, $dateTo])
                    ->orWhere(function($q) use ($dateFrom, $dateTo) {
                        $q->where('start_date', '<=', $dateFrom)
                            ->where('end_date', '>=', $dateTo);
                    });
                }
            });
        }

        // Order by start_date and paginate results
        $exchangeRate = $query->orderBy('start_date', 'desc')->paginate(10);

        // Get currencies for the dropdown
        $currencies = Currency::all();

        return view('exchange_rate/index', [
            'title' => 'List of Exchange Rate',
            'exchangeRate' => $exchangeRate,
            'currencies' => $currencies,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validation rules
            $request->validate([
                'add_currency' => 'required|integer',
                'add_start_date' => 'required|date',
                'add_end_date' => 'required|date|after_or_equal:add_start_date',
                'add_rate' => [
                    'required',
                    'numeric',  // Ensures the value is numeric
                    'regex:/^\d{1,9}(\.\d{1,11})?$/'  // Allows up to 9 digits before and 11 after the decimal point
                ],
            ], [
                // Custom error message for the regex validation
                'add_rate.regex' => 'The exchange rate must be a valid decimal number.',
            ]);

            // Check if exchange rate id already exists
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
                ->whereNull('deleted_at')
                ->first();

            if ($exists) {
                return response()->json(['success' => false, 'message' => '*The dates and currency already exist. Try another'], 422);
            }

            // Save input into exchange_rates table
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validation rules
        $request->validate([
            'edit_currency' => 'required|integer',
            'edit_start_date' => 'required|date',
            'edit_end_date' => 'required|date|after_or_equal:edit_start_date',
            'edit_rate' => [
                'required',
                'numeric',
                'regex:/^\d{1,9}(\.\d{1,11})?$/',  // Allows up to 9 digits before and 11 after the decimal point
            ],
        ], [
            'edit_rate.regex' => 'The exchange rate must be a valid decimal number.',
        ]);

        try {

            // Check if exchange rate id already exists
            $exists = ExchangeRate::where('currency', $request->input('edit_currency'))
                ->where(function($query) use ($request) {
                    $query->where(function($q) use ($request) {
                        // Check if the input start date falls within an existing date range
                        $q->where('start_date', '<=', $request->input('edit_start_date'))
                        ->where('end_date', '>=', $request->input('edit_start_date'));
                    })
                    ->orWhere(function($q) use ($request) {
                        // Check if the input end date falls within an existing date range
                        $q->where('start_date', '<=', $request->input('edit_end_date'))
                        ->where('end_date', '>=', $request->input('edit_end_date'));
                    })
                    ->orWhere(function($q) use ($request) {
                        // Check if an existing date range is fully inside the input range
                        $q->where('start_date', '>=', $request->input('edit_start_date'))
                        ->where('end_date', '<=', $request->input('edit_end_date'));
                    });
                })
                ->whereNull('deleted_at')
                ->where('id', '!=', $id)
                ->exists();

            if ($exists) {
                return response()->json(['success' => false, 'message' => '*The dates and currency already exist. Try another'], 422);
            }

            // Update data into exchange_rates table
            $exchangeRate = ExchangeRate::find($id);
            $exchangeRate->start_date = $request->input('edit_start_date');
            $exchangeRate->end_date = $request->input('edit_end_date');
            $exchangeRate->currency = $request->input('edit_currency');
            $exchangeRate->rate = $request->input('edit_rate');
            
            if ($exchangeRate->save()) {
                return response()->json(['success' => true, 'message' => 'Exchange rate updated successfully!']);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to update exchange rate']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update exchange rate. Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Soft delete the currencies
            $currency = ExchangeRate::findOrFail($id);
            $currency->delete();

            return response()->json(['success' => true, 'message' => 'Exchange rate deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete exchange rate. Please try again. Error: ' . $e->getMessage()]);
        }
    }
}
