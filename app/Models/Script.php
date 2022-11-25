<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Script extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'script_name',
        'mission_id',
        'room_id',
        'content',
        'prompt1',
        'prompt2',
        'prompt3',
        'pass',
        'next_pass',
        'next_sequence',
        'note',
        'image_url',
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
        //'pass' => 'array'
    ];

}
