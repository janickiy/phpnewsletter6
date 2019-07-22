<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplatesCategory extends Model
{
    protected $table = 'templates_category';

    public $timestamps = false;

    protected $fillable = [
        'templateId',
        'categoryId'
    ];
}