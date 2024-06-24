<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bucket extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function batches()
    {
        return $this->hasMany(BucketBatch::class);
    }

    public function categoryBuckets()
    {
        return $this->hasMany(CategoryBucket::class);
    }

    public function processingOrders()
    {
        return $this->hasMany(Order::class)->where('status', ORDER_STATUS_PROCESSING)->where('is_active', 1);
    }
}
