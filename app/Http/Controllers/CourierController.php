<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\CoverageCourier;
use App\Models\State;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CourierController extends Controller
{
    /**
     * List of Couriers.
     *
     * @return json
     */
    public function list()
    {
        $couriers = Courier::select('code', 'name')->where('is_active', IS_ACTIVE)->get();

        return response()->json($couriers);
    }

    public function listAll()
    {
        $title = 'List of Couriers';
        $crumbList = array(
            array(
                'name' => 'Setting',
                'url' => route('home'),
            ),
            array(
                'name' => 'Couriers',
                'url' => route('couriers.index'),
                'active' => 'active',
            ),
        );
        $couriers = Courier::select('id', 'code', 'name')->get();
        return view('couriers.list', compact('title', 'crumbList', 'couriers'));
    }

    public function listCourier(Request $request)
    {
        if($request->search){
            $item = Courier::select('*')->where('name', 'like', '%'.$request->search.'%')->orderBy('id', 'desc')->paginate(10);
        }else{
            $item = Courier::select('*')->orderBy('id', 'desc')->paginate(10);
        }
        $data = $item;

        return response()->json($data);
    }

    public function addCourier(Request $request)
    {

        $rules = [
            'courier_name' => 'required | unique:couriers,name,NULL,id,deleted_at,NULL',
            'courier_code' => 'required | unique:couriers,code,NULL,id,deleted_at,NULL',
            'minimum_attempt' => 'required | integer | min:1',
            'status_courier' => 'required | integer',
        ];

        $customMessages = [
            'required' => 'The :attribute field is required.',
            'unique' => 'The :attribute field is already exist.',
        ];

        $this->validate($request, $rules, $customMessages);

        try {
            $courier = new Courier();
            $courier->code = $request->courier_code;
            $courier->name = $request->courier_name;
            $courier->min_attempt = $request->minimum_attempt;
            $courier->status = $request->status_courier;

            $courier->save();

            return response([
                "status" => "success",
                "message" => "Courier has been added",
                "data" => []
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'error',
                'errors' => $e->errors()
            ], 422);
        }

    }

    public function deleteCourier(Request $request)
    {
        $request->validate([
            'id' => 'required | integer',
        ]);

        try {
            $courier = Courier::find($request->id);
            $courier->delete();

            return response([
                "status" => "success",
                "message" => "Courier has been deleted",
                "data" => []
            ]);

        } catch (\Throwable $th) {
            return response([
                "status" => "error",
                "message" => "Courier has been deleted",
                "data" => []
            ]);
        }
    }

    public function editPage($id)
    {
        $title = 'Courier Setting';

        $courier = Courier::find($id);

        $crumbList = array(
            array(
                'name' => 'Setting',
                'url' => route('home'),
            ),
            array(
                'name' => 'Couriers',
                'url' => route('couriers.index'),
            ),
            array(
                'name' => $courier->name,
                'url' => route('couriers.editPage', $courier->id),
                'active' => 'active',
            ),
        );

        $item['id'] = $id;
        $item['courier_id'] = $courier->id;
        $item['courier_name'] = $courier->name;

        return view('couriers.edit-courier', compact('title', 'item', 'crumbList'));
    }

    public function generalSetting($courier_id, $type)
    {
        if ($type == 1) {
            $title = 'General';
        } else if ($type == 2) {
            $title = 'Service-Level Aggrement (SLA)';
        } else {
            $title = 'Courier Coverage';
        }

        $courier = Courier::find($courier_id);
        $states =  Arr::where(MY_STATES, function ($value, $key) {
            return $key > 0 && $key < 17;
        });
        $crumbList = array(
            array(
                'name' => 'Setting',
                'url' => route('home'),
            ),
            array(
                'name' => 'Couriers',
                'url' => route('couriers.index'),
            ),
            array(
                'name' => $courier->name,
                'url' => route('couriers.editPage', $courier->id),
            ),
            array(
                'name' => $title,
                'url' => route('couriers.generalSetting', ['courier_id' => $courier_id, 'type' => $type]),
                'active' => 'active',
            ),
        );

        return view('couriers.setting', compact('title', 'crumbList', 'courier', 'type', 'states'));
    }

    public function listCoverage(Request $request)
    {
        $item  = array(
            array(
                'id' => 1,
                'postcode' => '05000',
                'area' => 'Alor Setar Town',
                'district' => 'Alor Setar',
                'state' => 'Kedah',
                'sla' => '1',
                'cod' => 'YES'
            ),
            array(
                'id' => 2,
                'postcode' => '05050',
                'area' => 'Taman Anggerik',
                'district' => 'Alor Setar',
                'state' => 'Kedah',
                'sla' => '1',
                'cod' => 'YES'
            ),
            array(
                'id' => 3,
                'postcode' => '05100',
                'area' => 'Hutan Kampung',
                'district' => 'Alor Setar',
                'state' => 'Kedah',
                'sla' => '1',
                'cod' => 'YES'
            ),
        );

        $data = $item;

        return response()->json($data);
    }

    public function addCoverage(Request $request){
        dd($request);
    }

    public function selectedcoverage()
    {
        $title = 'Selected Coverage';
        $crumbList = array(
            array(
                'name' => 'Setting',
                'url' => route('home'),
            ),
            array(
                'name' => 'Selected Coverage',
                'url' => route('couriers.selectedCoverage'),
            ),
        );

        return view('couriers.selectedCoverage', compact('title', 'crumbList'));
    }

    public function listSelectedCoverage(Request $request)
    {
        $data = [];
        $postcodes = CoverageCourier::with('courier')
            ->where('postcode', $request->input('search'))
            ->get();

        if(!$postcodes){
            return response([
                'status' => 'error',
                'message' => 'Postcode not found',
                'errors' => []
            ], 422);
        }

        $data['postcode'] = $request->input('search');
        $data['couriers'] = $postcodes;
        // $item = array(
        //     array(
        //         'id' => 1,
        //         'hash_id' => hash_url_encode(1),
        //         'postcode' => '08000',
        //         'couriers' => array(
        //             array(
        //                 'courier_id' => '1',
        //                 'courier_name' => 'DHL',
        //                 'delivery_type' => 'COD',
        //             ),
        //             array(
        //                 'courier_id' => '2',
        //                 'courier_name' => 'Fedex',
        //                 'delivery_type' => 'NON-COD',
        //             ),
        //         ),
        //     )
        // );

        // $data = $item;

        return response($data);
    }

    public function defaultCoverage()
    {
        $title = 'Default Coverage';
        $crumbList = array(
            array(
                'name' => 'Setting',
                'url' => route('home'),
            ),
            array(
                'name' => 'Selected Coverage',
                'url' => route('couriers.selectedCoverage'),
            ),
            array(
                'name' => 'Default Coverage',
                'url' => route('couriers.defaultCoverage'),
            ),
        );
        $states = State::select('id', 'name')->where('country_code', 'MY')->get();
        $couriers = Courier::select('id', 'name')->where('status', IS_ACTIVE)->get();

        return view('couriers.defaultCoverage', compact('title', 'crumbList', 'states', 'couriers'));
    }

    public function defaultCoverageState(Request $request)
    {
        $state_id = $request->state_id;

        $state = State::find($state_id);

        return response($state);

    }

    public function updateDefaultCoverageState(Request $request)
    {
        $state_id = $request->input('state_id');
        $cod_courier_id = $request->input('cod_courier_id');
        $non_cod_courier_id = $request->input('non_cod_courier_id');

        $state = State::find($state_id);

        if(!$state){
            return response([
                'status' => 'error',
                'message' => 'State not found',
                'errors' => []
            ], 422);
        }

        $state->cod_courier_id = $cod_courier_id;
        $state->non_cod_courier_id = $non_cod_courier_id;

        $state->save();

        return response([
            'status' => 'success',
            'message' => 'Courier updated',
            'errors' => []
        ], 200);
    }

    public function exceptionalCoverage(Request $request)
    {

        $data = [];
        $lists = CoverageCourier::with('courier')->where('state_id', $request->state);

        if($request->search != null){
            $lists = $lists->where('postcode', $request->search);
        }

        $lists = $lists->get();

        return response()->json($lists);
    }

    public function addExceptionalCoverage(Request $request)
    {
        $request->validate([
            'postcode' => 'required | numeric | digits:5',
            'delivery_type' => 'required | integer',
            'courier' => 'required | integer | exists:couriers,id',
            'state_id' => 'required | integer | exists:states,id',
            'status_courier' => 'required | integer',
        ]);

        $coverage = CoverageCourier::where('postcode', $request->input('postcode'))
            ->where('state_id', $request->input('state_id'))
            ->where('type', $request->input('delivery_type'))
            ->whereNull('deleted_at')
            ->first();

        if($coverage){
            return response([
                'status' => 'error',
                'message' => 'Postcode already exist',
                'errors' => []
            ], 422);
        }

        CoverageCourier::create([
            'postcode' => $request->input('postcode'),
            'type' => $request->input('delivery_type'),
            'courier_id' => $request->input('courier'),
            'state_id' => $request->input('state_id'),
            'status' => $request->input('status_courier'),
        ]);

        return response([
            'status' => 'success',
            'message' => 'Courier updated',
            'errors' => []
        ], 200);
    }

    public function deleteExceptionalCoverage(Request $request)
    {
        $request->validate([
            'id' => 'required | integer | exists:coverage_couriers,id',
        ]);

        $coverage = CoverageCourier::find($request->input('id'));
        $coverage->delete();

        return response([
            'status' => 'success',
            'message' => 'Courier updated',
            'errors' => []
        ], 200);
    }

    public function updateExceptionalCoverage(Request $request)
    {
        $request->validate([
            'id' => 'required | integer | exists:coverage_couriers,id',
            'status_courier' => 'required | integer',
        ]);

        $coverage = CoverageCourier::find($request->input('id'));
        $coverage->status = $request->input('status_courier');
        $coverage->save();

        return response([
            'status' => 'success',
            'message' => 'Courier updated',
            'errors' => []
        ], 200);
    }

    public function updateGeneralSettings(Request $request){

        $request->validate([
            'courier_id' => 'required | integer | exists:couriers,id',
            'courier_name' => 'required | string',
            'min_attempt' => 'required | integer | min:1',
        ]);

        $courier = Courier::find($request->courier_id);
        if(!$courier){
            return response([
                'status' => 'error',
                'message' => 'Courier not found',
                'errors' => []
            ], 422);
        }
        $courier->name = $request->courier_name;
        $courier->min_attempt = $request->min_attempt;
        $courier->save();
        return response([
            'status' => 'success',
            'message' => 'Courier updated',
            'errors' => []
        ], 200);
    }

    public function uploadExceptionalCoverage(Request $request)
    {
        $request->validate([
            'file' => 'required | mimes:xlsx,xls,csv',
            'state_id' => 'required | integer | exists:states,id',
        ]);

        $file = $request->file('file');
        // 1: postcode, 2: delivery type, 3: courier code, 4: status
        $state_id = $request->input('state_id');

        // store csv to array ignore first row
        $csv = array_map('str_getcsv', file($file));
        array_shift($csv);

        //check if all courier code exist
        //unique courier code
        $couriers = array_unique(array_column($csv, 2));
        foreach($couriers as $courier){
            $courier = Courier::where('code', $courier)->first();
            if(!$courier){
                return response([
                    'status' => 'error',
                    'message' => 'Courier code '.$courier.' not found',
                    'errors' => []
                ], 422);
            }
            $courier_ids[$courier->code] = $courier->id;
        }

        // store csv to database
        foreach($csv as $row){
            $postcode = $row[0];
            $delivery_type = $row[1] == 'YES' ? '1' : '0';
            $courier = $courier_ids[$row[2]];
            $status = $row[3] == 'YES' ? '1' : '0';

            $coverage = CoverageCourier::where('postcode', $postcode)
                ->where('state_id', $state_id)
                ->where('type', $delivery_type)
                ->first();

            if($coverage){
                $coverage->courier_id = $courier;
                $coverage->status = $status;
                $coverage->save();
            }
            else{
                CoverageCourier::create([
                    'postcode' => $postcode,
                    'type' => $delivery_type,
                    'courier_id' => $courier,
                    'state_id' => $state_id,
                    'status' => $status,
                ]);
            }
        }

        return response([
            'status' => 'success',
            'message' => 'Courier updated',
            'errors' => []
        ], 200);

    }
}
