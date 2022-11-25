@extends('Layout.escape')

@section('content')

    <!-- Search -->
    <div class="row breadcrumb">
        <div  class="col-sm-6 col-md-3 mt-1">
            <div class="mission_header">
                <!-- 選擇單位 -->
                @if($user->role_id<3)
                    <select onchange="location.href='?cp_id='+this.options[this.selectedIndex].value">
                        @foreach ($cps as $cp)
                            @if ($cp->id == $cp_id)
                                <option value="{{$cp->id}}" selected="selected">{{$cp->cp_name}}</option>
                            @else
                                <option value="{{$cp->id}}">{{$cp->cp_name}}</option>
                            @endif
                        @endforeach
                    </select>
                @else
                    <!-- 選擇遊戲場域 -->
                    {{__('layout.select') }}
                @endif
                <select onchange="location.href='?room_id='+this.options[this.selectedIndex].value">
                    @foreach ($rooms as $room)
                        @if ($room->id == $room_id)
                            <option value="{{$room->id}}" selected="selected">{{$room->room_name}}</option>
                        @else
                            <option value="{{$room->id}}">{{$room->room_name}}</option>
                        @endif
                    @endforeach
                </select>

            </div>
        </div>
        <div  class="col-sm-6 col-md-3 mt-3">
            排名搜尋方式
            <select v-cloak v-model="search.type" @change="changeType(search.type)">
                <option v-for="option in options" :value="option.value" :key="option.value">
                    @{{ option.text }}
                </option>
            </select>
        </div>

        <div class="col-sm-6 col-md-3 mt-2 text-left">

            <div  class="input-group input-daterange mb-2">
                <label class="mr-1 mt-2">選擇</label>
                <input id="report" name="report" type="text" class="form-control" maxlength="18" size="18">
                <span class="input-group-addon ml-1 mt-1">
                    <span class="fa fa-calendar fa-2x"></span>
                </span>
            </div>

        </div>

        <div class="col-md-3">
            <div v-cloak >

                <select v-if="search.type==2" v-model="search.range" class="mt-3" @change="changeRange(search.range)">
                    <option v-for="option in options2" :value="option.value" :key="option.value">
                        @{{ option.text }}
                    </option>
                </select>
                <button type="button" class= "btn btn-info float-right mt-2" onClick="toQuery()">
                    <span class="fa fa-search fa-1x"></span>
                    查詢
                </button>
            </div>

        </div>


    </div>
    <!-- Tab -->

    <div class="row mt-2">
        @if(env('ACCOUNT_MANAGER')==2)
        <div class="col-11">
            <ul class="nav nav-tabs">
            <li class="nav-item">
                @if($rank_tab == 1)
                <a class="nav-link active" data-toggle="tab" href="#1">{{__('record.local_records')}}</a>
                @else
                <a class="nav-link" data-toggle="tab" href="#1">{{__('record.local_records')}}</a>
                @endif
            </li>

            <li class="nav-item">
                @if($rank_tab == 2)
                    <a class="nav-link active" data-toggle="tab" href="#2">{{__('record.cp_records')}}</a>
                @else
                    <a class="nav-link" data-toggle="tab" href="#2">{{__('record.cp_records')}}</a>
                @endif
            </li>
            @if($user->role_id < 9)
            <li class="nav-item">
                @if($rank_tab == 3)
                    <a class="nav-link active" data-toggle="tab" href="#2">{{__('escape.emergency_button')}}</a>
                @else
                    <a class="nav-link" data-toggle="tab" href="#2">{{__('escape.emergency_button')}}</a>
                @endif
            </li>

            <li class="nav-item">
                @if($rank_tab == 4)
                    <a class="nav-link active" data-toggle="tab" href="#2">{{__('escape.timeout_failure')}}</a>
                @else
                    <a class="nav-link" data-toggle="tab" href="#2">{{__('escape.timeout_failure')}}</a>
                @endif
            </li>
            @endif
        </ul>
        </div>

        <div class="col-1">
            <button  title="{{__('layout.tutorials') }}" type="button" class="btn btn-warning" onclick="window.location='{{ url("/escape/carousel?app=3") }}'"><i class="fas fa-question"></i></button>
        </div>
        @endif
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
                <th >成績</th> <!-- 闖關時間 -->
            <!--<th >{{__('record.start_time')}}</th>  開始時間 -->
            <!--<th >{{__('record.end_time')}}</th>  結束時間 -->
                @if(env('ACCOUNT_MANAGER') == 2)
                    <th >{{__('layout.school')}}</th> <!-- 學校 -->
                @endif
                @if($user->role_id < 7)
                <th></th>
                @endif
            </tr>
            </thead>

            <tbody>
            @foreach($records as $item)
                <tr>
                    <td>{{$loop->index+1+(($page-1)*$limit)}}</td>
                    <td>{{ $item->name}}</td>
                    <td>{{ $item->total}}</td>
                    <!--<td>{{ $item->start}}</td> -->
                <!--<td>{{ $item->end}}</td> -->
                    @if(env('ACCOUNT_MANAGER') == 2)
                        <!-- 學校 -->
                        <td>{{ $item->cp_name}}</td>
                    @endif

                    @if($user->role_id < 7)
                        <td>
                            <button type="button" name="del" class="btn btn-danger btn-sm" @click="delCheck({{$loop->index}})">
                                {{__('layout.delete')}}
                            </button>
                        </td>
                    @endif
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{__('layout.waring')}}!</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('layout.delete_confirm')}} @{{record.name}} 時間:@{{record.total}}?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delRecord" id="delRecord">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="record.id" />
                        {{csrf_field()}}
                        <button type="button" onClick="toDelete()" class="btn btn-danger">
                            {{__('layout.yes')}}
                        </button>
                    </form>
                </div>
            </div>
        </div>
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
        let rank_tab = {!! $rank_tab !!};
        let page = {!! $page !!};
        let limit = {!! $limit !!};
        let search = {!! json_encode($search) !!};

    </script>
    <script src="{{asset('vender/bootstrap-4.3.1/js/bootstrap-datepicker.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/bootstrap-4.3.1/js/bootstrap-datepicker.zh-TW.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/extensions/Buttons-1.6.2/js/dataTables.buttons.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/extensions/Buttons-1.6.2/js/buttons.html5.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/extensions/Buttons-1.6.2/js/buttons.print.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/vfs_fonts.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/escape/rank.js')}}"></script>
@endsection


