<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['marital_status'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getMaritalStatusAttribute($key)
    {
        return self::where("order_id",$this->order_id)->where('status',1)->count() > 1 ? "married":"single";
    }
}
