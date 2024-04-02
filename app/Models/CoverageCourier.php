<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoverageCourier extends Model
{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }
}
