<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AttemptOrderListController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['shippings.events']);

        // Apply search query if present
        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $orders->where(function ($query) use ($searchTerm) {
                $query->where('sales_id', 'like', '%' . $searchTerm . '%')
                      ->orWhereHas('customer', function ($query) use ($searchTerm) {
                          $query->where('phone', 'like', '%' . $searchTerm . '%');
                      })
                      ->orWhereHas('courier', function ($query) use ($searchTerm) {
                          $query->where('name', 'like', '%' . $searchTerm . '%');
                      })
                      ->orWhereHas('shippings', function ($query) use ($searchTerm) {
                          $query->where('tracking_number', 'like', '%' . $searchTerm . '%');
                      });
            });
        }

        // Apply date filters and status for order logs
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $orders->whereHas('logs', function ($query) use ($request) {
                $query->whereBetween('created_at', [
                    $request->date_from,
                    $request->date_to
                ])->where('order_status_id', 5);
            });
        }

        // Execute the query
        $orders = $orders->paginate(10);

        return view('attempt_order_list.index', [
            'title' => 'Attempt Order List',
            'orders' => $orders
        ]);
    }

    public function filter(Request $request)
    {
        // Redirect to index with query parameters
        return redirect()->route('attempt_order_list', $request->all());
    }
}
