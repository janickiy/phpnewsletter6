<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{

	protected $table = '';

    protected $primaryKey = 'id';

	protected $fillable = [
		'name',
		'email',
        'ip',
		'active',
		'token'
	];
}