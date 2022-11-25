<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announce extends Model
{
    protected $fillable = [
        'title',
        'content',
        'tag'
    ];


    protected $dates = [
        'updated_at',
        'created_at',
    ];
}
