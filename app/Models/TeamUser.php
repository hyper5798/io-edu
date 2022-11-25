<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamUser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'team_users';

    protected $fillable = [
        'team_id',
        'user_id',
        'cp_id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be dates for arrays.
     *
     * @var array
     */

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
