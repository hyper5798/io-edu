<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceMission extends Model
{
    protected $table = 'device_mission';

    protected $fillable = [
        'device_id',
        'mission_id',
        'created_at',
        'updated_at',
    ];
}
