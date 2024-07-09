<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_id', 
        'attempt_status', 
        'description', 
        'attempt_time',
        'created_at',
        'updated_at',
    ];

    protected $guarded = [];

    public function shipping()
    {
        return $this->belongsTo(Shipping::class);
    }
}
