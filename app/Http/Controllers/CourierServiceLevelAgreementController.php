<?php

namespace App\Http\Controllers;

use App\Models\Courier;
use App\Models\CourierServiceLevelAgreement;
use Illuminate\Http\Request;

class CourierServiceLevelAgreementController extends Controller
{
    public function list($courier)
    {
        $sla_list = CourierServiceLevelAgreement::where('courier_id', $courier)
            ->orderBy('days', 'asc')
            ->get();

        foreach ($sla_list as $key => $value) {
            $sla_list[$key]['sla_name'] = 'D + ' . $value->days;
            $sla_list[$key]['postcodes'] = implode(', ', json_decode($value->postcodes));
        }

        return response()->json($sla_list);
    }

    public function show($id)
    {
        $sla = CourierServiceLevelAgreement::find($id);
        $sla->postcodes = implode(', ', json_decode($sla->postcodes));
        return response()->json($sla);
    }

    public function create($courier, Request $request)
    {
        $request->validate([
            'sla_name' => 'required | integer | min:0',
            'postcode' => 'required | string',
        ]);

        $postcodes = explode(',', $request->input('postcode'));
        $postcodes = array_map('trim', $postcodes);

        $sla = CourierServiceLevelAgreement::where('courier_id', $courier)
        ->where('days', $request->input('sla_name'))
        ->first();

        if($sla){
            return response()->json([
                'message' => 'SLA for this courier already exist',
            ], 400);
        }

        if(count($request->input('duplicate')) > 0){
            $this->remove_duplicate($courier, $postcodes, $request->input('duplicate'));
        }

        CourierServiceLevelAgreement::create([
            'days' => $request->input('sla_name'),
            'courier_id' => $courier,
            'postcodes' => json_encode($postcodes),
        ]);

        return response()->json([
            'message' => 'SLA created successfully',
        ], 200);
    }

    public function update(CourierServiceLevelAgreement $sla, Request $request)
    {
        $request->validate([
            'sla_name' => 'required | integer | min:0',
            'postcode' => 'required | string',
        ]);

        $postcodes = explode(',', $request->input('postcode'));
        $postcodes = array_map('trim', $postcodes);

        $sla->postcodes = array_values($postcodes);
        $sla->save();

        if(count($request->input('duplicate')) > 0){
            $this->remove_duplicate($sla->courier_id, $postcodes, $request->input('duplicate'), $sla->id);
        }

        return response()->json([
            'message' => 'SLA updated successfully',
        ], 200);
    }

    private function remove_duplicate($courier, $postcodes, $duplicates, $exception = null)
    {
        foreach($duplicates as $dup_postcode){
            $duplicate = CourierServiceLevelAgreement::where('courier_id', $courier);
            if($exception){
                $duplicate = $duplicate->whereNot('id', $exception);
            }
            $duplicate = $duplicate->whereJsonContains('postcodes', $dup_postcode)
            ->first();

            if($duplicate){
                $postcodes = array_diff($postcodes, [$dup_postcode]);
                $duplicate_postcodes = json_decode($duplicate->postcodes);
                $duplicate_postcodes = array_diff($duplicate_postcodes, [$dup_postcode]);
                $duplicate->postcodes = array_values($duplicate_postcodes);
                $duplicate->save();
            }
        }
    }

    public function check_duplicate($courier, $exception = null, Request $request)
    {
        $all_postcodes = CourierServiceLevelAgreement::where('courier_id', $courier);
        if ($exception) {
            $all_postcodes->whereNotIn('id', [$exception]);
        }
        $all_postcodes = $all_postcodes->get();
        $postcode = array_map('trim', explode(',', $request->input('postcode')));

        //check if not 5 digits
        foreach ($postcode as $key => $value) {
            if (strlen($value) != 5) {
                return response()->json([
                    'message' => 'Postcode must be 5 digit',
                    'postcode' => $value,
                ], 400);
            }
        }

        $same_postcode = [];
        //find same postcode
        foreach ($all_postcodes as $key => $value) {
            $value_postcodes = json_decode($value->postcodes);
            foreach ($postcode as $key2 => $value2) {
                if (in_array($value2, $value_postcodes)) {
                    $same_postcode[] = $value2;
                }
            }
        }

        return response(['duplicate' => $same_postcode]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required | integer',
        ]);

        $sla = CourierServiceLevelAgreement::find($request->input('id'));
        $sla->delete();

        return response()->json([
            'message' => 'SLA deleted successfully',
        ], 200);
    }
}
