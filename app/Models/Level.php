<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = [
        'title',
        'updated_at',
        'created_at'
    ];

    public function question()
    {
        return $this->hasMany('App\Models\Question');
    }

    protected $dates = [
        'updated_at',
        'created_at',
    ];
}
