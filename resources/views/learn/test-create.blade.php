@extends('Layout.diy')

@section('css')

@endsection

@section('content')
    @php
        $user = session('user');
        $fields = session('fields');
        $levels = session('levels');
    @endphp

    <div class="row breadcrumb">
        <div class="col-md-9">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">自我評量設定</li>
            </ol>
        </div>

        <div class="col-md-3 text-right">

        </div>
    </div>

    <!-- Create question-->
    <div class="row justify-content-center main-content">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-lg mt-2">

                <div class="card-body">
                    <form id="testRecord" method="post" action="/learn/test-record">
                        <input type="hidden" name="user_id" value="{{$user['id']}}" />
                        <input type="hidden" name="number" v-model="number" />
                        {{csrf_field()}}
                        <div class="form-row">
                            <div class="input-group mb-1 col-md-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >選擇評量領域</span>
                                </div>
                                <select name="field_id" class="form-control" onchange="changeField(this.selectedIndex)">

                                    @foreach ($fields as $field)


                                        @if ($field->id == $field_id)
                                            <option value="{{$field->id}}" selected="selected">{{$field->title}}</option>
                                        @else
                                            <option value="{{$field->id}}">{{$field->title}}</option>
                                        @endif

                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group mb-1 col-md-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >選擇評量等級</span>
                                </div>
                                <select name="level_id" class="form-control" onchange="changeLevel(this.options[this.selectedIndex].value)">
                                    @foreach ($levels as $level)
                                        @if ($level->id == $level_id)
                                            <option value="{{$level->id}}" selected="selected">{{$level->title}}</option>
                                        @else
                                            <option value="{{$level->id}}">{{$level->title}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-group mb-1 col-md-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >考題總數</span>
                                </div>
                                <input type="text" class="form-control" v-model="total">
                            </div>

                            <div v-cloak v-if="dropList.length>0" class="input-group mb-1 col-md-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >選擇評量題數</span>
                                </div>
                                <select  v-model="number" class="form-control">
                                    <option v-for="num in dropList" :value="num" :key="num">
                                        @{{ num }}
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <button type="button" class="btn btn-secondary" onClick="back()">{{__('layout.back')}}</button>
                                <button type="button" class="btn btn-primary" @click="toSubmit();">確認</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footerScripts')
    <script>
        function back() {
            history.go(-1);
        }
        let fields = {!! $fields !!};
        let field_id = {!! $field_id !!};
        let level_id = {!! $level_id !!};

    </script>
    <script src="{{asset('js/learn/testCreate.js')}}"></script>

@endsection
