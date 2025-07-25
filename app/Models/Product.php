<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeActive($query)
    {
        return $query->where('is_active', 1)->where('name', '!=', '');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function detail()
    {
        return $this->hasOne(ProductDetail::class);
    }

    public function customers()
    {
        return $this->hasMany(ProductCustomer::class);
    }

}
