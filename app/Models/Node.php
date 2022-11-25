<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    protected $table = 'nodes';

    protected $fillable = [
        'node_name',
        'node_mac',
        'user_id',
        'inputs',
        'outputs',
        'relation',
        'flow',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $casts = [
        'inputs' => 'array',
        'outputs' => 'array',
        'relation' => 'array',
        'flow'
    ];
}
