<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cp extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'role_id',
        'user_id',
        'cp_name',
        'phone',
        'address',
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
        return $this->belongsTo(Cp::class);
    }

    /**
     * 透過分類Id來尋找屬於該分類的子分類。
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParentCp( $query , $cp_id )
    {
        if ($cp_id != 'none') {
            return $query->where('parent_id', $cp_id);
        }else{
            return $query;
        }
    }

    /**
     * 限制查詢只包括父類的元素。
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }
}
