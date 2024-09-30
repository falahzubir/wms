<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function currencies()
    {
        return $this->hasMany(Currency::class); // One to many with Currency
    }

    public function exchange_rate()
    {
        // Use hasManyThrough to relate ExchangeRate through Currency
        return $this->hasManyThrough(
            ExchangeRate::class,      // The related model
            Currency::class,          // The intermediate model
            'country_id',             // Foreign key on currencies table
            'currency',               // Foreign key on exchange_rates table
            'id',                     // Local key on countries table
            'id'                      // Local key on currencies table
        );
    }

    // Override the delete method to cascade soft delete
    public static function boot()
    {
        parent::boot();

        static::deleting(function ($country) {
            // Soft delete exchange rates related through currencies
            foreach ($country->currencies as $currency) {
                $currency->exchange_rate()->delete();  // Soft delete exchange rates
            }

            $country->currencies()->delete(); // Soft delete currencies
        });
    }
}
