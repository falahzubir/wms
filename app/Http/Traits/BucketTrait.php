<?php

namespace App\Http\Traits;

use App\Models\BucketAutomation;

trait BucketTrait
{
    /**
     * Assign order to bucket based on the order's criteria
     *
     * @param array $order_data
     * @return int $bucket_id
     */
    public function assignBucket($order_data)
    {
        $automation = BucketAutomation::where('is_active', IS_ACTIVE)
            ->where(function ($query) use ($order_data) {
                $query->where('company_id', $order_data['company_id'])
                    ->orWhereNull('company_id');
            })
            ->where(function ($query) use ($order_data) {
                $query->where('operational_model_id', $order_data['operational_model_id'])
                    ->orWhereNull('operational_model_id');
            })
            ->where(function ($query) use ($order_data) {
                $query->where('payment_type_id', $order_data['payment_type'])
                    ->orWhereNull('payment_type_id');
            })
            ->where(function ($query) use ($order_data) {
                $query->where('shipment_type', $order_data['shipment_type'])
                    ->orWhereNull('shipment_type');
            })
            ->where(function ($query) use ($order_data) {
                $query->where('courier_id', $order_data['courier_id'])
                    ->orWhereNull('courier_id');
            })
            ->where(function ($query) use ($order_data) {
                $query->where('event_id', $order_data['event_id'])
                    ->orWhereNull('event_id');
            })
            ->orderBy('priority', 'asc')
            ->first();

        if ($automation) {
            $bucket_id = $automation->bucket_id;
        } else {
            $bucket_id = null;
        }

        return $bucket_id;
    }
}
