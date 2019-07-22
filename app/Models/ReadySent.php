<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadySent extends Model
{
	protected $table = 'ready_sent';

    protected $primaryKey = 'id';

	protected $fillable = [
        'subscriberId',
        'email',
        'templateId',
        'success',
        'errorMsg',
        'readMail',
        'date',
        'scheduleId',
	];
}