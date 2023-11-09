<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClaimItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function claim()
    {
        return $this->belongsTo(Claim::class);
    }

    public function order_item()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
