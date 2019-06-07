<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attach extends Model
{
	protected $table = 'attach';

    protected $primaryKey = 'id';

	protected $fillable = [
        'name',
        'templateId'
	];
}