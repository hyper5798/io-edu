<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamRecord extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'team_records';

    protected $fillable = [
        'team_id',
        'room_id',
        'cp_id',
        'total',
        'reduce',
        'status',
        'sequence',
        'start',
        'end',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $dates = [
        'start',
        'end',
    ];

    /**
     * The attributes that cast type for arrays.
     *
     * @var array
     */
    protected $casts = [
    ];
}
