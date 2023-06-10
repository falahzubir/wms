<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $appends = [
        "address"
    ];

    public function orders()
    {
        return $this->hasOne(Order::class);
    }

    public function getAddressAttribute()
    {
        // remove break line
        return str_replace(array("\r", "\n"), '', self::select("address as old_address")->where("id",$this->id)->first()->old_address);
    }
}
