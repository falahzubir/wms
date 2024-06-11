<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class AttemptOrderList extends Controller
{
    public function index()
    {
        $orders = Order::paginate(10);

        return view('attempt_order_list.index', [
            'title' => 'Attempt Order List',
            'orders' => $orders
        ]);
    }
}
