<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingCost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'state_group_id',
        'courier_id',
        'weight_category_id',
        'price'
    ];

    public function state_groups()
    {
        return $this->belongsTo(StateGroup::class, 'state_group_id');
    }

    public function couriers()
    {
        return $this->belongsTo(Courier::class,'courier_id');
    }

    public function weight_category()
    {
        return $this->belongsTo(WeightCategory::class, 'weight_category_id');
    }
}
