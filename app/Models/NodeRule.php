<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NodeRule extends Model
{
    protected $table = 'node_rules';

    protected $fillable = [
        'node_mac',
        'order',
        'input',
        'output',
        'trigger_value',
        'operator',
        'action',
        'action_value',
        'time',
        'input_type',
        'output_type',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'trigger_value' => 'array'
    ];

    protected $dates = [
        'updated_at',
        'created_at',
    ];
}
