<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'comments';

    protected $fillable = [
        'parent_id',
        'user_id',
        'course_id',
        'specify',
        'comment',
        'status'
    ];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    /**
     * 取得擁有該留言的用戶。
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 取得擁有該留言的課程。
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function children()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }


    //透過留言Id來尋找屬於該留言的子留言
    public function scopeParentComments( $query , $comment_id )
    {
        if ($comment_id != null) {
            return $query->where('parent_id', $comment_id);
        }else{
            return $query->where('parent_id', null);
        }
    }

    //透過課程Id來尋找屬於該課程的留言
    public function scopeOfCourse( $query , $course_id )
    {
        if ( $course_id != null) {
            return $query->where('course_id',  $course_id);
        }else{
            return $query;
        }
    }

    //透過狀態來尋找留言
    public function scopeOfStatus( $query , $status)
    {
        return $query->where('status',  $status);
    }
}
