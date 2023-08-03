<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductCustomer;
use App\Models\ProductDetail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['detail', 'detail.category', 'detail.subcategory'])->where('code', '!=', '');
        if (request()->has('search')) {
            $products = $products->where(function ($query) {
                $query->where('name', 'like', '%' . request()->search . '%')
                    ->orWhere('code', 'like', '%' . request()->search . '%')
                    ->orWhere('description', 'like', '%' . request()->search . '%');
            });
        }
        $products = $products->where('is_active', 1)->orderBy('code')->paginate(10);

        // return $products;
        return view('products.index', [
            'title' => 'List of Products',
            'products' => $products,
            'filter_data' => [],
        ]);
    }

    public function update(Request $request, $product)
    {
        $request->validate(
            [
                'company' => 'required|exists:companies,id',
                'storage_condition' => 'required|in:1,2,3,4',
                'product_category' => 'required|exists:product_categories,id',
                'product_subcategory' => ["nullable",Rule::requiredIf($request->product_category == 2), 'exists:product_categories,id'],
                'expiry' => 'required|in:0,1',
                'shelf_life' => 'required|in:0,1',
                'shelf_life_period' => 'required_if:shelf_life,1|nullable|integer',
                'qaqc' => 'required|in:0,1',
                'name' => 'required|string|max:100',
                'code' => 'required|string|max:10',
                'description' => 'required|string|max:255',
                'product_dimension_length' => 'required|numeric',
                'product_dimension_width' => 'required|numeric',
                'product_dimension_height' => 'required|numeric',
                'product_weight' => 'required|numeric',
                'case_pack_carton' => 'nullable|integer|required_with:case_pack_box,case_pack_unit',
                'case_pack_box' => 'nullable|integer|required_with:case_pack_carton,case_pack_unit',
                'case_pack_unit' => 'nullable|integer|required_with:case_pack_carton,case_pack_box',
                'pallet_tie' => 'required|integer',
                'pallet_high' => 'required|integer',
                'pallet_qty' => 'required|integer',
                'carton_dimension_length' => 'required_with:carton_dimension_width,carton_dimension_height|nullable|numeric',
                'carton_dimension_width' => 'required_with:carton_dimension_length,carton_dimension_height|nullable|numeric',
                'carton_dimension_height' => 'required_with:carton_dimension_length,carton_dimension_width|nullable|numeric',
                'carton_weight' => 'required|numeric',
                'container_load_qty' => 'nullable|integer',
                'customers' => 'required|array',
                'customers.*' => 'required|exists:companies,id',
                'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ],
            [
                'code.unique' => 'Product SKU already exists',
            ]
        );

        $product = Product::find($product);
        $product->name = $request->input('name');
        $product->code = $request->input('code');
        $product->description = $request->input('description');
        $product->save();

        $product_details = ProductDetail::where('product_id', $product->id)->first();
        if($product_details == null) {
            $product_details = new ProductDetail();
            $product_details->product_id = $product->id;
        }
        $product_details->company_id = $request->input('company');
        $product_details->storage_cond = $request->input('storage_condition');
        $product_details->category_id = $request->input('product_category');
        $product_details->sub_category_id = $request->input('product_subcategory');
        $product_details->expiry = $request->input('expiry');
        $product_details->shelf_life = $request->input('shelf_life');
        $product_details->shelf_life_period = $request->input('shelf_life_period');
        $product_details->qa_qc = $request->input('qaqc');
        if ($request->hasFile('image')) {
            $product_details->image_path = $request->image->store('products', 'public');
        }
        $product_details->length = $request->input('product_dimension_length');
        $product_details->width = $request->input('product_dimension_width');
        $product_details->height = $request->input('product_dimension_height');
        $product_details->weight = $request->input('product_weight');
        $product_details->case_pack_carton = $request->input('case_pack_carton');
        $product_details->case_pack_box = $request->input('case_pack_box');
        $product_details->case_pack_unit = $request->input('case_pack_unit');
        $product_details->tie = $request->input('pallet_tie');
        $product_details->high = $request->input('pallet_high');
        $product_details->pallet_qty = $request->input('pallet_qty');
        $product_details->carton_length = $request->input('carton_dimension_length');
        $product_details->carton_width = $request->input('carton_dimension_width');
        $product_details->carton_height = $request->input('carton_dimension_height');
        $product_details->carton_weight = $request->input('carton_weight');
        $product_details->container_load = $request->input('container_load_qty');
        $product_details->save();

        ProductCustomer::where('product_id', $product->id)->delete();
        foreach ($request->customers as $customer) {
            ProductCustomer::create([
                'product_id' => $product->id,
                'company_id' => $customer,
            ]);
        }

        return response([
            'status' => 'success',
            'message' => 'Product updated successfully',
        ]);
    }

    public function maxBoxUpdate(Request $request, Product $product)
    {
        $product->max_box = $request->max_box;
        $product->save();

        return back()->with('success', 'Product updated successfully');
    }

    public function create()
    {
        return view('products.show', [
            'title' => 'Product',
            'companies' => Company::all(),
            'product_categories' => ProductCategory::whereNull('product_category_id')->get(),
            'product_sub_categories' => ProductCategory::whereNotNull('product_category_id')->get(),
        ]);
    }

    public function show($product_id)
    {
        return view('products.show', [
            'title' => 'Product',
            'product' => Product::with('detail')->find($product_id),
            'companies' => Company::all(),
            'product_categories' => ProductCategory::whereNull('product_category_id')->get(),
            'product_sub_categories' => ProductCategory::whereNotNull('product_category_id')->get(),
        ]);
    }

    public function get($product){
        $product = Product::with(['detail', 'customers', 'customers.company', 'detail.owner', 'detail.category', 'detail.subcategory'])->find($product);
        return response([
            'status' => 'success',
            'product' => $product,
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate(
            [
                'company' => 'required|exists:companies,id',
                'storage_condition' => 'required|in:1,2,3,4',
                'product_category' => 'required|exists:product_categories,id',
                'product_subcategory' => ["nullable",Rule::requiredIf($request->product_category == 2), 'exists:product_categories,id'],
                'expiry' => 'required|in:0,1',
                'shelf_life' => 'required|in:0,1',
                'shelf_life_period' => 'required_if:shelf_life,1|nullable|integer',
                'qaqc' => 'required|in:0,1',
                'name' => 'required|string|max:100',
                'code' => 'required|string|max:10|unique:products,code',
                'description' => 'required|string|max:255',
                'product_dimension_length' => 'required|numeric',
                'product_dimension_width' => 'required|numeric',
                'product_dimension_height' => 'required|numeric',
                'product_weight' => 'required|numeric',
                'case_pack_carton' => 'nullable|integer|required_with:case_pack_box,case_pack_unit',
                'case_pack_box' => 'nullable|integer|required_with:case_pack_carton,case_pack_unit',
                'case_pack_unit' => 'nullable|integer|required_with:case_pack_carton,case_pack_box',
                'pallet_tie' => 'required|integer',
                'pallet_high' => 'required|integer',
                'pallet_qty' => 'required|integer',
                'carton_dimension_length' => 'required_with:carton_dimension_width,carton_dimension_height|nullable|numeric',
                'carton_dimension_width' => 'required_with:carton_dimension_length,carton_dimension_height|nullable|numeric',
                'carton_dimension_height' => 'required_with:carton_dimension_length,carton_dimension_width|nullable|numeric',
                'carton_weight' => 'required|numeric',
                'container_load_qty' => 'nullable|integer',
                'customers' => 'required|array',
                'customers.*' => 'required|exists:companies,id',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ],
            [
                'code.unique' => 'Product SKU already exists',
            ]
        );

        $product = Product::create([
            'name' => $request->name,
            'code' => $request->code,
            'description' => $request->description,
            'price' => 0,
        ]);

        $product_details = ProductDetail::create([
            'product_id' => $product->id,
            'company_id' => $request->input('company'),
            'storage_cond' => $request->input('storage_condition'),
            'category_id' => $request->input('product_category'),
            'sub_category_id' => $request->input('product_subcategory'),
            'expiry' => $request->input('expiry'),
            'shelf_life' => $request->input('shelf_life'),
            'shelf_life_period' => $request->input('shelf_life_period'),
            'qa_qc' => $request->input('qaqc'),
            'image_path' => $request->image->store('products', 'public'),
            'length' => $request->input('product_dimension_length'),
            'width' => $request->input('product_dimension_width'),
            'height' => $request->input('product_dimension_height'),
            'weight' => $request->input('product_weight'),
            'case_pack_carton' => $request->input('case_pack_carton'),
            'case_pack_box' => $request->input('case_pack_box'),
            'case_pack_unit' => $request->input('case_pack_unit'),
            'tie' => $request->input('pallet_tie'),
            'high' => $request->input('pallet_high'),
            'pallet_qty' => $request->input('pallet_qty'),
            'carton_length' => $request->input('carton_dimension_length'),
            'carton_width' => $request->input('carton_dimension_width'),
            'carton_height' => $request->input('carton_dimension_height'),
            'carton_weight' => $request->input('carton_weight'),
            'container_load' => $request->input('container_load_qty'),
        ]);

        foreach ($request->customers as $customer) {
            ProductCustomer::create([
                'product_id' => $product->id,
                'company_id' => $customer,
            ]);
        }

        return response([
            'status' => 'success',
            'message' => 'Product created successfully',
        ]);
    }

    public function destroy($product)
    {
        $product = Product::find($product);

        if ($product->detail) {
            $product->detail->delete();
        }

        if ($product->customers) {
            $product->customers()->delete();
        }

        $product->is_active = 0;
        $product->save();

        return response([
            'status' => 'success',
            'message' => 'Product deleted successfully',
        ]);
    }
}
