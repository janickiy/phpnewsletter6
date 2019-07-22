<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
	protected $table = 'schedule';

    protected $primaryKey = 'id';

	protected $fillable = [
        'date',
        'templatesId'
	];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function templates()
    {
        return $this->belongsTo(Templates::class, 'templatesId','id');
    }
}