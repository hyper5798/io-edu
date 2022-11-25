<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Notifications\Notifiable;

class User extends AuthUser
{
    use Notifiable;

    /**
         * The attributes that are mass assignable.
         *
         * @var array
         */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cp_id',
        'class_id',
        'role_id',
        'email_verified_at',
        'remember_token',
        'active',
        'phone',
        'created_at',
        'updated_at',
    ];

    /**
         * The attributes that should be hidden for arrays.
         *
         * @var array
         */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
         * The attributes that should be cast to native types.
         *
         * @var array
         */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
         * The attributes that should be dates for arrays.
         *
         * @var array
         */
    protected $dates = [
        'updated_at',
        'created_at',
        'email_verified_at',
    ];

    /**
         * 屬於該用戶的團隊們。
         */
    /*public function teams()
    {
        return $this->belongsToMany('App\Models\Team');
    }*/
    /**
     * 取得使用者的裝置
     */
    public function devices()
    {
        return $this->hasMany('App\Models\Device');
    }

    /**
     * 取得使用者的留言
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * 取得使用者的個人資料
     */
    public function profile()
    {
        return $this->hasOne('App\Models\Profile');
    }

    /**
     * 取得使用者的權限資料
     */
    public function role()
    {
        return $this->belongsTo('App\Models\Role');
    }

    public function groups()
    {   //預設來說，pivot 物件只提供模型的鍵。如果你的 pivot 資料表包含了其他的屬性，可以在定義關聯方法時指定那些欄位
        //取得中介表group_role_id值  $group->pivot->group_role_id
        return $this->belongsToMany('App\Models\Group')->withPivot('cp_id', 'group_role_id');
    }
}
