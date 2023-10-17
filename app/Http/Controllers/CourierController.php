<?php

namespace App\Http\Controllers;

use App\Models\Courier;
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
        $item = Courier::select('*')->orderBy('id', 'desc')->paginate(10);

        $data = $item;

        return response()->json($data);
    }

    public function addCourier(Request $request)
    {

        $rules = [
            'courier_name' => 'required | unique:couriers,name,NULL,id,deleted_at,NULL',
            'courier_code' => 'required | unique:couriers,code,NULL,id,deleted_at,NULL',
            'minimum_attempt' => 'required | integer',
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
                'msg' => 'error',
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
                'name' => 'DHL',
                'url' => route('couriers.editPage', $id),
                'active' => 'active',
            ),
        );
        $id = str_replace('wmsemzi', '', $id);
        $id = hash_url_decode($id);
        $item['id'] = $id;
        $item['hash_id'] = hash_url_encode($id);
        $item['courier_id'] = '1';
        $item['courier_name'] = 'DHL';

        return view('couriers.edit-courier', compact('title', 'item', 'crumbList'));
    }

    public function generalSetting($type)
    {
        if ($type == 1) {
            $title = 'General';
        } else if ($type == 2) {
            $title = 'Service-Level Aggrement (SLA)';
        } else {
            $title = 'Courier Coverage';
        }

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
                'name' => 'DHL',
                'url' => route('couriers.editPage', 'wmsemzi1'),
            ),
            array(
                'name' => $title,
                'url' => route('couriers.generalSetting', $type),
                'active' => 'active',
            ),
        );

        return view('couriers.setting', compact('title', 'crumbList'));
    }

    public function listSLA()
    {
        $item = array(
            array(
                'id' => 1,
                'hash_id' => hash_url_encode(1),
                'courier_id' => '1',
                'courier_name' => 'DHL',
                'sla_name' => 'D + 1',
                'postcode' =>  '00000,00001,00002,00003,00004,00005,00006,00007,00008,00009,10000,76221',
                'status' => 1,
            ),
            array(
                'id' => 2,
                'hash_id' => hash_url_encode(2),
                'courier_id' => '2',
                'courier_name' => 'Fedex',
                'sla_name' => 'D + 2',
                'postcode' => '00001,90100',
                'status' => 1,
            ),
            array(
                'id' => 3,
                'hash_id' => hash_url_encode(3),
                'courier_id' => '3',
                'courier_name' => 'JNE',
                'sla_name' => 'D + 3',
                'postcode' => '00003,18000',
                'status' => 1,
            ),
            array(
                'id' => 4,
                'hash_id' => hash_url_encode(4),
                'courier_id' => '4',
                'courier_name' => 'JNT',
                'sla_name' => 'D + 4',
                'postcode' => '10000,10002',
                'status' => 1,
            ),
            array(
                'id' => 5,
                'hash_id' => hash_url_encode(5),
                'courier_id' => '5',
                'courier_name' => 'TIKI',
                'sla_name' => 'D + 5',
                'postcode' => '30000',
                'status' => 1,
            ),
            array(
                'id' => 6,
                'hash_id' => hash_url_encode(6),
                'courier_id' => '6',
                'courier_name' => 'Wahana',
                'sla_name' => 'D + 6',
                'postcode' => '50000',
                'status' => 1,
            ),
        );

        $data = $item;

        return response()->json($data);
    }

    public function addSLA(Request $request)
    {
        dd($request->all());
    }

    public function editSLA(Request $request)
    {
        $form = $request->form;
        $form = explode('&', $form);
        $data = array();
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

    public function listSelectedCoverage()
    {
        $item = array(
            array(
                'id' => 1,
                'hash_id' => hash_url_encode(1),
                'postcode' => '08000',
                'couriers' => array(
                    array(
                        'courier_id' => '1',
                        'courier_name' => 'DHL',
                        'delivery_type' => 'COD',
                    ),
                    array(
                        'courier_id' => '2',
                        'courier_name' => 'Fedex',
                        'delivery_type' => 'NON-COD',
                    ),
                ),
            )
        );

        $data = $item;

        return response()->json($data);
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

        return view('couriers.defaultCoverage', compact('title', 'crumbList'));
    }

    public function defaultCoverageState(Request $request)
    {
        $state_id = $request->state_id;

        $item = array(
            array(
                'id' => 1,
                'state_id' => $state_id,
                'hash_id' => hash_url_encode(1),
                'couriers' => 1,2
            )
        );

        $data = $item;

        return response()->json($data);

    }

    public function exceptionalCoverage(Request $request)
    {
        $item = array(
            array(
                'id' => 1,
                'courier_id' => '1',
                'postcode' => '08000',
                'courier_name' => 'DHL',
                'delivery_type' => 'COD',
                'status' => 1,
            ),
            array(
                'id' => 2,
                'courier_id' => '2',
                'postcode' => '08001',
                'courier_name' => 'Fedex',
                'delivery_type' => 'NON-COD',
                'status' => 0,
            ),
            array(
                'id' => 3,
                'courier_id' => '3',
                'postcode' => '08002',
                'courier_name' => 'JNE',
                'delivery_type' => 'COD',
                'status' => 1,
            ),
            array(
                'id' => 4,
                'courier_id' => '4',
                'postcode' => '08003',
                'courier_name' => 'JNT',
                'delivery_type' => 'NON-COD',
                'status' => 1,
            ),
        );

        $data = $item;

        return response()->json($data);
    }
}
