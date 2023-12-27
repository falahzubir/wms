<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateColumn extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_main_id', 
        'column_main_id', 
        'column_position', 
        'updated_at'
    ];
}
