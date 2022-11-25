<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    //被批量賦值的欄位
    protected $fillable = [
        'title', 'tag', 'plant_key','box','plant','kind','color','colorBlock','device_id','plant_time','crop_time','maturity','sort', 'created_at', 'updated_at'
    ];

    protected $casts = [
        'box' => 'array',
        'plant' => 'array',
        'colorBlock' => 'array',
    ];


    public function device()
    {
        return $this->belongsTo('App\Models\Device');
    }
}
