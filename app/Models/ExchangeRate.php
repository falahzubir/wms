<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExchangeRate extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exchange_rates';

    protected $fillable = [
        'currency',
        'rate',
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function currencies()
    {
        // Use the 'currency' column in exchange_rates to link to the id in currencies table
        return $this->belongsTo(Currency::class, 'currency', 'id');
    }
}
