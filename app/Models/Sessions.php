<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sessions extends Model
{
	protected $table = 'sessions';

    protected $primaryKey = 'id';

	protected $fillable = [
		'userId',
        'token',
        'expiry'
	];
}