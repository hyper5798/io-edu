@extends('Layout.room')

@section('content')
    <div class="room_header row">
        <!--<span class="ml-3"> 現在位置：</span>-->
        <div class="col-md-4 col-lg-3">
            <span class="breadcrumb-item">
                <a href="/room/index">{{__('layout.my_room')}}</a>
            </span>
            <span> / </span>
            <span class="breadcrumb-item">
                {{$device->device_name}}
            </span>
        </div>

        <div class="col-md-6 col-lg-4">

            <div v-cloak v-if="alertMessage.length>0" class="text-danger">
                @{{ alertMessage }}
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <span class="ml-5 text-left">
            @if($user->role_id < 3 && $devices != null && count($devices)>1)
                    {{__('layout.select') }}
                    <select onchange="location.href='agriBot?room_id={{$room->id}}&device_id='+this.options[this.selectedIndex].value">
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

        <div class="col-md-6 col-lg-4 ">
            <!--<span class="float-right">
                <input type="button" class="btn btn-warning btn-sm mr-2" @click="toViewControl()" value="影像監控" />
                <input type="button" class="btn btn-info btn-sm mr-2" @click="toHistory()" value="GPS歷史紀錄" />
            </span>-->
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



    <div class="row main-content mt-1">
<!-- Left farm block -->
        <div v-cloak class="col-lg-9 mb-2">
            <div class="farmMsgBlock mb-1">
                <div class="row">
                    <div v-if="tab==1" class="col-7">
                        <span v-if="isShowPlantBorder" class="mr-2"><button class="black-point rounded-circle">  </button>未種植</span>
                        <span v-if="isShowPlantBorder" class="mr-2"><button class="green-point rounded-circle"> </button>已上架</span>
                        <span v-if="isShowPlantBorder" class="mr-2"><button class="red-point rounded-circle"> </button>可採收</span>
                        <span v-if="isShowPlantBorder" class="mr-2"><button class="gray-point rounded-circle"> </button>已採收</span>
                        <!--<span v-if="isShowPlantBorder" class="mr-2"><button class="blue-point rounded-circle"> </button>澆水</span>
                        <span v-if="isShowPlantBorder" class="mr-2"><button class="brown-point rounded-circle"> </button>施肥</span>-->
                        <span class="ml-2 mr-2 text-info"> 點選右方命令做動作</span>
                    </div>
                    <div v-if="tab==2" class="col-7">
                        <span class="ml-2 mr-2 text-info"> 顯示上報資料</span>
                    </div>
                    <div v-if="tab==3" class="col-7">
                        <span class="ml-2 mr-2 text-info"> 滑鼠移動到植栽點選後，影像鏡頭移動到植栽上方</span>
                    </div>
                    <div v-if="tab==4" class="col-7">
                        <input type="checkbox" class="ml-2"  @change="setAllPlant($event)" />全選
                        <!--<span v-cloak class="col-4" v-for="(item, index) in kindObject">
                            <button  type="button" :style="item.colorBlock" class="btn btn-sm" @click="changeKind(index)">
                                @{{ item.name }}
                            </button>
                        </span>-->
                        選擇菜種
                        <select v-cloak v-model="kindIndex" @change="changeKind($event)" :style="kindObject[kindIndex].colorBlock">
                            <option v-for="(item, index) in kindObject" :value="index" :key="index" :style="item.colorBlock">
                                @{{ item.name }}
                            </option>
                        </select>
                        <!--<input :style="kind.colorBlock" class="text-white" type="text" v-model="kindObject[kindIndex]['name']" size="10"  maxlength="10" disabled />-->
                        <span class="mb-1 mr-2">
                        <button type="button" class="button btn-primary" @click="toSaveAllPlants();" :disabled="isSend">
                            <i class="fa fa-database"></i> 儲存所有植栽設定
                        </button>

                    </span>
                    </div>
                    <div class="col-5">
                        <span v-cloak class="text-success">

                            <span v-cloak v-if="message.length>0">
                                @{{ message }}
                            </span>
                        </span>
                    </div>
                </div>





            </div>
            <div class="farmBlock">
                <table v-cloak>
                    <tr v-for="x1 in boxObject.row" class="rowBlock">
                        <td width="10%" v-for="y1 in boxObject.column" class="mb-3">

                            <table >

                                <tr v-for="x in plantObject.row" class="boxBlock">
                                    <td >

                                        <span v-if="tab==4" class="mr-2">
                                            <input type="checkbox" v-model="checkList[((y1-1)*boxObject.number+(x1-1))]" @change="setRowPlant(x1, y1)" />整列
                                        </span>
                                        <span v-if="tab!=4" class="mr-3">
                                            &emsp;
                                        </span>

                                    </td>

                                    <td width="10%" v-for="y in plantObject.column">
                                    <!--<td width="10%" v-for="y in plantObject.column" @click="setRowPlant(x1,y1)">-->
                                        <div :style="farmObject[farmSize.field+x1+y1+x+y]['colorBlock']" @click="setPlant(farmSize.field+x1+y1+x+y)"  @mouseover="showPlant(farmSize.field+x1+y1+x+y)"
                                             class="plant-point rounded-circle mr-1 ">

                                            <label v-if="farmObject[farmSize.field+x1+y1+x+y]['maturity']>0" style="color:black;">@{{ farmObject[farmSize.field+x1+y1+x+y]['countdown'] }}天</label>
                                            <label >@{{ farmObject[farmSize.field+x1+y1+x+y]['title'] }}</label>
                                        </div>

                                    </td>
                                    <td v-if="tab!=4">

                                        <span  class="mr-3">
                                            &emsp;
                                        </span>

                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

            </div>
        </div>
<!-- Right control & setting -->
        <div v-cloak class="col-lg-3 text-center">
            <!-- 切換頁籤 -->
            <div id="timeselector" class="btn-group btn-group-toggle mb-1" data-toggle="buttons">
                <label class="btn btn-secondary active">
                    <input type="radio" name="options" id="1" autocomplete="off" />手動操作
                </label>
                <label class="btn btn-secondary">
                    <input type="radio" name="options" id="2" autocomplete="off" checked />上報
                </label>
                <label class="btn btn-secondary">
                    <input type="radio" name="options" id="3" autocomplete="off" />影像
                </label>
                <label class="btn btn-secondary">
                    <input type="radio" name="options" id="4" autocomplete="off" />設定
                </label>

            </div>
            <!-- Command & Setting Button bot:2, plate:3, home:4, script:5 -->
            <div v-if="tab==1 || tab==4" class="mapBlock row">
                <!-- Command -->
                <table class="text-center" style="width:100%; font-size: 1em;">

                    <tr v-if="tab==1">
                        <td width="50%">
                            <button type="button" class="btn btn-outline-primary btn-block" @click="sendPlantCmd('plant')" :disabled="isSend">
                                <i class="fas fa-sitemap"></i> @{{ commandObject['plant'].name }}
                            </button>
                        </td>
                        <td width="50%">
                            <button type="button" class="btn btn-outline-primary btn-block" @click="sendPlantCmd('crop')" :disabled="isSend">
                                <i class="fa fa-baby-carriage"></i> @{{ commandObject['crop'].name }}
                            </button>
                        </td>
                    </tr>
                    <tr v-if="tab==1" >
                        <td colspan="2">
                            <button type="button" class="btn btn-outline-danger btn-block mt-1 mb-1" @click="sendPlantCmd('stop')">
                                <i class="fa fa-ban"></i> @{{ commandObject['stop'].name }}
                            </button>
                        </td>
                    </tr>
                    <tr v-if="tab==1" >
                        <td colspan="2">
                            <div class="input-group mb-1">

                                <span class="mt-2" style="font-size: 16px;">是否自動指定操做目標(打勾自動指定目標)</span>
                                <input type="checkbox" style="color: #007bff" class="form-control" v-model="isAutoTarget" @change="changeTargetAuto()"/>
                            </div>
                        </td>
                    </tr>

                </table>
                <!-- Setting Button bot:2, plate:3, home:4, script:5 -->
                <div class="row">
                    <div v-show="tab==4" class="col-md-4 col-sm-6 mb-1">
                        <button  type="button" class="btn btn-outline-primary btn-block" @click="settingTab=5;">
                            命令腳本
                        </button>
                    </div>
                    <div v-show="tab==4" class="col-md-4 col-sm-6 mb-1">
                        <button  type="button" class="btn btn-outline-primary btn-block" @click="settingTab=4;">
                            手動命令
                        </button>
                    </div>

                    <div v-show="tab==4" class="col-md-4 col-sm-6 mb-1">
                        <button  type="button" class="btn btn-outline-primary btn-block" @click="settingTab=6;">
                            上報觸發
                        </button>
                    </div>

                    <div v-show="tab==4" class="col-md-4 col-sm-6 mb-1">
                        <button  type="button" class="btn btn-outline-primary btn-block" @click="settingTab=2;">
                            機器人
                        </button>
                    </div>
                    <div v-show="tab==4" class="col-md-4 col-sm-6 mb-1">
                        <button type="button" class="btn btn-outline-primary btn-block" @click="showKindList();">
                            菜種
                        </button>
                    </div>

                    <div v-show="tab==4" class="col-md-4 col-sm-6 mb-1">
                        <button  type="button" class="btn btn-outline-primary btn-block" @click="settingTab=1;">
                            植栽
                        </button>
                    </div>
                </div>


            </div>
            <!-- Report -->
            <div v-if="tab==2">
                <div class="mapBlock row" >
                    <div v-for="(target,index) in appList" class="input-group mb-2">
                        <div>
                            <span class="block-title mr-4">@{{ target.name }}</span>

                            <span>
                                <!--<a :href="'/node/apps/reports?app_id=' + target.id" target="_new">紀錄連結</a>-->
                                <a :href="'/node/apps/reports?app_id=' + target.id">紀錄連結</a>
                            </span>
                        </div>


                        <table class="text-center" style="width:100%; font-size: 1em;">

                            <tr v-for="(item, key, index) in label[target.sequence]">
                                <td >
                                   <span class="input-group-text" > @{{ item }}</span>
                                </td>
                                <td>
                                    <!--<span v-if="index == 0 || index==1"class="form-control" > @{{ status[3][key] }} </span>
                                    <span v-else-if="status[3][key] == 0"class="form-control" > 關閉 </span>
                                    <span v-else-if="status[3][key] == 1"class="form-control" > 開啟 </span>-->
                                    <span class="form-control" > @{{ status[target.sequence][key] }} </span>
                                </td>
                            </tr>

                        </table>
                    </div>
                </div>
            </div>
            <!-- IP CAM -->
            <div v-show="tab==3">
                <div v-if="isWebRTC" class="farmWebRTCBlock row mb-1">
                    <iframe v-if="isWebRTC" id="iframe1" src="{{url('/room/webrtc/'.$device->id)}}" ></iframe>
                </div>
                <div v-if="isWebRTC==false" class="farmCamBlock row mb-1">


                    <span class="float-right">
                           <button type="button" @click="showVideoData();">
                               <i class="fa fa-coins"></i>影像資料
                           </button>
                       </span>

                    <video :name="element.id" style="width: 100%; height: 220px;" controls autoplay muted>
                    </video>
                </div>
                <!-- Command Button-->
                <div class="mapBlock row">
                    <div class="row">
                        <div class="col-md-4 col-sm-6 mb-1">
                            <button  type="button" class="btn btn-outline-primary btn-block" @click="sendCmd('stretch')" :disabled="isSend">
                                <i class="fa fa-arrow-circle-up"></i>@{{ commandObject['stretch'].name }}
                            </button>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-1">
                            <button  type="button" class="btn btn-outline-primary btn-block" @click="sendCmd('pullback')" :disabled="isSend">
                                <i class="fa fa-arrow-circle-down"></i>@{{ commandObject['pullback'].name }}
                            </button>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-1">
                            <button  type="button" class="btn btn-outline-primary btn-block" @click="sendCmd('up')" :disabled="isSend">
                                <i class="fa fa-arrow-up"></i>@{{ commandObject['up'].name }}
                            </button>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-1">
                            <button  type="button" class="btn btn-outline-primary btn-block" @click="sendCmd('left')" :disabled="isSend">
                                <i class="fa fa-arrow-left"></i>@{{ commandObject['left'].name }}
                            </button>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-1">
                            <button  type="button" class="btn btn-outline-primary btn-block" @click="sendCmd('right')" :disabled="isSend">
                                <i class="fa fa-arrow-right"></i>@{{ commandObject['right'].name }}
                            </button>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-1">
                            <button  type="button" class="btn btn-outline-primary btn-block" @click="sendCmd('down')" :disabled="isSend">
                                <i class="fa fa-arrow-down"></i>@{{ commandObject['down'].name }}
                            </button>
                        </div>
                        <div class="col-md-12 col-sm-12 mb-1">
                            <button type="button" class="btn btn-outline-danger btn-block mt-1 mb-1" @click="sendPlantCmd('stop')">
                                <i class="fa fa-ban"></i> @{{ commandObject['stop'].name }}
                            </button>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-1">
                            <button type="button" class="btn btn-outline-primary btn-block" @click="sendCmd('watering')" :disabled="isSend">
                                <i class="fas fa-shower"></i> @{{ commandObject['watering'].name }}
                            </button>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-1">
                            <button type="button" class="btn btn-outline-primary btn-block" @click="sendCmd('stop_watering')" :disabled="isSend">
                                <i class="fa fa-stop"></i> @{{ commandObject['stop_watering'].name }}
                            </button>
                        </div>
                        <div class="col-md-4 col-sm-6 mb-1">
                            <button type="button" class="btn btn-outline-success btn-block" @click="sendCmd('home')" :disabled="isSend">
                                <i class="fa fa-anchor"></i> @{{ commandObject['home'].name }}
                            </button>
                        </div>

                    </div>
                </div>
            </div>
            <!-- Setting -->
            <div >
                <!-- Plant detail-->
                <div v-if="tab==1 || (tab==4 && settingTab==1)" class="mapBlock row" >
                    <div class="form-row align-items-center" style="width:100%; font-size: 1em;">
                        <div v-if="tab==1"class="col-8 input-group mb-1">
                            <span class="block-title mr-4">操做目標</span>
                        </div>
                        <div v-if="tab==4"class="col-12 input-group mb-1">
                            <span class="block-title mr-5">植栽設定</span>
                        </div>

                        <div v-if="tab==1" class="col-4 input-group mb-1 loat-right">
                            <button type="button" @click="toSavePlant()" class="btn btn-primary">
                                 更新
                            </button>
                        </div>

                        <div class="col-6 input-group mb-1">
                            <div class="input-group-append">
                                <span class="input-group-text" >代號</span>
                            </div>
                            <input type="text" class="form-control" v-model="plant.tag" disabled/>
                        </div>
                        <div class="col-6 input-group mb-1">
                            <div class="input-group-append">
                                <span class="input-group-text" >菜種</span>
                            </div>
                            <input type="text" class="form-control" v-model="plant.title" disabled/>
                        </div>

                        <!--<div class="col-12 input-group mb-1">
                            <div class="input-group-append">
                                <span class="input-group-text" >G code</span>
                            </div>
                            <input type="text" class="form-control" v-model="plant.code" disabled/>
                        </div>-->

                        <div class="col-6 input-group mb-1">

                            <div class="input-group-append">
                                <span class="input-group-text" >種植類型</span>
                            </div>
                            <input type="text" class="form-control" v-model="plant.kind" disabled/>
                        </div>
                        <div class="col-6 input-group mb-1">

                            <div class="input-group-append">
                                <span class="input-group-text" > 可採收?</span>
                            </div>
                            <input type="checkbox" style="color: #007bff" class="form-control" v-model="plant.checked" disabled />
                        </div>
                        <div class="col-6 input-group mb-1">

                            <div class="input-group-append">
                                <span class="input-group-text" >天數</span>
                            </div>
                            <input type="number" class="form-control" v-model="plant.maturity" disabled/>
                        </div>
                        <div class="col-6 input-group mb-1">

                            <div class="input-group-append">
                                <span v-if="plant.checked==true" class="input-group-text" >逾期</span>
                                <span v-else class="input-group-text" >倒數</span>
                            </div>
                            <input type="number" class="form-control" v-model="plant.countdown" disabled/>
                        </div>
                        <div class="col-12 input-group mb-1">

                            <div class="input-group-append">
                                <span class="input-group-text" >上架日</span>
                            </div>
                            <input v-if="tab==1" type="text" class="form-control" v-model="plant.plant_time"/>
                            <input v-else type="text" class="form-control" v-model="plant.plant_time" disabled/>
                        </div>
                        <div class="col-12 input-group mb-1">

                            <div class="input-group-append">
                                <span class="input-group-text" >採收日</span>
                            </div>
                            <input v-if="tab==1" type="text" class="form-control" v-model="plant.crop_time" />
                            <input v-else type="text" class="form-control" v-model="plant.crop_time" disabled/>
                        </div>
                        <!--<div class="col-12 input-group mb-1">

                            <div class="input-group-append">
                                <span class="input-group-text" >澆水時間</span>
                            </div>
                            <input type="text" class="form-control" v-model="plant.watering_time" disabled/>
                        </div>
                        <div class="col-12 input-group mb-1">

                            <div class="input-group-append">
                                <span class="input-group-text" >施肥時間</span>
                            </div>
                            <input type="text" class="form-control" v-model="plant.muck_time" disabled/>
                        </div>-->

                        <div class="col-6 input-group mb-1">

                            <div class="input-group-append">
                                <span class="input-group-text" >外箱行</span>
                            </div>
                            <input type="text" class="form-control" v-model="plant.box.x" size="15" maxlength="15" disabled />
                        </div>
                        <div class="col-6 input-group mb-1">

                            <div class="input-group-append">
                                <span class="input-group-text" >外箱列</span>
                            </div>
                            <input type="text" class="form-control" v-model="plant.box.y" size="15" maxlength="15" disabled />
                        </div>
                        <div class="col-6 input-group mb-1">

                            <div class="input-group-append">
                                <span class="input-group-text" >植栽行</span>
                            </div>
                            <input type="text" class="form-control" v-model="plant.plant.x" size="15" maxlength="15" disabled />
                        </div>
                        <div class="col-6 input-group mb-1">

                            <div class="input-group-append">
                                <span class="input-group-text" >植栽列</span>
                            </div>
                            <input type="text" class="form-control" v-model="plant.plant.y" size="15" maxlength="15" disabled />
                        </div>
                    </div>
                </div>
                <div v-if="tab==4 && (settingTab!=1 && settingTab!=5)" class="codeListBlock row" >
                    <!-- Farm bot setting-->
                    <div v-if="settingTab==2">
                        <div class="mb-1 text-left">
                            <span class="block-title mr-2">農業機器人設定</span>
                        </div>
                        <div class="mb-1 text-left">
                            <span class="font-weight-bold">預備字</span>
                            <input type="text" v-model="farmSize.field" size="2"  maxlength="2" />
                            <!--<span class="font-weight-bold ml-2">植栽半徑(mm)</span>
                            <input type="number" style="width: 80px" v-model="farmSize.radius"  min="30" max="200" />-->
                            <!-- speed setting -->
                            <!--<span class="font-weight-bold ml-2">速度</span>
                            <input type="number" style="width: 80px" v-model="farmSize.speed"  min="1000" max="5000" />
                            <span class="text-info mr-1">1000~5000</span>-->
                        </div>
                        <div class="mb-1 text-left">
                            <span class="font-weight-bold">起始Z軸位置(mm)</span>
                            <input type="number" style="width: 60px" v-model="farmSize.start.z"  min="30" max="800" />
                        </div>
                        <div class="mb-1 text-left">
                            <span class="font-weight-bold">起始Y軸位置(mm)</span>
                            <input type="number" style="width: 60px" v-model="farmSize.start.y"  min="30" max="800" />
                        </div>
                        <div class="mb-1">
                            <table class="text-left" style="width:100%;">
                                <tbody >
                                <tr>
                                    <th width="17%">類型</th>
                                    <th width="18%">數量</th>
                                    <th width="18%">行數</th>
                                    <th width="18%">列數</th>
                                    <th width="29%">間距(mm)</th>
                                </tr>
                                <tr>
                                    <th >外箱</th>
                                    <th >
                                        <input type="number" style="width: 50px" v-model="farmSize.box.number" min="1" max="10" />
                                    </th>
                                    <th >
                                        <input type="number" style="width: 50px" v-model="farmSize.box.row" min="1" max="30" />
                                    </th>
                                    <th >
                                        <input type="number"  style="width: 50px" v-model="farmSize.box.column" min="1" max="30" />
                                    </th>
                                    <th >
                                        <input type="number"  style="width: 70px" v-model="farmSize.box.interval" min="100" max="1000" />
                                    </th>
                                </tr>
                                <tr>
                                    <th >植栽</th>
                                    <th >
                                        <input type="number" style="width: 50px" v-model="farmSize.plant.number" min="1" max="10" />
                                    </th>
                                    <th >
                                        <input type="number" style="width: 50px" v-model="farmSize.plant.row" min="1" max="30" />
                                    </th>
                                    <th >
                                        <input type="number" style="width: 50px" v-model="farmSize.plant.column" min="1" max="30" />
                                    </th>
                                    <th >
                                        <input type="number"  style="width: 70px" v-model="farmSize.plant.interval" min="100" max="500" />
                                    </th>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mb-1 mr-2">
                            <form method="post" action="editFarmSetting" id="editFarmBotSetting">
                                <input type="hidden" name="device_id" v-model="device.id"/>
                                <input type="hidden" name="set" v-model="farmSizeStr"/>
                                <input type="hidden" name="field" value="farm_bot"/>
                                {{csrf_field()}}

                                <button type="button" class="button btn-primary btn-block" @click="saveSetting('editFarmBotSetting');">
                                    <i class="fa fa-database"></i> 儲存農業機器人設定
                                </button>
                            </form>
                        </div>
                    </div>
                    <!-- Farm plate setting-->
                    <div v-if="settingTab==3">
                        <div class="mb-1 text-left">
                            <span class="block-title mr-2">置換平台位置設定</span>
                        </div>
                        <div class="mb-1">
                            <table class="text-left" style="width:100%;">
                                <tbody >
                                <tr>
                                    <th width="17%">類型</th>
                                    <th width="18%">行數</th>
                                    <th width="18%">列數</th>
                                </tr>
                                <tr>
                                    <th >外箱</th>
                                    <th >
                                        <input type="number" style="width: 50px" v-model="farmPlate.box.row" min="1" max="30" />
                                    </th>
                                    <th >
                                        <input type="number"  style="width: 50px" v-model="farmPlate.box.column" min="1" max="30" />
                                    </th>
                                </tr>
                                <tr>
                                    <th >植栽</th>
                                    <th >
                                        <input type="number" style="width: 50px" v-model="farmPlate.plant.row" min="1" max="30" />
                                    </th>
                                    <th >
                                        <input type="number" style="width: 50px" v-model="farmPlate.plant.column" min="1" max="30" />
                                    </th>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mb-1 mr-2">
                            <form method="post" action="editFarmSetting" id="editPlateSetting">
                                <input type="hidden" name="device_id" v-model="device.id"/>
                                <input type="hidden" name="set" v-model="farmSizeStr"/>
                                <input type="hidden" name="field" value="farm_plate"/>
                                {{csrf_field()}}

                                <button type="button" class="button btn-primary btn-block" @click="saveSetting('editPlateSetting');">
                                    <i class="fa fa-database"></i> 儲存置放平台設定
                                </button>
                            </form>
                        </div>
                    </div>
                    <!-- Command & script mapping setting-->
                    <div v-if="settingTab==4">
                        <div class="mb-1 text-left">
                            <span class="block-title mr-2">手動命令設定</span>
                        </div>
                        <div class="mb-1">
                            <table class="text-left" style="width:100%;">
                                <tbody >
                                <tr>
                                    <th width="10%">No</th>
                                    <th width="30%">名稱</th>

                                    <th width="30%">命令腳本</th>
                                    <th width="30%"></th>
                                </tr>
                                <tr v-for="(item, key, index) in commandObject">
                                    <th >@{{ index+1 }}</th>
                                    <th >
                                        <input type="text"  v-model="item.name"  size="10" maxlength="10" :disabled="index<11"/>
                                    </th>

                                    <th >
                                        <span v-if="index!=2">
                                            <select v-model="item.command" style="width: 120px">
                                                <option v-for="(sItem, index) in farmScriptList" :value="sItem.set.id">
                                                    @{{ sItem.set.name }}
                                                </option>
                                            </select>
                                        </span>
                                        <span v-if="index==2">
                                            不指定腳本
                                        </span>
                                    </th>
                                    <th>
                                        <!--<button type="button" class="btn btn-primary btn-sm">
                                            <i class="fa fa-edit"></i>
                                        </button>-->
                                    </th>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                        <div class="mb-1 mr-2">
                            <form method="post" action="editFarmSetting" id="editFarmCommandsSetting">
                                <input type="hidden" name="device_id" v-model="device.id"/>
                                <input type="hidden" name="set" v-model="farmSizeStr"/>
                                <input type="hidden" name="field" value="farm_commands"/>
                                {{csrf_field()}}

                                <button type="button" class="button btn-primary btn-block" @click="saveSetting('editFarmCommandsSetting');">
                                    <i class="fa fa-database"></i> 儲存
                                </button>
                            </form>
                        </div>
                    </div>
                    <!-- Sensor trigger setting-->
                    <div v-if="settingTab==6">
                        <div class="mb-1 text-left">
                            <span class="block-title mr-2">上報觸發通知設定</span>
                            <span v-show="isEditTrigger==0">
                                <button type="button" class="btn btn-success btn-sm" @click="addTrigger();"><i class="fa fa-plus"></i></button>
                            </span>
                        </div>
                        <div class="mb-1">
                            觸發時以電子信箱及Line Notify通知
                        </div>

                        <div v-if="isEditTrigger==0" class="row mb-1 text-left">
                            <div class="col-12">
                                <span class="block-title">觸發列表</span>
                                滑鼠移到列表點選進行編輯或刪除
                            </div>

                            <div class="col-12 mt-3">
                                <table Border="1"  class="text-left" style="width:100%">
                                    <tbody >
                                    <tr>
                                        <td width="10%">No</td>
                                        <td width="30%">感測</td>
                                        <td width="20%">欄位</td>
                                        <td width="20%">操作</td>
                                        <td width="20%">數值</td>
                                    </tr>
                                    <tr v-for="(item, index) in triggerList" @click="editTrigger(index)"  onmouseover="toHighlight(this);" onmouseout="restore(this);">
                                        <th >
                                            @{{ index+1 }}
                                        </th>

                                        <th >
                                            @{{ item.name }}
                                        </th>
                                        <th >
                                            @{{ item.field}}
                                        </th>
                                        <th >
                                            <select v-cloak v-model="item.operator" name="operator" disabled>
                                                <option v-for="operator in operatorList" :value="operator.id">
                                                    @{{ operator.value }}
                                                </option>
                                            </select>
                                        </th>
                                        <th >
                                            @{{ item.value}}
                                        </th>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>
                            <div class="col-11 mt-3">
                                <button type="button" class="button btn-primary btn-block" @click="saveSetting('editFarmTriggerSetting');">
                                    <i class="fa fa-database"></i> 儲存
                                </button>
                            </div>
                            <form method="post" action="editFarmSetting" id="editFarmTriggerSetting">
                                <input type="hidden" name="device_id" v-model="device.id"/>
                                <input type="hidden" name="set" v-model="farmSizeStr"/>
                                <input type="hidden" name="field" value="sensor_trigger"/>
                                {{csrf_field()}}
                            </form>

                        </div>

                        <div v-if="isEditTrigger>0" class="mb-1">

                            <div class="row" >
                                <div v-cloak v-if="appList.length==0">
                                    <div class="alert alert-danger" role="alert">
                                        尚未設定上報感測資料欄位
                                    </div>
                                </div>

                                <div v-for="(target,index) in appList" class="input-group rom text-left">

                                    <div class="block-title col-12">@{{ target.name }}</div>

                                    <div class="col-3 mt-1">

                                        選擇觸發
                                    </div>
                                    <div class="col-7">

                                        <select  v-model="trigger.field" class="custom-select" @change="changeTriggerObject(target.sequence, $event)">
                                            <option v-for="(item, key, index) in label[target.sequence]" :value="key" :key="key">
                                                @{{ item }} @{{ key }}
                                            </option>
                                        </select>

                                    </div>
                                    <div class="col-2">
                                        <!--<button type="button" class="btn btn-success"><i class="fa fa-plus"></i></button>-->
                                    </div>
                                    <div class="col-12 mt-3">
                                        <div class="form-row">
                                            <div class="input-group mb-1 col-12">
                                                <label class="input-group-text">感測</label>
                                                <input type="text" class="form-control" v-model="trigger.name" name="name" disabled>
                                            </div>
                                            <div class="input-group mb-1 col-6">
                                                <label class="input-group-text">欄位</label>
                                                <input type="text" class="form-control" v-model="trigger.field" name="name" disabled>
                                            </div>
                                            <div class="input-group mb-1 col-6">

                                                <select v-cloak v-model="trigger.operator" name="operator">
                                                    <option v-for="operator in operatorList" :value="operator.id">
                                                        @{{ operator.value }}
                                                    </option>
                                                </select>

                                                <input type="text" class="form-control" v-model="trigger.value" name="name">
                                            </div>
                                            <div class="input-group mb-3 col-12">
                                                <label class="input-group-text">訊息</label>
                                                <input type="text" class="form-control" v-model="trigger.message" name="name">
                                            </div>
                                            <div class="input-group mb-1 col-12">
                                                <button v-if="isEditTrigger==2" type="button" class="btn btn-danger mr-2" @click="delTrigger();">
                                                    <i class="fa fa-trash"></i>刪除
                                                </button>
                                                <button type="button" class="btn btn-secondary mr-2" @click="isEditTrigger=0;">
                                                    X 取消
                                                </button>
                                                <button type="button" class="btn btn-primary mr-2" @click="checkTrigger();">
                                                    <i class="fa fa-pen"></i>確定
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-1 mr-2">

                        </div>
                    </div>

                </div>
                <!-- Farm script setting-->
                <div v-if="settingTab==5  && !isEditCodeStruct" class="codeListBlock row" >
                    <div class="mb-1 text-left">
                        <span class="block-title mr-2">腳本設定</span>
                        <span class="mr-1">
                            <input type="checkbox" v-model="isCodeEditHelp" @change="onChangeEditHelp(this);">
                            命令編輯輔助
                        </span>
                        <div v-cloak v-if="codeMessage.length>0" class="text-danger">
                            @{{ codeMessage }}
                        </div>
                    </div>

                    <div class="mb-1 text-left">

                        <!-- Script list -->
                        <span v-show="farmScriptList.length>0">
                            <span class="mr-2">選單</span>
                            <select v-model="farmScriptIndex" @change="onChangeScript($event)" >
                                <option v-for="(item, index) in farmScriptList" :value="index" >
                                    @{{ item.set.name }}
                                </option>
                            </select>
                        </span>

                        <!-- 新增命令腳本 -->
                        <span v-if="!isNewScript && farmScriptList.length>0" class="mr-1">
                            <button type="button" class="btn btn-success btn-sm" @click="newScript();">
                                <i class="fa fa-plus"></i>腳本
                            </button>
                        </span>
                        <!-- 取消新增-->
                        <span v-if="isNewScript" class="mr-1">
                            <button type="button" class="btn btn-secondary btn-sm" @click="cancelNewScript();">
                                <i class="fa fa-window-close"></i>取消新增
                            </button>
                        </span>
                        <!-- 刪除命令腳本 -->
                        <span v-if="farmScriptList.length>0 && !isNewScript" class="mr-1">
                            <button type="button" class="btn btn-danger btn-sm" @click="checkDeleteFarmScript('deleteFarmSetting');">
                                <i class="fa fa-trash"></i>腳本
                            </button>
                        </span>
                        <form method="post" action="deleteFarmSetting" id="deleteFarmSetting">
                            <input type="hidden" name="_method" value="delete" />
                            <input type="hidden" name="id" v-model="farmScript.id"/>
                            {{csrf_field()}}
                        </form>

                    </div>

                    <div class="mb-1">
                        <hr>
                        <div class="mb-2 text-left">
                            <span class="font-weight-bold">名稱</span>
                            <input type="text" v-model="farmScript.set.name" size="10"  maxlength="10" />
                            <!--<span class="font-weight-bold ml-2">識別碼</span>
                            <input type="text" v-model="farmScript.set.id"  size="10" maxlength="10" />-->


                        </div>
                        <!-- 命令編輯列表-->
                        <div v-if="isCodeEditHelp">
                            <div class="mb-1 text-left">
                                <span class="block-title mr-1">G code列表</span>
                                移動到列表點擊後編輯
                                <button v-if="isCodeEditHelp" type="button" class="btn btn-success btn-sm" @click="newStruct();">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                            <div class="codeTableBlock">
                                <table Border="1"  class="text-left" style="width:100%">
                                    <tbody >

                                    <tr v-for="(item, index) in farmScript.set.codeList" @click="editCodeStruct(index)"  onmouseover="toHighlight(this);" onmouseout="restore(this);">
                                        <th width="10%">
                                            @{{ index+1 }}
                                        </th>

                                        <th width="90%">
                                            @{{ item }}
                                        </th>
                                    </tr>

                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <!-- 文字編輯區塊-->
                        <div v-if="!isCodeEditHelp">
                            <textarea v-model="editText" name="story" rows="30" cols="40">
                            </textarea>
                        </div>

                    </div>

                    <div class="mt-2 mb-1 mr-2">
                        <form method="post" action="editFarmSetting" id="editFarmScriptSetting">
                            <input type="hidden" name="device_id" v-model="device.id"/>
                            <input type="hidden" name="id" v-model="farmScript.id"/>
                            <input type="hidden" name="set" v-model="farmSizeStr"/>
                            <input type="hidden" name="field" value="farm_script"/>
                            {{csrf_field()}}

                            <button type="button" class="button btn-primary btn-block" @click="saveSetting('editFarmScriptSetting');">
                                <i class="fa fa-database"></i> 儲存腳本設定
                            </button>
                        </form>
                    </div>
                </div>
                <!-- Script code setting-->
                <div v-if="settingTab==5 && isEditCodeStruct" class="codeListBlock row" >
                    <div class="mb-1 text-left">
                        <span class="block-title mr-2">G code編輯</span>
                        按確定命令自動轉成大寫
                    </div>
                    <div class="input-group mb-1">
                        <div class="input-group-append">
                            <span class="input-group-text" >命令</span>
                        </div>
                        <input id="codeStr" type="text" class="form-control" v-model="codeStr"/>
                    </div>
                    <!-- Code style list -->
                    <div v-if="isShowCodeStyle==true" class="input-group mb-1">
                        <div class="input-group-append">
                            <span class="input-group-text" >選擇</span>
                        </div>
                        <select id="codeStyleOption" v-model="selectedCodeStyle" class="form-control" @change="onChangeStyle($event)" >
                            <option v-for="(item,index) in codeStyleList" :value="item.value" :disabled="index==0">
                                @{{ item.name }}
                            </option>
                        </select>
                    </div>
                    <!-- Position list -->
                    <div v-if="isShowPosition==true" class="input-group mb-1">
                        <div class="input-group-append">
                            <span class="input-group-text" >選擇</span>
                        </div>
                        <select id="positionOption" v-model="selectedCodeStyle" class="form-control" @change="onChangePosition($event)" >
                            <option v-for="(item,index) in positionList" :value="item.value" :disabled="index==0">
                                @{{ item.name }}
                            </option>
                        </select>
                    </div>
                    <div v-if="codeStruct !=null" class="form-row align-items-center" style="width:100%; font-size: 1em;">

                        <div v-if="codeStruct.g_code !=null" class="col-6 input-group mb-1">
                            <div class="input-group-append">
                                <span class="input-group-text" >G code</span>
                            </div>
                            <input type="text" class="form-control" v-model="codeStruct.g_code" disabled/>
                        </div>
                        <div v-if="codeStruct.f !=null" class="col-6 input-group mb-1">
                            <div class="input-group-append">
                                <span class="input-group-text" >速度</span>
                            </div>
                            <input type="text" class="form-control" v-model="codeStruct.f" disabled/>
                        </div>
                        <div v-if="codeStruct.x !=null" class="col-6 input-group mb-1">
                            <div class="input-group-append">
                                <span class="input-group-text" >X軸</span>
                            </div>
                            <input type="text" class="form-control" v-model="codeStruct.x" disabled/>
                        </div>
                        <div v-if="codeStruct.y !=null" class="col-6 input-group mb-1">
                            <div class="input-group-append">
                                <span class="input-group-text" >Y軸</span>
                            </div>
                            <input type="text" class="form-control" v-model="codeStruct.y" disabled/>
                        </div>
                        <div v-if="codeStruct.z !=null" class="col-6 input-group mb-1">
                            <div class="input-group-append">
                                <span class="input-group-text" >Z軸</span>
                            </div>
                            <input type="text" class="form-control" v-model="codeStruct.z" disabled/>
                        </div>
                    </div>

                    <div class="mt-3 mb-1 mr-2">
                        <form method="post" action="editFarmSetting" id="editFarmHomeSetting">
                            <input type="hidden" name="device_id" v-model="device.id"/>
                            <input type="hidden" name="set" v-model="farmSizeStr"/>
                            <input type="hidden" name="field" value="farm_home"/>
                            {{csrf_field()}}
                        </form>
                        <button v-if="structIndex>-1" type="button" class="button btn-danger" @click="delStruct();">
                            <i class="fa fa-trash"></i> 刪除
                        </button>
                        <button type="button" class="button btn-secondary" @click="cancelStruct();">
                            X 取消
                        </button>

                        <button type="button" class="button btn-primary" @click="saveStruct();">
                            <i class="fa fa-pen"></i> 確定
                        </button>
                    </div>
                </div>


            </div>

        </div>

        <!-- Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="font-size: 20px;">
                        <span v-if="modalIndex==1" class="modal-title" id="myModalLabel">植栽資料</span>
                        <span v-if="modalIndex==2" class="modal-title" id="myModalLabel">菜種列表</span>
                        <span v-if="modalIndex==3" class="modal-title" id="myModalLabel">影像資料</span>
                        <span v-if="modalIndex==2" class="float-right">
                            <button v-if="modalIndex==2" type="button" class="btn btn-success" @click="setKind();">
                                {{__('layout.add')}}
                            </button>
                        </span>
                    </div>
                    <div class="modal-body">
                        <!-- 1. Set plant-->
                        <div v-if="modalIndex==1">
                            <div class="form-row align-items-center">
                                <div class="col-12 input-group mb-2">
                                    <div class="input-group-append">
                                        <span class="input-group-text" >名稱</span>
                                    </div>
                                    <input type="text" class="form-control" v-model="plant.title" />
                                </div>
                                <div class="col-6 input-group mb-2">

                                    <div class="input-group-append">
                                        <span class="input-group-text" >種植類型</span>
                                    </div>
                                    <input type="text" class="form-control" v-model="plant.kind" />
                                </div>
                                <div class="col-6 input-group mb-2">

                                    <div class="input-group-append">
                                        <span class="input-group-text" >可採收天數</span>
                                    </div>
                                    <input type="text" class="form-control" v-model="plant.maturity" />
                                </div>
                                <div class="col-6 input-group mb-2">

                                    <div class="input-group-append">
                                        <span class="input-group-text" >上架日</span>
                                    </div>
                                    <input type="text" class="form-control" v-model="plant.start_time"/>
                                </div>
                                <div class="col-6 input-group mb-2">

                                    <div class="input-group-append">
                                        <span class="input-group-text" >採收日</span>
                                    </div>
                                    <input type="text" class="form-control" v-model="plant.crop_time"/>
                                </div>
                                <div class="col-6 input-group mb-2">

                                    <div class="input-group-append">
                                        <span class="input-group-text" >澆水時間</span>
                                    </div>
                                    <input type="text" class="form-control" v-model="plant.watering_time" disabled/>
                                </div>
                                <div class="col-6 input-group mb-2">

                                    <div class="input-group-append">
                                        <span class="input-group-text" >施肥時間</span>
                                    </div>
                                    <input type="text" class="form-control" v-model="plant.muck_time" disabled/>
                                </div>

                                <div class="col-6 input-group mb-2">

                                    <div class="input-group-append">
                                        <span class="input-group-text" >箱子行</span>
                                    </div>
                                    <input type="text" class="form-control" v-model="plant.box.x" size="15" maxlength="15" disabled />
                                </div>
                                <div class="col-6 input-group mb-2">

                                    <div class="input-group-append">
                                        <span class="input-group-text" >箱子列</span>
                                    </div>
                                    <input type="text" class="form-control" v-model="plant.box.y" size="15" maxlength="15" disabled />
                                </div>
                                <div class="col-6 input-group mb-2">

                                    <div class="input-group-append">
                                        <span class="input-group-text" >植栽行</span>
                                    </div>
                                    <input type="text" class="form-control" v-model="plant.plant.x" size="15" maxlength="15" disabled />
                                </div>
                                <div class="col-6 input-group mb-2">

                                    <div class="input-group-append">
                                        <span class="input-group-text" >植栽列</span>
                                    </div>
                                    <input type="text" class="form-control" v-model="plant.plant.y" size="15" maxlength="15" disabled />
                                </div>
                                <div class="col-6 input-group mb-2">

                                    <div class="input-group-append">
                                        <span class="input-group-text" >key </span>
                                    </div>
                                    <input type="text" class="form-control" v-model="plant.plant_key" disabled/>
                                </div>

                            </div>
                        </div>
                        <!-- 2. Show kind list-->
                        <div v-if="modalIndex==2">
                            <div class="form-row align-items-center">
                                <table id ="table1"  class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>項目</th>
                                        <th>名稱</th>
                                        <th>控制</th>
                                        <th>顏色</th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-if="index!=0" v-for="(item, index) in kindObject">
                                        <th>@{{ index }}</th>
                                        <th>@{{ item.name }}</th>
                                        <th>@{{ item.key }}</th>
                                        <th >
                                            <span type="text" :style="item.colorBlock" class="form-control" >
                                                @{{ item.color }}
                                            </span>
                                        </th>
                                        <th>
                                            <button type="button" class="button" style="color: #007bff" @click="setKind(index);">
                                                <i class="fas fa-pen fa-fw"></i>
                                            </button>
                                            <button type="button" class="button" style="color: #ff0048" @click="delKind(index);">
                                                <i class="fas fa-trash fa-fw"></i>
                                            </button>
                                        </th>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                        <!-- 3. Video data-->
                        <div v-if="modalIndex==3" class="form-row align-items-center">

                                <div class="col-12 input-group mb-2">
                                    <div class="input-group-append">
                                        <span class="input-group-text" >名稱</span>
                                    </div>
                                    <input type="text" class="form-control" v-model="element.name" />
                                </div>
                                <div class="col-12 input-group mb-2">

                                    <div class="input-group-append">
                                        <span class="input-group-text" >推播位址</span>
                                    </div>
                                    <input id="rtmp_url" type="text" class="form-control" v-model="element.rtmp" disabled/>

                                </div>

                                <div class="col-12 input-group mb-2">
                                    <button class="btn btn-outline-primary" type="button" @click="copyUrl();">
                                        <i class="fas fa-copy"></i>  複製推播位址到剪貼簿
                                    </button>
                                </div>


                        </div>
                    </div>
                    <div class="modal-footer">
                        <!-- modalIndex:1=>plant data , 2=>kind list  -->
                        <button v-if="modalIndex == 1" type="button" class="btn btn-default"
                                data-dismiss="modal">{{__('layout.cancel')}}
                        </button>
                        <button v-if="modalIndex == 2" type="button" class="btn btn-default"
                                data-dismiss="modal" @click="cancelEditKindList();">
                            {{__('layout.cancel')}}
                        </button>

                        <button v-if="modalIndex == 1" type="button" @click="toSavePlant()" class="btn btn-primary">
                            {{__('layout.yes')}}
                        </button>
                        <button v-if="modalIndex == 2" type="button" @click="toSaveAllKinds()" class="btn btn-primary">
                            {{__('layout.yes')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal2 -->
        <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content" >
                    <div class="modal-header" style="font-size: 20px;">
                        <span v-if="modalIndex==2" class="modal-title" id="myModalLabel">編輯菜種</span>
                        <span v-if="modalIndex==4" class="modal-title" id="myModalLabel">編輯@{{ set.name }}</span>

                    </div>
                    <div class="modal-body">
                        <!-- 3. Edit kind-->
                        <div v-show="modalIndex==2">
                            <div class="form-row align-items-center">
                                <div class="col-6 input-group mb-2">
                                    <div class="input-group-append">
                                        <span class="input-group-text" >菜種名稱</span>
                                    </div>
                                    <!--<input type="text" class="form-control" v-model="kindOption.name" />-->
                                    <select v-model="selectedKind.name" class="form-control" @change="onChangeOption($event)" >
                                        <option v-for="kindOption in kindOptionList" :value="kindOption.name" >
                                            @{{ kindOption.name }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-6 input-group mb-2">

                                    <div class="input-group-append">
                                        <span class="input-group-text" >菜種類型
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" v-model="selectedKind.key"  disabled />
                                </div>
                                <div class="col-6 input-group mb-2">

                                    <div class="input-group-append">
                                        <span class="input-group-text" >
                                            菜種顏色
                                        </span>
                                    </div>
                                    <input :style="selectedKind.colorBlock" id="demo-input" type="text" class="form-control" v-model="selectedKind.color" />
                                </div>
                                <div class="col-6 input-group mb-2">

                                    <div class="input-group-append">
                                        <span class="input-group-text" >可採收天數
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" v-model="selectedKind.maturity"  disabled />
                                </div>


                            </div>
                        </div>
                        <div v-show="modalIndex==4">
                            <div class="form-row align-items-center">
                                <div class="col-10 input-group mb-2">
                                    <div class="input-group-append">
                                        <span class="input-group-text" >執行時間(秒)</span>
                                    </div>
                                    <input type="number" class="form-control" v-model="set.duration" :min="duration_min" :max="duration_max" />
                                    <span class="text-info ml-2 mt-2">範圍: @{{ duration_min }} ~ @{{ duration_max }} 秒</span>
                                </div>
                            </div>
                        </div>
                        <div v-if="modalIndex==4">
                            <div class="form-row align-items-center">
                                <div class="col-10 input-group mb-2">
                                    <div class="input-group-append">
                                        <span class="input-group-text" >每日定時操作時間</span>
                                    </div>
                                    <input type="text" class="form-control" v-model="set.time" />
                                    <span class="text-info ml-2 mt-2">範例: 07:00</span>
                                </div>
                            </div>
                        </div>
                        <span v-if="modalIndex==4" class="text-info">
                            設定定時操作時間，才會對農業機器人作定時設定
                        </span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal" @click="cancelEditKind();">
                            {{__('layout.cancel')}}
                        </button>

                        <button v-if="modalIndex==2" type="button" @click="toSaveKind()" class="btn btn-primary">
                            {{__('layout.yes')}}
                        </button>
                        <button v-if="modalIndex==4" type="button" @click="toSaveSet()" class="btn btn-primary">
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
                let apps = {!! $apps !!};
                let device = {!! $device !!};
                let user = {!! $user !!};
                let statusObj = {!! json_encode($status) !!};
                let app_url = '{{ env('APP_URL') }}';
                let token = '{{$user->remember_token}}';
                let farm_size_set = {!! json_encode($setting['farm_size_set']) !!};
                let farm_home_set = {!! json_encode($setting['farm_home_set']) !!};
                let farm_plate_set = {!! json_encode($setting['farm_plate_set']) !!};
                let farm_script_set = {!! json_encode($setting['farm_script_set']) !!};
                let farm_script_empty = {!! json_encode($setting['farm_script_empty']) !!};
                let farm_commands_set = {!! json_encode($setting['farm_commands_set']) !!};
                @if($setting['trigger_set'] == null)
                let trigger_set = [];
                @else
                let trigger_set = {!! json_encode($setting['trigger_set']) !!};
                @endif

                let api_url = '{!! env('API_URL') !!}';
                @if($plant_kinds_set == null)
                    let plant_kinds = null;
                @else
                    let plant_kinds = {!! json_encode($plant_kinds_set) !!};
                @endif
                let farmObject = {!! json_encode($farmObject) !!};

                //alert(cmd);
            </script>

            <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
            <script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script>
            <script src="{{asset('js/option/tools.js')}}" crossorigin="anonymous"></script>
            <script src="{{asset('vender/flvjs/flv.js')}}" crossorigin="anonymous"></script>
            <script src="{{asset('js/option/flvTools.js')}}" crossorigin="anonymous"></script>
            <script src="{{asset('js/room/farm/index.js')}}" crossorigin="anonymous"></script>

@endsection
