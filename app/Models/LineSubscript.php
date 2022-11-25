<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LineSubscript extends Model
{
    protected $table = 'line_subscripts';

    protected $fillable = [
        'user_id',
        'line_group',
        'token',
        'input_mac',
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
