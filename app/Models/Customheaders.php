<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customheaders extends Model
{

	protected $table = 'сustomheaders';

    protected $primaryKey = 'id';

	protected $fillable = [
		'name',
        'value'
	];
}