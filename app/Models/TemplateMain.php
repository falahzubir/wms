<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateMain extends Model
{
    use HasFactory;

    protected $fillable = [
        'template_name',
        'template_type',
        'template_header',
    ];
}
