<?php

namespace App\Services;

use App\Repositories\RecordRepository;
use App\Repositories\TestRepository;
use App\Services\Base\Interfaces\CacheServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yish\Generators\Foundation\Service\Service;

class RecordService extends Service
{
    protected $repository, $testRpository;

    public function __construct(RecordRepository $repository, TestRepository $testRpository) {
        $this->repository = $repository;
        $this->testRpository = $testRpository;
    }

    public function getByFieldWithLevel($field_id, $level_id) {
        return $this->repository->getByFieldAndLevel($field_id, $level_id);
    }

    public function getCountOfTests($testList=[]) {
        if(count($testList) == 0) return 0;
        $keys = array_keys($testList);
        $count = 0;
        foreach ($keys as $key) {
            $count = $count + count( $testList[$key] );
        }
        return $count;
    }

    /* 判斷有無輸入參數record id，有時存入(快取/session)，無時從(快取/session)取回
     * @param $request : Request
     * @param $user_id : 帳戶ID
     * @param $hasCache : (快取/session) 切換
     * @return int $record id
     * */
    public function getRecordId(Request $request, $user_id, $hasCache=null) {
        $checkId = (int)$request->input('id', 0);
        $record_id = (int)$request->input('id', 0);
        $key = 'record_id';
        if($hasCache) {
            $cacheService = app()->make(CacheServiceInterface::class);
            //當id=0 來自考題確認
            if($checkId == 0) {
                //從快取取回record_id
                $record_id = (int)getUserCache($cacheService, $user_id, $key);
            } else {
                //儲存record_id到快取
                setUserCache($cacheService, $user_id, $key, $record_id);
            }
        } else {
            if($checkId == 0) {
                //從session取回record_id
                $record_id = session($key);
            } else {
                //儲存record_id到session
                session($key, $record_id);
            }
        }
        return $record_id;
    }

    /* 判斷有無輸入參數record id，有時存入(快取/session)，無時從(快取/session)取回
     * @param $request : Request
     * @param $user_id : 帳戶ID
     * @param $hasCache : (快取/session) 切換
     * @return int $record id
     * */
    public function checkRecordDataByUser($userId) {
        $records = $this->repository->getBy('user_id', $userId);
        foreach ($records as $record) {

            if($record->field_id == 1) {
                $record->testData = $this->getAllFieldTests($record->id, $record->level_id);
            } else {
                $record->testData =  $this->getOneFieldTests($record->id, $record->field_id, $record->level_id);
            }
            $record = $this->checkRecord($record);
        }
        return $records;
    }

    public function checkRecord($record) {
        $data = [];
        $score = 0;
        $time = 0;

        if($record->testData) {

            $keys = array_keys($record->testData);

            foreach ($keys as $key) {
                $tests = $record->testData[$key];
                $score = $score + $tests->sum('score');
                $time = $time + $tests->sum('time');
                $data[$key] = ['score'=>$tests->sum('score'), 'time'=>$tests->sum('time')];
            }

        }
        $atr = ['time'=> $time, 'score'=>$score];

        if($record && $record->time==0) {
            $record = $this->repository->update($record->id, $atr);
        }
        $record->testData = $data;

        return $record;
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
        return $this->testRpository->getAllTest($recordId, $fieldId, $levelId);
    }
}

