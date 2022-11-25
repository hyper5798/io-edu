<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    public $timestamps = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'cellphone',
        'telephone',
        'birthday',
        'address',
        'image_url'
    ];

    /**
     * 取得擁有個人資料的使用者。
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
