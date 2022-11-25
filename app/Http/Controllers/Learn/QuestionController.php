<?php

namespace App\Http\Controllers\Learn;

use App\Constant\QuestionConstant;
use App\Models\Profile;
use App\Models\Question;

use App\Services\QuestionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class QuestionController extends Common3Controller
{
    private $questionService;

    public function __construct(
        QuestionService $questionService
       )
    {
        $this->questionService = $questionService;

    }
    /**
     * Display a listing of the resource.
     *
     * @return view
     */
    public function index(Request $request)
    {

        $field_id = $request->input('field_id', QuestionConstant::FIELD_DEFAULT_ID);
        $level_id = $request->input('level_id', QuestionConstant::LEVEL_DEFAULT_ID);

        $questions = $this->questionService->getByFieldWithLevel($field_id, $level_id);
        $groups =  $this->questionService->getFieldGroup($field_id);
        //dd($questions);
        return view('learn.questions',compact(['questions', 'field_id','level_id', 'groups']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response|view
     */
    public function create(Request $request)
    {
        $field_id = $request->input('field_id', QuestionConstant::FIELD_DEFAULT_ID);
        $level_id = $request->input('level_id', QuestionConstant::LEVEL_DEFAULT_ID);
        return view('learn.question-create',compact(['field_id', 'level_id']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $input = $request->all();

        $arr = [];
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
        $input['answer'] = $arr;
        $this->questionService->create($input);

        return redirect ('/learn/question');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function edit(Question $question)
    {

        //dd($answerCheck);
        return view('learn.question-edit',compact(['question']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $input = $request->all();

        $arr = [];
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
        $input['answer'] = $arr;
        $this->questionService->update($id, $input);

        return back ();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //dd($id);
        $this->questionService->destroy($id);
        return redirect(route('learn.question.index'));
    }
}
