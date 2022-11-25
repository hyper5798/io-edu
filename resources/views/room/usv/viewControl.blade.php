@extends('Layout.room')

@section('content')
    <div class="room_header mb-3">
        <!--<span class="ml-3"> 現在位置：</span>-->
        <!--<span class="breadcrumb-item">
            <a href="/room/develop/{{$room->id}}">無人船選擇</a>
        </span>
        <span> / </span>
        <span class="breadcrumb-item">
            <a href="/room/usv?device_id={{$device->id}}">{{$device->device_name}}</a>
        </span>-->
        <span class="breadcrumb-item">
            <a href="javascript:history.back()" onclick="self.location=document.referrer;">回上一頁</a>
        </span>
        <span> / </span>
        <span> 影像監控 </span>

        <span v-if="isWebRTC==false" class="ml-5">
            <link href="/media/examples/link-element-example.css" rel="stylesheet">

        </span>
        <span v-cloak v-if="isWebRTC==false" class="ml-5">
            <button class="btn btn-default" @click="addVideo()">
                影像 <i class="fa fa-plus-circle"></i>
            </button>

        </span>
        <!--<span class="float-right">
            <a href="{{url('/room/webrtc/'.$device->id.'?size=large')}}">webrtc</a>
        </span>-->
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
        <div class="col-lg-9 mb-2">
            <div v-if="isWebRTC" class="farmWebRTCBlock2 row mb-1">
                <iframe v-if="isWebRTC" id="iframe1" src="{{url('/room/webrtc/'.$device->id.'?size=large')}}" ></iframe>
            </div>
            <div v-show="isWebRTC==false" v-cloak class="row justify-content-center">

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
            <!--<div id="timeselector" class="btn-group btn-group-toggle mb-1" data-toggle="buttons">
                <label class="btn btn-secondary active">
                    <input type="radio" name="options" id="1" autocomplete="off" >控制面板
                </label>
                <label class="btn btn-secondary ">
                    <input type="radio" name="options" id="2" autocomplete="off" checked>無人船訊息
                </label>
                <label class="btn btn-secondary">
                    <input type="radio" name="options" id="3" autocomplete="off">航向訊息
                </label>
            </div>-->
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
                            <button v-if="isWebRTC==false" type="button" class="btn btn-outline-primary btn-sm" onClick="load()">
                                影像重載入
                            </button>
                            <!--<button type="button" class="btn btn-secondary btn-sm" @click="editVideo()">
                                影像 <i class="fa fa-pen"></i>
                            </button>-->
                        </div>
                    </div>
                </div>

            </div>

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
                let apps = {!! $apps !!};
                let device = {!! $device !!};
                let user = {!! $user !!};
                let commands = {!! $commands !!};
                let data = {!! json_encode($data) !!};
                let statusObj = {!! json_encode($status) !!};
                let point_url = "{!! url('/Images/point.png')!!}";
                let diamond_url = "{!! url('/Images/diamond.png')!!}";
                let app_url = '{{ env('APP_URL') }}';
                let rtmp_url = '{{ env('RTMP_URL') }}';
                let media_url = '{{ env('MEDIA_URL') }}';

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
            <script src="{{asset('js/room/usv/work_alg.js')}}"></script>
            <script src="{{asset('vender/flvjs/flv.js')}}" crossorigin="anonymous"></script>
            <script src="{{asset('js/room/usv/viewControl.js')}}"></script>
            <script src="{{asset('js/option/boatSocket.js')}}"></script>
@endsection




