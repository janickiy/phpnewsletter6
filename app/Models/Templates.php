<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Templates extends Model
{

	protected $table = 'templates';

    protected $primaryKey = 'id';

	protected $fillable = [
		'name',
        'body',
        'prior',
        'pos',
        'categoryId'
	];

    public function category()
    {
        return $this->belongsTo(Category::class,'id','categoryId');
    }
}