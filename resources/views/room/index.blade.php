@extends('Layout.room')



@section('content')

    @if (count($errors) > 0)
        <div class="alert alert-danger" id="message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

        <div class="room_header mb-3">
            <span class="ml-3">

                <label class="font-weight-bold">我的場地</label>

            </span>
            @if( $cps!=null && $cps->count()>0 && $user->role_id < 3)
                <span class="ml-3">
                    公司
                    <select onchange="location.href='?cp_id='+this.options[this.selectedIndex].value">
                       @foreach ($cps as $item)
                            @if ($item->id == $cp_id)
                                <option value="{{$item->id}}" selected="selected">{{$item->cp_name}}</option>
                            @else
                                <option value="{{$item->id}}">{{$item->cp_name}}</option>
                            @endif
                        @endforeach
                    </select>

                </span>

            @endif
            @if( $users!=null && $cps->count()>0 && $user->role_id < 3)

                <span class="ml-3">
                    用戶
                    <select onchange="location.href='?user_id='+this.options[this.selectedIndex].value">
                       @foreach ($users as $item)
                            @if ($item->id == $user_id)
                                <option value="{{$item->id}}" selected="selected">{{$item->name}}</option>
                            @else
                                <option value="{{$item->id}}">{{$item->name}}</option>
                            @endif
                        @endforeach
                    </select>

                </span>
            @endif

        </div>

        <div class="card rounded-lg">
            <div class="card-body homeBlock">
                <div class="row justify-content-center">
                @if($develops!=null && count($develops)>0)
                        <div class="col-sm-6 col-md-4  col-lg-3 justify-content-center mb-2" onclick="toDevelop()">
                            <div class="card roomBlock">
                                <!-- Product details-->
                                <div class="card-body">
                                    <div class="text-center" >
                                        <!-- Product name-->
                                        <h4 class="fw-bolder">
                                            <label class="text-info font-weight-bold">
                                                個人開發板
                                            </label>
                                        </h4>
                                        <div class="roomMissionBlock">
                                            <img class="item_img" src="{{url('/Images/ESP32.png')}}" alt="ESP32圖標"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                @endif
                @if($modules!=null && count($modules)>0)
                        <div class="col-sm-6 col-md-4  col-lg-3 justify-content-center mb-2" onclick="toModule()">
                            <div class="card roomBlock">
                                <!-- Product details-->
                                <div class="card-body">
                                    <div class="text-center" >
                                        <!-- Product name-->
                                        <h4 class="fw-bolder">
                                            <label class="text-info font-weight-bold">
                                                控制器模組
                                            </label>
                                        </h4>
                                        <div class="roomMissionBlock">
                                            <img class="item_img" src="{{url('/Images/module_controller.png')}}" alt="模組圖標"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @if(count($rooms)>0)
                  @foreach($rooms as $item)
                    <div class="col-sm-6 col-md-4  col-lg-3  justify-content-center mb-2" onclick="toRoom({{$item->id}}, {{count($arr[$item->id])}})">
                        <div class="card roomBlock">

                            <div class="card-body">
                                <div class="text-center" >
                                    <!-- Room name-->
                                    <h4 class="fw-bolder">
                                        <!--<i class="fa fa-bullseye mr-2" aria-hidden="true"></i>-->
                                        <label class="font-weight-bold text-info">
                                            {{$item->room_name}}
                                        </label>
                                    </h4>
                                    <!-- Device or Mission name-->
                                    @if(count($arr[$item->id]) > 0)
                                        <div class="roomMissionBlock">
                                            <!--<label class="text-info font-weight-bold">主題</label>-->
                                            @if($item->work == 'demo')
                                                @foreach($arr[$item->id] as $mission)
                                                    <div class="text-lg-left">
                                                        <i class="fa fa-anchor mr-1 ml-1" aria-hidden="true"></i>
                                                        <label style="color:dodgerblue;font-size:18px;">{{$mission->mission_name}}</label>
                                                    </div>
                                                @endforeach

                                            @else
                                                @foreach($arr[$item->id] as $device)
                                                    <div class="text-lg-left">
                                                        <i class="fa fa-podcast mr-1 ml-1" aria-hidden="true"></i>
                                                        <label style="color:dodgerblue;font-size:18px;">{{$device->device_name}}</label>
                                                    </div>
                                                @endforeach

                                            @endif
                                        </div>
                                    @else
                                        <div class="roomMissionBlock">
                                            <label class="text-danger">尚未設定控制器</label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                  @endforeach
                @else
                   <div class="ml-2 text-danger"><h1>尚未綁定裝置</h1></div>
                @endif
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footerScripts')
    <script>
        let rooms = {!! $rooms !!};
        let arr = {!! json_encode($arr) !!};
        //target => super admin: 選擇用戶， 一般用戶:自己
        let target_id = {!! $user_id !!};
    </script>
    <script src="{{asset('js/room/index.js')}}"></script>
@endsection


