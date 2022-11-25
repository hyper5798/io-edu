<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMission extends Model
{
    protected $table = 'group_mission';

    protected $fillable = [
        'group_id',
        'mission_id',
        'created_at',
        'updated_at',
    ];
}
