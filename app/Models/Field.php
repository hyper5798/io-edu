<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    public $table = "fields";

    protected $fillable = [
        'title',
        'isAll',
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
