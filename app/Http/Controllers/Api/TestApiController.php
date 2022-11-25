<?php

namespace App\Http\Controllers\Api;

use App\Formatters\FailFormatter;
use App\Formatters\SuccessFormatter;
use App\Http\Controllers\Controller;
use App\Repositories\FieldRepository;
use App\Services\Base\Services\CacheService;
use App\Services\QuestionService;
use App\Services\RecordService;
use App\Services\TestService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TestApiController extends Controller
{
    private $questionService, $recordService,$cacheService, $testService, $fieldRepository;

    public function __construct(
        QuestionService $questionService,
        RecordService $recordService,
        CacheService $cacheService,
        TestService $testService,
        FieldRepository $fieldRepository
    )
    {
        $this->questionService = $questionService;
        $this->recordService = $recordService;
        $this->cacheService = $cacheService;
        $this->testService = $testService;
        $this->fieldRepository = $fieldRepository;
    }

    /**
     * Self test verify.
     * parameter Request $request
     * @return
     */
    public function testVerify(
        Request $request
    ) {

        $input = $request->all();

        /* tokenVerify : 用戶憑證檢查
         * $input user_id: 用戶ID
         * $input user_id: 用戶憑證
         * return boolean
         * */
        if(!tokenVerify($input)) {
            return response('驗證失敗!', 401);
        }
        $cache_record_key = $input['record_id'].'_record';
        $cache_score_key = $input['record_id'].'_score';
        $cache_question_data_key = $input['record_id'].'_question_data';
        //從快取取回累積分數
        $record_score = (int)getUserCache($this->cacheService, $input['user_id'],  $cache_score_key);
        //從快取取回 record
        $record = getUserCache($this->cacheService, $input['user_id'],  $cache_record_key );
        //從快取取回考題資料
        $questionData = $this->questionService->getApiQuestionByUserRecord($input['user_id'],$record, true);


        //$diff = getNowDiff($questionData['time']);
        $arr = [];
        //計算測試時間(秒)
        $input['time'] = getNowDiff($input['start_at']);
        //答案驗證
        //快取答案
        $cacheAnswer = $questionData['cache_answer'];
        //評量答案
        $inputAnswer = json_decode($input['answer']);
        $check = false;
        //答案比對
        if(array_equal($cacheAnswer, $inputAnswer)) {
            //答案相同
            $check = true;
            //分數累加1
            $record_score = $record_score+1;
            //Save to test
            $input['score'] = 1;
        } else {
            //答案不同
            $check = false;
            //Save to test
            $input['score'] = 0;
        }
        //存入test table
        $test = $this->testService->create($input);

        //更新累積分數及測試結果
        $questionData['score'] = $record_score;
        $questionData['check'] = $check;
        setUserCache($this->cacheService, $input['user_id'], $cache_score_key, $record_score);
        setUserCache($this->cacheService, $input['user_id'], $cache_question_data_key, $questionData );
        //return response('更新成功!' , 200);
        return response()->json([
            'status' => 200,
            'message' => 'Get something successful.',
            'items'   => $questionData
        ]);
    }

    public function test(Request $request)
    {
        return response('刪除成功!', 200);
    }
}


