<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Helper\StringHelpers;

class Templates extends Model
{

	protected $table = 'templates';

    protected $primaryKey = 'id';

	protected $fillable = [
		'name',
        'body',
        'prior',
        'pos',
        'categoryId'
	];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function category()
    {
        return $this->hasMany(TemplatesCategory::class, 'categoryId','templatesId');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function attach()
    {
        return $this->belongsTo(Attach::class, 'templateId');
    }

    /**
     * @return string
     */
    public function excerpt()
    {
        $content = $this->body;
        $content = preg_replace('/(<.*?>)|(&.*?;)/', '', $content);

        return StringHelpers::shortText($content,500);
    }

    /**
     * @param $prior
     * @return string
     */
    public static function getPrior($prior)
    {
        switch ($prior) {
            case 1:
                return 'высокая';

            case 2:
                return 'низкая';

            case 3:
                return 'нормальная';
        }
    }


}