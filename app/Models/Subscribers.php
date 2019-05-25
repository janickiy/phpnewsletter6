<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscribers extends Model
{
	protected $table = 'subscribers';

    protected $primaryKey = 'id';

	protected $fillable = [
		'name',
        'email',
        'ip',
        'active',
        'token'
	];

    protected $hidden = [
        'token',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', 'true');
    }

}