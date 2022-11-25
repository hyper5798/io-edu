@extends('Layout.diy')

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


    <div v-show="isNew==false" class="row justify-content-center main-content">
        <div class="col-md-12">
            <div  class="card shadow-lg  rounded-lg mt-3">
                <div  class="card-header">
                    <div class = "row">
                        <div class="col-6 ">
                            <span class="text-info statusText">我的裝置</span>
                            <button id="step1" type="button" class="btn btn-primary btn-sm float-right" @click="newCheck()">
                                新增裝置
                            </button>
                        </div>

                        <div class="col-6">
                            <div class="input-group">
                                <div class="input-group rounded">
                                    <input type="search" class="form-control rounded" placeholder="查詢裝置輸入裝置識別碼" aria-label="Search"
                                           aria-describedby="search-addon" v-model="choice"/>
                                    <span class="input-group-text border-0" id="search-addon" @click="find()">
                                        <i class="fas fa-search"></i>
                                    </span>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                <div v-cloak class="card-body">
                    <div  class="row justify-content-center">
                        <!-- 輸入型裝置 -->
                        <div v-show="isType==0 || isType==1" class="col-sm-12 col-md-6">
                            <h5>
                                個人開發板 <!--<i class="fa fa-arrow-right" aria-hidden="true"></i>-->
                            </h5>

                            <div class="statuslBlock">
                                <div v-cloak class="list-group" :list="deviceList" group="input">
                                    <div
                                        class="list-group-item"
                                        v-for="(element, index) in deviceList"
                                        :key="element.device_name"
                                    >
                                        <div class="row">
                                            <div  class="col-md-12">
                                                <button class="btn btn-default" @click="toApp(element.macAddr)">
                                                    <img src="{{url('/Images/http.png')}}" width="40" height="40" title="HTTP命令管理" alt="HTTP命令管理連結">
                                                </button>
                                                <span :style="element.bg" @click="editDevice(index)">
                                                    @{{ element.device_name }} - @{{ element.macAddr }}
                                                </span>

                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-show="isType==0 || isType==2" class="col-sm-12 col-md-6">
                            <h5>
                                控制模組 <!--<i class="fa fa-arrow-right" aria-hidden="true"></i>-->
                            </h5>

                            <div class="statuslBlock">
                                <div v-cloak class="list-group" :list="controllerList" group="input">
                                    <div
                                        class="list-group-item"
                                        v-for="(element, index) in controllerList"
                                        :key="element.data.device_name"
                                    >
                                        <div class="row">
                                            <div class="col-md-12">

                                                <div  >
                                                    <span>
                                                        <button class="btn btn-default" @click="toControlModule(element.data.macAddr)">
                                                            <img src="{{url('/Images/click.png')}}" width="40" height="40" title="設定控制模組" alt="設定模組連結">
                                                        </button>
                                                    </span>

                                                    <span :style="element.bg" @click="editController(element.data.macAddr)">
                                                        @{{ element.data.device_name }} - @{{ element.data.macAddr }}
                                                    </span>

                                                </div>
                                                <div :id="element.data.macAddr" class="demo">
                                                    <!--<ul>
                                                        <li data-jstree='{ "opened" : false }'>輸入裝置
                                                            <ul>
                                                                <li v-for="(item, index) in controllerList[index]['inputs']">
                                                                    @{{  item.device_name}}
                                                                </li>
                                                            </ul>
                                                        </li>
                                                    </ul>-->
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                    <div class="emptyBlock" >
                                        可加入控制器裝置
                                        <div id="free" class="demo"></div>
                                    </div>
                                </div>


                            </div>


                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div v-show="device.type_id>200" class="col-lg-12 alert alert-info">
            <div>{{__('device.defined_prompt')}}</div>
            <div>{{__('device.common_prompt')}}</div>
    </div>
    <!-- Edit device-->
    <div v-show="isNew" class="row justify-content-center main-content">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-lg mt-2">
                <div class="card-header">
                    <!-- edit company data -->
                    <h3 v-if="device.id>0" class="text-center font-weight-light my-4">
                        {{__('layout.edit')}}{{__('layout.data')}}
                    </h3>
                    <!-- Add company data -->
                    <h3 v-else class="text-center font-weight-light my-4">
                        {{__('layout.add')}}{{__('layout.data')}}
                    </h3>
                </div>
                <div class="card-body">
                    <form method="post" action="editDevice" id="editDevice">
                        <input type="hidden" name="_method" value="put" />
                        <input type="hidden" name="id" v-model="device.id" />
                        <input type="hidden" name="user_id" v-model="device.user_id"/>
                        <input type="hidden" name="mac" v-model="device.macAddr"/>
                        <input type="hidden" name="network_id" v-model="device.network_id"/>
                        <input type="hidden" name="type_id" v-model="device.type_id"/>
                        <input type="hidden" name="make_command" v-model="device.make_command"/>

                        {{csrf_field()}}
                        <div class="form-row">
                            <!-- device name -->
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >{{__('device.device_name')}}</span>
                                </div>
                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="device.device_name" name="device_name">
                            </div>
                            <!-- device mac -->
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">{{__('device.device_mac')}}</span>
                                </div>
                                <input v-if="device.id>0" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="device.macAddr" disabled>
                                <input v-else type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="device.macAddr">

                            </div>

                            <!-- device create command -->
                            <div v-if="device.type_id>200" class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="inputGroupSelect01">{{__('device.make_command')}}</label>
                                </div>
                                <select  v-model="device.make_command" class="custom-select" name="make_command">
                                    <option v-for="act in actList" :value="act.id" :key="act.id">
                                        @{{ act.value }}
                                    </option>
                                </select>
                            </div>
                            <!-- device type -->
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="inputGroupSelect01">{{__('layout.types')}}</label>
                                </div>
                                <select  v-model="device.type_id" class="custom-select" disabled>
                                    <option v-for="type in typeList" :value="type.type_id" :key="type.type_id">
                                        @{{ type.type_name }}
                                    </option>
                                </select>
                            </div>

                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">{{__('layout.accounts')}}</span>
                                </div>
                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" value="{{$user->email}}" disabled>
                            </div>
                            <div class="col-md-12">
                                <button type="button" class="btn btn-secondary" @click="back()">{{__('layout.back')}}</button>
                                <span v-if="isVerify">
                                    <button type="button" class="btn btn-success" @click="toSubmit()">{{__('layout.submit')}}</button>
                                </span>
                                <span v-else>
                                    <button type="button" class="btn btn-primary" @click="toVerify()">{{__('layout.verify')}}</button>
                                </span>
                                <span class="float-right">
                                    <button type="button" class="btn btn-danger" @click="delCheck()">{{__('layout.delete')}}</button>
                                </span>
                            </div>

                            <div class="col-md-12">
                                <span v-if="!isVerify">
                                    <!--請先填入裝置MAC,然後按下[校驗]按鍵進行檢查MAC-->
                                    {{__('device.filled_mac_prompt')}}
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 v-if="device.type_id>100" class="modal-title" id="myModalLabel">{{__('app.delete_device_waring')}}</h5>
                    <h5 v-else-if="device.type_id==99" class="modal-title" id="myModalLabel">{{__('node.delete_device_waring')}}</h5>
                    <h5 v-else class="modal-title" id="myModalLabel">{{__('layout.waring')}}!</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <h5>{{__('layout.delete_confirm')}} @{{device.device_name}}?</h5>

                    <div v-show="device.type_id==99" class="row">
                        <div class="col-md-6">

                            {{__('node.change_controller_message')}}

                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="inputGroupSelect01">轉移控制器</label>
                                </div>

                                <select  v-model="change_mac" class="custom-select">

                                    <option v-for="(item, index) in controllerList" :value="item.data.macAddr" :key="item.data.macAddr">
                                        @{{ item.data.device_name }}
                                    </option>
                                </select>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <button type="button" onClick="toDelete()" class="btn btn-danger">
                        {{__('layout.yes')}}
                    </button>
                </div>
                <form method="post" action="delDevice" id="delDevice">
                    <input type="hidden" name="_method" value="delete" />
                    <input type="hidden" name="id" v-model="device.id" />
                    <input type="hidden" name="change_mac" v-model="change_mac" />
                    {{csrf_field()}}
                </form>
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
    <script>
        let link = "{!! $link !!}";
        let devices = {!! $devices !!};
        let types = {!! $types !!};
        let controllers = {!! $controllers !!};
        let inputs = {!! $inputs !!};
        let outputs = {!! $outputs !!};
        let nodes = {!! $nodes !!};
        let user = {!! $user !!};
        let token = "{!! $token !!}";
        let defined = "{{__('device.defined_commands')}}";
        let common = "{{__('device.common_commands')}}";
        let unfilledMac = "{{__('device.unfilled_mac')}}";
        let notFoundMac = "{{__('device.not_found_mac')}}";
        let beBoundMsg1 = "{{__('device.be_bound_msg1')}}";
        let beBoundMsg2 = "{{__('device.be_bound_msg2')}}";
        let verifyOkMsg1 = "{{__('device.verify_ok_msg1')}}";
        let verifyOkMsg2 = "{{__('device.verify_ok_msg2')}}";
        let app_url = '{{ env('APP_URL') }}';
        let io_url = "{!! url('/Images/io.png')!!}";
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/node/userDevices.js')}}"></script>
@endsection



