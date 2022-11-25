<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Video extends Model
{
    protected $fillable = [
        'video_name',
        'title',
        'content',
        'storage_path',
        'video_url',
        'sort',
        'user_id',
        'category_id',
        'course_id',
        'sort',
        'duration'
    ];

    /**
     * The attributes that should be dates for arrays.
     *
     * @var array
     */

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @return HasOne
     */
    public function chapter()
    {
        return $this->hasOne('App\Models\Chapter');
    }
}
