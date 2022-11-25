@extends('Layout.escape')

@section('content')

    <!-- Search -->
    <div class="row breadcrumb">
        <div  class="col-sm-6 col-md-3 mt-2">
            檢索個別團隊闖關紀錄
        </div>

        <div class="col-sm-6 col-md-5 mt-1">
                請輸入團隊名稱

            <input type="text"   v-model="search" name="search" maxlength="18" size="18">
        </div>

        <div class="col-md-4">

                <button type="button" class= "btn btn-info" onClick="toQuery()">
                    <span class="fa fa-search fa-1x"></span>
                    查詢
                </button>


        </div>


    </div>

    <div class="btn-group mt-1 mb-1">
        @if($page>2)
            <button class="btn btn-secondary" @click="first">第一頁</button>
        @endif
        @if($page>1)
            <button class="btn btn-secondary" @click="previous">{{__('pagination.previous')}}</button>
        @endif
        @if($page!=1)
            <button class="btn btn-secondary" disabled>{{$page}}</button>
        @endif
        @if($records->count() === $limit)
            <button class="btn btn-secondary" @click="next">{{__('pagination.next')}}</button>
        @endif
    </div>
    <div class="tableBlock">
        <table id ="table1"  class="table table-striped table-hover">
            <thead>
            <tr>
                <th >{{__('record.rank')}}</th> <!-- 排名 -->
                <th >{{__('team.team_name')}}</th> <!-- 團隊名稱 -->
                <th >{{__('escape.pass_time')}}</th> <!-- 闖關時間 -->
                <th >{{__('record.start_time')}}</th> <!-- 開始時間 -->
                <th >{{__('record.end_time')}}</th> <!-- 結束時間 -->
                <th >{{__('layout.school')}}</th> <!-- 學校 -->
                <th >狀態</th> <!-- 學校 -->
            </tr>
            </thead>

            <tbody>
            @foreach($records as $item)
                <tr>
                    <td>{{$loop->index+1+(($page-1)*$limit)}}</td>
                    <td>{{ $item->name}}</td>
                    <td>{{ $item->total_time}}</td>
                    <td>{{ $item->start}}</td>
                    <td>{{ $item->end}}</td>
                    <td>{{ $item->cp_name}}</td>
                    <td>
                        @if($item->status == 3)
                            <button type="button" class="btn btn-success btn-sm">闖關成功</button>
                        @elseif($item->status == 4)
                            <button type="button" class="btn btn-warning btn-sm">逾時失敗</button>
                        @elseif($item->status == 6)
                            <button type="button" class="btn btn-danger btn-sm">緊急按鈕</button>
                        @endif
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>

@endsection

@section('footerScripts')
    <script>
        let records = {!! $records !!};
        let user = {!! $user !!};
        let uri = "{!! $uri !!}";
        let room_id = {!! $room_id !!};
        let menu1 = "{{__('record.local_records')}}";
        let menu2 = "{{__('record.cp_records')}}";
        let menu3 = "{{__('escape.emergency_button')}}";
        let menu4 = "{{__('escape.timeout_failure')}}";
        let page = {!! $page !!};
        let limit = {!! $limit !!};
        let search = "{!! $search !!}";

    </script>
    <script src="{{asset('vender/bootstrap-4.3.1/js/bootstrap-datepicker.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/bootstrap-4.3.1/js/bootstrap-datepicker.zh-TW.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/extensions/Buttons-1.6.2/js/dataTables.buttons.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/extensions/Buttons-1.6.2/js/buttons.html5.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/extensions/Buttons-1.6.2/js/buttons.print.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/vfs_fonts.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/escape/teamRecords.js')}}"></script>
@endsection


