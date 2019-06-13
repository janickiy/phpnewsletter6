<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Charset extends Model
{
	protected $table = 'charset';

    public $timestamps = false;

	protected $fillable = [
        'charset'
	];
}