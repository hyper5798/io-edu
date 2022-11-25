@extends('Layout.default')
@inject('QuestionPresenter', 'App\Presenters\QuestionPresenter')
@php
    $fields = session('fields');
    $levels = session('levels');
@endphp

@section('content')
    <div class="row breadcrumb">
        <div class="col-md-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/backend">後台儀表板</a></li>
                <!--<li class="breadcrumb-item">{{__('layout.management') }}</li> -->
                <li class="breadcrumb-item active" aria-current="page">評量考題</li>
            </ol>
        </div>
        <div class="col-md-6 text-center">
        </div>
        <div class="col-md-3 text-right">
            <button type="button" class="btn btn-success text-right" onclick="create()">{{__('layout.add')}}</button>
        </div>
    </div>

<div v-show="!isNew" class="main-content">
    <div class="row">
        <div class="input-group mb-3 col-md-3">
            領域 : &emsp;
            <select onchange="location.href='/learn/question?field_id='+this.options[this.selectedIndex].value+'&level_id='+{{$level_id}}">
                @foreach ($fields as $field)
                    @if ($field->id == $field_id)
                        <option value="{{$field->id}}" selected="selected">{{$field->title}}</option>
                    @else
                        <option value="{{$field->id}}">{{$field->title}}</option>
                    @endif

                @endforeach
            </select>
        </div>
        <div class="input-group mb-3 col-md-3">
            等級 : &emsp;
            <select onchange="location.href='/learn/question?level_id='+this.options[this.selectedIndex].value+'&field_id='+{{$field_id}}">
                @foreach ($levels as $level)
                    @if ($level->id == $level_id)
                        <option value="{{$level->id}}" selected="selected">{{$level->title}}</option>
                    @else
                        <option value="{{$level->id}}">{{$level->title}}</option>
                    @endif
                @endforeach
            </select>
        </div>


        <div class="input-group mb-3 col-md-6">
            @foreach($groups as $group)
                <span class="mr-3">
                    {{$QuestionPresenter->level($group->level_id)}} : {{$group->total}}
                </span>
            @endforeach
        </div>

    </div>

    <table id ="table1"  class="table table-striped table-hover">
        <thead>
        <tr>
            <th >{{__('layout.item')}}</th>
            <th >標題</th>
            <th >領域</th>
            <th >等級</th>
            <th >{{__('layout.update_at')}}</th>
            <th > </th>
        </tr>

        </thead>

        <tbody>
        @if($questions)
            @foreach ($questions as $question)
            <tr>
                <td width="10%"> {{$loop->index +1}} </td>
                <td width="40%"> {{$question->title}} </td>
                <td width="10%"> {{$QuestionPresenter->field($question->field_id)}} </td>
                <td width="10%"> {{$QuestionPresenter->level($question->level_id)}} </td>
                <td width="20%"> {{$question->updated_at}} </td>
                <td>
                    <a href="{{ route('learn.question.edit', [$question->id]) }}" class="btn btn-primary btn-sm">{{__('layout.edit')}}</a>
                </td>
            </tr>
        @endforeach
        @endif
        </tbody>
    </table>
</div>


@endsection

@section('footerScripts')
    <script>
        let questions = {!! $questions !!};
        let field_id = {!! $field_id !!};
        let level_id = {!! $level_id !!};
        function create() {
            let newUrl = "/learn/question/create?field_id="+field_id+'&level_id='+level_id;
            document.location.href = newUrl;
        }
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/learn/questions.js')}}"></script>
@endsection
