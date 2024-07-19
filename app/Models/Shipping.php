<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function scannedBy()
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function events()
    {
        return $this->hasMany(ShippingEvent::class);
    }

    public function shipping_product()
    {
        return $this->hasMany(ShippingProduct::class);
    }

    public function shipping_cost()
    {
        return $this->belongsTo(ShippingCost::class);
    }
}
