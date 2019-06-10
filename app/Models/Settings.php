<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{

	protected $table = 'settings';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'value',
        'description'
    ];

    /**
     * @param $value
     */
    public function setNameAttribute($name) {
        $this->attributes['name'] = str_replace(' ', '_', strtoupper($name));
    }
}