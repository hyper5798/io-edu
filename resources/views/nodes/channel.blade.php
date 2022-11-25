@extends('Layout.diy')

@section('content')
    <div class="breadcrumb">
        <div class="col-md-6">
            <ol class="breadcrumb">
                <ol class="breadcrumb">
                    <li id="step6" class="breadcrumb-item"><a href="/node/myDevices?link=develop">{{__('device.my_devices') }}</a></li>
                <!--<li class="breadcrumb-item"><a href="{{url('/node/admin/?device_id=')}}{{$device->id}}">{{$device->device_name}}</a></li>-->
                    <li class="breadcrumb-item"><a href="{{url('/node/apps/?device_id=')}}{{$device->id}}">{{$device->device_name}} - 應用列表</a></li>
                    <li class="breadcrumb-item active" aria-current="page">編輯通道</li>
                </ol>
            </ol>
        </div>
        <div class="col-md-6 mt-2 text-left">

        </div>

        <div class="col-md-3 text-right">
        <!--<button type="button" class="btn btn-success text-right" @click="newCheck()">{{__('layout.add')}}</button>-->
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
    <div class="row main-content">
        <!-- Sidebar Menu -->
        <div class="col-md-2 mt-3">
            <div class="list-group">
                <!-- 應用選項 -->
                <div class="list-group-item bg-light text-black-50 font-weight-bold">
                    {{__('app.app_option') }}
                </div>
                <!-- Data management -->
                <a href="{{url('/node/apps/reports?app_id=')}}{{$app_id}}" class="list-group-item">{{__('app.data_management') }}</a>
                <!-- Edit channel -->
                <a id="step1" href="{{url('/node/apps/channel?app_id=')}}{{$app_id}}" class="list-group-item list-group-item-action active">{{__('app.edit_channel') }}</a>
                <!-- API key -->
                <div class="list-group-item">
                    <a href="{{url('/node/apps/APIkey?app_id=')}}{{$app_id}}">{{__('app.api_key') }}</a>
                </div>
            </div>
        </div>

        <div class="col-md-10 mt-3">
            <!-- Edit App-->
            <div v-cloak class="justify-content-center">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header">
                        <!-- edit app data -->
                        <span v-if="appObj.id>0" class="font-weight-light ml-4">
                            {{__('layout.set')}}{{$data['device_name']}}{{__('device.device_app')}}
                        </span>
                        <!-- Add app data -->
                        <span v-else class="font-weight-light ml-4">
                            {{__('layout.add')}}{{$data['device_name']}}{{__('device.device_app')}}
                        </span>
                        <span class="font-weight-light ml-4">
                            <button v-if="dataTab==1" type="button" class="btn btn-secondary">{{$data['data_title']}}</button>
                            <button v-if="dataTab==2" type="button" class="btn btn-outline-secondary" @click="dataTab=1">{{$data['data_title']}}</button>
                            <button v-if="dataTab==2" type="button" class="btn btn-secondary">{{$data['control_setting_title']}}</button>
                            <button v-if="dataTab==1" type="button" class="btn btn-outline-secondary" @click="dataTab=2">{{$data['control_setting_title']}}</button>
                        </span>
                    </div>
                    <div class="card-body">
                        <div v-show="dataTab==1">
                            <form method="post" action="updateChannel" id="updateChannel">
                                <input type="hidden" name="_method" value="put" />
                                <input type="hidden" name="id" v-model="appObj.id" />
                                <input type="hidden" name="label" v-model="sendLabel" />
                                <input type="hidden" name="parse" v-model="sendParse" />
                                <input type="hidden" name="sequence" v-model="appObj.sequence" />
                                <input type="hidden" name="myIntro" v-model="myIntro"/>
                                {{csrf_field()}}

                                <div class="form-row">
                                    <!-- app name -->
                                    <div id="step2" class="input-group mb-3 col-md-6">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-default" >{{__('app.name')}}</span>
                                        </div>
                                        <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="appObj.name" name="name">
                                    </div>
                                    <div class="input-group mb-3 col-md-2">
                                    </div>

                                </div>
                                <div class="row text-center">
                                    <!-- app name -->
                                    <div class=" mb-3 col-md-1 text-center">
                                        <h5  class="text-center">{{__('layout.select')}}</h5>
                                    </div>
                                    <div class=" mb-3 col-md-5">
                                        <h5>{{__('layout.field_name')}}</h5>
                                    </div>
                                    @if( !($type_id == 102 && $app->sequence<5) )
                                        <div class=" mb-3 col-md-2 text-center">
                                            <h5><span>觸發條件 </span></h5>
                                        </div>
                                    @endif
                                </div>

                                <div v-for = "(item, index) in appObj.fieldList" class="form-row">
                                    <!-- channel -->
                                    <div class="input-group mb-1 col-md-1">
                                        <input v-if="index==0" type="checkbox" class="form-control" v-model="item.check">
                                        <input v-else type="checkbox" class="form-control" v-model="item.check">
                                    </div>
                                    <div class="input-group mb-1 col-md-5">

                                        <div class="input-group-prepend">
                                            <span v-if="index<8" class="input-group-text" id="inputGroup-sizing-default" >key@{{ index+1 }}</span>
                                            <span v-if="index==8" class="input-group-text" id="inputGroup-sizing-default" >lat</span>
                                            <span v-if="index==9" class="input-group-text" id="inputGroup-sizing-default" >lng</span>
                                        </div>
                                        <input v-if="index==0" type="text" class="form-control"  v-model="item.key" :disabled="!item.check">
                                        <input v-else-if="index==8" type="text" class="form-control"  v-model="item.key" disabled>
                                        <input v-else-if="index==9" type="text" class="form-control"  v-model="item.key" disabled>
                                        <input v-else type="text" class="form-control"  v-model="item.key" :disabled="!item.check">
                                        <!--<input type="checkbox" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">-->
                                    </div>
                                    <!-- Trigger checkbox-->
                                    @if( !($type_id == 102 && $app->sequence<5) )
                                        <div class="input-group mb-1 col-md-1">
                                            <input  type="checkbox" class="form-control" @change="check($event, index)" v-model="checkList[index].check">
                                        </div>

                                        <div class="input-group mb-1 col-md-2">
                                            <button v-if="checkList[index].check" type="button" class="btn btn-primary btn-block" @click="toSetTrigger(index)">@{{checkList[index]['key']}}</button>

                                        </div>
                                    @endif
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mt-2">
                                        <button  type="button" class="btn btn-primary" @click="toSubmit()">{{__('layout.submit')}}</button>
                                    </div>
                                    @if( !($type_id == 102 && $app->sequence<5) )
                                        <div class="col-md-3 mt-2">
                                            <button  type="button" class="btn btn-primary" @click="toSaveAllTrigger()"> 觸發通知設定</button>
                                        </div>
                                        <div class="col-md-3 mt-2">

                                        </div>
                                    @endif
                                </div>

                            </form>
                        </div>
                        <div v-cloak v-show="dataTab==2" class="channelBlock">
                            <!-- controlSetting : object-->
                             <div v-for="(item,key, index) in controlSetting">
                            <!-- controlSetting : array -->
                            <!--<div v-for="(item, index) in controlSetting">-->

                                 <div v-if="index<controlSettingMax" class="row mb-1 ml-2">
                                     <div class="co-2 mr-1">
                                         <span class="form-control bg-secondary text-white"> @{{ item.key }} </span>
                                     </div>
                                     <div class="input-group mb-1 col-4 justify-content-center">
                                         <div class="input-group-prepend ">
                                             <label class="input-group-text">別名</label>
                                         </div>

                                         <input text="text" class="form-control" v-model="item.title" placeholder="別名">

                                     </div>
                                     <div class="input-group mb-1 col-4 justify-content-center">
                                         <div class="input-group-prepend ">
                                             <label class="input-group-text">設定值</label>
                                         </div>

                                         <input text="text" class="form-control"  v-model="item.value" placeholder="設定值(值，字串)(max:10字元)" maxlength="10">

                                     </div>

                                 </div>

                             </div>

                             <div class="row">
                                <div class="col-12 mt-1">
                                    <button  type="button" class="btn btn-primary" @click="toSaveControlSetting()">更新設定</button>
                                </div>

                            </div>
                            <hr>
                             <div class="form-group mt-2">
                                 <div v-cloak class="alert alert-info mt-1 mb-3" >
                                     回復訊息 : @{{ prompt }}
                                 </div>
                                <div>
                                    <label>
                                        API KEY
                                    </label>
                                    <input type="text" v-model="myKey" id="api_key" maxlength="20" size="20" disabled/>
                                    <input type="button" class="btn btn-info btn-sm" @click="copyKey('api_key');" value="複製 API KEY" />
                                </div>
                                <div >
                                    <label>
                                        寫入雙向通道API網址
                                    </label>

                                    <input type="text" v-model="write_url" id="write_url" maxlength="100" size="100" disabled/>
                                </div>

                                <div class="mt-1 ">
                                    <input type="button" class="btn btn-info " @click="copyUrl('write_url');" value="{{__('app.copy_api')}}" />
                                    <input id="step4" type="button" class="btn btn-primary" @click="toSenApi('write_url');" value="{{__('app.run_api')}}" />
                                </div>

                                 <div class="mt-3 mb-1">
                                     <hr>
                                 </div>

                                 <div>
                                     <label>
                                         讀取雙向通道API網址
                                     </label>

                                     <input type="text" v-model="read_url" id="read_url" maxlength="100" size="100" disabled/>
                                 </div>

                                 <div class="mt-1">
                                     <input type="button" class="btn btn-info " @click="copyUrl('read_url');" value="{{__('app.copy_api')}}" />
                                     <input id="step4" type="button" class="btn btn-primary" @click="toSenApi('read_url');" value="{{__('app.run_api')}}" />
                                 </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel"><span class="block-title mr-2">觸發通知設定</span></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
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
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" @click="delSetting();">
                        <i class="fa fa-trash"></i>刪除
                    </button>
                    <button type="button" class="btn btn-secondary mr-2"
                            data-dismiss="modal" @click="cancelSetting();"> X 取消
                    </button>
                    <button type="button" class="btn btn-primary mr-2" @click="saveSetting();">
                        <i class="fa fa-pen"></i>確定
                    </button>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footerScripts')
    <script>
        let myapp = {!! $app !!};
        let device_id = {!! $device_id !!};
        let app_id = {!! $app_id !!};
        let target = {!! $target !!};
        let user = {!! $user !!};
        let url = "{{$_SERVER['HTTP_HOST']}}";
        let field_required = "{{__('app.field_required') }}";
        let name_required = "{{__('app.name_required') }}";
        let data = {!! json_encode($data) !!};
        let app_url = '{{ env('APP_URL')}}';
        let api_url = '{{ env('API_URL')}}';


        @if($user->remember_token == null)
        let token = null;
        @else
        let token = '{!! $user->remember_token !!}';
        @endif
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/node/channel.js')}}"></script>
@endsection



