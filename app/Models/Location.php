<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    public $timestamps = false;
    protected $dates = [
        'recv'=> 'datetime:Y-m-d H:i:s.v',
    ];

    protected $fillable = [
        'macAddr',
        'lat',
        'lng',
        'image_url',
        'recv',
    ];
}
