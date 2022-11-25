<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'group_user';

    protected $fillable = [
        'group_id',
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
