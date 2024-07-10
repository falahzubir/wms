<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $fillable = ['name'];

    public function group_state_list()
    {
        return $this->hasMany(GroupStateList::class, 'state_id', 'id');
    }
}
