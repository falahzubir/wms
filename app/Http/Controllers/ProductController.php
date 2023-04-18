<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index', [
            'title' => 'Products',
            'products' => Product::where('code', '!=', '')->orderBy('code')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $product->max_box = $request->max_box;
        $product->save();

        return back()->with('success', 'Product updated successfully');
    }
}
