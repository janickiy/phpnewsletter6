<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
	protected $table = 'log';

    protected $primaryKey = 'id';

	protected $fillable = [
		'userId',
        'template',
        'sendStatusId',
        'time'
	];
}