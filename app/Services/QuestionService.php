<?php

namespace App\Services;

use App\Constant\QuestionConstant;
use App\Models\Record;
use App\Repositories\FieldRepository;
use App\Repositories\QuestionRepository;
use App\Services\Base\Interfaces\CacheServiceInterface;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Integer;
use Yish\Generators\Foundation\Service\Service;

class QuestionService extends Service
{
    protected $repository, $fieldRepository;

    public function __construct(
        QuestionRepository $repository,
        FieldRepository $fieldRepository)
    {
        $this->repository = $repository;
        $this->fieldRepository = $fieldRepository;
    }

    public function getFieldsWithoutAll() {
        return $this->fieldRepository->getBy('isAll', 0);
    }

    public function getFieldsWithAll() {
        return $this->fieldRepository->all();
    }

    public function saveQuestionListToCache(int $userId,Record $record, bool $hasCache) {
        $fields = session('fields');
        $allQuestion = $record->id.'_all_question';
        //FAST_ANSWER_TEST true : 只取得兩個考題進行快速驗證
        if(QuestionConstant::FAST_ANSWER_TEST)
            //test
            $questions = $this->getCheckByFieldWithLevel($fields, $record->field_id, $record->level_id, 2)->toArray();
        else
            //DB取得指定考題
            $questions = $this->getCheckByFieldWithLevel($fields, $record->field_id, $record->level_id, $record->number)->toArray();

        if($hasCache) {
            //存入快取
            $cacheService = app()->make(CacheServiceInterface::class);
            setUserCache($cacheService, $userId, $allQuestion, $questions);
        } else {
            //存入session
            session($allQuestion, $questions);
        }
    }

    public function getApiQuestionByUserRecord($userId, $record,  $hasCache) {

        $targetQuestion = $record->id.'_target_question';
        if($hasCache) {
            $cacheService = app()->make(CacheServiceInterface::class);
            $target = getUserCache($cacheService, $userId, $targetQuestion);
        } else {
            $target = session($targetQuestion);
        }

        $question_data = $this->getQuestionByUserRecord($userId, $record,  $hasCache);
        //用來比對的上一題答案
        if($target) {
            $question_data['cache_answer'] =  $target['answer'];
        }
        return $question_data;
    }

    /* 判斷有無輸入參數record id，有時取得所有考題隨機取出後存入(快取/session)，無時從(快取/session)取回
     * @parameter $userId : 帳戶ID
     * @parameter $record : 紀錄 model
     * @parameter $check : 無輸入參數record id
     * @parameter $hasCache : (快取/session) 切換
     * @return record id
     * */
    public function getQuestionByUserRecord($userId, $record,  $hasCache=null) {

        $allQuestion = $record->id.'_all_question';
        $targetQuestion = $record->id.'_target_question';
        $cache_score_key = $record->id.'_score';
        $questions = null;
        $questions_count = 0;
        $cacheService = app()->make(CacheServiceInterface::class);

        if($hasCache) {
            //從快取取回指定考題
            $questions = getUserCache($cacheService, $userId, $allQuestion);
        } else {
            //從session取回指定考題
            $questions = session($allQuestion);
        }

        if( $questions==null ) {
            if(!$hasCache) {
                //清除session
                session($allQuestion, null);
                session($targetQuestion, null);
            }
        }

        if($questions) {
            $questions_count = count($questions);
        }

        $target_question_data = [
            "record_id"=> $record->id,
            'start_at' => $record->start_at,
            'number' => $record->number,
            "time"=>now(),
            "count"=> ($record->number-$questions_count)+1,
            "sorts" => $this->randomSortList(),
            "score" => getUserCache($cacheService, $userId, $cache_score_key)
        ];

        if($questions_count > 0) {
            $index = array_rand($questions, 1);
            $question = $questions[$index];
            $question['content'] = html_entity_decode($question['content']);
            array_splice($questions, $index  , 1);
            $target_question_data['question'] = $question;
            $target_question_data['field_id'] = $question['field_id'];
            $target_question_data['level_id'] = $question['level_id'];
            $target_question_data['options'] = $this->getTesOptionList($question);
            //存更新後的考題列表及目前考題
            if($hasCache) {
                //存入快取
                setUserCache($cacheService, $userId, $allQuestion, $questions);
                setUserCache($cacheService, $userId, $targetQuestion, $question);
            } else {
                //存入session
                session($allQuestion, $questions);
                session($targetQuestion, $question);
            }
            //測時時顯示目前題目答案(陣列轉字串，網頁端由字串轉陣列)
            if(QuestionConstant::WEB_ANSWER_TEST) {
                $target_question_data['answer'] = json_encode($question['answer']);
            }
            //把考題答案移除
            unset($target_question_data['question']['answer']);

        } else {
            $target_question_data['question'] = null;
        }

        return $target_question_data;
    }

    /* 全領域時(isAll=1)平均取得考題
     * parameter $fields : 領域列表
     * parameter $field_id : 指定領域ID
     * parameter $field_id : 指定領域ID
     * parameter $number : 指定考題數
     * return $arr : 返回考題列表
     * */
    public function getCheckByFieldWithLevel($fields, $field_id, $level_id, $number) {
        $isAll = 0;
        $arr = null;

        foreach($fields as $field) {
            if($field->id == $field_id) {
                $isAll = $field->isAll;
            }
        }
        if($isAll) {
            $average = $number/($fields->count()-1);
            $tmp = $number%($fields->count()-1);
            $inx = 0;

            foreach($fields as $field) {
                if($field->isAll == 0) {

                    if($inx == 0) {
                        $tmp = $tmp + $average;
                    } else {
                        $tmp = $average;
                    }
                    $new = $this->repository->getByFieldAndLevel($field->id, $level_id)->random($tmp);
                    if($inx == 0) {
                        $arr = $new;
                    } else {
                        $arr = $arr->merge($new);
                    }
                    $inx++;
                }
            }
        } else {
            $arr = $this->repository->getByFieldAndLevel($field_id, $level_id)->random($number);
        }
        return $arr;
        //return $this->repository->getByFieldAndLevel($field_id, $level_id, $arr);
    }

    public function getByFieldWithLevel($field_id, $level_id=null , $arr=null) {
        return $this->repository->getByFieldAndLevel($field_id, $level_id, $arr);
    }

    public function getFieldGroup($field_id) {
        $groups = $this->repository->fieldGroup($field_id)->toArray();
        if(count($groups)<3) {
            $array = [1,2,3];
            foreach ($groups as $group) {
                //$group = json_decode(json_encode($group), true);;
                if (in_array( $group->level_id, $array)) {
                    unset($array[$group->level_id-1]);
                }
            }

            foreach ($array as $item) {
                $index = $item-1;
                $obj = json_decode(json_encode([$index=>["level_id"=>$item, "total"=>0]]));
                array_splice($groups, $index , 0, $obj);
            }
        }
        return $groups;
    }

    public function fieldWithLevelGroup($fields) {
        foreach ($fields as $field) {
            if($field->isAll != 1) {
                $groups = $this->getFieldGroup($field->id);
                $field->groups = $groups;
            }

        }
        return $fields;
    }

    public function getTestData($record)
    {
        $targetQuestion = $record->id.'_target_question';
        $cacheService = app()->make(CacheServiceInterface::class);
        $target = getUserCache($cacheService, $record->user_id, $targetQuestion);
        $questions = $this->repository->getByFieldAndLevel(2, 1)->toArray();
        $options = $this->getTesOptionList($questions[0]);
        $question_data = [
            "record_id"=> $record->id,
            'field_id' =>  $questions[0]['field_id'],
            'level_id' =>  $questions[0]['level_id'],
            'start_at' => $record->start_at,
            'number' => $record->number,
            "question"=> $questions[0],
            "time"=>now(), "count"=> 1,
            "options"=>$options,
            "sorts"=>$this->randomSortList(),
            "answer" => json_encode($target['answer']),
            "score"=> 0
        ];
        if(QuestionConstant::WEB_ANSWER_TEST) {
            $question_data['answer'] = json_encode($questions[0]['answer']);
        }

        return  $question_data;
    }

    public function getTesOptionList($question)
    {
        $arr['a'] = $question['option_a'];
        $arr['b'] = $question['option_b'];
        $arr['c'] = $question['option_c'];
        $arr['d'] = $question['option_d'];
        $arr['e'] = $question['option_e'];
        return $arr;
    }

    public function randomSortList()
    {
        $checks = ['a', 'b', 'c', 'd', 'e'];
        $arr = [];
        //$arr1 = [];

        while(count($checks)>0) {
            $inx = rand(0, count($checks)-1);
            $key = $checks[$inx];
            array_push($arr, $key) ;
            //array_push($arr1, 'option_'.$key) ;
            array_splice($checks, $inx , 1);
        }
        return $arr;
    }
}
