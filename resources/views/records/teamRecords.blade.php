@extends('Layout.escape')

@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">{{__('layout.index_title') }}</a></li>
        <li class="breadcrumb-item">{{__('layout.management') }}</li>
        <li class="breadcrumb-item active" aria-current="page">{{__('layout.teams') }}</li>
    </ol>
<div v-show="!isNew">
    <table id ="table1"  class="table table-striped table-hover">
        <thead>
        <tr>
            <th >排名</th>
            <th >團隊名稱</th>
            <th >闖關時間</th>
            <th >闖關分數</th>

        </tr>
        </thead>

        <tbody>
        @foreach($records as $record)
        <tr>
            <td>{{$loop->index+1}}</td>
            <td>{{$record->team_id}}</td>
            <td>{{$record->total_time}}</td>
            <td>{{$record->total_score}}</td>

        </tr>
        @endforeach

        </tbody>
    </table>
</div>

@endsection

@section('footerScripts')
    <script>
        let records = {!! $records !!};
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/teamRecords.js')}}"></script>
@endsection


