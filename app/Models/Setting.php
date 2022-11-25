<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_id',
        'name',
        'app_id',
        'user_id',
        'cp_id',
        'room_id',
        'field',
        'set',
        'set_index',
        'created_at',
        'updated_at',
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

    /**
     * The attributes that cast type for arrays.
     *
     * @var array
     */
    protected $casts = [
        'set' => 'array'
    ];

    /**
     * 取得擁有的設定裝置。
     */
    public function device()
    {
        return $this->belongsTo('App\Models\Device');
    }
}
