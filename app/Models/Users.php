<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
	protected $table = 'users';

    protected $primaryKey = 'id';

	protected $fillable = [
		'login',
		'name',
		'description',
		'password',
        'role'
	];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = password_hash($value,PASSWORD_DEFAULT);
        /*
        $this->update([
            'password' => password_hash($password,PASSWORD_DEFAULT)
        ]);
        */
    }



    protected $hidden = [
        'password',
    ];
}