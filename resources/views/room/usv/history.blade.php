@extends('Layout.room')

@section('content')
    <div class="room_header row">
        <!--<span class="ml-3"> 現在位置：</span>-->
        <div class="col-md-6 col-lg-5">

            <span class="breadcrumb-item">
                @if($url != null)
                    <a href="{{$url}}">返回</a>
                @else
                    <a href="javascript:history.back()" onclick="self.location=document.referrer;">返回</a>
                @endif

            </span>
            <span> / </span>
            <span class="breadcrumb-item">
                GPS歷史紀錄
            </span>
            @if($user->role_id<8)
            <span class="ml-2">
                選擇裝置:
                <select onchange="location.href=this.options[this.selectedIndex].value">
                    @foreach ($devices as $item)
                        @if ($item->id == $device_id)
                            <option value="{{$item->id}}" selected="selected">{{$item->device_name}}</option>
                        @else
                            <option value="{{$item->id}}">{{$item->device_name}}</option>
                        @endif
                    @endforeach
                </select>
            </span>
                @endif
        </div>


        <div class="col-md-6 col-lg-3">


        </div>

        <div class="col-md-6 col-lg-4 ">
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

    <div class="row main-content mt-1">
        <div class="col-lg-9 mb-2">
            <div id="map" style="height: 500px;width: 100%;"></div>
        </div>
        <div v-cloak class="col-lg-3 text-center">
            <div class="mapBlock row" >
                <div v-show="isTimeSelect==false" class="col-12 btn-group btn-group-toggle mb-1">
                    <div  class="input-group input-daterange">
                        <label class="mr-1 mt-1">起始</label>
                        <input id="start" name="start" type="text" class="form-control" value="" maxlength="18" size="18">
                        <span class="input-group-addon ml-1 mt-1">
                            <!--<span class="fa fa-calendar fa-2x"></span>-->
                        </span>
                    </div>
                </div>
                <div class="col-12 btn-group btn-group-toggle mb-1">
                    <div  class="input-group input-daterange">
                        <label class="mr-1 mt-1">
                           <span v-if="isTimeSelect==false"> 結束 </span>
                           <span v-else> 時間 </span>
                        </label>
                        <input id="end" name="end" type="text" class="form-control" value="" maxlength="18" size="18">
                        <span class="input-group-addon ml-1 mt-1">
                        <!--<span class="fa fa-calendar fa-2x"></span>-->
                        </span>
                    </div>
                </div>
                <div class="col-12 mb-1">
                    <div  class="input-group input-daterange">
                        <label class="mr-1 mt-1">往前</label>
                        <select v-model="timeOption">
                            <option v-for="time in timeList" :value="time" class="form-control">
                                @{{ time }} 小時
                            </option>
                        </select>

                        <span class="ml-2 mt-2">
                            @{{ notifyMessage }}
                        </span>

                        <span v-if="skipList.length>1" class="mr-3">
                            <label class="mt-1">選擇</label>
                            <select v-model="skip" @change="changeSkip($event)">
                                <option v-for="item in skipList" :value="item.value" class="form-control">
                                    @{{ item.title }}
                                </option>
                            </select>
                            <label class="mr-3 mt-1">筆記錄</label>
                        </span>


                    </div>
                </div>



                <div class="col-12 mb-1">

                    <button type="button" class="btn btn-primary mb-1" @click="search()">
                        搜尋紀錄
                    </button>
                    @if($user->role_id<8)
                    <button type="button" class="btn btn-danger mb-1" @click="deleteCheck()">
                        刪除紀錄
                    </button>
                    @endif
                </div>


                <div v-if="cmdMessage.length>0" class="alert alert-success" role="alert">
                    @{{ cmdMessage }}
                </div>
            </div>

            <div class="mapBlock row" >
                <div v-if="isMeasure==false" class="mb-1">

                    <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearReports()">
                        清除記錄<!--清上報點-->
                    </button>

                    <button type="button" class="btn btn-outline-primary btn-sm" @click="measureTool()">
                        量測距離
                    </button>
                </div>

                <div v-show="isMeasure==true">
                    <button type="button" class="btn btn-outline-danger btn-sm" @click="clearLine()">
                        清除測量線
                    </button>
                    <button v-if="isMeasure" type="button" class="btn btn-outline-dark btn-sm" @click="isMeasure=false;">
                        返回
                    </button>
                </div>
            </div>

            <!--1. 搜尋紀錄 -->
            <div v-show="tab==1">
                <!-- -->
                <div v-show="!isMeasure" class="searchListBlock row">
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


                                <tr v-for="(item, index) in searchList" @click="showInfo(index)" :style="item.data" @mouseover="highlight(index);" @mouseout="restoreColor(index); ">
                                    <td >@{{ index+1 }}</td>
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
                                <tr v-cloak v-for="(item, index) in measureList">

                                    <td >@{{ index+1 }}</td>
                                    <td >
                                        <input type="text" style="font-size:12px" class="form-control" v-model="item.lat" aria-describedby="basic-addon1" maxlength="10" size="10" >
                                    </td>
                                    <td >
                                        <input type="text" style="font-size:12px" class="form-control" v-model="item.lng" aria-describedby="basic-addon1" maxlength="10" size="10" >
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
            <!-- Report -->
            <div v-if="tab==2">
                <div class="mapBlock row" >
                    <div v-for="(target,index) in appList" class="input-group mb-2">
                        <div>
                            <span >
                            <span>@{{ target.name }}</span>
                        </span>
                            <span class="ml-3">
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
            <!-- Modal -->
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
                            刪除選擇範圍內紀錄?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{__('layout.cancel')}}
                            </button>
                            <button type="button" @click="removeReport()" class="btn btn-danger" >
                                {{__('layout.yes')}}
                            </button>
                        </div>
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
                let point_url = "{!! url('/Images/point.png')!!}";
                let diamond_url = "{!! url('/Images/diamond.png')!!}";
                let app_url = '{{ env('APP_URL') }}';
                let api_url = '{{ env('API_URL') }}';
                let center_index = {!! $center_index !!};
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
                let cmd = app_url+'/reports/search?macAddr='+device.macAddr;
                let token = '{{$user->remember_token}}';
                //alert(cmd);
            </script>
            <script async defer
                    src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP_KEY')}}">
            </script>
            <script src="{{asset('vender/jQueryRotate/jQueryRotate.2.1.js')}}" crossorigin="anonymous"></script>
            <script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script>
            <script src="{{asset('js/room/usv/work_alg.js')}}"></script>
            <script src="{{asset('js/option/tools.js')}}" crossorigin="anonymous"></script>
            <script src="{{asset('js/room/usv/history.js')}}"></script>

@endsection
