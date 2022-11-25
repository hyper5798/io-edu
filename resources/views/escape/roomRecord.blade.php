@extends('Layout.escape')

@section('content')
<div class="main-content">

    <div class="row breadcrumb">
        <div class="col-md-3">
            @if($from)
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:history.back()">{{__('layout.leader_board') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{__('layout.records') }}</li>
            </ol>
            @endif
        </div>
        <div class="col-md-6 mt-2 text-left">


        </div>

        <div class="col-md-3 text-right">
        </div>
    </div>

    <div class="row ">
        <div class="col-12">
            <h1>{{$room->room_name}}</h1>
            <hr>
        </div>
        <div class="col-sm-12 col-md-9 mt-4">
            <table style="width:100%;">

                <thead>
                    <tr>
                        <th ></th>
                        <th >{{__('escape.mission')}}</th>
                        <th>{{__('record.start_time')}}</th>
                        <th>{{__('record.end_time')}}</th>
                        <th>{{__('escape.pass_time')}}</th>
                    </tr>

                </thead>
                <tbody >
                <tr v-cloak v-for="(item, index) in missionList">

                    <td >
                        <div >
                            <div :style="item.gamePass" class="game-point rounded-circle text-center">
                                @{{item.sequence}}
                            </div>
                            <div v-if="index<missionList.length-1" class="game-line"></div>
                        </div>
                    </td>
                    <td >@{{item.mission_name}}</td>
                    <td >@{{item.start_at}}</td>
                    <td >@{{item.end_at}}</td>
                    <td >@{{item.time}}</td>

                </tr>
                </tbody>

            </table>
        </div>
        <div class="col-sm-12 col-md-3 mt-4">
            <div class="card" >
                <div class="card-header">
                    {{__('layout.teams')}}
                    @if($type==1)
                        {{$year}} 年
                    @elseif($type==2)
                        {{$year}} 年 第 {{$range}}季
                    @elseif($type==3)
                        {{$year}} 年 {{$range}}月
                    @endif
                    紀錄
                </div>
                <ul class="list-group list-group-flush">
                @if($team)

                        <li class="list-group-item">{{__('layout.team_name')}} : {{$team->name}}</li>
                        <li class="list-group-item">{{__('layout.total_time')}}: {{$team->total_time}}</li>

                        <li class="list-group-item">
                            <!--<button type="button" class="btn btn-link" @click="back(1,{{$team->local_rank}})">-->
                                {{__('record.local_records') }}
                            <!--</button>-->
                            {{__('layout.rank')}}: {{$team->local_rank}}
                        </li>

                        <li class="list-group-item">
                            <!--<button type="button" class="btn btn-link" @click="back(2,{{$team->cp_rank}})">-->
                                {{__('record.cp_records') }}
                            <!--</button>-->
                            {{__('layout.rank')}}: {{$team->cp_rank}}
                        </li>



                @else

                        <li class="list-group-item">{{__('layout.team_name')}} : </li>
                        <li class="list-group-item">{{__('layout.total_time')}}: </li>
                        <li class="list-group-item">{{__('record.cp_records') }}{{__('layout.rank')}}: </li>
                        <li class="list-group-item">{{__('record.local_records')}}{{__('layout.rank')}}: </li>

                @endif
                    <li v-cloak class="list-group-item">累計扣除時間: @{{reduce_time}}秒</li>
                </ul>
            </div>
            <div class="card" >
                <div class="card-header">
                    {{__('team.members')}}
                </div>
                <ul class="list-group list-group-flush">
                    @foreach($users as $team_user)
                    <li class="list-group-item">{{$team_user->name}}</li>
                    <!--<li class="list-group-item">{{__('layout.knowledge_power')}}: 4</li>-->
                    @endforeach
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
        let reduce = {!! $reduce !!};
        let from = {!! $from !!};
        let page = {!! $page !!};
    </script>
    <script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/escape/roomRecord.js')}}"></script>
@endsection


