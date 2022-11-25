<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $dates = [
        'recv'=> 'datetime:Y-m-d H:i:s.v',
    ];

    protected $fillable = [
        'macAddr',
        'type_id',
        'key1',
        'key2',
        'key3',
        'key4',
        'key5',
        'key6',
        'key7',
        'key8',
        'lat',
        'lng',
        'data',
        'extra',
        'app_id',
        'recv',
    ];




    /**
     * The attributes that cast type for arrays.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'extra' => 'array',
    ];
}
