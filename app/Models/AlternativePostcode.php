<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlternativePostcode extends Model
{
    use HasFactory;

    protected $table = 'alternative_postcode';
    protected $fillable = ['state', 'actual_postcode', 'actual_city', 'alternative_postcode', 'alternative_city'];

    public function state()
    {
        return $this->belongsTo(State::class, 'state', 'id');
    }
}
