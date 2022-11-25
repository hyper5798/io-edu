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


    <div  class="row justify-content-center main-content">
        <div class="col-md-12">
            <div  class="card shadow-lg  rounded-lg mt-3">
                <div  class="card-header">
                    <div class = "row">
                        <div class="col-12 col-sm-6 col-md-3 mt-1">
                            <span class="text-info statusText ml-2">
                                我的控制器
                            </span>
                            @if($user->role_id < 2)
                                <span class="mr-2>">選擇公司</span>
                                <select onchange="location.href='?cp_id='+this.options[this.selectedIndex].value">
                                    @foreach ($cps as $cp)
                                        @if ($cp->id == $cp_id)
                                            <option value="{{$cp->id}}" selected="selected">{{$cp->cp_name}}</option>
                                        @else
                                            <option value="{{$cp->id}}">{{$cp->cp_name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        @if($user->role_id < 2)
                        <div class="col-6 col-sm-6 col-md-2 mt-1">


                                <span class="mr-2>">選擇帳戶</span>
                                <select onchange="location.href='?user_id='+this.options[this.selectedIndex].value">
                                    @foreach ($users as $tmp)
                                        @if ($tmp->id == $user_id)
                                            <option value="{{$tmp->id}}" selected="selected">{{$tmp->name}}</option>
                                        @else
                                            <option value="{{$tmp->id}}">{{$tmp->name}}</option>
                                        @endif
                                    @endforeach
                                </select>

                        </div>
                        @endif

                        <div class="col-6 col-sm-6 col-md-3" style="height: 25px;">

                                <div class="input-group">
                                    <div class="input-group rounded">
                                        <input type="text" class="form-control rounded" placeholder="輸入控制器名稱查詢" aria-label="Search"
                                               aria-describedby="search-addon" v-model="device_name" size="10" maxlength="10"/>
                                        <span class="input-group-text border-0" id="search-addon" @click="filterDevice()">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    </div>
                                </div>

                        </div>
                        <div class="col-6 col-sm-6 col-md-4">

                            <span class="float-right mt-1 mr-2">
                                @if($users->count()>0)
                                    <button type="button" class="btn btn-primary btn-sm " @click="newCheck()">
                                        新增控制器 <i class="fa fa-plus"></i>
                                    </button>
                                @else
                                    <button type="button" class="btn btn-danger btn-block">
                                        請先加入帳戶
                                    </button>
                                @endif
                            </span>

                        </div>
                    </div>
                </div>
                <div v-cloak class="card-body">
                    <div v-show="isNew==false" class="row justify-content-center">
                        <!-- 輸入型控制器 -->
                        <div v-show="isType==0 || isType==1" class="col-sm-12 col-md-10 col-xl-8">
                            <div class="deviceBlock">
                                <h5>
                                    開發板列表
                                    <span>裝置數量 : @{{ deviceList.length }}</span>
                                    <span v-if="deviceList.length==0" class="text-danger">
                                        尚無控制器，請新增控制器。
                                    </span>
                                </h5>
                                <hr>
                                <div v-cloak class="list-group" :list="deviceList" group="input">
                                    <div
                                        class="list-group-item"
                                        v-for="(element, index) in deviceList"
                                        :key="element.device_name"
                                    >
                                        <div class="row">
                                            <div  class="col-md-12">
                                                <div class="row">
                                                    <div  class="col-6 col-sm-6 col-md-6 col-xl-6 mb-2 float-right">
                                                        <button class="btn btn-primary btn-block text-left" @click="editDevice(index)">
                                                            @{{ element.device_name }} <i class="fa fa-pen"></i>   <!-- - @{{ element.macAddr }} -->
                                                        </button>

                                                    </div>
                                                    <div  class="col-6 col-sm-6 col-md-6 col-xl-6 mb-2">
                                                        <button class="btn btn-info btn-block" @click="toApp(element.macAddr)">
                                                            應用列表 <i class="fa fa-table"></i><!--<img src="{{url('/Images/http.png')}}" width="40" height="40" title="應用管理" alt="應用管理">-->
                                                        </button>

                                                    </div>
                                                    <div  class="col-sm-6 col-md-6 col-xl-3 mb-2" >

                                                        <!--<button class="btn btn-success btn-block" @click="toCommand(element.macAddr)">
                                                            命令列表 <i class="fa fa-table"></i>
                                                        </button>-->

                                                    </div>

                                                </div>



                                                <!--<span v-if="element.make_command>=1">
                                                    <button class="btn btn-default" @click="toMap(element.macAddr)">
                                                        <img src="{{url('/Images/earth.png')}}" width="40" height="40" title="地圖管理" alt="地圖管理">
                                                    </button>
                                                </span>-->




                                            </div>

                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                        <div v-show="isType==0 || isType==2" class="col-sm-12 col-md-8">
                            <div class="deviceBlock">
                                <div class="row">
                                    <div class="col-6">
                                        <h5>
                                            控制模組列表 <!--<i class="fa fa-arrow-right" aria-hidden="true"></i>-->
                                        <!--
                                            <img src="{{url('/Images/monitor.png')}}" width="40" height="30" title="上報資料" alt="上報資料" @click="toMonitor()">
                                            -->
                                        </h5>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <div class="input-group">
                                            <div class="input-group rounded">
                                                <input type="text" class="form-control rounded" placeholder="輸入專屬註冊碼尋找" aria-label="Search"
                                                       aria-describedby="search-addon" v-model="choice"/>
                                                <span class="input-group-text border-0" id="search-addon" @click="find()">
                                                    <i class="fas fa-search"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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

                                                    <button class="btn btn-outline-dark" @click="editController(element.data.macAddr)">
                                                        @{{ element.data.device_name }} - @{{ element.data.macAddr }}
                                                    </button>
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
                                    <div class="emptyBlock mt-3" >
                                        可加入控制器
                                        <div id="free" class="demo"></div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                    <!-- Edit device-->
                    <div v-show="isNew" class="row justify-content-center main-content">
                        <div class="col-lg-8">
                            <div class="card shadow-lg border-0 rounded-lg mt-2">
                                <div class="card-header">
                                    <!-- edit company data -->
                                    <h3 v-if="device.id>0" class="text-center font-weight-light my-4">
                                        {{__('layout.edit')}}控制器
                                    </h3>
                                    <!-- Add company data -->
                                    <h3 v-else class="text-center font-weight-light my-4">
                                        {{__('layout.add')}}控制器
                                    </h3>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="editDevice" id="editDevice">
                                        <input type="hidden" name="_method" value="put" />
                                        <input type="hidden" name="id" v-model="device.id" />
                                        <input type="hidden" name="user_id" v-model="device.user_id"/>
                                        <input type="hidden" name="macAddr" v-model="device.macAddr"/>
                                        <input type="hidden" name="network_id" v-model="device.network_id"/>
                                        <input type="hidden" name="type_id" v-model="device.type_id"/>
                                        <input type="hidden" name="make_command" v-model="device.make_command"/>
                                        {{csrf_field()}}
                                        <div class="form-row justify-content-center">
                                            <div class="col-sm-12 col-md-8">
                                                <!-- 專屬註冊碼 -->
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="inputGroup-sizing-default">{{__('device.device_mac')}}</span>
                                                    </div>
                                                    <input v-if="device.id>0" type="text" class="form-control" aria-label="Sizing example input"
                                                           aria-describedby="inputGroup-sizing-default" v-model="device.macAddr" disabled>
                                                    <input v-else type="text" class="form-control" aria-label="Sizing example input" name="macAddr"
                                                           aria-describedby="inputGroup-sizing-default" v-model="device.macAddr" placeholder="{{__('device.filled_mac_prompt')}}">
                                                </div>
                                                <!-- device name -->
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="inputGroup-sizing-default" >{{__('device.device_name')}}</span>
                                                    </div>
                                                    <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="device.device_name" name="device_name" :disabled="isVerify==false">
                                                </div>

                                        <!-- 裝置類型 -->
                                        @if($user->role_id < 2)
                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="inputGroup-sizing-default" >{{__('layout.types')}} [Super Admin]</span>
                                                    </div>
                                                    <select  v-model="device.type_id" class="custom-select" disabled>
                                                        <option v-for="type in typeList" :value="type.type_id" :key="type.type_id">
                                                            @{{ type.type_name }}
                                                        </option>
                                                    </select>
                                                </div>

                                                <div class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="inputGroup-sizing-default">{{__('layout.accounts')}}</span>
                                                    </div>
                                                    <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" value="{{$bindUser->email}}" disabled>
                                                </div>

                                                <div v-if="device.id>0" class="input-group mb-3">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="inputGroup-sizing-default">變更註冊碼</span>
                                                    </div>
                                                    <input v-if="device.id>0" type="text" class="form-control"  name="changeMac" placeholder="Super Admin 才能變更">

                                                </div>

                                        @endif

                                            </div>

                                            <div v-if="isVerify && device.id==0 && device.type_id>101" class="col-sm-12 col-md-6">
                                                <!-- device name -->
                                                <div class="input-group mb-3">
                                                    <div class="alert alert-info" role="alert">
                                                        自動複製客製化公共控制器應用及相關設定!
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="justify-content-center">

                                            <span class="float-right">
                                                <span v-if="device.id==0">
                                                    <span v-show="!isVerify" class="text-info mr-5">
                                                        第一步：驗證註冊碼(共12碼，輸入大小寫不拘)
                                                    </span>
                                                    <span v-show="isVerify" class="text-info mr-5">
                                                        第二步：取別名，送出 確認註冊
                                                    </span>
                                                </span>

                                                <span v-else>

                                                    <span class="text-info mr-5">
                                                        變更別名，送出 變更
                                                    </span>
                                                </span>

                                                <button type="button" class="btn btn-secondary" @click="back()">{{__('layout.back')}}</button>
                                                <span v-if="isVerify">
                                                    <button v-if="device.id==0" type="button" class="btn btn-success" @click="toSubmit()">確認註冊</button>
                                                    <button v-else type="button" class="btn btn-success" @click="toSubmit()">變更</button>
                                                </span>
                                                <span v-else>
                                                    <button type="button" class="btn btn-primary" @click="toVerify()">{{__('layout.verify')}}</button>
                                                </span>
                                                <span v-if="device.id>0" class="ml-3">
                                                    <button type="button" class="btn btn-danger" @click="delCheck()">{{__('layout.delete')}}</button>
                                                </span>

                                            </span>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row justify-content-center main-content mt-5">
                        <div class="col-lg-9">
                            <div ></div>
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
        let user = {!! $bindUser !!};
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
        let api_url = '{{ env('API_URL') }}';
        let io_url = "{!! url('/Images/io.png')!!}";
        let select_user_id = {!! $user_id !!};
        let user_id = {!! $user_id !!};
    </script>
    <script src="{{asset('js/node/myDevices.js')}}"></script>
@endsection



