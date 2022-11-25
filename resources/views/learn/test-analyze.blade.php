@extends('Layout.diy')
@inject('QuestionPresenter', 'App\Presenters\QuestionPresenter')
@php
    $fields = session('fields');
    $levels = session('levels');
@endphp

@section('content')
    <div class="row breadcrumb">
        <div class="col-md-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active" aria-current="page">評量分析</li>
            </ol>
        </div>
        <div class="col-md-6 text-center">
        </div>
        <div class="col-md-3 text-right">

        </div>
    </div>

<div v-show="!isNew" class="main-content">
    <!--<div class="row mt-1">
        <div class="col-sm-12 col-md-2 col-lg-1 text-center font-weight-bold">等級</div>
        <div class="col-sm-12 col-md-5 col-lg-6 text-center font-weight-bold" >分析</div>
        <div class="col-sm-12 col-md-5 col-lg-5 text-center font-weight-bold">雷達圖</div>
        <div class="col-12"><hr></div>
    </div>-->
    <div class="row mt-1">
        @if($levels)
            @foreach ($levels as $item)
                <div class="col-sm-12 col-md-2 col-lg-1 text-center">{{$item->title}}</div>
                <div class="col-sm-12 col-md-5 col-lg-6">
                    <table id ="table2"  class="table table-striped table-hover">
                        <thead>
                        <tr>

                            <th >領域</th>
                            <th >分數</th>
                            <th >雷達圖得分點</th>
                            <th >已考題數</th>
                            <th >題庫題數</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($levelData[$item->id] as $data)
                            <tr>
                                <td >{{$data['text']}}</td>
                                <td >{{$data['sum']}}</td>
                                <td >{{$data['average']}}</td>
                                <td >{{$data['count']}} </td>
                                <td >  {{$data['total']}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-sm-12 col-md-5 col-lg-5" ><iframe src="{{url('/learn/radar?level_id='.$item->id)}}" width="100%" height="300px"></iframe><hr></div>
            @endforeach
        @endif
    </div>
</div>


@endsection

@section('footerScripts')
    <script>
        let fields = {!! $fields !!};
        let levels = {!! $levels !!};
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
@endsection
