@extends('Layout.diy')

@section('content')
    <div class="room_header row">
        <!--<span class="ml-3"> 現在位置：</span>-->
        <div class="col-md-6 col-lg-3">
            <span class="breadcrumb-item">
                <a href="/node/myDevices?link=develop">{{__('device.my_devices') }}</a>
            </span>
            <span> / </span>
            <span class="breadcrumb-item">
                地圖管理
            </span>
        </div>

        <div class="col-md-6 col-lg-2">
            <span v-cloak class="ml-5 mt-4 font-weight-bold">航向角@{{ status[3]['key3'] }}度</span>
        </div>

        <div class="col-md-6 col-lg-2">
            <span class="ml-5 text-left">
            @if($user->role_id < 3 && $devices != null)

                    <select onchange="location.href='node/map?mac='+this.options[this.selectedIndex].value">
                    @foreach ($devices as $item)
                            @if ($item->id == $device_id)
                                <option value="{{$item->macAddr}}" selected="selected">{{$item->device_name}}</option>
                            @else
                                <option value="{{$item->macAddr}}">{{$item->device_name}}</option>
                            @endif
                        @endforeach
                </select>
            @endif
            </span>
        </div>

        <div class="col-md-6 col-lg-5">
            <span class="float-right">
                @if($user->role_id < 3)
                    <span v-cloak>
                    <!-- For test of Super Admin-->
                    <input v-if="isOpenWindow==true" type="button" class="btn btn-outline-primary" @click="isOpenWindow=false;" value="隱藏上報點" />
                    <input v-else type="button" class="btn btn-outline-primary" @click="isOpenWindow=true;" value="顯示上報點訊息" />
                    </span>
                    <input type="button" class="btn btn-secondary" @click="toParamTest()" value="工程測試" />
                @endif
                <input v-if="isDemo==false" type="button" class="btn btn-success" @click="isDemo=true;" value="展示模式" />
                <input v-cloak v-else type="button" class="btn btn-primary" @click="isDemo=false;" value="一般模式" />
                <input type="button" class="btn btn-warning" @click="toViewControl()" value="影像監控" />
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

    <div class="imageBlock">
        <img  class="testBlock" id="usv_direction" src="{{url('/Images/boat/boat8.png')}}">
    </div>

    <div class="row main-content mt-1">
        <div class="col-lg-9">
            <div id="map" style="height: 500px;width: 100%;"></div>
        </div>
        <div v-cloak class="col-lg-3 text-center">
            <!-- 切換頁籤 -->
            <div id="timeselector" class="btn-group btn-group-toggle mb-1" data-toggle="buttons">
                <label class="btn btn-secondary active">
                    <input type="radio" name="options" id="1" autocomplete="off" >巡航
                </label>
                <label class="btn btn-secondary">
                    <input type="radio" name="options" id="2" autocomplete="off" checked>無人船
                </label>
                <label class="btn btn-secondary">
                    <input type="radio" name="options" id="3" autocomplete="off">航向
                </label>
                @if($mechanism != null)
                <label class="btn btn-secondary">
                    <input type="radio" name="options" id="4" autocomplete="off">機構
                </label>
                @endif
            </div>

            <!-- 控制按鍵 -->
            <div class="mapBlock" >
                <div v-show="isMeasure==false" class="mb-1">
                    <span v-if="isDemo==true">
                        <button v-if="isRun==false" type="button" class="btn btn-success btn-sm" @click="demo()">
                            巡航展示
                        </button>
                        <button v-else type="button" class="btn btn-success btn-sm" disabled>
                            巡航展示
                        </button>
                    </span>
                    <span v-else>
                        <button v-if="isRun==false" type="button" class="btn btn-primary btn-sm" @click="fence()">
                            開始巡航
                        </button>
                        <button v-if="isRun!=false" type="button" class="btn btn-primary btn-sm" disabled>
                            開始巡航
                        </button>
                    </span>

                    <button v-if="isRun!=false" type="button" class="btn btn-danger btn-sm" onclick="stop()">
                        停止巡航
                    </button>
                    <button v-if="isRun==false" type="button" class="btn btn-danger btn-sm" disabled>
                        停止巡航
                    </button>
                    <button v-if="isRun==false" type="button" class="btn btn-primary btn-sm" onclick="toHome()">
                        回HOME點
                    </button>
                    <button v-if="isRun!=false" type="button" class="btn btn-primary btn-sm" disabled>
                        回HOME點
                    </button>
                </div>
                <div v-show="isBlock" class="mb-1">
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearMarkers()">
                        清定位點
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" @click="clearNewBlock()">
                        清新區塊
                    </button>
                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearReports()">
                        清上報點
                    </button>

                </div>
                <div v-show="isBlock" class="mb-1">
                    <button type="button" class="btn btn-outline-primary btn-sm" @click="createBlock()">
                        劃新區塊
                    </button>
                    <!--<button type="button" class="btn btn-outline-success btn-sm" @click="demo()">
                        圍籬展示
                    </button>-->
                    <button v-if="isRun==false" type="button" class="btn btn-outline-primary btn-sm" @click="fenceOutline()">
                        圍籬展示
                    </button>
                    <button v-else type="button" class="btn btn-outline-primary btn-sm" disabled>
                        圍籬展示
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm" @click="measureTool()">
                        量測距離
                    </button>

                </div>

                <div v-show="isBlock" class="mb-1">
                    <button type="button" class="btn btn-primary btn-sm" @click="setParameter()">
                        設定巡航間隔距離 <i class="fa fa-pen"></i>
                    </button>
                    <input type="number"  v-model="parameter.set.interval" placeholder="1~10" min="1" max="10" />
                    公尺
                </div>

                <div v-show="isMeasure==true">
                    <button type="button" class="btn btn-outline-danger btn-sm" @click="clearLine()">
                        清除
                    </button>
                    <button v-if="isMeasure" type="button" class="btn btn-outline-dark btn-sm" @click="isMeasure=false;isBlock=true;">
                        返回電子圍籬
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
            <!--1. 圍籬座標-->
            <div v-show="tab==1">
                <!-- Home & Center -->
                <div v-show="!isMeasure" class="mapBlock">
                    <div>
                        <div class="mb-1">
                            <form method="post" action="editSetting" id="editSetting">
                                <input type="hidden" name="_method" value="put" />
                                <input type="hidden" name="id" v-model="setting.id" />
                                <input type="hidden" name="device_id" value="{{$device_id}}"/>
                                <input type="hidden" name="field" v-model="setting.field"/>
                                <input type="hidden" name="set" v-model="setting.set"/>
                                {{csrf_field()}}
                            </form>
                            <form method="post" action="editSetting" id="editHomeSetting">
                                <input type="hidden" name="_method" value="put" />
                                <input type="hidden" name="id" v-model="home_setting.id" />
                                <input type="hidden" name="device_id" value="{{$device_id}}"/>
                                <input type="hidden" name="field" v-model="home_setting.field"/>
                                <input type="hidden" name="set" v-model="home_setting.set"/>
                                {{csrf_field()}}
                            </form>
                            <form method="post" action="editSetting" id="editParamSetting">
                                <input type="hidden" name="_method" value="put" />
                                <input type="hidden" name="id" v-model="parameter.id" />
                                <input type="hidden" name="device_id" value="{{$device_id}}"/>
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

                                </thead>
                                <tbody >
                                    <!-- Home Start-->
                                    <tr >
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
                                    </tr>
                                    <tr >
                                        <td colspan="2" >
                                            <div class="form-control">
                                                <span >
                                                    <button class="btn btn-success btn-sm" @click="addHome()">
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                    <button v-if="home_setting.set.length>0" type="button" class="btn btn-primary btn-sm" @click="setHome()">
                                                        <i class="fa fa-pen"></i>
                                                    </button>
                                                    <button v-if="home_setting.set.length>0" type="button" class="btn btn-danger btn-sm" @click="delHome()">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </span>
                                                <span v-if="home_setting.set.length>0" class="float-right">
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

                                    <!-- Report -->
                                    <tr >
                                        <td >上報</td>
                                        <td >
                                            <input type="text" style="font-size:12px" class="form-control" v-model="status[1].lat" aria-describedby="basic-addon1" maxlength="10" size="10" disabled>
                                        </td>
                                        <td >
                                            <input type="text" style="font-size:12px" class="form-control" v-model="status[1].lng" aria-describedby="basic-addon1" maxlength="10" size="10" disabled>
                                        </td>
                                    </tr>

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
                <!-- 電子圍籬 -->
                <div v-show="!isMeasure" class="mapBlock">
                    <div v-show="!isShowDistance">
                        <h5>
                            <span>電子圍籬座標</span>
                            <span class="float-right">
                            <button type="button" class="btn btn-warning btn-sm" @click="change(2)">距離</button>
                        </span>
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
                <div v-if="isMeasure" class="mapBlock">
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
                                <tr >

                                    <td >1</td>
                                    <td >
                                        <input type="text" style="font-size:12px" class="form-control" v-model="test.lat" aria-describedby="basic-addon1" maxlength="10" size="10" >
                                    </td>
                                    <td >
                                        <input type="text" style="font-size:12px" class="form-control" v-model="test.lng" aria-describedby="basic-addon1" maxlength="10" size="10" >
                                    </td>
                                </tr>
                                <tr >

                                    <td >2</td>
                                    <td >
                                        <input type="text" style="font-size:12px" class="form-control" v-model="test2.lat" aria-describedby="basic-addon1" maxlength="10" size="10" >
                                    </td>
                                    <td >
                                        <input type="text" style="font-size:12px" class="form-control" v-model="test2.lng" aria-describedby="basic-addon1" maxlength="10" size="10" >
                                    </td>
                                </tr>
                                </tbody>

                            </table>
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon1">兩點距離</span>
                            </div>
                            <input type="text" class="form-control" v-model="distance" aria-describedby="basic-addon1" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <!--2. 無人船訊息-->
            <div v-show="tab==2">
                <!--2. 無人船訊息-->
                <div class="mapBlock">
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
                                <tr>
                                    <td colspan="2">@{{ label[2].key6 }}</td>
                                    <td colspan="2">@{{ label[2].key7 }}</td>
                                    <td colspan="2">@{{ label[2].key8 }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">@{{ status[2].key6 }}</td>
                                    <td colspan="2">@{{ status[2].key7 }}</td>
                                    <td colspan="2">@{{ status[2].key8 }}</td>
                                </tr>

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
                            <tr>
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
                            </tr>
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

                        <!-- 電力 -->
                        <table class="text-center" style="width:100%;">
                            <thead>
                            <tr>
                                <!-- 船艙溫度 title -->
                                <th width="35%">@{{ label[1].key5 }}</th>
                                <!-- Item -->
                                <th width="30%"></th>
                                <!-- 船艙濕度 title -->
                                <th width="35%">@{{ label[1].key6 }}</th>
                            </tr>

                            </thead>
                            <tbody >
                            <tr >

                                <td >
                                    <!--Left battery_power -->
                                    <span class="form-control" style="font-size:12px"> @{{ status[1].key5 }} 度</span>
                                </td>
                                <td >船艙</td>
                                <td >
                                    <!--Right battery_power -->
                                    <span class="form-control" style="font-size:12px"> @{{ status[1].key6 }} %</span>
                                </td>
                            </tr>
                            <tr >

                                <td >
                                    <!-- battery_lower_trigger -->
                                    <input type="number"  v-model="parameter.set.trigger1" placeholder="20~23" min="20" max="23" />V
                                </td>
                                <td >
                                    <button type="button" class="btn btn-primary btn-sm" @click="setParameter()">
                                        設定低電力 觸發警告<i class="fa fa-pen"></i>
                                    </button>
                                </td>
                                <td >
                                    <!-- battery_lower_trigger -->
                                    <input type="number"  v-model="parameter.set.trigger2" placeholder="20~23" min="20" max="23" />V
                                </td>
                            </tr>
                            </tbody>

                        </table>


                    </div>
                </div>
            </div>
            <!--3. 航向訊息-->
            <div v-show="tab==3">
                <!--3. 航向訊息-->
                <div class="mapBlock">
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
                                        <!--<span v-if="index == 0 || index==1"class="form-control" > @{{ status[3][key] }} </span>
                                        <span v-else-if="status[3][key] == 0"class="form-control" > 關閉 </span>
                                        <span v-else-if="status[3][key] == 1"class="form-control" > 開啟 </span>-->
                                        <span class="form-control" > @{{ status[3][key] }} </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
            <!--4. 機構控制 -->
            <div v-show="tab==4">
                <div class="mapBlock">
                    <h5>推進器</h5>
                    <div class="input-group mb-2">
                        <button type="button" class="btn btn-primary btn-block" @click="toPropeller(1)" :disabled="isSendCmd">
                            推進器 - 放下
                        </button>
                    </div>
                    <div class="input-group mb-2">
                        <button type="button" class="btn btn-secondary btn-block" @click="toPropeller(0)" :disabled="isSendCmd">
                            推進器 - 收回
                        </button>
                    </div>
                </div>
                <div class="mapBlock">
                    <h5>捕捉機構</h5>
                    <div class="input-group mb-2">
                        <button type="button" class="btn btn-success btn-block" @click="toCatcher(1)" :disabled="isSendCmd">
                            捕捉機構 - 啟動
                        </button>
                    </div>
                    <div class="input-group mb-2">
                        <button type="button" class="btn btn-secondary btn-block" @click="toCatcher(0)" :disabled="isSendCmd">
                            捕捉機構 - 停止
                        </button>
                    </div>
                </div>
                <div v-if="cmdMessage.length>0" class="alert alert-success" role="alert">
                    @{{ cmdMessage }}
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
                    <div v-if="centerTab == 4 || centerTab == 5">
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

@endsection

@section('footerScripts')
    <script>
        let devices = {!! json_encode($devices) !!};
        let apps = {!! json_encode($apps) !!};
        let device_id = {!! $device_id !!};
        let target = {!! json_encode($myTarget) !!};
        let user = {!! $user !!};
        let statusObj = {!! json_encode($status) !!};
        let point_url = "{!! url('/Images/point.png')!!}";
        let diamond_url = "{!! url('/Images/diamond.png')!!}";
        let app_url = '{{ env('APP_URL') }}';
        let center_index = {!! $center_index !!};
        @if($setting == null)
        let setting = null;
        @else
        let setting = {!! json_encode($setting) !!};
        @endif

        @if($home_setting == null)
            let home_setting = null;
        @else
            let home_setting = {!! json_encode($home_setting) !!};
        @endif
        let parameter = {!! $parameter!!};
        @if($mechanism != null)
        let mechanism = {!! json_encode($mechanism) !!};
        let cmd = app_url+'/send_control?command='+mechanism.command;
        @else
        let cmd = null;
        @endif
        let token = '{{$user->remember_token}}';
        //alert(cmd);
    </script>
    <script async deferindex
            src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP_KEY')}}">
    </script>
    <script src="{{asset('vender/jQueryRotate/jQueryRotate.2.1.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/work_alg.js')}}"></script>
    <script src="{{asset('js/node/map.js')}}"></script>

@endsection



