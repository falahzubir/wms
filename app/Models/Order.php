<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function bucket()
    {
        return $this->belongsTo(Bucket::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function tracking()
    {
        return $this->hasOne(OrderTracking::class);
    }

    public function operations()
    {
        return $this->hasMany(OrderOperation::class);
    }

    public function events()
    {
        return $this->hasMany(OrderEvent::class);
    }
}
