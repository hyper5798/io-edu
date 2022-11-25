<?php

namespace App\Repositories;

use App\Models\Test;
use Yish\Generators\Foundation\Repository\Repository;

class TestRepository extends Repository
{
    protected $model;

    public function __construct(Test $model) {
        $this->model = $model;
    }

    public function getByRecordAndQuestion($record_id, $question_id=null) {

        return  $this->model->record($record_id)->question($question_id)->get();
    }

    public function getAllTest($recordId=null, $fieldId=null, $levelId=null) {
        $query = $this->model;
        if($recordId) $query = $query->ofRecord($recordId);
        if($fieldId) $query = $query->ofField($fieldId);
        if($levelId) $query = $query->ofLevel($levelId);

        return  $query->get();
    }
}
