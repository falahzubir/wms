<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WeightCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'weight_categories';

    protected $fillable = ['name','min_weight', 'max_weight', 'price'];

    protected $appends = ['weight_range'];

    public function getWeightRangeAttribute()
    {
        $min_weight = $this->min_weight / 1000;
        $max_weight = $this->max_weight / 1000;
        return $min_weight.'kg' . ' - ' . $max_weight.'kg';
    }
}
