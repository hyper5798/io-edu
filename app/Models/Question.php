<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'title',
        'content',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'answer',
        'field_id',
        'level_id'
    ];

    public function field()
    {
        return $this->belongsTo('App\Models\Field');
    }

    public function level()
    {
        return $this->belongsTo('App\Models\Level');
    }

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $casts = [
        'answer' => 'array'
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
