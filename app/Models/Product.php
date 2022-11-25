<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'type_id',
        'macAddr',
        'description',
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
     * 取得與產品有關的類型。
     */
    public function type()
    {
        return $this->belongsTo('App\Models\Type');
    }

    public function device()
    {
        return $this->hasOne('App\Models\Device');
    }
}
