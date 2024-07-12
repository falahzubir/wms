<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StateGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    public function group_state_lists()
    {
        return $this->hasMany(GroupStateList::class);
    }
}
