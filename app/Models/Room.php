<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'room_name',
        'pass_time',
        'cp_id',
        'user_id',
        'work',
        'type',
        'isSale',
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
}
