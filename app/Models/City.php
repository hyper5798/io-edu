<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'cities';

    protected $fillable = [
        'parent_id',
        'city_name',
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

    public function parent(){
        return $this->belongsTo(City::class);
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }
}
