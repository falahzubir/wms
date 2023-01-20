<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    /**
     * List of Couriers.
     *
     * @return json
     */
    public function list()
    {
        $couriers = Courier::select('code','name')->where('is_active', IS_ACTIVE)->get();

        return response()->json($couriers);
    }
}
