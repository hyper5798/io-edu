<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CourseCategory extends Model
{
    protected $table = 'course_categories';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'tag', 'created_at','updated_at',
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

    public function courses()
    {
        return $this->hasMany('App\Models\Course', 'category_id');
    }
}
