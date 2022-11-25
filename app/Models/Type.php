<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type_id',
        'category',
        'type_name',
        'description',
        'image_url',
        'fields',
        'rules',
        'work',
        'created_at',
        'updated_at',
    ];

    protected $dates = [
        'updated_at',
        'created_at',
    ];

    protected $casts = [
        'fields' => 'array',
        'rules' => 'array',
    ];

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * 取得同類型的裝置
     */
    public function devices()
    {
        return $this->hasMany('App\Models\Device');
    }

    public function productss()
    {
        return $this->hasMany('App\Models\Product');
    }
}
