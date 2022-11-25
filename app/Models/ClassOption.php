<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassOption extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'option_name',
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
}
