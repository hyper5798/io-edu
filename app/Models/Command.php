<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Command extends Model
{
    protected $table = 'commands';
    /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
    protected $fillable = [
        'sequence',
        'type_id',
        'device_id',
        'macAddr',
        'cmd_name',
        'command'
    ];

    /**
         * The attributes that should be dates for arrays.
         *
         * @var array
         */
    protected $dates = [
        'updated_at',
        'created_at',
    ];

    public function type()
    {
        return $this->belongsTo('App\Models\Type');
    }

    public function device()
    {
        return $this->belongsTo('App\Models\Device');
    }
}
