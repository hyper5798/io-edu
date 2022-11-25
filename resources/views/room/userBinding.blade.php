@extends('Layout.room')

@section('content')
    <div class="row breadcrumb">
        <div class="col-md-3">
            <!--<ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">{{__('layout.index_title') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">裝置綁定</li>
            </ol>-->
            <label class="font-weight-bold mt-1">綁定控制器</label>
        </div>
        <div class="col-md-3 mt-2 text-left">

        </div>
        <div class="col-md-3 mt-2 text-left">

        </div>

        <div class="col-md-3 text-right mt-1">

        </div>
    </div>
    @if (count($errors) > 0)
        <div class="alert alert-danger mt-2" id="message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <!--Bind flow -->
    <div v-cloak v-show="isFlow!=0" class="main-content mt-2">
        <div class="row justify-content-center">
            <div class="col-10">
                <div class="card rounded-lg">
                    <div class="card-header">
                        <label class="font-weight-bold mt-1 ml-3">綁定流程</label>
                        @if( $cps!=null && $cps->count()>0 && $user->role_id < 3)
                            <span class="ml-3">

                                <select onchange="location.href='?cp_id='+this.options[this.selectedIndex].value+'&user_id={{$user_id}}'">
                                   @foreach ($cps as $item)
                                        @if ($item->id == $cp_id)
                                            <option value="{{$item->id}}" selected="selected">{{$item->cp_name}}</option>
                                        @else
                                            <option value="{{$item->id}}">{{$item->cp_name}}</option>
                                        @endif
                                    @endforeach
                                </select>

                            </span>

                        @endif
                        @if( $users!=null && $users->count()>0 && $user->role_id < 3)
                            <span class="ml-3">

                                <select onchange="location.href='?user_id='+this.options[this.selectedIndex].value+'&cp_id={{$cp_id}}'">
                                   @foreach ($users as $item)
                                        @if ($item->id == $user_id)
                                            <option value="{{$item->id}}" selected="selected">{{$item->name}}</option>
                                        @else
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                        @endif
                                    @endforeach
                                </select>

                            </span>`
                        @endif
                        <span v-if="isFlow==2 || isFlow==3" class="float-right">
                            <button type="button" class="btn btn-success btn-sm mt-1 mr-3" @click="newCheck()">{{__('layout.add')}}控制器</button>
                        </span>
                    </div>
                    <div v-cloak class="card-body">
                </div>
                    <div v-cloak class="card-body">

                        <div v-if="isFlow==1" class="row justify-content-center">
                            <table>
                                <tr>
                                    <td>
                                        <div class="flow-point-action rounded-circle">
                                            1.綁定控制器
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flow-line-none"></div>
                                    </td>
                                    <td>
                                        <div class="flow-point-none rounded-circle">
                                            2.控制器分享帳戶
                                        </div>
                                    </td>
                                </tr>

                            </table>
                        </div>

                        <div v-if="isFlow==2 || isFlow==3 || isFlow==4" class="row justify-content-center">
                            <table>
                                <tr>
                                    <td>
                                        <div class="flow-point-none rounded-circle">
                                            1.綁定控制器
                                        </div>
                                    </td>
                                    <td>
                                        <div class="flow-line"></div>
                                    </td>
                                    <td>
                                        <div class="flow-point-action rounded-circle">
                                            2.控制器分享帳戶
                                        </div>
                                    </td>
                                </tr>

                            </table>
                        </div>


                        <!-- Edit device-->
                        <div v-if="isFlow==1" class="mt-5 mb-5 ml-3">
                            <div class="ml-3">
                                <span v-if="!isVerify">請先輸入專屬註冊碼輸入後按校驗按鍵，驗證是否本公司產品。</span>
                                <span v-cloak v-else>接著綁定你購買的控制器到你的場地，請輸入
                                    <span v-if="device.id==0">(或選擇)</span>
                                    場地及控制器別名，輸入後按提交按鍵。
                                </span>
                            </div>

                            <div class="mt-2 ml-3">
                                <form method="post" action="editUserDevice" id="editUserDevice">
                                    <input type="hidden" name="id" v-model="device.id" />
                                    <input type="hidden" name="macAddr" v-model="device.macAddr"/>
                                    <input type="hidden" name="make_command" value="0"/>
                                    <input type="hidden" name="type_id" v-model="device.type_id"/>
                                    <input type="hidden" name="room_name" v-model="room.room_name"/>
                                    <input type="hidden" name="room_id" v-model="room.id"/>
                                    <input type="hidden" name="user_id" v-model="userId"/>
                                    {{csrf_field()}}
                                    <div class="row">
                                        <div class="input-group mb-3 col-md-6">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-default">{{__('device.device_mac')}}</span>
                                            </div>
                                            <input v-if="device.id>0" type="text" class="form-control" aria-label="Sizing example input"
                                                   aria-describedby="inputGroup-sizing-default" v-model="device.macAddr" disabled>
                                            <input v-else type="text" class="form-control" aria-label="Sizing example input"
                                                   aria-describedby="inputGroup-sizing-default" v-model="device.macAddr" placeholder="{{__('device.filled_mac_prompt')}}">
                                        </div>
                                        <div class="input-group mb-3 col-md-6"></div>
                                        <div class="input-group mb-3 col-md-6">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-default" >{{__('device.device_name')}}</span>
                                            </div>
                                            <input  v-if="isVerify==false ||  room.room_name==''" type="text" class="form-control"  v-model="device.device_name" name="device_name" disabled>
                                            <input  v-else type="text" class="form-control"  v-model="device.device_name" name="device_name" placeholder="輸入別名，未輸入別名以註冊碼作別名">
                                        </div>

                                        <div class="input-group mb-3 col-md-6"></div>

                                        <div v-if="isVerify==true" class="input-group mb-3 col-md-6">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-default" >場地名稱</span>
                                            </div>
                                            <input  type="text" class="form-control"
                                                   v-model="room.room_name" name="room_name" placeholder="請填入場地名稱"
                                                    size="8" maxlength="8" :disabled="room.id != 0">

                                        </div>
                                        <div v-if="isVerify==true" class="input-group mb-3 col-md-6">
                                            <button v-if="room.id>0 && roomList.length<maxRoomLength && device.id==0" type="button" class="btn btn-success btn-sm ml-2" @click="addRoomCheck()">
                                                新場地
                                            </button>

                                            <button v-if="room.id > 0" type="button" class="btn btn-danger btn-sm ml-2" @click="delRoomCheck()">
                                                刪除場地
                                            </button>

                                        </div>
                                        <div v-if="isVerify==true && room.id>0" class="input-group mb-3 col-md-12">

                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="inputGroup-sizing-default" >更換場地</span>
                                                </div>

                                                <span class="ml-3">


                                                    <select v-model="room.id" @change="onChangeRoom($event)" class="form-control">

                                                        <option v-for="(item, index) in roomList" :value="item.id" :key="item.id" >
                                                            @{{ item.room_name }}
                                                        </option>
                                                    </select>

                                                </span>


                                            <span class="mt-2 ml-3 text-info">已建立 @{{ roomList.length }} 場地 , 限制 @{{ maxRoomLength }} 場地</span>
                                        </div>



                                        <div class="col-md-12 mb-2">
                                            <button type="button" class="btn btn-secondary" @click="back()">{{__('layout.back')}}</button>
                                            <span v-if="isVerify">
                                                <button type="button" class="btn btn-success" @click="toSubmit()">{{__('layout.submit')}}</button>
                                            </span>
                                            <span v-else>
                                                <button type="button" class="btn btn-primary" @click="toVerify()">{{__('layout.verify')}}</button>
                                            </span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- 控制器列表 -->

                        <div v-if="isFlow==2" class="mt-5 mb-5 ml-3">
                            <span class="text-info">控制器列表 </span>
                            <span>請點選控制器分享帳戶按鍵來分享。</span>
                            <div class="main-content mt-3">
                                <table id ="table1"  class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th >{{__('layout.item')}}</th>
                                        <th >{{__('device.device_name')}}</th>
                                        <th >{{__('device.device_mac')}}</th>

                                        <th >管理用戶</th>

                                        <th >{{__('layout.update_at')}}</th>
                                        <th > </th>
                                    </tr>

                                    </thead>

                                    <tbody >
                                    @foreach ($devices as $device)
                                        <tr>
                                            <td> {{$loop->index +1}} </td>
                                            <td> {{$device->device_name}} </td>
                                            <td> {{$device->macAddr}} </td>
                                            <td> {{$device->name}} </td>
                                            <td> {{$device->updated_at}} </td>
                                            <td>
                                                <button @v-if="editPoint!={!! $loop->index !!}" type="button" name="edit" class="btn btn-info btn-sm" @click="bindingUser({!! $loop->index !!})">
                                                    分享帳戶
                                                </button>
                                                <button @v-if="editPoint!={!! $loop->index !!}" type="button" name="edit" class="btn btn-primary btn-sm" @click="editCheck({!! $loop->index !!})">
                                                    {{__('layout.edit')}}
                                                </button>
                                                <button type="button" name="del" class="btn btn-danger btn-sm" @click="delCheck({{$loop->index}})">
                                                    {{__('layout.delete')}}
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 分享帳戶列表-->
                        <div v-if="isFlow==3" class="mt-5 mb-5 ml-3">

                            <div class="main-content">
                                <span>控制器 ( @{{ device.device_name }} ) 分享帳戶列表</span>
                                <button type="button" name="button" class="float-lg-right btn btn-secondary btn-sm mb-3 mr-2" @click="isFlow=2;">
                                    返回
                                </button>
                                <button v-if="userList.length<4" type="button" class="float-lg-right btn btn-success btn-sm mb-3 mr-2 " @click="addNewUser()">
                                    新增分享帳戶
                                </button>

                                <table id ="table1"  class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th >{{__('layout.item')}}</th>
                                        <th >用戶暱名</th>
                                        <th >電子信箱</th>
                                        <th >{{__('layout.update_at')}}</th>
                                        <th > </th>
                                    </tr>

                                    </thead>
                                    <tr v-for="(item, key) in userList" :key="key">
                                        <td> @{{key+1}} </td>
                                        <td>
                                            <input v-if="item.id==0" type="text" class="form-control"  name="name" v-model="user.name">
                                            <label v-else>@{{item.name}}</label>
                                        </td>
                                        <td>
                                            <input v-if="item.id==0" type="text" class="form-control"  name="email" v-model="user.email">
                                            <label v-else>@{{item.email}}</label>
                                        </td>
                                        <td> @{{item.updated_at}} </td>
                                        <th >

                                            <button type="button" name="edit" class="btn btn-primary btn-sm" @click="editUser(key)">
                                                編輯帳戶
                                            </button>

                                            <button v-if="key>0" type="button" name="edit" class="btn btn-danger btn-sm" @click="deleteUser(key)">
                                                刪除帳戶
                                            </button>
                                        </th>
                                    </tr>
                                    <tbody >


                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 綁定帳戶 -->
                        <div v-if="isFlow==4" class="mt-5 mb-5 ml-3">
                            <span>請輸入分享用戶名稱及電子信箱。</span>
                            <div class="main-content mt-3">
                                <form method="post" action="bindingUser" id="bindingUser">
                                    <input type="hidden" name="id" v-model="user.id" />
                                    <input type="hidden" name="password" v-model="user.password" />
                                    <input type="hidden" name="email" v-model="user.email" />
                                    <input type="hidden" name="name" v-model="user.name" />
                                    <input type="hidden" name="cp_id" v-model="user.cp_id" />
                                    <input type="hidden" name="role_id" v-model="user.role_id" />
                                    <input type="hidden" name="device_id" v-model="device.id" />
                                    <input v-if="user.group_role_id != null" type="hidden" name="group_role_id" v-model="user.group_role_id" />

                                    {{csrf_field()}}
                                    <div class="form-row ">
                                        <div class="col-sm-12 col-md-6">
                                            <!--User Name -->
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">{{__('user.name') }}</span>
                                                </div>
                                                <input type="text" class="form-control" v-model="user.name" name="name">
                                            </div>
                                            <!--User Email -->
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">{{__('user.email') }}</span>
                                                </div>
                                                <input v-if="user.id==0" type="text" class="form-control"  v-model="user.email">
                                                <input v-else type="email" class="form-control"  v-model="user.email" disabled>
                                            </div>

                                            <div>
                                                {{__('user.warning6')}}
                                                <span class="float-right">
                                        <button type="button" class="btn btn-secondary" @click="backUserList()">{{__('layout.back')}}</button>
                                        <button type="button" class="btn btn-primary" @click="toBindingUser()">{{__('layout.submit')}}</button>
                                    </span>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
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
                    <span v-if="isFlow==2">{{__('layout.delete_confirm')}}@{{device.device_name}}?</span>
                    <span v-else>{{__('layout.delete_confirm')}}@{{user.name}}?</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delUserDevice" id="delUserDevice">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="device.id" />
                        {{csrf_field()}}
                        <button v-if="isFlow==2" type="button" onClick="toDelete()" class="btn btn-danger">
                            {{__('layout.yes')}}
                        </button>
                    </form>
                    <form method="post" action="delBindUser" id="delBindUser">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="user.id" />
                        <input type="hidden" name="device_id" v-model="device.id" />
                        {{csrf_field()}}
                        <button v-if="isFlow==3" type="button" @Click="toDeleteUser()" class="btn btn-danger">
                            {{__('layout.yes')}}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal2 -->
    <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel">{{__('layout.waring')}}!</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <span>
                            {{__('layout.delete_confirm')}}@{{room.room_name}}?會一併刪除綁定的裝置!
                        </span>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal">{{__('layout.cancel')}}
                        </button>
                        <button type="button" @Click="toDeleteRoom()" class="btn btn-danger">
                            {{__('layout.yes')}}
                        </button>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('footerScripts')
    <script>
        let networks = {!! json_encode($networks->toArray())!!};
        let devices = {!! $devices !!};
        let deviceUsers = {!! json_encode($deviceUsers) !!};
        let deviceRooms = {!! json_encode($deviceRooms) !!};
        let app_url = '{{ env('APP_URL') }}';
        let token = '{{$user->remember_token}}';
        @if($room == null)
            let room = null;
        @else
            let room = {!! $room !!};
        @endif
        let rooms = {!! $rooms !!};
        let api_url = '{!! env('API_URL') !!}';
        let user_id = {!! $user_id !!};
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/room/userBinding.js')}}" crossorigin="anonymous"></script>
@endsection



