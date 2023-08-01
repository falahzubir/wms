<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];
    protected $appends = ['storage_condition'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(ProductCategory::class, 'sub_category_id');
    }

    public function owner()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function getStorageConditionAttribute()
    {
        return PROD_STORAGE_COND[$this->storage_cond] ?? null;
    }
}
