@extends('Layout.room')

@section('content')
    <div class="room_header row">
        <!--<span class="ml-3"> 現在位置：</span>-->
        <div class="col-md-6 col-lg-3">
            <span class="breadcrumb-item">
                <a href="/room/index">{{__('layout.my_room')}}</a>
            </span>
            <span> / </span>
            <span class="breadcrumb-item">
                {{$device->device_name}}
            </span>
        </div>

        <div class="col-md-6 col-lg-3">
            <span class="ml-5 text-left">
            @if($user->role_id < 8 && $devices != null)
                    {{__('layout.select') }}
                    <select onchange="location.href='?device_id='+this.options[this.selectedIndex].value">
                    @foreach ($devices as $item)
                        @if ($item->id == $device_id)
                            <option value="{{$item->id}}" selected="selected">{{$item->device_name}}</option>
                        @else
                            <option value="{{$item->id}}">{{$item->device_name}}</option>
                        @endif
                    @endforeach
                </select>
                @endif
            </span>
        </div>

        <div class="col-md-6 col-lg-4">
            <div v-cloak v-if="message.length>0" class="text-center text-light bg-info">
                @{{ message }}
            </div>
        </div>

        <div class="col-md-6 col-lg-2 ">
            <!--<input v-if="isDemo==false" type="button" class="btn btn-success btn-sm" @click="isDemo=true;" value="展示模式" />
            <input v-cloak v-else type="button" class="btn btn-primary btn-sm" @click="isDemo=false;" value="一般模式" />-->
            <span class="float-right">
                <input type="button" class="btn btn-warning btn-sm mr-2" @click="toViewControl()" value="影像監控" />
                <!--<input type="button" class="btn btn-info btn-sm mr-2" @click="toHistory()" value="GPS歷史紀錄" />-->
            </span>
       </div>
    </div>

    @if (count($errors) > 0)
        <div class="alert alert-danger" id="message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div v-cloak v-if="alertMessage.length>0" class="alert alert-danger">
        @{{ alertMessage }}
    </div>

    <!--<div class="imageBlock">
        <img  class="testBlock" id="usv_direction" src="{{url('/Images/boat/boat8.png')}}">
    </div>-->

    <div class="row main-content mt-1">
        <div class="col-lg-9 mb-2">
            <div id="map" style="height: 500px;width: 100%;"></div>
        </div>
        <div v-cloak class="col-lg-3 text-center">
            <!-- 切換頁籤 -->
            <div id="timeselector" class="btn-group btn-group-toggle mb-1" data-toggle="buttons">
                <label class="btn btn-secondary active">
                    <input type="radio" name="options" id="1" autocomplete="off" checked>上報
                </label>
                <label class="btn btn-secondary">
                    <input type="radio" name="options" id="2" autocomplete="off" checked>歷史紀錄
                </label>
                <label class="btn btn-secondary ">
                    <input type="radio" name="options" id="3" autocomplete="off" > 中心設定
                </label>
                @if($user->role_id <= 7 || ($user->room_limit != null && ($user->room_limit[$room->id] == 8)) )
                <label class="btn btn-secondary">
                    <input type="radio" name="options" id="4" autocomplete="off" checked>危險區域設定
                </label>
                @endif

            </div>
            <!-- 控制按鍵 -->

            <div  class="mapBlock row" >

                <div class="mb-1">
                    <button v-show="tab==1 || tab==3" type="button" class="btn btn-outline-danger btn-sm" onclick="clearReports()">
                        清除上報點
                    </button>
                    <button v-show="tab==3" type="button" class="btn btn-outline-danger btn-sm" @click="clearLine()">
                        清除量測點
                    </button>
                    <span v-show="tab==1" class="ml-2 mt-2">
                            最新上報排第一筆
                        </span>
                </div>
                <div v-show="tab==4" class="mb-1">
                    <button type="button" class="btn btn-outline-primary btn-sm" @click="createBlock()">
                        劃新區塊
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" @click="clearBlockMarkers()">
                       清區塊定位點
                   </button>
                   <button type="button" class="btn btn-outline-danger btn-sm" @click="clearNewBlock()">
                       清新區塊
                   </button>
               </div>

           </div>
            <!-- Report -->
            <div v-show="tab==1" class="reportListBlock row">
                <div>
                    <div class="input-group mb-2">
                        <!-- Search list -->
                        <table id="table_test" class="text-center" style="width:100%;">
                            <thead>
                            <tr>
                                <th width="10%">NO</th>
                                <th width="25%">緯度</th>
                                <th width="25%">經度</th>
                                <th width="40%">時間</th>
                            </tr>

                            </thead>
                            <tbody >
                            <tr v-cloak v-for="(item, index) in searchList" @click="showInfo(index)" :style="item.data" @mouseover="highlight(index);" @mouseout="restoreColor(index); ">
                                <td >@{{ checkList.indexOf(item.macAddr)+1 }}</td>
                                <td style="font-size:8px">
                                    @{{ searchList[index].lat }}
                                </td>
                                <td style="font-size:8px">
                                    @{{ searchList[index].lng }}
                                </td>
                                <td style="font-size:8px">
                                    @{{ searchList[index].recv }}
                                </td>

                            </tr>

                            </tbody>

                        </table>
                    </div>
                </div>

            </div>
           <!--1. 圍籬座標-->
            <div v-show="tab==3">
                <!-- Home & Center -->
                <div v-show="isMeasure" class="mapBlock row">
                    <div>
                        <div class="mb-1">
                            <form method="post" action="editSetting" id="editSetting">
                                <input type="hidden" name="_method" value="put" />
                                <input type="hidden" name="id" v-model="setting.id" />
                                <input type="hidden" name="app_id" v-model="setting.app_id"/>
                                <input type="hidden" name="device_id" v-model="setting.device_id"/>
                                <input type="hidden" name="field" v-model="setting.field"/>
                                <input type="hidden" name="set" v-model="setting.set"/>
                                {{csrf_field()}}
                            </form>
                        </div>
                    </div>
                    <div>
                        <div class="input-group mb-2">
                            <table class="text-left" style="width:100%;">
                                <thead>
                                <tr>
                                    <th width="14%"></th>
                                    <th width="43%">緯度</th>
                                    <th width="43%">經度</th>
                                </tr>

                                </thead>
                                <tbody >

                                <!-- Center Start-->
                                <tr >
                                    <td rowspan="3">
                                        中心選單
                                    </td>
                                    <td v-if="setting.set.length>0" colspan="2">

                                        <select v-model="centerIndex" class="form-control" @change="changeSetting($event)">
                                            <option v-for="(item,index) in setting.set" :value="index" :key="item.name">
                                                @{{ item.name }}
                                            </option>
                                        </select>
                                    </td>
                                    <td v-if="setting.set.length==0" colspan="2">
                                    </td>
                                </tr>
                                <tr >
                                    <td >
                                        <input type="text" style="font-size:12px" class="form-control" v-model="center.lat" aria-describedby="basic-addon1" maxlength="10" size="10">
                                    </td>
                                    <td >
                                        <input type="text" style="font-size:12px" class="form-control" v-model="center.lng" aria-describedby="basic-addon1" maxlength="10" size="10">
                                    </td>
                                </tr>
                                <tr >
                                    <td colspan="2" >
                                            <span class="form-control">
                                                <button class="btn btn-success btn-sm" @click="addCenter()">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                                <button v-if="setting.set.length>0" type="button" class="btn btn-primary btn-sm" @click="setCenter()">
                                                    <i class="fa fa-pen"></i>
                                                </button>
                                                <button v-if="setting.set.length>0" type="button" class="btn btn-danger btn-sm" @click="delCenter()">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </span>
                                    </td>
                                </tr>
                                <!-- Center End-->

                                </tbody>

                            </table>
                        </div>
                    </div>

                </div>

                <!-- 量測距離 -->
                <div v-if="isMeasure" class="mapBlock row">
                    <div>
                        <h6> 量測距離 </h6>
                    </div>


                    <div class="mt-1">

                        <div class="input-group mb-2"><table class="text-center" style="width:100%;">

                                <thead>
                                <tr>
                                    <th width="14%">定位</th>
                                    <th width="43%">緯度</th>
                                    <th width="43%">經度</th>
                                </tr>

                                </thead>
                                <tbody >
                                <tr v-cloak v-for="(item, index) in measureList">

                                    <td >@{{ index+1 }}</td>
                                    <td >
                                        <input type="text" class="form-control" v-model="item.lat" aria-describedby="basic-addon1" maxlength="10" size="10" >
                                    </td>
                                    <td >
                                        <input type="text" class="form-control" v-model="item.lng" aria-describedby="basic-addon1" maxlength="10" size="10" >
                                    </td>
                                </tr>
                                <tr>

                                    <td >距離</td>
                                    <td colspan="2">
                                        <input type="text"  class="form-control" v-model="distance"  maxlength="20" size="20" disabled>
                                    </td>
                                </tr>

                                </tbody>

                            </table>

                        </div>
                    </div>
                </div>
            </div>


            <!-- 危險區塊 -->
            <div v-show="tab==4" class="mapBlock row">
                <div>
                    <div class="input-group mb-2">
                        <form method="post" action="editSetting" id="editDangerSetting">
                            <input type="hidden" name="_method" value="put" />
                            <input type="hidden" name="id" v-model="setting.id" />
                            <input type="hidden" name="room_id" v-model="setting.room_id"/>
                            <input type="hidden" name="field" v-model="setting.field"/>
                            <input type="hidden" name="name" v-model="setting.name"/>
                            <input type="hidden" name="setString" v-model="setString"/>
                            {{csrf_field()}}
                        </form>
                        <table class="text-left" style="width:100%;">
                            <thead>
                            <tr>
                                <th width="17%"></th>
                                <th width="23%"></th>
                                <th width="60%"></th>
                            </tr>

                            </thead>
                            <tbody >

                            <!-- Danger Start-->
                            <tr >
                                <td rowspan="3">
                                    危險區域設定
                                </td>
                                <td v-if="setting.id>0 && dangerList.length>0" colspan="2">

                                    <select v-model="dangerIndex" class="form-control" @change="changeDangers($event)">
                                        <option v-for="(item,index) in dangerList" :value="index" :key="item.name">
                                            @{{ item.name }}
                                        </option>
                                    </select>
                                </td>
                                <td v-if="setting.set.length==0" colspan="2">
                                </td>
                            </tr>
                            <tr v-show="isDangerSetting">
                                <td >
                                    <input type="text" class="form-control" value="區域名稱" disabled>
                                </td>
                                <td >
                                    <input type="text" class="form-control" name="name" v-model="setting.name" aria-describedby="basic-addon1">
                                </td>
                            </tr>
                            <tr >
                                <td colspan="2" >
                                    <span class="form-control">
                                        <button  v-show="isDangerSetting" type="button" class="btn btn-secondary btn-sm" @click="cancel();">
                                             取消
                                        </button>
                                        <button  v-show="isDangerSetting" type="button" class="btn btn-outline-primary btn-sm" @click="saveDanger()">
                                             儲存設定
                                        </button>
                                        <button v-if="dangerList.length < dangerBlockMax" v-show="!isDangerSetting" class="btn btn-success btn-sm" @click="addDanger()">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                        <button v-show="!isDangerSetting && dangerList.length>0" type="button" class="btn btn-primary btn-sm" @click="setDanger()">
                                            <i class="fa fa-pen"></i>
                                        </button>
                                        <button  v-show="!isDangerSetting && dangerList.length>0" type="button" class="btn btn-danger btn-sm" @click="delDanger()">
                                            <i class="fa fa-trash"></i>
                                        </button>

                                    </span>
                                </td>
                            </tr>
                            <!-- Danger End-->

                            </tbody>

                        </table>
                    </div>
                </div>
                <div>
                    <h5>
                        <span>區塊座標</span>
                    </h5>
                    <table class="text-center" style="width:100%;">

                        <thead>
                        <tr>
                            <th width="14%">定位</th>
                            <th width="43%">緯度</th>
                            <th width="43%">經度</th>
                        </tr>

                        </thead>
                        <tbody >
                        <tr v-cloak v-for="(item, index) in list">

                            <td >@{{index+1}}</td>
                            <td >
                                <input type="number" step="any" style="font-size:12px" class="form-control" v-model="list[index]['lat']" aria-describedby="basic-addon1" maxlength="10" size="10" disabled>
                            </td>
                            <td >
                                <input type="number" step="any" style="font-size:12px" class="form-control" v-model="list[index]['lng']" aria-describedby="basic-addon1" maxlength="10" size="10" disabled>
                            </td>
                        </tr>
                        </tbody>

                    </table>
                </div>
                <div v-cloak v-if="distanceList.length>0" class="mt-1">
                    <h5>
                        <span>定位點間距</span>
                        <span class="float-right">
                            <button type="button" class="btn btn-warning btn-sm" @click="change(1)">座標</button>
                        </span>
                    </h5>
                    <table class="text-center" style="width:100%;">

                        <thead>
                        <tr >
                            <th>定位</th>
                            <th>距離(公尺)</th>
                        </tr>

                        </thead>
                        <tbody >
                        <tr v-cloak v-for="(item2, index2) in distanceList">

                            <td v-if="index2 < (distanceList.length-1)" >@{{index2+1}} ~ @{{index2+2}}</td>
                            <td v-else >@{{index2+1}} ~ 1</td>
                            <td >
                                <input type="text" step="any" style="font-size:12px" class="form-control" v-model="distanceList[index2]" aria-describedby="basic-addon1" maxlength="10" size="10" disabled>
                            </td>
                        </tr>
                        </tbody>

                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <span v-if="centerTab < 3">
                        <h4 class="modal-title" id="myModalLabel">編輯中心點</h4>
                    </span>
                        <span v-if="centerTab == 3 || centerTab == 6">
                        <h4 class="modal-title" id="myModalLabel">{{__('layout.waring')}}!</h4>
                    </span>
                        <span v-if="centerTab == 4 || centerTab == 5">
                        <h4 class="modal-title" id="myModalLabel">編輯Home點</h4>
                    </span>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div v-if="centerTab < 3">
                            <form>
                                <div class="form-row align-items-center">
                                    <div class="col-6 input-group mb-3">
                                        <div class="input-group-append">
                                            <span class="input-group-text" >名稱</span>
                                        </div>
                                        <input type="text" class="form-control" v-model="center.name" >
                                    </div>
                                    <div class="col-6 input-group mb-3">

                                        <div class="input-group-append">
                                            <span class="input-group-text" >放大</span>
                                        </div>
                                        <input type="number" class="form-control" v-model="center.room" placeholder="1~22" min="1" max="22">
                                    </div>
                                    <div class="col-6 input-group mb-3">
                                        <div class="input-group-append">
                                            <span class="input-group-text" >緯度</span>
                                        </div>
                                        <input type="text" class="form-control" v-model="center.lat" >
                                    </div>
                                    <div class="col-6 input-group mb-3">

                                        <div class="input-group-append">
                                            <span class="input-group-text" >經度</span>
                                        </div>
                                        <input type="text" class="form-control" v-model="center.lng">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div v-if="centerTab == 3">
                            {{__('layout.delete_confirm')}} [@{{center.name}}] 中心點?
                            <form method="post" action="delSetting" id="delSetting">
                                <input type="hidden" name="_method" value="delete" />
                                <input type="hidden" name="id" v-model="setting.id" />
                                {{csrf_field()}}
                            </form>
                        </div>
                        <div v-if="centerTab > 3 && centerTab < 6">
                            <form>
                                <div class="form-row align-items-center">
                                    <div class="col-12 input-group mb-3">
                                        <div class="input-group-append">
                                            <span class="input-group-text" >名稱</span>
                                        </div>
                                        <input type="text" class="form-control" v-model="home.name" >
                                    </div>
                                    <div class="col-6 input-group mb-3">
                                        <div class="input-group-append">
                                            <span class="input-group-text" >緯度</span>
                                        </div>
                                        <input type="text" class="form-control" v-model="home.lat" >
                                    </div>
                                    <div class="col-6 input-group mb-3">

                                        <div class="input-group-append">
                                            <span class="input-group-text" >經度</span>
                                        </div>
                                        <input type="text" class="form-control" v-model="home.lng">
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div v-if="centerTab == 6">
                            {{__('layout.delete_confirm')}} [@{{home.name}}] Home點?
                            <form method="post" action="delSetting" id="delHomeSetting">
                                <input type="hidden" name="_method" value="delete" />
                                <input type="hidden" name="id" v-model="home_setting.id" />
                                {{csrf_field()}}
                            </form>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal">{{__('layout.cancel')}}
                        </button>
                        <button v-if="centerTab < 3" type="button" @click="toAddSetting()" class="btn btn-primary">
                            {{__('layout.yes')}}
                        </button>
                        <button v-if="centerTab == 3" type="button" @click="toDelSetting()" class="btn btn-danger">
                            {{__('layout.yes')}}
                        </button>
                        <button v-if="centerTab < 6 && centerTab > 3" type="button" @click="toAddHomeSetting()" class="btn btn-primary">
                            {{__('layout.yes')}}
                        </button>
                        <button v-if="centerTab == 6" type="button" @click="toDelHomeSetting()" class="btn btn-danger">
                            {{__('layout.yes')}}
                        </button>

                    </div>
                </div>
            </div>
        </div>
        <!-- Modal2 -->
        <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">{{__('layout.waring')}}!</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        {{__('layout.delete_confirm')}} @{{ setting.name }}?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal">{{__('layout.cancel')}}
                        </button>
                        <form method="post" action="delSetting" id="delDanger">
                            <input type="hidden" name="_method" value="delete" />
                            <input type="hidden" name="id" v-model="setting.id" />
                            {{csrf_field()}}
                            <button type="button" @click="toDelDanger();" class="btn btn-danger">
                                {{__('layout.yes')}}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
            <script>
                let apps = {!! json_encode($apps) !!};
                let device = {!!json_encode($device) !!};
                let devices = {!!json_encode($devices) !!};
                let user = {!! json_encode($user) !!};
                let statusObj = {!! json_encode($status) !!};
                let point_url = "{!! url('/Images/point.png')!!}";
                let diamond_url = "{!! url('/Images/diamond.png')!!}";
                let app_url = '{{ env('APP_URL') }}';
                let api_url = '{{ env('API_URL') }}';
                let center_index = {!! $center_index !!};
                let order = '{!! $order !!}';
                let room_id =  {!! $room->id !!};
                let dangers = {!! json_encode($dangers) !!};
            @if($setting == null)
                let setting = null;
            @else
                let setting = {!! $setting !!};
            @endif

            @if($user->role_id <= 7 || ($user->room_limit != null && ($user->room_limit[$room->id] == 8)) )
                let isAdmin = true;
            @else
                let isAdmin = false;
            @endif
                let token = '{{$user->remember_token}}';
                //alert(cmd);
            </script>
            <script async defer
                    src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP_KEY')}}">
            </script>
            <script src="{{asset('vender/jQueryRotate/jQueryRotate.2.1.js')}}" crossorigin="anonymous"></script>
            <script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script>
            <script src="{{asset('js/option/tools.js')}}"></script>
            <script src="{{asset('js/room/usv/work_alg.js')}}"></script>
            <script src="{{asset('js/room/usv/thruster.js')}}"></script>

@endsection
