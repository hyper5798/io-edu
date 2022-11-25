@extends('Layout.escape')

@section('content')

    <div class="main-content">

        <div class="row">
            <div v-cloak v-if="status==6" class="col-12 alert alert-danger" role="alert">
                {{__('escape.emergency_button')}}
            </div>
            <div v-cloak v-if="status==4" class="col-12 alert alert-warning" role="alert">
                {{__('escape.timeout_failure')}}
            </div>
            <div v-cloak v-if="status==3" class="col-12 alert alert-success" role="alert">
                {{__('escape.successfully_passed')}}
            </div>
            <div class="col-sm-6 col-md-5 col-xl-5">
                <!-- 選擇遊戲場域 -->
                <span class="title-room ml-2">
                @if(count($rooms) == 1)
                        {{$room->room_name}}
                    @elseif(count($rooms) > 1)
                        @if($user->role_id < 3)
                            <select onchange="location.href='?cp_id='+this.options[this.selectedIndex].value">
                            @foreach ($rooms as $item)
                                    @if ($item->id == $cp_id)
                                        <option value="{{$item->id}}" selected="selected">{{$item->room_name}}</option>
                                    @else
                                        <option value="{{$item->id}}">{{$item->room_name}}</option>
                                    @endif
                                @endforeach
                        </select>
                        @else
                            {{__('layout.select') }}{{__('escape.room') }}
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

                    @endif
            </span>
            </div>
            <div class="col-sm-6 col-md-7 col-xl-7 mt-2">
            <!--<div  v-cloak class=" time-label">{{__('escape.remain_time')}}:@{{ left_time }}
                <span class="float-right">
                    <button  title="{{__('layout.tutorials') }}" type="button" class="btn btn-warning" onclick="window.location='{{ url("/escape/carousel?app=2") }}'"><i class="fas fa-question"></i></button>
                </span>
            </div>-->

            </div>
            <div class="col-12">
                <hr>
            </div>

            <!--div class="col-sm-12 col-md-4 col-xl-3 mb-3">
                <div  v-cloak class="col-12 time-label">闖關時間:@{{ total_time }}分鐘</div>
            </div>
            <div class="progress col-sm-12 col-md-8 col-xl-7 " style="height: 40px;">
                <div v-cloak class="progress-bar" role="progressbar" :style="progress_style" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">@{{progress}}%</div>
            </div>-->

        </div>


        <div class="row ">

            <div class="col-sm-12 col-md-9 mt-4">
                <table style="width:100%;">

                    <thead>
                    <tr>
                        <th ></th>
                        <th >{{__('escape.mission')}}</th>
                        <th width="25%">{{__('record.start_time')}}</th> <!-- 開始時間 -->
                        <th width="25%">{{__('record.end_time')}}</th> <!-- 結束時間 -->
                        <th width="10%">耗時(秒)</th>
                    </tr>

                    </thead>
                    <tbody >
                    <tr v-cloak v-for="(item, index) in missionList">

                        <td >
                            <div >
                                <div :style="item.gamePass" class="game-point rounded-circle text-center">
                                    <label>@{{item.sequence}}</label>
                                </div>
                                <!--<div v-if="index<missionList.length-1" class="game-line"></div>-->
                            </div>
                        </td>
                        <td ><label class="text-info font-weight-bold">@{{item.mission_name}}</label></td>
                        <td ><input class="form-control" v-model="item.start_at" disabled></td>
                        <td ><input class="form-control" v-model="item.end_at" disabled></td>
                        <td >
                            <span v-if="item.time==''">
                                <input class="form-control" value="0" disabled>
                            </span>
                            <span v-else>
                                <input class="form-control" v-model="item.time" disabled>

                            </span>
                        </td>
                    </tr>
                    </tbody>

                </table>
            </div>
            <div class="col-sm-12 col-md-3 mt-4">
                <div class="card" >
                    <div class="card-header">
                        {{__('layout.teams')}}
                    </div>
                    <ul class="list-group list-group-flush">
                        @if($team)
                            <form method="post" action="editTeamName" id="editUserTeamName">
                                <input type="hidden" name="id" value="{{$team->id}}" />
                                {{csrf_field()}}
                                <li class="list-group-item">
                                    <div >
                                        <span>{{__('layout.team_name')}} :</span>

                                        <span class="float-right">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                        編輯
                                        </button>
                                    </span>
                                    </div>
                                    <div class="mt-4">
                                        <input type="text" name="name" value="{{$team->name}}" />
                                    </div>
                                </li>
                            </form>
                        @else
                            <li class="list-group-item">{{__('layout.team_name')}} : </li>
                    @endif

                    </ul>
                </div>
                <div class="card" >
                    <div class="card-header">
                        {{__('layout.personal')}}
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">{{$user->name}}</li>
                    <!--<li class="list-group-item">{{__('layout.knowledge_power')}}: 4</li>-->
                    </ul>

                </div>
            </div>



        </div>
    </div>
@endsection

@section('footerScripts')
    <script>
        let rooms = {!! $rooms !!};
        let room = {!! $room !!};
        let missions = {!! $missions !!};
        let start_time = '{!! $start_time !!}';
        let app_url = '{{ env('APP_URL') }}';
        let status = {!! $status !!};
        let sequence = {!! $sequence !!};
        let menu1 = "{{__('layout.pass_time_info') }}";
        let menu2 = "{{__('layout.records') }}";
        let team_id = {!! $team_id !!};
        //Fix facebook return hash
        if (window.location.hash && window.location.hash == '#_=_') {
            if (window.history && history.pushState) {
                window.history.pushState("", document.title, window.location.pathname);
            } else {
                // Prevent scrolling by storing the page's current scroll offset
                let scroll = {
                    top: document.body.scrollTop,
                    left: document.body.scrollLeft
                };
                window.location.hash = '';
                // Restore the scroll offset, should be flicker free
                document.body.scrollTop = scroll.top;
                document.body.scrollLeft = scroll.left;
            }
        }
    </script>
    <script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/escape/escapePersonal.js')}}"></script>
@endsection


