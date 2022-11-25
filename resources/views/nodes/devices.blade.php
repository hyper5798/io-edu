@extends('Layout.default')

@section('content')
    <div class="row breadcrumb">
        <div v-show="tab!=3" class="col-md-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">{{__('layout.index_title') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{__('layout.devices') }}</li>
                <li class="breadcrumb-item active" aria-current="page">{{__('layout.devices_manager') }}</li>
            </ol>
        </div>

        <div v-show="tab!=3" class="col-md-6 mt-2 text-left">
            {{__('layout.category') }}
            <select v-cloak v-model="category" @change="changeCategory()">
                <option v-for="item in categoryList" :value="item.value">
                    @{{ item.name }}
                </option>
            </select>
            {{__('layout.types') }}
            <select onchange="location.href='devices?type_id='+this.options[this.selectedIndex].value+'&category='+{{$category}}">
                @foreach ($types as $type)
                    @if ($type->type_id === $type_id)
                        <option value="{{$type->type_id}}" selected="selected">{{$type->type_name}}</option>
                    @else
                        <option value="{{$type->type_id}}">{{$type->type_name}}</option>
                    @endif

                @endforeach
            </select>

        </div>
        <div class="input-group mb-3 col-md-3">
            <div class="input-group-prepend">
                <label class="input-group-text">輸入MAC</label>
            </div>

            <input type="text" class="form-control" v-model="targetProduct">
            <button class="btn btn-outline-primary" type="button" @click="searchProduct()"><i class="fas fa-search fa-fw"></i></button>
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

    <!-- Device list -->
    <div v-show="tab==1" class="main-content">
        <div v-cloak v-if="alertMessage.length>0" class="alert alert-success mt-1">
            @{{ alertMessage }}
        </div>
        <div class="card shadow-lg border-0 rounded-lg mt-2">
            <div class="card-header">

                <span class="font-weight-bold text-center font-weight-light ml-2">
                    控制器列表
                </span>
                @if(session('user')->role_id < 2)
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
                @else
                    {{__('layout.select') }}{{__('escape.room') }}
                @endif
                <!--<span class="text-danger">公共裝置不可刪除</span>-->
                <span class="float-lg-right mr-2">
                    <span class="mr-2 text-left">
                        <button v-show="tab!=3" type="button" class="btn btn-outline-primary btn-sm mb-1" @Click="tab=3"> 控制器 QRCode </button>
                        <button v-cloak v-show="tab==3" type="button" class="btn btn-outline-primary btn-sm mb-1" @Click="tab=1"> 返回控制器列表 </button>
                    </span>
                    <button type="button" class="btn btn-success btn-sm ml-5" @click="newCheck()">{{__('layout.add')}}</button>
                </span>

            </div>
            <div class="card-body">
                <table id ="table1"  class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th >{{__('layout.item')}}</th>
                        <th >{{__('device.device_name')}}</th>
                        <th >{{__('device.device_mac')}}</th>
                        <th >{{__('device.bind_user')}}</th>
                        <th >{{__('layout.update_at')}}</th>
                        <!--<th >公共裝置</th>-->
                        <th > </th>
                    </tr>

                    </thead>

                    <tbody >
                    @foreach ($devices as $device)
                        <tr>
                            <td> {{$loop->index +1}} </td>
                            <td> {{$device->device_name}} </td>
                            <td> {{$device->macAddr}} </td>

                            <td>
                                @if($device->user && $device->user->email)
                                    {{$device->user->email}}
                                @endif
                            </td>

                            <td> {{$device->updated_at}} </td>
                            <!--<td>
                                @if($device->isPublic ==0)
                                    不是
                                @else
                                    是
                                @endif
                            </td>-->
                            <td>
                                @if($device->type_id==99)
                                <button @v-if="editPoint!={!! $loop->index !!}" type="button" name="edit" class="btn btn-outline-primary btn-sm" @click="setScript({!! $loop->index !!})">
                                    設定腳本
                                </button>
                                @endif
                                <button @v-if="editPoint!={!! $loop->index !!}" type="button" name="edit" class="btn btn-primary btn-sm" @click="editCheck({!! $loop->index !!})">
                                    {{__('layout.edit')}}
                                </button>
                                @if($device->isPublic==0)
                                <button type="button" name="del" class="btn btn-danger btn-sm" @click="delCheck({{$loop->index}})">
                                    {{__('layout.delete')}}
                                </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Edit device-->
    <div v-show="tab==2" class="row justify-content-center main-content">
        <div class="col-lg-12">
            <div class="card shadow-lg border-0 rounded-lg mt-2">
                <div class="card-header">
                    <!-- edit company data -->
                    <span v-if="device.id>0" class="font-weight-bold text-center font-weight-light mr-4">
                        {{__('layout.edit')}}{{__('layout.data')}}
                    </span>
                    <!-- Add company data -->
                    <span v-else class="font-weight-bold text-center font-weight-light mr-4">
                        {{__('layout.add')}}{{__('layout.data')}}
                    </span>
                    <span v-if="device.id==0" class="ml-5">
                        請先至產品管理新增產同類型產品，在專屬註冊碼下拉選單選擇加入的產品註冊碼
                    </span>
                </div>
                <div class="card-body">
                    <form method="post" id="editForm">
                        <input type="hidden" name="_method" value="put" />
                        <input type="hidden" name="id" v-model="device.id" />
                        <input type="hidden" name="macAddr" v-model="device.macAddr"/>
                        <input type="hidden" name="make_command" value="0"/>
                        <input type="hidden" name="user_id" v-model="device.user_id"/>
                        <input type="hidden" name="isPublic" v-model="device.isPublic"/>
                        <input type="hidden" name="setting_id" v-model="isRoom"/>
                        <input type="hidden" name="room_id" v-model="selectRoom_id"/>
                        <input type="hidden" name="product_id" v-model="device.product_id"/>

                        {{csrf_field()}}
                        <div class="form-row">
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >{{__('device.device_name')}}</span>
                                </div>
                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="device.device_name" placeholder="未填時以註冊碼為別名" name="device_name">
                            </div>
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">{{__('device.device_mac')}}</span>
                                </div>
                                <input v-if="device.id>0" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="device.macAddr" name="mac" disabled>
                                <select  v-else v-model="device.macAddr" class="custom-select" name="mac" @change="changeProduct($event)">
                                    <option v-for="product in productList" :value="product.macAddr" :key="product.id">
                                        @{{ product.macAddr }}
                                    </option>
                                </select>
                            </div>
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="inputGroupSelect01">{{__('layout.types')}}</label>
                                </div>
                                <select  v-model="device.type_id" class="custom-select" name="type_id">
                                    <option v-for="type in typeList" :value="type.type_id" :key="type.type_id">
                                        @{{ type.type_name }}
                                    </option>
                                </select>
                            </div>

                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="inputGroupSelect01">是否啟用</label>
                                </div>
                                <select  v-model="device.status" class="custom-select" name="status">
                                    <option v-for="act in actList" :value="act.id" :key="act.id">
                                        @{{ act.value }}
                                    </option>
                                </select>
                            </div>


                            <div class="input-group mb-3 col-md-4">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="inputGroupSelect01">綁定用戶</label>
                                </div>
                                <select  v-model="device.user_id" class="custom-select" name="user_id" @change="isRoom=false">
                                    <option v-for="tmp in userList" :value="tmp.id" :key="tmp.id">
                                        @{{ tmp.name }}
                                    </option>
                                </select>
                            </div>
                            <!-- The rooms for custom devices -->
                            <div class="input-group mb-3 col-md-2">
                                @if($type_id > 101)
                                    <div v-if="device.user_id>0" class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-default" >加入場域?</span>
                                    </div>
                                    <input v-if="device.user_id>0" type="checkbox" class="form-control" v-model="isRoom">
                                @endif
                            </div>

                            <div v-if="isRoom==true" class="input-group mb-3 col-md-6">

                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="inputGroupSelect01">選擇場域</label>
                                </div>
                                <select v-if="roomList.length>0" v-model="selectRoom_id" class="custom-select" name="support">
                                    <option v-for="room in roomList" :value="room.id" :key="room.id">
                                        @{{ room.room_name }}
                                    </option>
                                </select>
                                <input v-else class="form-control" value="沒有場域" disabled>

                            </div>
                            @if($type_id > 101)
                                <div class="input-group mb-3 col-md-6">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="inputGroupSelect01">{{__('device.network_connection')}}</label>
                                    </div>
                                    <select  v-model="device.network_id" class="custom-select" name="network_id">
                                        <option v-for="network in networkList" :value="network.id" :key="network.id">
                                            @{{ network.network_name }}
                                        </option>
                                    </select>
                                </div>
                            @endif
                        <!-- The support only for USV : type_id:102 -->
                            @if($type_id == 102)
                                <div class="input-group mb-3 col-md-6">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="inputGroupSelect01">外掛</label>
                                    </div>
                                    <select  v-model="device.support" class="custom-select" name="support">
                                        <option v-for="support in supportList" :value="support.id" :key="support.id">
                                            @{{ support.value }}
                                        </option>
                                    </select>
                                </div>
                            @endif
                        <!-- Only for custom devices -->
                            @if($type_id > 101)
                                <div class="input-group mb-3 col-md-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-default" >是否為公版</span>
                                    </div>
                                    <input type="checkbox" class="form-control" v-model="device.isPublic">
                                </div>
                        @endif
                        <!--<div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">加入密室</span>
                                </div>
                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="device.setting_id" name="setting_id">
                            </div>-->
                            <div class="col-md-12">
                                <button type="button" class="btn btn-secondary" @click="back()">{{__('layout.back')}}</button>
                                <button type="button" class="btn btn-primary" @click="toSubmit()">{{__('layout.submit')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Device QRCode list -->
    <div v-show="tab==3" class="main-content">
        <div class="card shadow-lg border-0 rounded-lg mt-2">
            <div class="card-header">
                <span class="font-weight-bold text-center font-weight-light ml-2">
                    控制器QRCode列表
                </span>
                <span class="ml-2">
                    <button type="button" class="btn btn-secondary btn-sm" @click="print();">列印</button>
                </span>
            </div>
            <div class="card-body">
                <table id ="table2"  class="table table-striped table-hover">
                    <tbody >
                    @foreach ($devices as $device)
                        @if($loop->index %2 == 0)
                            <tr >
                                <td>

                                    <div id="qrcode{{$loop->index}}" ></div>
                                    <div> <h4>註冊碼 {{$devices[$loop->index]->macAddr}}</h4>  </div>
                                </td>

                                <td>
                                    @if($devices->count()>$loop->index +1)
                                        <div id="qrcode{{$loop->index +1}}" ></div>
                                        <div> <h4>註冊碼 {{$devices[$loop->index+1]->macAddr}}</h4>  </div>
                                    @endif
                                </td>

                            </tr>
                        @endif
                    @endforeach
                    </tbody>
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
                    {{__('layout.delete_confirm')}}@{{device.device_name}}?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delDevice" id="delDevice">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="device.id" />
                        {{csrf_field()}}
                        <button type="button" onClick="toDelete()" class="btn btn-danger">
                            {{__('layout.yes')}}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
    <script>
        let networks = {!! json_encode($networks->toArray())!!};
        let supports = {!! json_encode($supports)!!};
        let userRooms =  {!! json_encode($userRooms)!!};
        let types = {!! $types !!};
        let devices = {!! $devices !!};
        let products = {!! $products !!};
        let type_id = {!! $type_id !!};
        let category = {!! $category !!};
        let app_url = '{{ env('APP_URL')}}';
        let token = '{{Session::get('user')->remember_token}}';
        let user_id = {{Session::get('user')->id}};
        let users = {!! $users !!};
        let api_url = '{!! env('API_URL') !!}';
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/QRCode/qrcode.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/devices.js')}}"></script>
@endsection



