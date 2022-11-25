<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class CourseOption extends Model
{
    protected $fillable = [
        'user_id',
        'category_selects'
    ];

    /**
     * The attributes that cast type for arrays.
     *
     * @var array
     */
    protected $casts = [
        'category_selects' => 'array'
    ];
}
