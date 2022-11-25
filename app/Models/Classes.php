<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'classes';

    protected $fillable = [
        'class_name',
        'cp_id',
        'user_id',
        'class_option',
        'members',
        'devices',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    /**
     * The attributes that cast type for arrays.
     *
     * @var array
     */
    protected $casts = [
        'members' => 'array',
        'devices' => 'array',
    ];
}
