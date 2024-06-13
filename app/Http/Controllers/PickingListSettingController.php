<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class PickingListSettingController extends Controller
{
    public function index() {

        $products = Product::all();

        return view('picking_list_setting/index',['title' => 'Picking List Product Sequence','products' => $products]);
    }

}
