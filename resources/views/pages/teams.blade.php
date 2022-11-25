@extends('Layout.default')

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
            <th >項目</th>
            <th >分隊名稱</th>
            <th >組織</th>
            <th >日期</th>
            <th > </th>
        </tr>
        </thead>

        <tbody>
        @foreach($teams as $team)
        <tr>
            <td>{{$loop->index}}</td>
            <td>{{$team->name}}</td>
            <td>{{$team->cp_id}}</td>
            <td>{{$team->updated_at}}</td>
            <td>
                <button v-if="editPoint!=index" type="button" name="edit" class="btn btn-primary btn-sm" @click="editCheck(index)">
                    編輯
                </button>
                <button v-else type="button" name="edit" class="btn btn-success btn-sm" @click="saveEdit(index)">
                    儲存
                </button>
                <button type="button" name="del" class="btn btn-danger btn-sm" @click="delCheck(index, user.userName)">
                    刪除
                </button>
            </td>
        </tr>
        @endforeach

        </tbody>
    </table>
</div>

@endsection

@section('footerScripts')
    <script>
        let teams = {!! $teams !!};
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/teams.js')}}"></script>
@endsection


