<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Shipping;
use Illuminate\Http\Request;

class AttemptOrderListController extends Controller
{
    public function index(Request $request)
    {
        // Start with the Shipping model
        $shippings = Shipping::with(['order.customer', 'order.courier', 'events'])
            ->whereHas('order', function ($query) use ($request) {
                // Apply search query if present
                if ($request->filled('search')) {
                    $searchTerm = $request->search;
                    $query->where(function ($subQuery) use ($searchTerm) {
                        $subQuery->where('tracking_number', 'like', '%' . $searchTerm . '%')
                            ->orWhereHas('customer', function ($query) use ($searchTerm) {
                                $query->where('phone', 'like', '%' . $searchTerm . '%');
                            })
                            ->orWhereHas('courier', function ($query) use ($searchTerm) {
                                $query->where('name', 'like', '%' . $searchTerm . '%');
                            });
                    });
                }

                // Apply date filters and status for order logs
                if ($request->filled('date_from') && $request->filled('date_to')) {
                    $query->whereHas('logs', function ($subQuery) use ($request) {
                        $subQuery->whereBetween('created_at', [
                            $request->date_from,
                            $request->date_to
                        ])->where('order_status_id', 5);
                    });
                }
            })
            ->paginate(10);

        return view('attempt_order_list.index', [
            'title' => 'Attempt Order List',
            'shippings' => $shippings
        ]);
    }

    public function filter(Request $request)
    {
        // Redirect to index with query parameters
        return redirect()->route('attempt_order_list', $request->all());
    }
}
