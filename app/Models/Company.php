<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function access_tokens()
    {
        return $this->hasMany(AccessToken::class);
    }

    public function operational_model()
    {
        return $this->belongsTo(OperationalModel::class);
    }

    public function products()
    {
        return $this->hasMany(ProductCustomer::class);
    }
}
