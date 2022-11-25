<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $table = 'courses';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'image_url',
        'title',
        'content_small',
        'content',
        'freeChapterMax',
        'isShow',
        'user_id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be dates for arrays.
     *
     * @var array
     */
    protected $dates = [
        'updated_at',
        'created_at',
    ];

    /**
     * 取得擁有該課程的分類。
     */
    public function category()
    {
        return $this->belongsTo('App\Models\CourseCategory');
    }

    /**
     * 取得擁有該課程的分類。
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * 取得課程的留言
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * 取得課程的評分
     */
    public function scores()
    {
        return $this->hasMany(Score::class);
    }

    /**
     * @return HasMany
     */
    public function chapters()
    {
        return $this->hasMany('App\Models\Chapter');
    }

    public function scopeIsShow($query) {
        return $query->where('isShow',  1);
    }

    public function scopeOfCategory($query, $category_id)
    {
        return $query->where('category_id', $category_id);
    }

    public function scopeOfUser($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }

}
