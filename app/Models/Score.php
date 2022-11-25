<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Score extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'rating',
        'comment',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
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
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 透過用戶Id來尋找屬於該用戶的子評分。
     * @param  Builder  $query
     * @param  integer  $user_id
     * @return Builder  $query
     */
    public function scopeOfUser( $query , $user_id )
    {
        if ($user_id != 'none') {
            return $query->where('user_id', $user_id);
        }else{
            return $query;
        }
    }

    /**
     * 透過用戶Id來尋找屬於該用戶的子評分。
     * @param  Builder  $query
     * @param  integer  $course_id
     * @return Builder  $query
     */
    public function scopeOfCourse( $query , $course_id )
    {
        if ($course_id != 'none') {
            return $query->where('course_id', $course_id);
        }else{
            return $query;
        }
    }
}
