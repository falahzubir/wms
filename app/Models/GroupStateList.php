<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GroupStateList extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['state_group_id', 'state_id'];

    public $appends = ['state_name'];

    public function getStateNameAttribute()
    {
        return $this->states->name;
    }

    public function states()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function state_groups()
    {
        return $this->belongsTo(StateGroup::class, 'state_group_id');
    }
}
