<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bucket;
use App\Models\BucketAutomation;
use App\Models\OrderEvent;
use App\Models\Courier;
use App\Models\OperationalModel;
use App\Models\Company;

class BucketAutomationController extends BucketController
{
    public function automation()
    {
        return view('buckets.automation', [
            'title' => 'Bucket Automation',
            'buckets' => Bucket::where('status', IS_ACTIVE)->get(),
            'events' => OrderEvent::all(),
            'shipment_types' => [
                '1' => 'Delivery',
                '2' => 'Self Collect',
            ],
            'couriers' => Courier::all(),
            'operational_models' => OperationalModel::all(),
            'platforms' => [
                PAYMENT_TYPE_SHOPEE => 'Shopee',
                PAYMENT_TYPE_TIKTOK => 'Tiktok',
            ],
            'companies' => Company::all(),
        ]);
    }

    public function create(Request $request)
    {
        $request->validate([
            'bucket' => ['required', 'exists:buckets,id'],
            'shipment_type' => ['nullable', 'in:1,2'],
            'courier' => ['nullable', 'exists:couriers,id'],
            'platform' => ['nullable', 'in:' . PAYMENT_TYPE_SHOPEE . ',' . PAYMENT_TYPE_TIKTOK],
            'operational_model' => ['nullable', 'exists:operational_models,id'],
            'company' => ['nullable', 'exists:companies,id', 'required_if:event_id,!=,null'],
            'event' => ['nullable', 'exists:order_events,id'],
        ]);

        $bucket_automation = BucketAutomation::create([
            'bucket_id' => $request->input('bucket'),
            'shipment_type' => $request->input('shipment_type'),
            'courier_id' => $request->input('courier'),
            'payment_type_id' => $request->input('platform'),
            'operational_model_id' => $request->input('operational_model'),
            'company_id' => $request->input('company'),
            'event_id' => $request->input('event'),
            'created_by' => auth()->user()->id,
        ]);

        $bucket_automation->update([
            'priority' => $bucket_automation->id,
        ]);

        if($bucket_automation){
            return response([
                'status' => 'success',
                'message' => 'Bucket Automation created successfully',
                'data' => $bucket_automation,
            ]);
        }

        return response([
            'status' => 'error',
            'message' => 'Bucket Automation failed to create',
        ]);
    }

    public function list()
    {
        $bucket_automations = BucketAutomation::with(['bucket', 'event', 'courier', 'operational_model', 'company'])
            ->orderBy('priority', 'asc')
            ->get();

        return response([
            'status' => 'success',
            'message' => 'Bucket Automation list',
            'data' => $bucket_automations,
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:bucket_automations,id'],
        ]);

        return response([
            'status' => 'success',
            'message' => 'Bucket Automation deleted successfully',
            'data' => BucketAutomation::where('id', $request->input('id'))->delete(),
        ]);
    }

    public function update_status(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:bucket_automations,id'],
            'status' => ['required', 'boolean'],
        ]);

        $bucket_automation = BucketAutomation::where('id', $request->input('id'))->update([
            'is_active' => $request->input('status'),
        ]);

        if($bucket_automation){
            return response([
                'status' => 'success',
                'message' => 'Bucket Automation status updated successfully',
                'data' => $bucket_automation,
            ]);
        }

        return response([
            'status' => 'error',
            'message' => 'Bucket Automation status failed to update',
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:bucket_automations,id'],
            'bucket' => ['required', 'exists:buckets,id'],
            'shipment_type' => ['nullable', 'in:1,2'],
            'courier' => ['nullable', 'exists:couriers,id'],
            'platform' => ['nullable', 'in:' . PAYMENT_TYPE_SHOPEE . ',' . PAYMENT_TYPE_TIKTOK],
            'operational_model' => ['nullable', 'exists:operational_models,id'],
            'company' => ['nullable', 'exists:companies,id', 'required_if:event_id,!=,null'],
            'event' => ['nullable', 'exists:order_events,id'],
        ]);

        $bucket_automation = BucketAutomation::where('id', $request->input('id'))->update([
            'bucket_id' => $request->input('bucket'),
            'shipment_type' => $request->input('shipment_type'),
            'courier_id' => $request->input('courier'),
            'payment_type_id' => $request->input('platform'),
            'operational_model_id' => $request->input('operational_model'),
            'company_id' => $request->input('company'),
            'event_id' => $request->input('event'),
            'created_by' => auth()->user()->id,
        ]);

        if($bucket_automation){
            return response([
                'status' => 'success',
                'message' => 'Bucket Automation updated successfully',
                'data' => $bucket_automation,
            ]);
        }

        return response([
            'status' => 'error',
            'message' => 'Bucket Automation failed to update',
        ]);
    }

    public function update_priority(Request $request)
    {
        foreach ($request->input('sort') as $data) {
            BucketAutomation::where('id', $data['id'])->update([
                'priority' => $data['priority'],
            ]);
        }

        return response([
            'status' => 'success',
            'message' => 'Bucket Automation priority updated successfully',
        ]);
    }
}
