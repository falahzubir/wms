<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Order;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    public function index_product()
    {
        $claims = Claim::with(['order', 'order.customer', 'items', 'items.order_item', 'items.order_item.product'])
            ->where('type', CLAIM_TYPE_PRODUCT)->paginate(10);

        return view('claims.index',[
            'title' => 'Claim List by Product',
            'filter_data' => [],
            'actions' => [],
            'claims' => $claims
        ]);
    }

    public function index_courier()
    {
        $claims = Claim::with(['order', 'items'])
            ->where('type', CLAIM_TYPE_COURIER)->paginate(10);

        return view('claims.index',[
            'title' => 'Claim List by Courier',
            'filter_data' => [],
            'actions' => [],
            'claims' => $claims
        ]);
    }


    public function create(Request $request){
        $request->validate([
            'parcel_condition' => 'required|boolean'
        ]);

        if($request->input("parcel_condition") == 0){
            $request->validate([
                'claim_type' => 'required|in:1,2', //1-Product, 2-Courier Cost
                'claim_from' => 'required|in:1,2', //1-Courier, 2-Company
                'claim_note' => 'required',
                'defect_unit' => 'required|array',
                'defect_unit.*' => 'required|integer',
                'batch_no' => 'required|array',
                'batch_no.*' => 'required',
                'upload_photo' => 'array',
                'upload_photo.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],[
                'defect_unit.*.required' => 'The defect unit field is required.',
                'batch_no.*.required' => 'The batch no field is required.',
                'upload_photo.*.required' => 'The photo field is required.',
                'upload_photo.*.image' => 'The photo must be an image.',
                'upload_photo.*.mimes' => 'The photo must be a file of type: jpeg, png, jpg, gif, svg.',
            ]);

            $claim['order_id'] = $request->input("order_id");
            $claim['type'] = $request->input("claim_type");
            $claim['claimant'] = $request->input("claim_from");
            $claim['note'] = $request->input("claim_note");

            $claim = Claim::create($claim);

            $claim_items = [];
            foreach($request->input("defect_unit") as $key => $value){
                if($value > 0){
                    $claim_items[] = [
                        'claim_id' => $claim->id,
                        'order_item_id' => $key, //order_item_id
                        'quantity' => $value,
                        'batch_no' => json_encode($request->input("batch_no")[$key]),
                        'img_path' => $request->file("upload_photo")[$key]->store('claim'),
                    ];
                }
            }
            $claim->items()->createMany($claim_items);

            return response([
                'message' => 'Claim created successfully',
                'success' => 'ok',
                'claim' => $claim
            ], 201);

        }

        return response()->json(['success' => 'ok']);
    }
}
