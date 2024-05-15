<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingDocumentTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'shipping_document_templates';
    protected $guarded = [];


    // public function add_new_shipping_document_template() // shipping document description
    // {
    //     return $this->belongsToMany(OperationalModel::class, 'operational_models');
    // }

    //     public function operationalModels()
    // {
    //     return $this->belongsToMany(OperationalModel::class);
    // }
    


}
