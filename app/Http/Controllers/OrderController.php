<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return view('orders.overall', ['title' => 'List Orders']);
    }
    public function pending(Request $request)
    {
        return view('orders.pending', ['title' => 'Pending Orders']);
    }
}
