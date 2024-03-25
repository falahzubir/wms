<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BucketAutomation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];
    protected $appends = ['platform', 'shipment_type_desc'];

    public function bucket()
    {
        return $this->belongsTo(Bucket::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function operational_model()
    {
        return $this->belongsTo(OperationalModel::class);
    }

    public function event()
    {
        return $this->belongsTo(OrderEvent::class);
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function getPlatformAttribute()
    {
        switch ($this->payment_type_id) {
            case PAYMENT_TYPE_SHOPEE:
                return 'Shopee';
            case PAYMENT_TYPE_TIKTOK:
                return 'Tiktok';
            default:
                return null;
        }
    }

    public function getShipmentTypeDescAttribute()
    {
        switch ($this->shipment_type) {
            case 1:
                return 'Delivery';
            case 2:
                return 'Self Collect';
            default:
                return null;
        }
    }

}
