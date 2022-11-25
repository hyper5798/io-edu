<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $table = 'tests';

    public $timestamps = FALSE;

    protected $fillable = [
        'record_id',
        'question_id',
        'field_id',
        'level_id',
        'score',
        'time'
    ];


    /**
     * 只查詢紀錄的 Scope。
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfRecord($query, $id)
    {
        return $query->where('record_id', $id);
    }

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

    /**
     * 只查詢考題 Scope。
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeQuestion($query, $id)
    {
        return $query->where('question_id', $id);
    }
}
