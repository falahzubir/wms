<?php

namespace App\Http\Controllers;

use App\Models\OrderLog;
use Illuminate\Http\Request;

class OrderLogController extends Controller
{
    /**
     * Add new order log
     *
     * @param Request $request
     */
    public function store(Request $request)
    {
        $orderLog = $request->validate([
            'order_id' => 'required',
            'order_status_id' => 'required',
            'description' => 'required',
        ]);

        $orderLog['created_by'] = auth()->user()->id;

        OrderLog::create($orderLog);

        return redirect()->route('orders.index')->with('success', 'Order log created successfully.');
    }
}
