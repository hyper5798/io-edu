<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'game_name',
        'room_id',
        'user_id',
        'cp_id',
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
}
