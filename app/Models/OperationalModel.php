<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalModel extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function company() {
        return $this->hasOne(Company::class, 'id', 'default_company_id');
    }

    // public function shippingDocumentTemplates()
    // {
    //     return $this->belongsToMany(ShippingDocumentTemplate::class);
    // }
}
