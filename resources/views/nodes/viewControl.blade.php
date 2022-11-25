@extends('Layout.diy')

@section('content')
    <div class="breadcrumb">
        <div class="col-md-4 mt-1">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:history.back()" onclick="self.location=document.referrer;">回上一頁</a></li>
                <li class="breadcrumb-item active" aria-current="page">影像監控</li>
            </ol>
        </div>
        <div class="col-md-5">

            <button class="btn btn-default" @click="addVideo()">
                影像 <i class="fa fa-plus-circle"></i>
            </button>

        </div>
        <div class="col-md-3 mt-2 text-left">
            @if($devices != null)
                {{__('layout.devices') }}
                <select onchange="location.href='map?mac='+this.options[this.selectedIndex].value" disabled>
                    @foreach ($devices as $device)
                        @if ($device->id == $device_id)
                            <option value="{{$device->id}}" selected="selected">{{$device->device_name}}</option>
                        @else
                            <option value="{{$device->id}}">{{$device->device_name}}</option>
                        @endif
                    @endforeach
                </select>
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

    <div class="row main-content mt-1">
        <div class="col-lg-9">
            <div v-cloak class="row justify-content-center">

                @if($video_setting!=null && count($video_setting->set)==1)
                    <div v-for="(element, index) in videoList" class="col-12">
                        <div class="url-input">
                            <span class="form-control">
                                <label>@{{ element.name }}</label>

                                <button class="float-right btn btn-default btn-sm" @click="editVideo(index)">
                                    <i class="fa fa-pen"></i>
                                </button>
                            </span>

                            <input :id="element.id" type="hidden" v-model="element.url" style="width: 90%;"/>

                        </div>
                        <div class="videoBlock">

                            <video :name="element.id+'test'" style="width: 100%; height: 500px;" controls autoplay muted>
                            </video>
                        </div>
                    </div>
                @else
                    <div v-for="(element, index) in videoList" class="col-6">
                        <div class="url-input">
                        <span class="form-control">
                            <label>@{{ element.name }}</label>

                            <button class="float-right btn btn-default btn-sm" @click="editVideo(index)">
                                <i class="fa fa-pen"></i>
                            </button>
                        </span>

                            <input :id="element.id" type="hidden" v-model="element.url" style="width: 90%;"/>

                        </div>
                        <div class="videoBlock">

                            <video :name="element.id+'test'" style="width: 100%; height: 300px;" controls autoplay muted>
                            </video>
                        </div>
                    </div>

                    <div v-if="videoList.length == 0">
                        <h1>
                            <!--<label class="text-danger">尚未加入監控影像</label>-->
                        </h1>
                        <img id="img_fan" src="{{url('/Images/no_video.png')}}" width="100%">
                    </div>
                @endif


            </div>
        </div>
        <div v-cloak class="col-lg-3 text-center">
            <!-- 切換頁籤 -->
            <div id="timeselector" class="btn-group btn-group-toggle mb-1" data-toggle="buttons">
                <label class="btn btn-secondary active">
                    <input type="radio" name="options" id="1" autocomplete="off" >控制面板
                </label>
                <label class="btn btn-secondary ">
                    <input type="radio" name="options" id="2" autocomplete="off" checked>無人船訊息
                </label>
                <label class="btn btn-secondary">
                    <input type="radio" name="options" id="3" autocomplete="off">航向訊息
                </label>
            </div>
            <!--1. 地圖 & 控制按鍵 -->
            <div v-show="tab==1">
                <div class="mapBlock">
                    <div class="mb-1">
                        <div id="map" style="height: 220px;width: 100%;"></div>
                    </div>
                </div>
                <div class="mapBlock">
                    <div>
                        <div class="mb-1">
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearReports()">
                                清上報點
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm" onClick="load()">
                                影像重載入
                            </button>
                            <!--<button type="button" class="btn btn-secondary btn-sm" @click="editVideo()">
                                影像 <i class="fa fa-pen"></i>
                            </button>-->
                        </div>
                    </div>
                </div>
                <div class="mapBlock">
                    <!-- 控制按鍵 -->
                    <div class="card">
                        <div class="card-header">
                            <label>控制按鍵</label>
                            @if($user->role_id<3)
                            <span class="float-right">
                                <button type="button" class="btn btn-secondary btn-sm" @click="editBtn()">
                                    按鍵 <i class="fa fa-pen"></i>
                                </button>
                            </span>
                            @endif
                        </div>
                        <div class="card-body">
                            <!-- command button -->

                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label >左馬達動力</label>
                                    <input type="number" v-model="key2" min="0" max="1023" >
                                </div>
                                <div class="mb-3 col-md-6">

                                    <label>右馬達動力</label>

                                    <input type="number" v-model="key1" min="0" max="1023">
                                </div>
                                <div v-for="(item, index) in btnList" class="col-4 mb-2">
                                    <button type="button" class="btn btn-primary btn-sm" @click="toCmd(index)" :disabled="item.id==0 || isSendCmd!=false">
                                        @{{ item.name }}
                                    </button>
                                </div>
                            </div>
                            <!-- command message -->
                            <div >
                                <label class="text-info mt-3">@{{ cmdMessage }}</label>
                            </div>
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
                        <table class="text-center"  style="font-size:1em;width:100%;">
                            <tr>
                                <td colspan="2"></td>
                                <!-- 前距 -->
                                <td>@{{ label[1].key1 }}</td>
                                <td >
                                    <!-- left_status -->
                                    <!--<span v-if="status[1].key1>0 && status[1].key1<100">
                                        <button type="button" class="btn btn-danger btn-sm">@{{status[1].key1}} 公分</button>
                                    </span>-->
                                    <span v-if="status[1].key1>0 && status[1].key1<800">
                                        <button type="button" class="btn btn-danger btn-sm">障礙物</button>
                                    </span>
                                    <span v-else>
                                        <button type="button" class="btn btn-success btn-sm">良好</button>
                                    </span>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                            <!-- boat image -->
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
                            <tr>
                                <td >動力</td>
                                <td > @{{status[2].key1}} %</td>
                                <td >動力</td>
                                <td > @{{status[2].key4}} %</td>
                            </tr>
                            <tr>
                                <td >電流</td>
                                <td > @{{status[2].key2}} A</td>
                                <td >電流</td>
                                <td > @{{status[2].key5}} A</td>
                            </tr>
                            <tr>
                                <td >溫度</td>
                                <td > @{{status[2].key3}} 度</td>
                                <td >溫度</td>
                                <td > @{{status[2].key6}} 度</td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                                <!-- 後距 -->
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
                                <td colspan="2"></td>
                            </tr>

                        </table>

                        <!-- 電力 -->
                        <table class="text-center" style="width:100%;">
                            <thead>
                            <tr>
                                <th width="20%"></th>
                                <!-- 左電力 -->
                                <th width="40%">@{{ label[1].key5 }}</th>
                                <!-- 右電力 -->
                                <th width="40%">@{{ label[1].key6 }}</th>
                            </tr>

                            </thead>
                            <tbody >
                            <tr >
                                <td >船艙</td>
                                <td >
                                    <!-- battery_temperature -->
                                    <span class="form-control" style="font-size:12px"> @{{ status[1].key5 }} 度</span>
                                </td>
                                <td >
                                    <!-- battery_humidity -->
                                    <span class="form-control" style="font-size:12px"> @{{ status[1].key6 }} %</span>
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
            <!-- edit setting form -->
            <form method="post" action="editSetting" id="editSetting">
                <input type="hidden" name="_method" value="put" />
                <input type="hidden" name="id" v-model="setting.id" />
                <input type="hidden" name="app_id" v-model="setting.app_id"/>
                <input type="hidden" name="field" v-model="setting.field"/>
                <input type="hidden" name="set" v-model="setting.set"/>
                <input type="hidden" name="device_id" value="{{$device_id}}"/>
                {{csrf_field()}}
            </form>
        </div>
        <!--Btn Modal -->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                <span>
                    <h4 class="modal-title" id="myModalLabel">設定控制按鍵</h4>
                </span>
                <span v-if="btnList.length>1" class="mt-2">
                    <select v-model="btnIndex" @change="onChangeBtn($event)">

                        <option v-for="(item, index) in btnList" :value="index" :key="index" >
                            @{{ item.name }}
                        </option>
                    </select>
                </span>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <div class="form-row align-items-center">
                                <div class="col-6 input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text" >名稱</span>
                                    </div>
                                    <input type="text" class="form-control" v-model="myBtn.name" >
                                </div>
                                <div class="col-6 input-group mb-3">

                                    <div class="input-group-append">
                                        <span class="input-group-text" >命令ID</span>
                                    </div>
                                    <input type="number" class="form-control" v-model="myBtn.id" disabled>
                                </div>
                                <div class="col-12 input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text" >命令</span>
                                    </div>
                                    <select v-model="myBtn.id" @change="onChangeCommand($event)" class="form-control">

                                        <option v-for="(item, index) in commandList" :value="item.id" :key="index" >
                                            @{{ item.cmd_name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" @click="addBtn()">
                                新增按鍵 <i class="fa fa-plus"></i>
                            </button>
                            <button type="button" class="btn btn-danger" @click="delBtn()">
                                刪除此按鍵 <i class="fa fa-trash"></i>
                            </button>
                            <span class="float-right">
                        <label class="text-danger mr-2">取消會恢復所有的按鍵設定</label>
                    </span>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="cancelBtnSetting()" class="btn btn-secondary"
                                data-dismiss="modal">{{__('layout.cancel')}}
                        </button>
                        <button type="button" @click="toBtnSetting()" class="btn btn-primary">
                            {{__('layout.yes')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!--Video Modal -->
        <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <span>
                            <h4 class="modal-title" id="myModalLabel">
                                <label v-if="isNewVideo==true">新增影像資料</label>
                                <label v-else>編輯影像資料</label>
                            </h4>
                        </span>
                        <span v-if="videoList.length>1" class="mt-2">
                            <select v-model="videoIndex" @change="onChangeVideo($event)">

                                <option v-for="(item, index) in videoList" :value="index" :key="index" >
                                    @{{ item.name }}
                                </option>
                            </select>
                        </span>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <div class="form-row align-items-center">
                                <div class="col-6 input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text" >名稱</span>
                                    </div>
                                    <input type="text" class="form-control" v-model="myVideo.name" >
                                </div>
                                <!--<div class="col-6 input-group mb-3">

                                    <div class="input-group-append">
                                        <span class="input-group-text" >命令ID</span>
                                    </div>
                                    <input type="number" class="form-control" v-model="myVideo.id" disabled>
                                </div>
                                <div class="col-12 input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text" >影像網址</span>
                                    </div>
                                    <input type="text" class="form-control" v-model="myVideo.url" disabled>
                                    <input type="hidden" id="videoUrl" v-model="myVideo.url" >
                                </div>-->
                                <div class="col-12 input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text" >上傳網址</span>
                                    </div>
                                    <input type="text" class="form-control" v-model="myVideo.rtmp" disabled style="width:90%">
                                    <input type="hidden" id="videoRtmp" v-model="myVideo.rtmp" >
                                </div>
                            </div>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary" @click="addVideo()">
                                新增影像 <i class="fa fa-plus"></i>
                            </button>
                            <button type="button" class="btn btn-danger" @click="delVideo()">
                                刪除此影像 <i class="fa fa-trash"></i>
                            </button>
                            <span class="float-right">
                        <label class="text-danger mr-2">取消會恢復所有的影像設定</label>
                    </span>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" @click="cancelVideoSetting()" class="btn btn-secondary"
                                data-dismiss="modal">{{__('layout.cancel')}}
                        </button>
                        <button type="button" @click="toVideoSetting()" class="btn btn-primary">
                            {{__('layout.yes')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endsection

        @section('footerScripts')
            <script>
                let devices = {!! $devices !!};
                let apps = {!! $apps !!};
                let device_id = {!! $device_id !!};
                let user = {!! $user !!};
                let commands = {!! $commands !!};
                let statusObj = {!! json_encode($status) !!};
                let point_url = "{!! url('/Images/point.png')!!}";
                let diamond_url = "{!! url('/Images/diamond.png')!!}";
                let app_url = '{{ env('APP_URL') }}';
                let rtmp_url = '{{ env('RTMP_URL') }}';
                let media_url = '{{ env('MEDIA_URL') }}';
                let data = {!! json_encode($data) !!};

                @if($center == null)
                    let center = null
                @else
                    let center = {!!  json_encode($center) !!};
                @endif

                @if($btn_setting == null)
                    let btn_setting = null;
                @else
                    let btn_setting = {!! $btn_setting !!};
                @endif
                @if($video_setting == null)
                    let video_setting = null;
                @else
                    let video_setting = {!! $video_setting !!};
                @endif

            </script>
            <script async defer
                    src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP_KEY')}}">
            </script>
            <script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script>
            <script src="{{asset('js/work_alg.js')}}"></script>
            <script src="{{asset('vender/flvjs/flv.js')}}" crossorigin="anonymous"></script>
            <script src="{{asset('js/node/viewControl.js')}}"></script>
            <script src="{{asset('js/option/boatSocket.js')}}"></script>
@endsection



