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
        return $this->belongsToMany(Product::class, 'order_items');
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function logs()
    {
        return $this->hasMany(OrderLog::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function shipping()
    {
        return $this->hasOne(Shipping::class);
    }

    public function batch()
    {
        return $this->belongsTo(BucketBatch::class, 'bucket_batch_id');
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

}
