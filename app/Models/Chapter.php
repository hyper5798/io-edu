<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chapter extends Model
{
    protected $table = 'chapters';

    protected $fillable = [
        'course_id',
        'title',
        'content',
        'sort',
        'video_id',
        'count',
        'image_url'
    ];

    /**
     * @return BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    /**
     * @return BelongsTo
     */
    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }

}
