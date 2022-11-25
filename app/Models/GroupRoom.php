<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupRoom extends Model
{
    protected $table = 'group_room';

    protected $fillable = [
        'group_id',
        'room_id',
        'created_at',
        'updated_at',
    ];
}
