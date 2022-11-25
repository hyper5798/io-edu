<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id',
        'role_name',
        'dataset',
        'created_at',
        'updated_at',
    ];

    /**
     *  應該應用日期轉換的屬性。
     *
     * @var array
     */

    protected $dates = [
        'updated_at',
        'created_at',
    ];
}
