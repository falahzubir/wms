<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryMain extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_name',
        'category_status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function categoryBuckets()
    {
        return $this->hasMany(CategoryBucket::class, 'category_id', 'id');
    }
}
