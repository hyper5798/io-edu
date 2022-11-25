@extends('Layout.room')

@section('content')
    <div class="breadcrumb">
        <div class="col-md-4 mt-1">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:history.back()" onclick="self.location=document.referrer;">回上一頁</a></li>
                <li class="breadcrumb-item active" aria-current="page">工程測試</li>
            </ol>
        </div>
        <div class="col-md-5">


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
    <div v-cloak class="alert alert-primary" role="alert">
        發送命令: @{{ newCommand }}
    </div>

    <div v-cloak class="row main-content mt-1">
        <div class="col-sm-12 col-lg-9">
            <div v-for="(myApp, index) in appList">
                <div  class="card shadow-lg  rounded-lg mt-3">
                    <div  class="card-header">
                        @{{myApp.name}}
                    </div>
                    <div class="card-body">
                        <div v-cloak class="row justify-content-center">
                            <div v-for="(item, key, mIndex) in myApp.key_label" class="input-group mb-1 col-sm-6 col-lg-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">@{{ item }}</span>
                                </div>
                                <input type="text" class="form-control" v-model="appList[index]['status'][key]" disabled>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div v-cloak class="col-lg-3 text-center">
            <!-- 切換頁籤 -->
            <div id="timeselector" class="btn-group btn-group-toggle mb-1" data-toggle="buttons">
                <button v-if="tab==1" type="button" class="btn btn-primary">按鍵參數</button>
                <button v-if="tab==2" type="button" class="btn btn-outline-primary" @click="tab=1;">按鍵參數</button>
                <button v-if="tab==1" type="button" class="btn btn-outline-primary" @click="tab=2;">地圖</button>
                <button v-if="tab==2" type="button" class="btn btn-primary">地圖</button>
            </div>
            <!--1. 地圖 & 控制按鍵 -->
            <div>
                <div v-show="tab==1" class="mapBlock">
                    <div class="row">
                        <div v-for="(item, key, index) in keyObj" class="col-6 mb-1">

                            <div class="input-group mb-">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">@{{ key }}</span>
                                </div>
                                <input type="number" class="form-control" v-model="keyObj[key]">
                            </div>
                        </div>
                    </div>
                </div>
                <div v-show="tab==2" class="mapBlock">
                    <div class="mb-1">
                        <div id="map" style="height: 220px;width: 100%;"></div>
                    </div>
                </div>
                <div class="mapBlock">
                    <div>
                        <div class="mb-1">
                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearReports();">
                                清上報點
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm" @click="clearParam();">
                                清除參數
                            </button>
                            <button v-if="!isReset"  type="button" class="btn btn-danger btn-sm" @click="toReset();">
                                重置無人船
                            </button>
                            <button v-if="isReset" type="button" class="btn btn-danger btn-sm" disabled>
                                重置無人船
                            </button>

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

        @endsection

        @section('footerScripts')
            <script>
                let devices = {!! $devices !!};
                let apps = {!! $apps !!};
                let device_id = {!! $device_id !!};
                let target = {!! $target !!};
                let user = {!! $user !!};
                let commands = {!! $commands !!};
                let statusObj = {!! json_encode($status) !!};
                let point_url = "{!! url('/Images/point.png')!!}";
                let diamond_url = "{!! url('/Images/diamond.png')!!}";
                let app_url = '{{ env('APP_URL') }}';
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


            </script>
            <script async defer
                    src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP_KEY')}}">
            </script>
            <script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script>
            <script src="{{asset('js/work_alg.js')}}"></script>
            <script src="{{asset('vender/flvjs/flv.js')}}" crossorigin="anonymous"></script>
            <script src="{{asset('js/node/paramTest.js')}}"></script>
@endsection



