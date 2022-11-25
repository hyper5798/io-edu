@extends('Layout.diy')
@inject('QuestionPresenter', 'App\Presenters\QuestionPresenter')
@php
    $user = session('user');
    $fields = session('fields');
    $levels = session('levels');
@endphp

@section('css')

    <style>
        #test { height: 500px; width: 100%; }
    </style>
@endsection

@section('content')
    <div class="row breadcrumb">
        <div class="col-md-9">
            <ol class="breadcrumb">

                <!--<li class="breadcrumb-item"><a href="/learn/question">評量考題</a></li>-->
                <li class="breadcrumb-item active" aria-current="page">自我評量</li>
            </ol>
        </div>

        <div class="col-md-3 text-right">

        </div>
    </div>

    <!-- Create question-->
    <div class="row justify-content-center main-content">
        <div class="col-lg-9 mt-2">
            <div class="card shadow-lg border-0 rounded-lg">

                <div v-cloak class="card-body">
                    <!--<form id="testVerify" method="post" action="{{url('/learn/test-verify')}}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{$user['id']}}" />
                        <input type="hidden" name="question_id" v-model="question.id" />
                        <input type="hidden" name="level_id" v-model="question.level_id" />
                        <input type="hidden" name="field_id" v-model="question.field_id" />

                        <div class="form-row">
                            <div class="input-group mb-1 col-md-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >第 @{{$question_data['count']}} 題</span>
                                </div>
                                <input type="text" class="form-control" value="{{$question_data['question']['title']}}">
                            </div>

                            <div class="input-group mb-1 col-md-12">
                                <div class="question-Content">{!! $question_data['question']['content'] !!}</p></div>
                            </div>

                            @foreach($question_data['sorts'] as $key)
                                <div class="input-group mb-1 col-md-11">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-default">{{$key}}選擇({{$loop->index}})</span>
                                    </div>
                                    <input type="text" class="form-control" name="option_a" value="{{ $question_data['question']['option_'.$key] }}">
                                </div>
                                <div class="input-group mb-1 col-md-1">
                                    <input type="checkbox" class="form-control" name="{{$key}}" >
                                </div>
                            @endforeach
                        </div>
                    </form>-->
                    <form id="testVerify" method="post" action="{{url('/learn/test-verify')}}">
                        @csrf
                        <input type="hidden" name="user_id" value="{{$user['id']}}" />
                        <input type="hidden" name="record_id" value="{{$question_data['record_id']}}" />
                        <input type="hidden" name="question_id" v-model="question.id" />
                        <input type="hidden" name="level_id" v-model="question.level_id" />
                        <input type="hidden" name="field_id" v-model="question.field_id" />

                        <div class="form-row">
                            <div class="input-group mb-1 col-md-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >第 @{{count}} 題</span>
                                </div>
                                <label type="text" class="form-control" >@{{question.title}}</label>
                            </div>

                            <div class="input-group mb-1 col-md-12">
                                <div class="question-Content"><p v-html="question.content"></p></div>
                            </div>
                        </div>
                        <div v-for="(item, index) in sortList" class="form-row">
                            <div class="input-group mb-1 col-md-11">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">
                                        @if($question_data['test'])
                                            測試: @{{item}}
                                        @endif
                                        選擇(@{{index+1}})
                                    </span>
                                </div>
                                <label type="text" class="form-control"  >@{{ question['option_'+item] }}</label>
                            </div>
                            <div class="input-group mb-1 col-md-1">
                                <input type="checkbox" class="form-control" :name="item" v-model="testObj[item]">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-3 mt-2">
            <div class="card">
                <div class="card-header">
                    <label class="font-weight-bold ml-4 mt-1" >評量</label>
                    @if($question_data['test'])
                        <span class="ml-5"><label v-cloak >測試: @{{questionData.answer}} </label></span>
                    @endif
                </div>
                <div class="card-body">
                    <!-- command button -->

                    <div class="row">
                       <div v-cloak v-if="isNext==true" class="input-group col-md-12">
                            <div v-show="isRight==false && answerString != ''" class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">答案</span>
                                </div>
                                <span class="form-control text-center"><label v-cloak >@{{answerString}} </label></span>
                            </div>
                            <div v-if="isRight">
                                <img src="{{url('/Images/right.png')}}" width="100%" >
                            </div>
                            <div v-else>
                                <img src="{{url('/Images/error.png')}}" width="100%" >
                            </div>
                        </div>

                        <div v-cloak v-if="isFinish" class="input-group col-md-12">
                                <img src="{{url('/Images/finish.png')}}" width="100%" >
                        </div>

                        <div class="input-group mb-2 col-md-12">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-sizing-default">領域</span>
                            </div>
                            <span class="form-control text-center"><label v-cloak >@{{ field_title }} </label></span>

                        </div>
                        <div class="input-group mb-2 col-md-12">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-sizing-default">等級</span>
                            </div>
                            <span class="form-control text-center"><label v-cloak >@{{ level_title }} </label></span>

                        </div>
                        <!--<div class="input-group mb-2 col-md-12">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-sizing-default">題次</span>
                            </div>
                            <span class="form-control text-center"><label v-cloak >@{{count}} </label></span>
                        </div>-->

                        <div class="input-group mb-2 col-md-12">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-sizing-default">得分</span>
                            </div>
                            <span class="form-control text-center"><label v-cloak >@{{score}} </label></span>

                        </div>
                        <div class="input-group mb-2 col-md-12">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-sizing-default">進度</span>
                            </div>
                            <span class="form-control text-center"><label v-cloak >@{{count}} / @{{number}} </label></span>
                        </div>
                        <div class="input-group mb-2 col-md-12">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-sizing-default">開始時間</span>
                            </div>
                            <span class="form-control text-center"><label v-cloak >@{{startAt}} </label></span>
                        </div>
                        <div v-show="isFinish==false" class="input-group mb-1 col-md-12">
                            <!--<button type="button" class="btn btn-primary btn-block" @click="toSubmit();">{{__('layout.submit')}}</button>-->
                            <button v-if="isNext==false && isSend==false" type="button" class="btn btn-primary btn-block" @click="toVerify();">驗證答案</button>
                            <button  v-else type="button" class="btn btn-primary btn-block" @click="showNext();">測驗下一題</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
        </div>
    </div>

@endsection

@section('footerScripts')

    <script>
        let fields = {!! $fields !!};
        let levels = {!! $levels !!};
        let question_data = {!! json_encode($question_data) !!}
        let token = '{{$user->remember_token}}';
        let api_url = '{!! env('API_URL') !!}';
        let user_id = {{$user['id']}}
    </script>

    <script src="{{asset('js/option/tools.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/learn/selfTest.js')}}"></script>
@endsection
