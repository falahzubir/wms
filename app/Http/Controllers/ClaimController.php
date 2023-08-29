<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClaimExport;

class ClaimController extends Controller
{

    private function index(){
        return Claim::with([
            'order', 'order.customer',
            'order.company', 'order.courier',
            'items', 'items.order_item', 'items.order_item.product',
            'items.order_item.product.detail', 'items.order_item.product.detail.owner']);
    }

    public function index_product()
    {
        $claims = $this->index()
            ->where('type', CLAIM_TYPE_PRODUCT)->orderBy('status')->orderBy('created_at')->paginate(10);

        return view('claims.index',[
            'title' => 'Claim List by Product',
            'filter_data' => [],
            'actions' => [ACTION_DOWNLOAD_CLAIM],
            'claims' => $claims
        ]);
    }

    public function index_courier()
    {
        $claims = $this->index()
            ->where('type', CLAIM_TYPE_COURIER)->orderBy('status')->orderBy('created_at')->paginate(10);

        return view('claims.index',[
            'title' => 'Claim List by Courier',
            'filter_data' => [],
            'actions' => [ACTION_DOWNLOAD_CLAIM],
            'claims' => $claims
        ]);
    }


    public function create(Request $request){
        $request->validate([
            'parcel_condition' => 'required|boolean',
            'order_id' => 'required|exists:orders,id',
        ]);

        $user_id = $request->input("user_id") ?? auth()->user()->id;

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
                'upload_photo.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:10240',
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
                        'img_path' => $request->file("upload_photo")[$key]->hashName()
                    ];
                    $request->file("upload_photo")[$key]->store('/public/claims/product');
                }
            }
            $claim->items()->createMany($claim_items);

            set_order_status(Order::find($request->input("order_id")), ORDER_STATUS_RETURN_COMPLETED, 'Return Order set as completed, claim created');

            return response([
                'message' => 'Claim created successfully',
                'success' => 'ok',
                'claim' => $claim
            ], 201);

        }

        set_order_status(Order::find($request->input("order_id")), ORDER_STATUS_RETURN_COMPLETED, 'Return Order set as completed', $user_id);

        return response()->json(['success' => 'ok']);
    }

    public function upload_cn(Request $request){
        $request->validate([
            'claim_id' => 'required|exists:claims,id',
            'reference_no' => 'required',
            'file' => ['required', File::types('pdf')],
        ]);

        $claim = Claim::find($request->input("claim_id"));
        $claim->reference_no = $request->input("reference_no");
        $claim->img_path = $request->file("file")->hashName();
        $claim->status = 1;
        $claim->save();

        $request->file("file")->store('/public/claims/credit_note');

        return response()->json(['success' => 'ok']);
    }

    public function delete(Request $request){
        $request->validate([
            'claim_id' => 'required|exists:claims,id',
            'user_id' => 'required|exists:users,id'
        ]);

        $claim = Claim::with('order')->findOrFail($request->input("claim_id"));
        $claim->delete();

        set_order_status($claim->order, ORDER_STATUS_RETURNED, 'Return Order set as pending, claim deleted', $request->input("user_id"));

        return response()->json(['success' => 'ok']);
    }


    /**
     * Download claim csv
     * @param  Request $request
     * @return json
     */
    public function download_claim_csv(Request $request)
    {
        // return $request;
        $fileName = date('Ymdhis') . '_list_of_claims.csv';
        $claims = $this->index();

        $claims->whereIn('id', $request->claim_ids);

        // $claims = $this->filter_claim($request, $claims);

        $claims = $claims->get();

        // dd($claims);

        Excel::store(new ClaimExport($claims),"public/".$fileName);

        return response([
            "file_name"=> $fileName
        ]);
    }
}
