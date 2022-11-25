<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $dates = [
        'recv'=> 'datetime:Y-m-d H:i:s.v',
    ];

    protected $fillable = [
        'client',
        'operation',
        'log',
        'recv',
    ];
}
