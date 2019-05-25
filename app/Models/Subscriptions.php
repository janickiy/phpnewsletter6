<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriptions extends Model
{

	protected $table = 'subscriptions';

    protected $primaryKey = 'id';

    public $timestamps = false;

	protected $fillable = [
		'userId',
        'categoryId'
	];

}