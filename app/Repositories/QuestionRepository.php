<?php

namespace App\Repositories;

use App\Models\Question;
use Illuminate\Support\Facades\DB;
use Yish\Generators\Foundation\Repository\Repository;

class QuestionRepository  extends Repository
{
    protected $model;

    public function __construct(Question $model) {
        $this->model = $model;
    }

    //$arr已測驗過作為過濾條件
    public function getByFieldAndLevel($field_id, $level_id=null, $arr=null) {
        if($level_id != null && $arr!= null) {
            return $this->model::ofField($field_id)->ofLevel($level_id)->whereNotIn('id', $arr)->get();
        }
        if($level_id)
            return $this->model::ofField($field_id)->ofLevel($level_id)->get();
        return $this->model::ofField($field_id)->get();
    }

    public function fieldGroup($field_id) {
        return DB::table('questions')
            ->select('level_id', DB::raw('count(*) as total'))
            ->where('field_id', $field_id)
            ->orderBy('level_id', 'asc')
            ->groupBy('level_id')
            ->get();
    }
}
