@extends('Layout.room')

@section('content')
    <div class="room_header row mt-1">
        <!--<span class="ml-3"> 現在位置：</span>-->

        @if( Session::has('room'))
        <div class="col-md-6 col-lg-2">

            <span class="breadcrumb-item">
                <a href="/room/index">{{__('layout.my_room')}}</a>
            </span>
            <span> / </span>

            <span class="breadcrumb-item">
                {{$device->device_name}}
            </span>
        </div>
        @endif
        <div class="col-md-6 col-lg-4">

            <ul class="nav nav-tabs" id="myTab">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#0">地圖</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#1">影像</a>
                </li>

            </ul>




        </div>

        <div class="col-md-6 col-lg-6 ">
            @if($user->role_id < 3)
                <span v-cloak>
                    <!-- For test of Super Admin-->

                    </span>

            @endif


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

    <div v-show="isShow==0" class="imageBlock">
        <img  class="testBlock" id="usv_direction" src="{{url('/Images/boat/boat8.png')}}">
        <br><br>
        <span v-cloak class="font-weight-bold"> 航向角@{{ status[3]['key3'] }}度</span>
    </div>

    <div class="row main-content mt-1">
        <div class="col-lg-9 mb-2">
            <div v-show="isShow==0" id="map" style="height: 550px;width: 100%;"></div>
            <div v-if="isShow==1" class="farmWebRTCBlock2 row mb-1">
                <iframe  id="iframe1" src="{{url('/room/webrtc/'.$device->id.'?size=large')}}" ></iframe>
            </div>
        </div>
        <div v-cloak class="col-lg-3 text-center">
            <!-- 切換頁籤 -->
            <div id="timeselector" class="btn-group btn-group-toggle mb-1" data-toggle="buttons">
                <label class="btn btn-secondary active">
                    <input type="radio" name="options" id="1" autocomplete="off" >巡航
                </label>
                <label class="btn btn-secondary">
                    <input type="radio" name="options" id="2" autocomplete="off" checked>航訊
                </label>
                @if($isShowReport)
                    <label class="btn btn-secondary">
                        <input type="radio" name="options" id="3" autocomplete="off">上報
                    </label>
                @endif
                @if($mechanism != null)
                    <label class="btn btn-secondary">
                        <input type="radio" name="options" id="4" autocomplete="off">機構
                    </label>
                @endif
                @if($device->support ==1)
                <label class="btn btn-secondary">
                    <input type="radio" name="options" id="5" autocomplete="off">標的
                </label>
                @endif
                <label class="btn btn-secondary">
                    <input type="radio" name="options" id="6" autocomplete="off">功能
                </label>
            </div>
            <!-- 控制按鍵 場域成員無使用權限 -->
            @if($user->role_id != 9 )
                <div v-show="tab==1" class="mapBlock row" >
                    <div v-show="isMeasure==false" class="mb-1 row">
                        <div v-if="isDemo" class="col-4">
                            <button type="button" class="btn btn-success btn-block" @click="demo()" :disabled="isRun">
                                巡航展示
                            </button>
                        </div>
                        <div v-else class="col-4">
                            <button type="button" class="btn btn-primary btn-block" @click="fence()" :disabled="isRun">
                                開始巡航
                            </button>
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-danger btn-block" onclick="stop()" :disabled="!isRun">
                                停止巡航
                            </button>
                        </div>

                        <div class="col-4">
                            <button type="button" class="btn btn-primary btn-block" onclick="toHome()" :disabled="isRun">
                                回 Home
                            </button>
                        </div>
                    </div>
                    <div v-show="isBlock" class="mb-1 row">
                        <div class="col-4">
                            <button type="button" class="btn btn-outline-danger btn-block" @click="clearBlockMarkers()">
                                清定位點
                            </button>
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-outline-danger btn-block" @click="clearNewBlock()">
                                清新區塊
                            </button>
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-outline-danger btn-block" onclick="clearReports()">
                                清上報點
                            </button>
                        </div>
                    </div>
                    <div v-show="isBlock" class="mb-1 row">
                        <div class="col-4">
                            <button type="button" class="btn btn-outline-primary btn-block" @click="createBlock()">
                                劃新區塊
                            </button>
                        </div>
                        <div class="col-4">
                            <button  type="button" class="btn btn-outline-primary btn-block" @click="fenceOutline()" :disabled="isDemo">
                                圍籬展示
                            </button>
                        </div>
                        <div class="col-4">
                            <button type="button" class="btn btn-outline-primary btn-block" @click="measureTool()">
                                量測距離
                            </button>
                        </div>
                    </div>

                    <div v-show="isBlock" class="mb-1">
                        <button type="button" class="btn btn-primary btn-sm" @click="setParameter()">
                            設定巡航間隔距離 <i class="fa fa-pen"></i>
                        </button>
                        <input type="number"  v-model="parameter.set.interval" placeholder="1~10" min="1" max="10" />
                        公尺
                    </div>
                    <div v-cloak v-show="isBlock" class="mb-1 row">
                        <div class="col-6">
                            <button v-if="isOpenWindow==true" type="button" class="btn btn-secondary btn-block" @click="isOpenWindow=false;">
                                隱藏上報點訊息
                            </button>
                            <button v-else type="button" class="btn btn-outline-secondary btn-block" @click="isOpenWindow=true;">
                                顯示上報點訊息
                            </button>
                        </div>
                        <div class="col-6">
                            <button v-if="isDemo==false" type="button" class="btn btn-success btn-block" @click="isDemo=true;">
                                切換為展示模式
                            </button>
                            <button v-else type="button" class="btn btn-primary btn-block" @@click="isDemo=false;">
                                切換為一般模式
                            </button>
                        </div>

                    </div>

                    <div v-show="isMeasure==true">
                        <button type="button" class="btn btn-outline-danger btn-sm" @click="clearLine()">
                            清除
                        </button>
                        <button type="button" class="btn btn-outline-dark btn-sm" @click="isMeasure=false;isBlock=true;clearLine();">
                            返回巡航操作
                        </button>
                    </div>


                    <!--<div class="mb-1">
                        <button type="button" class="btn btn-outline-primary btn-sm" @click="createOldBlock()">
                            顯示舊區塊
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearOldBlock()">
                            清除舊區塊
                        </button>
                    </div>-->
                </div>
            @endif
            <!--<div v-show="tab==1">
                <div v-show="isMeasure==false" class="mapBlock row">
                    <button v-if="!isList" type="button" class="btn btn-dark btn-sm" @click="isList=true;">
                        切換為電子圍籬座標
                    </button>
                    <button v-if="isList" type="button" class="btn btn-dark btn-sm" @click="isList=false;">
                        切換為中心/Home選單
                    </button>
                </div>
            </div>-->

            <div v-show="tab==1">
                <!-- Home & Center -->
                <div v-show="!isMeasure" class="mapBlock row">
                    <div>
                        <div class="mb-1">
                            <form method="post" action="editSetting" id="editSetting">
                                <input type="hidden" name="_method" value="put" />
                                <input type="hidden" name="id" v-model="setting.id" />
                                <input type="hidden" name="device_id" value="{{$device->id}}"/>
                                <input type="hidden" name="field" v-model="setting.field"/>
                                <input type="hidden" name="set" v-model="setting.set"/>
                                {{csrf_field()}}
                            </form>
                            <form method="post" action="editSetting" id="editHomeSetting">
                                <input type="hidden" name="_method" value="put" />
                                <input type="hidden" name="id" v-model="home_setting.id" />
                                <input type="hidden" name="device_id" value="{{$device->id}}"/>
                                <input type="hidden" name="field" v-model="home_setting.field"/>
                                <input type="hidden" name="set" v-model="home_setting.set"/>
                                {{csrf_field()}}
                            </form>
                            <form method="post" action="editSetting" id="editParamSetting">
                                <input type="hidden" name="_method" value="put" />
                                <input type="hidden" name="id" v-model="parameter.id" />
                                <input type="hidden" name="device_id" value="{{$device->id}}"/>
                                <input type="hidden" name="field" v-model="parameter.field"/>
                                <input type="hidden" name="set" v-model="parameter.set"/>
                                {{csrf_field()}}
                            </form>
                        </div>
                    </div>
                    <div>
                        <div class="input-group mb-2">
                            <table class="text-left" style="width:100%;">
                                <thead>
                                <tr>
                                    <th width="14%">定位</th>
                                    <th width="43%">緯度</th>
                                    <th width="43%">經度</th>
                                </tr>
                                <tr >
                                    <td >座標</td>
                                    <td >
                                        <input type="text" style="font-size:12px" class="form-control" v-model="list[0]['lat']" aria-describedby="basic-addon1" maxlength="10" size="10" disabled>
                                    </td>
                                    <td >
                                        <input type="text" style="font-size:12px" class="form-control" v-model="list[0]['lng']" aria-describedby="basic-addon1" maxlength="10" size="10" disabled>
                                    </td>
                                </tr>

                                </thead>
                                <tbody >
                                <!-- Home Start-->

                                <!--<tr >
                                    <td rowspan="3">
                                        Home選單
                                    </td>
                                    <td v-if="home_setting.set.length>0" colspan="2">

                                        <select v-model="homeIndex" class="form-control" @change="changeHomeSetting($event)">
                                            <option v-for="(item1,index) in home_setting.set" :value="index" :key="item1.name">
                                                @{{ item1.name }}
                                            </option>
                                        </select>
                                    </td>
                                    <td v-if="home_setting.set.length==0" colspan="2">
                                    </td>
                                </tr>
                                <tr >
                                    <td >
                                        <input type="text" style="font-size:12px" class="form-control" v-model="home.lat" aria-describedby="basic-addon1" maxlength="10" size="10" >
                                    </td>
                                    <td >
                                        <input type="text" style="font-size:12px" class="form-control" v-model="home.lng" aria-describedby="basic-addon1" maxlength="10" size="10" >
                                    </td>
                                </tr>-->
                                <tr >
                                    <td colspan="3" >
                                        <div class="mt-1" >
                                            <span >
                                                <!--<button class="btn btn-success btn-sm" @click="addHome()">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                                <button v-if="home_setting.set.length>0" type="button" class="btn btn-primary btn-sm" @click="setHome()">
                                                    <i class="fa fa-pen"></i>
                                                </button>
                                                <button v-if="home_setting.set.length>0" type="button" class="btn btn-danger btn-sm" @click="delHome()">
                                                    <i class="fa fa-trash"></i>
                                                </button>-->
                                            </span>
                                            <span class="float-right">
                                                <button v-if="!isCheckHome" type="button" class="btn btn-outline-primary btn-sm" @click="setHomeToNode()">
                                                    設HOME點
                                                </button>
                                                <button v-else type="button" class="btn btn-primary btn-sm" disabled>
                                                    設HOME點
                                                </button>
                                            </span>

                                        </div>
                                    </td>
                                </tr>

                                <!-- Home End-->


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
                                            <span>
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
                <!-- 電子圍籬 -->
                <div v-show="!isMeasure" class="mapBlock row">
                    <div v-show="!isShowDistance">
                        <div>
                            <span>電子圍籬座標</span>
                            <span class="float-right">
                                <button type="button" class="btn btn-warning btn-sm" @click="change(2)">距離</button>
                            </span>
                        </div>
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
                    <div v-show="isShowDistance">
                        <h5>
                            <span>電子圍籬距離</span>
                            <span class="float-right">
                            <button type="button" class="btn btn-warning btn-sm" @click="change(1)">座標</button>
                        </span>
                        </h5>
                        <table class="text-center" style="width:100%;">

                            <thead>
                            <tr>
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
                <!-- 量測距離 -->
                <div v-if="isMeasure" class="mapBlock row">
                    <div>
                        <h6> 量測距離 </h6>
                        <!--<div class="mb-1">
                            <button type="button" class="btn btn-outline-success btn-sm" @click="setReport()">
                                設定R1定位
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" @click="setReport2()">
                                設定R2定位
                            </button>
                        </div>-->
                        <div>
                            <!--<button type="button" class="btn btn-outline-primary btn-sm" @click="betweenDistance()">
                                計算距離
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" @click="betweenLine()">
                                兩點畫線
                            </button>-->
                        </div>
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
            <!--2. 無人船訊息-->
            <div v-show="tab==2">
                <!--2. 無人船訊息-->
                <div class="mapBlock row">
                    <div class="input-group mb-2">
                        <!-- 超音波 -->
                        <table class="text-center" border="0" style="font-size:1em;width:100%;">
                            <!-- 前距 -->
                            @if(env('HAS_ULTRASOUND')==true)
                                <tr>
                                    <td colspan="2"></td>

                                    <td>@{{ label[1].key1 }}</td>
                                    <td >
                                    <span v-if="status[1].key1>0 && status[1].key1<800">
                                        <button type="button" class="btn btn-danger btn-sm">障礙物</button>
                                    </span>
                                        <span v-else>
                                        <button type="button" class="btn btn-success btn-sm">良好</button>
                                    </span>
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            @endif
                        <!-- boat image -->
                            @if(env('HAS_ULTRASOUND')==true)
                                <tr>
                                    <!-- 左距 -->
                                    <td>@{{ label[1].key3 }}</td>
                                    <td width="20%">
                                        <!-- left_status -->
                                        <!--<span v-if="status[1].key3>0 && status[1].key3<100">
                                            <button type="button" class="btn btn-danger btn-sm">@{{status[1].key3}} 公分</button>
                                        </span>-->
                                        <span v-if="status[1].key3>0 && status[1].key3<800">
                                            <button type="button" class="btn btn-danger btn-sm">障礙物</button>
                                        </span>
                                        <span v-else>
                                            <button type="button" class="btn btn-success btn-sm">良好</button>
                                        </span>
                                    </td>

                                    <td colspan="2" rowspan="5" >
                                        <!-- Boat image-->
                                        <div class="mt-1 mb-1">
                                            <img id="img_fan" src="{{url('/Images/myboat.png')}}" height="150px" width="100px">
                                        </div>

                                    </td>
                                    <!-- 右距 -->
                                    <td>@{{ label[1].key1 }}</td>
                                    <td >
                                        <!--<span v-if="status[1].key4>0 && status[1].key4<100">
                                            <button type="button" class="btn btn-danger btn-sm">@{{status[1].key4}} 公分</button>
                                        </span>-->
                                        <span v-if="status[1].key4>0 && status[1].key4<800">
                                            <button type="button" class="btn btn-danger btn-sm">障礙物</button>
                                        </span>
                                        <span v-else>
                                            <button type="button" class="btn btn-success btn-sm">良好</button>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2">左馬達</td>
                                    <td colspan="2">右馬達</td>
                                </tr>
                            @else
                                <!--<tr>
                                    <td colspan="2">@{{ label[2].key6 }}</td>
                                    <td colspan="2">@{{ label[2].key7 }}</td>
                                    <td colspan="2">@{{ label[2].key8 }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">@{{ status[2].key6 }}</td>
                                    <td colspan="2">@{{ status[2].key7 }}</td>
                                    <td colspan="2">@{{ status[2].key8 }}</td>
                                </tr>-->

                                <tr>
                                    <td colspan="2">左馬達</td>
                                    <td colspan="2" rowspan="5" >
                                        <!-- Boat image-->
                                        <div class="mt-1 mb-1">
                                            <img id="img_fan" src="{{url('/Images/boat/boat8.png')}}" height="150px" width="100px">
                                        </div>

                                    </td>
                                    <td colspan="2">右馬達</td>
                                </tr>
                            @endif


                            <tr>
                                <td >動力</td>
                                <td > @{{status[2].key1}} %</td>
                                <td >動力</td>
                                <td > @{{status[2].key3}} %</td>
                            </tr>
                            <!--<tr>
                                <td >電流</td>
                                <td > @{{status[2].key2}} A</td>
                                <td >電流</td>
                                <td > @{{status[2].key4}} A</td>
                            </tr>
                            <tr>
                                <td >溫度</td>
                                <td > @{{status[2].key3}} 度</td>
                                <td >溫度</td>
                                <td > @{{status[2].key7}} 度</td>
                            </tr>-->
                            <tr>
                                <td >電力</td>
                                <td > @{{status[1].key3}} V</td>
                                <!-- 後距 -->
                                @if(env('HAS_ULTRASOUND')==true)
                                    <td>@{{ label[1].key2 }}</td>
                                    <td >
                                        <!--<span v-if="status[1].key2>0 && status[1].key2<100">
                                            <button type="button" class="btn btn-danger btn-sm">@{{status[1].key2}} 公分</button>
                                        </span>-->
                                        <span v-if="status[1].key2>0 && status[1].key2<800">
                                        <button type="button" class="btn btn-danger btn-sm">障礙物</button>
                                    </span>
                                        <span v-else>
                                        <button type="button" class="btn btn-success btn-sm">良好</button>
                                    </span>
                                    </td>
                                @endif
                                <td >電力</td>
                                <td > @{{status[1].key4}} V</td>
                            </tr>

                        </table>
                        <br>
                        <br>
                        <!-- 船艙溫度 & 濕度 -->
                        <table class="text-center" style="width:100%;">
                            <tr>
                                <th width="30%"><span class="form-control" >航向角</span></th>
                                <th width="70%"><span class="form-control" >@{{ status[3]['key3'] }}度</span></th>
                            </tr>
                            <tr>
                                <th width="30%"><span class="form-control" >緯度</span></th>
                                <th width="70%"><span class="form-control" >@{{ status[1]['lat'] }}</span></th>
                            </tr>
                            <tr>
                                <th width="30%"><span class="form-control" >經度</span></th>
                                <th width="70%"><span class="form-control" >@{{ status[1]['lng'] }}</span></th>
                            </tr>





                        </table>
                            <!-- 船艙溫度 & 濕度 -->
                            <!--<thead>

                              <tr>

                                   <th width="35%">@{{ label[1].key5 }}</th>
                                   <th width="30%"></th>
                                   <th width="35%">@{{ label[1].key6 }}</th>
                               </tr>

                            </thead>
                            <tbody >-->
                                <!-- 船艙溫度 & 濕度 -->
                                <!--<tr >

                                    <td >
                                        <span class="form-control" style="font-size:12px"> @{{ status[1].key5 }} 度</span>
                                    </td>
                                    <td ></td>
                                    <td >
                                        <span class="form-control" style="font-size:12px"> @{{ status[1].key6 }} %</span>
                                    </td>
                                </tr>-->
                                <!--設定低電力 觸發警告 -->
                                <!--<tr >
                                    <td >
                                        <input type="number"  v-model="parameter.set.trigger1" placeholder="20~23" min="10" max="23" />V
                                    </td>
                                    <td >
                                        <button type="button" class="btn btn-primary btn-sm" @click="setParameter()">
                                            設定低電力 觸發警告<i class="fa fa-pen"></i>
                                        </button>
                                    </td>
                                    <td >
                                        <input type="number"  v-model="parameter.set.trigger2" placeholder="20~23" min="20" max="23" />V
                                    </td>
                                </tr
                            </tbody>>

                        </table>-->


                    </div>
                </div>
            </div>
            <!--  航向訊息-->
            <div v-show="tab==2">
                <!--3. 航向訊息-->
                <!--<div class="mapBlock row">
                    <div class="input-group mb-2">
                        <h5>
                            <span>航向訊息</span>
                        </h5>

                        <table class="text-center" style="width:100%; font-size: 1em;">
                            <tbody >
                            <tr v-for="(item, key, index) in label[3]">
                                <td >
                                    @{{ item }}
                                </td>
                                <td>
                                    <span class="form-control" > @{{ status[3][key] }} </span>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>-->

            </div>
            <!--  上報 -->
            <div v-if="tab==3 && appList.length>5">
                <div class="mapBlock row" >
                    <div v-if="target.sequence>5" v-for="(target,index) in appList" class="input-group mb-2">
                        <div>
                            <span >
                                <span>@{{ target.name }}</span>
                            </span>
                            <span class="ml-3">
                                <a :href="'/node/apps/reports?app_id='+target.id+'&macAddr='+deviceMac">紀錄連結</a>
                            </span>
                        </div>

                        <table class="text-center" style="width:100%; font-size: 1em;">

                            <tr v-for="(item, key, index) in label[target.sequence]">
                                <td >
                                    <span class="input-group-text" > @{{ item }}</span>
                                </td>
                                <td>
                                    <span class="form-control" > @{{ status[target.sequence][key] }} </span>
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>
            </div>
            <!--4. 機構控制 -->
            <div v-show="tab==4">
                <div class="mapBlock row">
                    <h5>推進器</h5>
                    <div class="input-group mb-2">
                        <button type="button" class="btn btn-primary btn-block" @click="toPropeller(1)">
                            推進器 - 放下
                        </button>
                    </div>
                    <div class="input-group mb-2">
                        <button type="button" class="btn btn-secondary btn-block" @click="toPropeller(0)">
                            推進器 - 收回
                        </button>
                    </div>
                </div>
                <div class="mapBlock row">
                    <h5>捕捉機構</h5>
                    <div class="input-group mb-2">
                        <button type="button" class="btn btn-success btn-block" @click="toCatcher(1)">
                            捕捉機構 - 啟動
                        </button>
                    </div>
                    <div class="input-group mb-2">
                        <button type="button" class="btn btn-secondary btn-block" @click="toCatcher(0)">
                            捕捉機構 - 停止
                        </button>
                    </div>

                </div>
                <div v-if="cmdMessage.length>0" class="alert alert-success" role="alert">
                    @{{ cmdMessage }}
                </div>
            </div>

            <!--5. 標的 -->
            <div v-show="tab==5">
                <div class="usvCamBlock row">
                    <div class="mb-1">

                        <span>
                            標的
                            @if($device->support==1)
                                <button type="button" class="btn btn-outline-secondary btn-sm" @click="changeTargetOption(1);" >無人機即時資訊</button>
                                <button type="button" class="btn btn-outline-secondary btn-sm" @click="changeTargetOption(2);" >位置列表</button>
                            @endif
                        </span>
                    </div>
                    <div v-show="targetOption==1" class="mb-1">
                        <div class="input-group mb-1">
                            <span class="input-group-text" id="inputGroup-sizing-default">緯度</span>
                            <input type="text" v-model="location.lat" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                        </div>
                        <div class="input-group mb-3">
                            <span class="input-group-text" id="inputGroup-sizing-default">經度</span>
                            <input type="text" v-model="location.lng" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">
                        </div>

                        <div class="input-group mb-2">
                            <button type="button" class="btn btn-primary btn-block" @click="saveLocation()">
                                儲存標的位置
                            </button>
                        </div>
                        <div v-if="cmdMessage.length>0" class="alert alert-success" role="alert">
                            @{{ cmdMessage }}
                        </div>
                    </div>
                    <!-- UAV WEBRTC -->
                    <div v-show="targetOption==1 && isSupportUAV" id="uavCam" class="uavCamBlock">
                        <iframe  class="ml-2" id="iframe1" src="{{url('/room/webrtc/'.$device->id.'&size=small')}}" ></iframe>
                    </div>
                    <!-- UAV Image -->
                    <div v-show="targetOption==2  && isSupportUAV" class="uavImageBlock justify-content-center">
                        <img v-if="image_url==null" src="{{url('/Images/no_image.png')}}" style="background-color: #ffffff" width="300" height="240">
                        <img v-else  :src="image_url" width="300" height="240">
                    </div>

                </div>
                <!-- 位置列表 -->
                <div v-show="targetOption==2  || !isSupportUAV" class="mapBlock row mt-1">
                    <h5>位置列表</h5>

                    <div class="input-group mb-2">
                        <!-- Search list -->
                        <table id="table_test" class="text-center" style="width:100%;">
                            <thead>
                            <tr>
                                <th width="25%">標的</th>
                                <th width="25%">緯度</th>
                                <th width="25%">經度</th>
                                <th width="25%">操作</th>
                            </tr>

                            </thead>
                            <tbody >
                            <tr v-for="(item, index) in searchList" @click="showInfo(index)" onmouseover="toHighlight(this);" onmouseout="restore(this);">
                                <td >@{{ index+1 }}</td>
                                <td style="font-size:8px">
                                    @{{ searchList[index].lat }}
                                </td>
                                <td style="font-size:8px">
                                    @{{ searchList[index].lng }}
                                </td>
                                <td style="font-size:8px">
                                    <button type="button" class="btn btn-danger btn-sm" @click="removeLocation(searchList[index].id)">
                                        刪除
                                    </button>
                                </td>

                            </tr>

                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
            <!--6. 功能 -->
            <div v-show="tab==6">
                <div class="mapBlock row">
                    <div>
                        @if($user->role_id < 3 )
                        <div class="text-left mb-2">
                            <input type="button" class="btn btn-secondary btn-sm" @click="toParamTest()" value="工程測試" />
                        </div>
                        @endif
                        <div class="text-left mt-1 mb-2">
                            <input type="button" class="btn btn-info btn-sm mr-2" @click="toHistory()" value="GPS歷史紀錄" />
                        </div>
                         <div class="text-left mb-2">
                         @if($user->role_id < 3 && $devices != null)
                             {{__('layout.select') }}
                             <select onchange="location.href='usv/?device_id='+this.options[this.selectedIndex].value">
                            @foreach ($devices as $item)
                                 @if ($item->id == $device_id)
                                     <option value="{{$item->id}}" selected="selected">{{$item->device_name}}</option>
                                 @else
                                     <option value="{{$item->id}}">{{$item->device_name}}</option>
                                 @endif
                             @endforeach
                            </select>
                         @endif
                        </div>

                    </div>

                </div>

            </div>
        </div>

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

    </div>
@endsection

@section('footerScripts')
    <script>
        let apps = {!! json_encode($apps) !!};
        let device = {!! json_encode($device) !!};
        let user = {!! json_encode($user) !!};
        let statusObj = {!! json_encode($status) !!};
        let point_url = "{!! url('/Images/point.png')!!}";
        let diamond_url = "{!! url('/Images/diamond.png')!!}";
        let star_url = "{!! url('/Images/star.png')!!}";
        let app_url = '{{ env('APP_URL') }}';
        let center_index = {!! $center_index !!};
        @if($setting == null)
        let setting = null;
        @else
        let setting = {!! $setting !!};
        @endif
        @if($home_setting == null)
        let home_setting = null;
        @else
        let home_setting = {!! $home_setting!!};
        @endif
        let parameter = {!! $parameter!!};
        @if($user->role_id <= 7 || ($user->room_limit != null && ($user->room_limit[$room->id] == 8)) )
        let isAdmin = true;
        @else
        let isAdmin = false;
        @endif
        @if($mechanism != null)
        let mechanism = {!! json_encode($mechanism) !!};
        let cmd = app_url+'/search?command='+mechanism.command;
        @else
        let mechanism = {};
        let cmd = null;
        @endif
        let token = '{{$user->remember_token}}';
        let api_url = '{!! env('API_URL') !!}';
        let locations = {!! json_encode($locations) !!};
        //alert(cmd);
        @if($report_setting == null)
        let reportSetting = null;
        @else
        let reportSetting = {!! json_encode($report_setting)!!};
        @endif
        let menu0 = "地圖";
        let menu1 = "影像";
    </script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP_KEY')}}">
    </script>
    <script src="{{asset('vender/jQueryRotate/jQueryRotate.2.1.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/option/tools.js')}}"></script>
    <script src="{{asset('js/room/usv/work_alg.js')}}"></script>
    <script src="{{asset('js/room/usv/index.js')}}"></script>

@endsection
