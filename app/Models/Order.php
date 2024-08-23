<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $appends = ['is_multiple_carton', 'sum_item_quantity', 'sum_item_weight'];

    public function scopeActive($query)
    {
        return $query->where('is_active', IS_ACTIVE);
    }

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
        return $this->hasMany(OrderItem::class)->where('status', IS_ACTIVE);
    }

    public function logs()
    {
        return $this->hasMany(OrderLog::class)->where('status', IS_ACTIVE);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function shippings()
    {
        return $this->hasMany(Shipping::class)->where('status', IS_ACTIVE);
    }

    public function batch()
    {
        return $this->belongsTo(BucketBatch::class, 'bucket_batch_id');
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentType::class, 'payment_type');
    }

    public function operationalModel()
    {
        return $this->belongsTo(OperationalModel::class);
    }

    public function getIsMultipleCartonAttribute()
    {
        $items = $this->items;
        foreach ($items as $item) {
            if ($item->quantity > $item->product->max_box) {
                return true;
            }
        }
        return false;
    }

    public function claim()
    {
        return $this->hasOne(Claim::class);
    }

    public function getSumItemQuantityAttribute()
    {
        return $this->items->sum('quantity');
    }

    public function getSumItemWeightAttribute()
    {
        $items = $this->items;
        $weight = 0;
        foreach ($items as $item) {
            $weight += $item->product->weight;
        }

        return $weight;
    }
}
