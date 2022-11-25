<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'field_id',
        'level_id',
        'user_id',
        'tests',
        'start_at',
        'end_at',
        'time',
        'score',
        'number'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    protected $dates = [
        'start_at'=> 'datetime:Y-m-d H:i:s.v',
        'end_at'=> 'datetime:Y-m-d H:i:s.v',
    ];

    /**
     * The attributes that cast type for arrays.
     *
     * @var array
     */
    protected $casts = [
        'tests' => 'array',
    ];

    /**
     * 作用域：過濾領域
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeOfField($query, $id)
    {
        return $query->where('field_id', $id);
    }

    /**
     * 作用域：過濾等級
     *
     * @param $query
     *
     * @return mixed
     */
    public function scopeOfLevel($query, $id)
    {
        return $query->where('level_id', $id);
    }
}
