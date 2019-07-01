<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customheaders extends Model
{

	protected $table = 'customheaders';

    protected $primaryKey = 'id';

	protected $fillable = [
		'name',
        'value'
	];
}