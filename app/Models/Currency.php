<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Currency extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'country_id',
        'currency',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class); // Many to one relationship with Country
    }

    public function exchange_rate()
    {
        // Establish a one-to-many relationship with ExchangeRate using the `currency` column
        return $this->hasMany(ExchangeRate::class, 'currency', 'id');
    }
}
