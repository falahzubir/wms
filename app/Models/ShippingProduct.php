<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingProduct extends Model
{
    use HasFactory;

    public function products()
    {
        return $this->belongsTo(Product::class);
    }
}
