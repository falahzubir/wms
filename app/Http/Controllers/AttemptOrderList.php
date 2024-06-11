<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AttemptOrderList extends Controller
{
    public function index()
    {
        return view('attempt_order_list.index');
    }
}
