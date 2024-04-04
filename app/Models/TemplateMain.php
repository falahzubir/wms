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

    public function columns()
    {
        return $this->hasMany(TemplateColumn::class, 'template_main_id', 'id');
    }

    public function templateColumns()
    {
        return $this->hasMany(TemplateColumn::class, 'template_main_id');
    }
}
