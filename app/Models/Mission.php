<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    protected $fillable = [
        'mission_name',
        'sequence',
        'room_id',
        'game_id',
        'device_id',
        'macAddr',
        'user_id',
        'created_at',
        'updated_at',
    ];

    /**
     *  應該應用日期轉換的屬性。
     *
     * @var array
     */
    protected $dates = [
        'updated_at',
        'created_at',
    ];

    public function groups()
    {
        return $this->belongsToMany('App\Models\Group');
    }

    public function device()
    {
        return $this->belongsTo('App\Models\Device');
    }
}
