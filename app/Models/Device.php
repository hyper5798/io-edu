<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'device_name',
        'macAddr',
        'status',
        'cp_id',
        'user_id',
        'type_id',
        'product_id',
        'network_id',
        'setting_id',
        'make_command',
        'description',
        'image_url',
        'isPublic',
        'support',
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

    public function scopePublic($query) {
        return $query->where('isPublic',  1);
    }

    public function scopeOfCp($query, $cpId)
    {
        return $query->where('cp_id', $cpId);
    }

    public function scopeType($query, $type)
    {
        return $query->where('type_id', $type);
    }

    /**
     * 取得擁有該裝置的用戶。
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * 取得與裝置有關的類型。
     */
    public function type()
    {
        return $this->belongsTo('App\Models\Type');
    }

    /**
     * 取得與裝置有關的設定。
     */
    public function setting()
    {
        return $this->hasOne('App\Models\Setting');
    }

    public function mission()
    {
        return $this->hasOne('App\Models\Mission');
    }

    /**
     * 取得與裝置有關的應用。
     */
    /*public function apps()
    {
        return $this->hasMany('App\Models\App');
    }*/

    public function plants()
    {
        return $this->hasMany('App\Models\Plant');
    }

    public function commands()
    {
        return $this->hasMany('App\Models\Command');
    }
}
