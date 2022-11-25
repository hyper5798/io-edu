<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'cp_id',
        'room_id',
        'mission_id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    public function users()
    {   //預設來說，pivot 物件只提供模型的鍵。如果你的 pivot 資料表包含了其他的屬性，可以在定義關聯方法時指定那些欄位
        return $this->belongsToMany('App\Models\User')->withPivot('cp_id', 'group_role_id');
    }

    public function missions()
    {
        return $this->belongsToMany('App\Models\Mission');
    }

    public function rooms()
    {
        return $this->belongsToMany('App\Models\Room');
    }
}
