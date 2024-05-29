<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function orders()
    {
        return $this->hasOne(Order::class);
    }

    protected function address(): Attribute
    {
        return Attribute::make(
            fn ($value) => str_replace(array("\r", "\n"), '', $value),
        );
    }

    public function states()
    {
        return $this->belongsTo(State::class, 'state', 'id');
    }
}
