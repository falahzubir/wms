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

    // One Country has many ExchangeRate
    public function exchange_rate() {
        return $this->hasMany(ExchangeRate::class);
    }

    // Override the delete method to cascade soft delete
    public static function boot() {
        parent::boot();

        static::deleting(function ($country) {
            $country->exchange_rate()->delete(); // Soft delete related exchange rate
        });
    }
}
