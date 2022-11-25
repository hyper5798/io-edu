<?php

namespace App\Http\Controllers\Learn;

use App\Constant\QuestionConstant;
use App\Models\Field;
use App\Repositories\FieldRepository;
use App\Services\Base\Services\CacheService;
use App\Services\QuestionService;
use App\Services\RecordService;
use App\Services\TestService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class TestController extends Common3Controller
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
     * Display a listing of the resource.
     *
     * @return view
     */
    public function testCreate(Request $request)
    {
        $field_id = (int)$request->input('field_id', QuestionConstant::FIELD_ALL_ID);
        $level_id = $request->input('level_id', QuestionConstant::LEVEL_DEFAULT_ID);
        $fields = $this->fieldRepository->all();
        $fields =  $this->questionService->fieldWithLevelGroup($fields);
        session(['fields' => $fields]);
        return view('learn.test-create',compact(['field_id', 'level_id', 'fields']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function testRecord(Request $request)
    {
        $user = session('user');

        session(['current'=> url()->current()]);
        $input = $request->all();
        //Default
        $input['start_at'] = now();
        $input['tests'] = [];
        $record = $this->recordService->create($input);
        //Save init score of record
        $cache_record_key = $record->id.'_record';
        $cache_score_key = $record->id.'_score';
        //存記錄到快取
        setUserCache($this->cacheService, $user['id'] , $cache_record_key, $record);
        //存預設分數到快取
        setUserCache($this->cacheService, $user['id'] , $cache_score_key, 0);
        //取得記錄指定數量考題，存入快取
        $this->questionService->saveQuestionListToCache($user['id'], $record, true);
        return redirect ('/learn/self-test?id='.$record->id);
    }

    /**
     * Self test by record number.
     * parameter Request $request
     * @return \Illuminate\Contracts\Foundation\Application|View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function selfTest(Request $request)
    {

        $user = session('user');
        $current = session('current');
        //判斷有無輸入參數record id，有時存入(快取/session)，無時從(快取/session)取回
        $record_id = $this->recordService->getRecordId($request, $user['id'], true);
        $cache_question_data_key = $record_id.'_question_data';
        $question_data = null;
        //取得紀錄
        $record = $this->recordService->find($record_id);

        if (!str_contains($current, 'test-record')) {
            //dd('按F5或重新整理');
            //從快取取得考題資料，用於self-test refresh時不重新取資料
            $question_data = getUserCache($this->cacheService, $user['id'],  $cache_question_data_key);
        } else {

            //取得考題資料 time:考題開始時間， count:完成測驗次數， question:考題
            $question_data = $this->questionService->getQuestionByUserRecord($user['id'], $record, true);
            //存此資料用於self-test refresh時不重新取資料
            setUserCache($this->cacheService, $user['id'], $cache_question_data_key, $question_data );
        }
        session(['current'=> url()->current()]);

        if(QuestionConstant::CONTENT_HTML_TEST)
            $question_data = $this->questionService->getTestData($record);

        if($question_data == null || $question_data['question'] == null) {
            //用戶已全部完成考題
            return redirect('/learn/test-analyze');
        }

        //設定分數預設值
        $question_data['test'] = QuestionConstant::WEB_ANSWER_TEST;
        //dd($questions);
        return view('learn.self-test',compact(['record', 'question_data']));
    }

    /**
     * Self test verify.
     * parameter Request $request
     * @return \Illuminate\Contracts\Foundation\Application|View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    /*public function testVerify(Request $request) {
        $user = session('user');
        $input = $request->all();
        $checkId = (int)$request->input('id', 0);
        //判斷有無輸入參數record id，有時存入(快取/session)，無時從(快取/session)取回
        $record_id = $this->recordService->getRecordId($request, $user['id'], true);
        //取得紀錄
        $record = $this->recordService->find($record_id);
        //從快取取回考題資料
        $questionData = $this->questionService->getQuestionByUserRecord($input['user_id'], $record, true);
        //$diff = getNowDiff($questionData['time']);
        //計算測試時間(秒)
        $input['time'] = getNowDiff($questionData['time']);
        //答案驗證
        $arr = [];
        $answer = $questionData['question']['answer'];
        if(array_key_exists('a', $input)) {
            array_push($arr,'a');
        }
        if(array_key_exists('b', $input)) {
            array_push($arr,'b');
        }
        if(array_key_exists('c', $input)) {
            array_push($arr,'c');
        }
        if(array_key_exists('d', $input)) {
            array_push($arr,'d');
        }
        if(array_key_exists('e', $input)) {
            array_push($arr,'e');
        }
        $check = false;
        if(array_diff($arr, $answer) == false) {
            //答案相同
            $input['score'] = 1;
            $check = true;
        } else {
            //答案不同
            $input['score'] = 0;
            $check = false;
        }
        $this->testService->create($input);
        return view ('/learn/test-verify')->withErrors(['check'=>$check]);
    }*/

   /**
    * Self test verify.
    * parameter Request $request
    * @return \Illuminate\Contracts\Foundation\Application|View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
    */
    public function testAnalyze(Request $request) {
        $fields = session('fields');
        $levels = session('levels');
        $fields =  $this->questionService->fieldWithLevelGroup($fields);
        //返回陣列計算過的每點數平均值，以50題而言，10點數的平均為5
        //  [{field_id=>2,'text'=>'field_title', average=>5 }]
        //返回陣列計算過的每點數平均值，以50題而言，10點數的平均為5
        $levelData = $this->testService->getAllFieldScore($fields,  $levels);


        return view('/learn/test-analyze', compact(['levelData']));
    }

    public function radar(Request $request) {
        $user = session('user');
        $level_id = (int)$request->input('level_id', 0);
        $fields = session('fields');
        $levels = session('levels');

        $field_title = getIdTitleList($fields);
        $level_title = getIdTitleList($levels);

        //設定雷達圖點數: 預設10點數
        $RADAR_LEVEL = QuestionConstant::RADAR_LEVEL;
        $fields =  $this->questionService->fieldWithLevelGroup($fields);
        //返回陣列計算過的每點數平均值，以50題而言，10點數的平均為5
        //  [{field_id=>2,'text'=>'field_title', average=>5 }]
        $fieldAverages = $this->testService->getFieldMaxByLevel($fields, $level_id);

        $tests = $this->testService->getAllFieldTests(null, $level_id);
        //取得分數再除以點數平均值
        $values = $this->testService->getSumForName($tests, 'score' , $fieldAverages);


        return view('/learn/radar',compact(['fields', 'level_id', 'fieldAverages', 'values']));
    }

}

