<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Attach extends Model
{



	protected $table = 'attach';

    protected $primaryKey = 'id';

	protected $fillable = [
        'name',
        'file_name',
        'templateId'
	];

    protected $attributes = [
        'name' => 'user',
    ];


	public function scopeRemove(Builder $query, $id, $directory)
    {
        $q = $query->where('id', $id);

        if ($q->exists()) {
            $attach = $q->first();
            if (file_exists($directory . '/' . $attach->name)) unlink($directory . '/' . $attach->name);
            return $q->delete();
        }

        return false;
    }


}