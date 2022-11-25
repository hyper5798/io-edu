@extends('Layout.escape')

@section('content')
<div class="main-content">
    <div class="row ">
        <!-- Room name -->
        <div class="col-sm-12 col-md-6 col-xl-6 mt-2">
            <!-- 選擇遊戲場域 -->
            <span class="title-room ml-2">
            @if(count($rooms) == 1)
            {{$room->room_name}}
            @elseif(count($rooms) > 1)
                    @if($user->role_id < 3)
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
                        {{__('layout.select') }}{{__('escape.room') }}
                    @endif
                </span>
                <span class="title-room ml-2">
                    <select onchange="location.href='?room_id='+this.options[this.selectedIndex].value">
                        @foreach ($rooms as $room)
                            @if ($room->id == $room_id)
                                <option value="{{$room->id}}" selected="selected">{{$room->room_name}}</option>
                            @else
                                <option value="{{$room->id}}">{{$room->room_name}}</option>
                            @endif
                        @endforeach
                    </select>
                </span>
            @endif
            <span v-if="event!=7" class="float-right">
                <button type="button" @click="reset" class="btn btn-outline-danger" >
                    {{__('escape.reset')}}
                </button>
            </span>
        </div>

        <!-- Mode button -->
        <div v-cloak v-if="event!=7" class="col-sm-12 col-md-6 col-xl-6 mt-2 text-center">
            <!--<button v-if="mode==30" type="button" @click="setGame" class="btn btn-info btn-sm" >
                {{__('escape.game_mode')}}
            </button>
            <button v-else type="button" @click="setGame" class="btn btn-outline-dark btn-sm" >
                {{__('escape.game_mode')}}
            </button>

            <button v-if="mode==31" type="button" @click="setDemo" class="btn btn btn-info btn-sm" >
                {{__('escape.demo_mode')}}
            </button>
            <button v-else type="button" @click="setDemo" class="btn btn-outline-dark btn-sm" >
                {{__('escape.demo_mode')}}
            </button>

            <button v-if="mode==32" type="button" @click="setSecurity" class="btn btn-info btn-sm" >
                {{__('escape.security_mode')}}
            </button>
            <button v-else type="button" @click="setSecurity" class="btn btn-outline-dark btn-sm" >
                {{__('escape.security_mode')}}
            </button>-->
            <span class="float-right">
                <button  title="{{__('layout.tutorials') }}" type="button" class="btn btn-warning" onclick="window.location='{{ url("/escape/carousel?app=1") }}'"><i class="fas fa-question"></i></button>
            </span>
        </div>

        <div class="col-sm-12 col-md-12">
            <hr>
      </div>

        <!-- status introduction -->
        <div class="col-md-12 col-xl-12 mt-2">
            <table style="width:100%;">
                <thead>
                <tr class="missionBlock" >
                    <th width="10%">號碼</th>
                    <th width="20%">{{__('escape.mission')}}</th>
                    <th width="20%">{{__('layout.devices')}}</th>
                    <!-- 連線狀態 -->
                    <th width="20%">{{__('escape.mission_status')}}</th>
                    <th width="20%">機台狀態</th>
                    <th width="10%">設定</th>
                </tr>

                </thead>
                <tbody >
                <tr class="missionBlock" v-cloak v-for="(item, index) in missionList">

                    <td>
                        <div class="game-point text-center" >
                            @{{item.sequence}}
                        </div>
                    </td>
                    <td >@{{item.mission_name}}</td>
                    <td >@{{item.macAddr}}</td>
                    <td >
                        <div :style="item.missionBg" class="mission-status text-center">
                            <div>
                                @{{item.missionStatus}}
                            </div>
                            <div>
                                團隊:
                                <span v-if="item.mission_status != null">
                                    @{{item.mission_status.team}}
                                </span>
                            </div>

                        </div>
                    </td>
                    <td >

                        <div :style="item.macBg" class="mission-status text-center">
                            <div>
                                @{{item.macStatus}}
                            </div>
                            <div>
                                團隊:
                                <span v-if="item.mission_status != null">
                                    @{{item.mission_status.team}}
                                </span>
                            </div>
                        </div>



                    </td>

                    <td >
                        <span v-show="item.sequence==0">
                            <button type="button" name="del" class="btn btn-warning btn-sm" @click="setCheck(index)">
                            開門
                            </button>
                        </span>
                        <span v-show="item.sequence>0">
                            <button type="button" name="del" class="btn btn-warning btn-sm" @click="setCheck(index)">
                            通關
                            </button>
                        </span>

                    </td>
                </tr>
                </tbody>

            </table>
        </div>


        <div v-cloak class="col-md-12 col-xl-4 mt-4">
            <!-- door switch -->
            <div class="card mb-3" >
                <div class="card-header">
                    {{__('escape.manual_switch')}}
                </div>
                <div class="card-body">
                    <button type="button" class="btn btn-outline-primary " @click="openDoor">
                        開所有門
                    </button>
                    <!--<button type="button" class="btn btn-outline-primary float-center" @click="standby">
                        啟動關卡
                    </button>
                    <button type="button" class="btn btn-outline-primary float-right" @click="replay">
                        重新闖關
                    </button>-->
                </div>
            </div>
            <!-- Security devices -->
            <div v-cloak v-if="securityList.length>0" class="card mb-3" >
                <div class="card-header">
                    {{__('escape.security')}}{{__('layout.devices')}}

                    <span v-if="event==7" class="float-right">
                        <button type="button" class="btn btn-outline-danger btn-sm" @click="securityReset">
                        {{__('escape.security_reset')}}
                        </button>
                    </span>
                </div>
                <div class="card-body">
                    <table style="width:100%;">

                        <thead>
                        <tr>
                            <th >{{__('layout.devices')}}</th>
                            <th >{{__('device.device_mac')}}</th>
                            <th>{{__('escape.connection_status')}}</th>
                            <th></th>
                        </tr>

                        </thead>
                        <tbody >
                        <tr v-cloak v-for="(item, index) in securityList">

                            <td >@{{item.device_name}}</td>
                            <td >@{{item.macAddr}}</td>
                            <td >
                                <div :style="item.bg" class="mission-status text-center">
                                    @{{item.status}}
                                </div>
                            </td>
                        </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel">
                    設定通關
                    <label v-if="setEndIndex >0">
                        設定通關
                    </label>
                    <label v-else>
                        開啟大門
                    </label>
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <label v-if="setEndIndex >0">
                    確定要手動設定通過關卡 @{{ setEndIndex }} ?
                </label>
                <label v-else>
                    確定要手動開啟大門 ?
                </label>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-dismiss="modal">{{__('layout.cancel')}}
                </button>
                <button type="button" @Click="setGameCommand()" class="btn btn-primary" >
                    {{__('layout.yes')}}
                </button>
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
        let devices = {!! $devices !!};
        let user = {!! $user !!};
        let app_url = '{{ env('APP_URL') }}';
        let data = {!! json_encode($data) !!};
        let logout_url = '{{url('/logout')}}';

    </script>
    <script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/escape/escapeAdmin.js')}}"></script>
@endsection


