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
        return $this->belongsTo(Country::class); // Many to One
    }

    public function exchange_rate() {
        return $this->hasMany(ExchangeRate::class); // One to Many
    }

    // Override the delete method to cascade soft delete
    public static function boot() {
        parent::boot();

        static::deleting(function ($currency) {
            $currency->exchange_rate()->delete(); // Soft delete related exchange rate
        });
    }
}
