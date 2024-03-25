<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryBucket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'bucket_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function categoryMain()
    {
        return $this->belongsTo(CategoryMain::class, 'category_id', 'id');
    }

    public function bucket()
    {
        return $this->belongsTo(Bucket::class, 'bucket_id', 'id');
    }
}
