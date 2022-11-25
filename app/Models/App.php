<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'macAddr',
        'device_id',
        'api_key',
        'key_label',
        'key_parse',
        'sequence',
        'image_url',
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

    /**
     * The attributes that cast type for arrays.
     *
     * @var array
     */
    protected $casts = [
        'key_label' => 'array',
        'key_parse' => 'array',
    ];
    /**
     * 屬於該app的裝置。 //要實現此多對一功能需加入device_id
     */
    /*public function device()
    {
        return $this->belongsTo('App\Models\Device');
    }*/
}
