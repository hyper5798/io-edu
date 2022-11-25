<?php

namespace App\Services;

use App\Constant\QuestionConstant;
use App\Repositories\TestRepository;
use Yish\Generators\Foundation\Service\Service;

class TestService extends Service
{
    protected $repository;

    public function __construct(TestRepository $repository) {
        $this->repository = $repository;
    }

    public function getTest($record_id, $question_id, $field_id, $level_id) {
        $test = $this->repository->getByRecordAndQuestion($record_id, $question_id);

        if($test->count() == 0) {
            $test =  $this->repository->create([
                'record_id'   => $record_id,
                'question_id' => $question_id,
                'field_id'    => $field_id,
                'level_id'    => $level_id
            ]);
        }
        return $test;
    }

    /*
     * @param $fields : 領域帶有數量群組
     * @param $level_id : 等級ID
     * @return array : [{ text: 'level_title', max: 150 }]
     * */
    public function getFieldMaxByLevel($fields, $level_id) {
        $arr = [];
        foreach($fields as $field) {
            if( $field->id >1 ) {
                $levelData = $field['groups'][$level_id-1];
                $arr[$field->id] =["text"=>$field->title, "average"=>$levelData->total/QuestionConstant::RADAR_LEVEL,"total"=> $levelData->total];
            }
        }
        return $arr;
    }

    public function getAllFieldTests($recordId, $levelId) {
        $fields = session('fields');
        $arr = [];
        foreach ($fields as $field) {
            if($field->id != 1) {
                $arr[$field->id] = $this->getTests($recordId, $field->id, $levelId);
            }
        }
        return $arr;
    }

    public function getOneFieldTests($recordId, $fieldId, $levelId) {
        $data = $this->getTests($recordId, $fieldId, $levelId);
        $arr = [$fieldId=>$data];
        return $arr;
    }

    public function getTests($recordId, $fieldId, $levelId) {
        return $this->repository->getAllTest($recordId, $fieldId, $levelId);
    }

    public function getSumForName($tests, $name , $indicators=null) {
        $arr = [];
        $keys = array_keys($tests);
        foreach ($keys as $key) {
            $item = $tests[$key];
            $sum = (float)$item->sum($name);
            if($indicators) {
                $check = $indicators[$key];
                $average = (float)$check['average'];
                array_push($arr, ceil($sum/$average));
            } else {
                array_push($arr, $sum);
            }
        }

        return $arr;
    }

    public function getFieldSumByName($tests, $name, $fieldAverages) {
        $arr = [];
        $keys = array_keys($tests);
        foreach ($keys as $key) {
            $item = $tests[$key];
            if($fieldAverages) {
                $check = $fieldAverages[$key];
                $average = (float)$check['average'];
                $sum = (float)$item->sum($name);
                $check['sum'] = $sum;
                $check['average'] = (float)$sum/$average;
                $check['count'] = count($item);
                $arr[$key] = $check;
            }
        }

        return $arr;
    }

    public function getAllFieldScore($fields,  $levels) {
        $arr = [];
        foreach ($levels as $level) {
            $tests = $this->getAllFieldTests(null, $level->id);
            $fieldAverages = $this->getFieldMaxByLevel($fields, $level->id);
            //取得分數再除以點數平均值
            $arr[$level->id] = $this->getFieldSumByName($tests, 'score' , $fieldAverages);
        }

        return $arr;
    }
}
