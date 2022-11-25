<?php

namespace App\Repositories;

use App\Models\Record;
use Yish\Generators\Foundation\Repository\Repository;

class RecordRepository extends Repository
{
    protected $model;

    public function __construct(Record $model) {
        $this->model = $model;
    }

    public function getByFieldAndLevel($field_id, $level_id=null) {
        if($level_id)
            return $this->model->ofField($field_id)->ofLevel($level_id)->get();
        return $this->model->ofField($field_id)->get();
    }
}
