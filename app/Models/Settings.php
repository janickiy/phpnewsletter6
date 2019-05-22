<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{

	protected $table = 'settings';

    protected $primaryKey = 'id';

    protected $fillable = [
        'key_cd',
        'type',
        'display_value',
        'value'
    ];
    /**
     * @param $value
     */
    public function setKeyCdAttribute($value) {
        $this->attributes['key_cd'] = str_replace(' ', '_', strtoupper($value));
    }
    /**
     * @return string
     */
    public function getTypeAttribute() {
        return $this->attributes['type'] = strtoupper($this->attributes['type']);
    }
    /**
     * @return array|string
     */
    public function getValueAttribute() {
        if ($this->attributes['type'] == 'FILE') {
            return 'uploads/settings/' . $this->attributes['value'];
        }
        if ($this->attributes['type'] == 'SELECT') {
            $values = json_decode($this->attributes['value']);
            $array = [];
            foreach ($values as $value) {
                $array[$value] = $value;
            }
            return $array;
        }
        return $this->attributes['value'];
    }
}