<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NodeScripts extends Model
{
    protected $table = 'node_scripts';

    protected $fillable = [
        'script_name',
        'node_id',
        'node_mac',
        'api_key',
        'relation',
        'flow',
        'notify',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $casts = [
        'relation' => 'array',
        'flow' => 'array',
        'notify' => 'array',
    ];
}
