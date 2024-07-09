<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickingSequence extends Model
{
    use HasFactory;

    protected $fillable =
        [
            'product_id',
            'sequence',
            'created_by',
            'updated_by',
            'deleted_by',
        ];

        public function product()
        {
            return $this->belongsTo(Product::class, 'product_id');
        }
}
