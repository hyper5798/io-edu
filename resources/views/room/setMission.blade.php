@extends('Layout.room')

@section('content')
    <!-- Tab -->
    <div class="row mt-3">
        <div class="col-11">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#1">{{__('escape.room') }}</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#2">{{__('escape.mission') }}</a>
                </li>
                @if($user->role_id < 7)
                    <!--<li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#3">{{__('escape.security_devices') }}</a>
                    </li>-->
                @endif
            </ul>
        </div>
        <div class="col-1">
            <button  title="{{__('layout.tutorials') }}" type="button" class="btn btn-warning" onclick="window.location='{{ url("/escape/carousel?app=7&item=1") }}'"><i class="fas fa-question"></i></button>
        </div>
    </div>

    <div v-cloak v-show="target==2" class="row justify-content-center main-content">
        <!-- Edit mission -->
        <div class="col-md-12 col-xl-4">
            <div class="card shadow-lg  rounded-lg mt-3">
                <div  class="card-header">
                    <span v-if="missionList.length>0" class="mission_header ml-2">
                            <!-- 選擇任務 -->
                            {{__('layout.select') }}{{__('escape.mission') }}
                            <select v-cloak v-model="selected" @change="onChangeMission($event)">
                                <option v-for="(item, index) in missionList" :value="index" >@{{item.mission_name}}</option>
                            </select>
                    </span>
                    <span v-if="missionList.length>0" class="float-right mr-2 mt-2">
                        @if($user->role_id < 8)
                            <button type="button" class="btn btn-success btn-sm mr-1" @click="newMission">
                                {{__('layout.add')}}{{__('escape.mission') }}
                            </button>
                        @endif
                    </span>

                </div>
                <!--Edit mission-->
                <div class="card-body">
                    <form method="post" action="editMission" id="editMission">
                        <input type="hidden" name="id" v-model="mission.id" />
                        <input type="hidden" name="room_id" v-model="mission.room_id" />
                        <input type="hidden" name="user_id" v-model="mission.user_id" />
                        <input type="hidden" name="sequence" v-model="mission.sequence" />
                        <input type="hidden" name="macAddr" v-model="mission.macAddr" />
                        <input type="hidden" name="device_id" v-model="mission.device_id" />
                        <input type="hidden" name="pass_time" v-model="mission.pass_time" />
                        <input type="hidden" name="target" v-model="target" />
                        {{csrf_field()}}
                        <div class="form-row">
                            <!-- 密室 -->
                            <div class="input-group mb-3 col-md-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{__('escape.room')}}</span>
                                </div>
                                <input type="text" class="form-control" value="{{$data['room_name']}}" disabled>
                            </div>
                            <!-- 任務 -->
                            <div class="input-group mb-3 col-md-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{__('escape.mission')}}</span>
                                </div>
                                <input type="text" class="form-control" v-model="mission.mission_name" name="mission_name">
                            </div>
                            <!-- 任務序號 -->
                            <div class="input-group mb-3 col-md-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{__('escape.mission_sequence')}}</span>
                                </div>
                                <input type="text" class="form-control" v-model="mission.sequence" disabled>
                            </div>
                            <!-- 闖關時間 -->
                            <!--<div class="input-group mb-3 col-md-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">{{__('escape.pass_time')}}</span>
                                </div>
                                <input type="text" class="form-control" v-model="mission.pass_time" name="pass_time">
                            </div>-->
                            <!-- 裝置 -->
                            @if($user->role_id < 8)
                                <div class="input-group mb-3 col-md-12">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text">{{__('layout.devices')}}</label>
                                    </div>
                                    <!--<input type="text" class="form-control" v-model="mission.macAddr" name="mac">-->
                                    <select v-cloak v-model="mission.macAddr" @change="onChangeDevice($event)">
                                        <option v-for="(item, index) in deviceList" :value="item.macAddr" >@{{item.device_name}}</option>
                                    </select>
                                </div>
                        @endif
                        <!-- 按鍵 -->
                            <div class="col-md-12">
                                <span class="float-right">
                                    @if($user->role_id < 7)
                                        <button type="button" class="btn btn-danger text-right mr-1" @click="deleteMission">
                                            {{__('layout.delete')}}
                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-primary" @click="setMission">
                                        <span v-if="mission.id==0">{{__('layout.set')}}</span>
                                        <span v-else>{{__('layout.reset')}}</span>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Script -->
        <div class="col-md-12 col-xl-8">

            <div  v-cloak v-if="!isEditScript" class="card shadow-lg rounded-lg mt-3">

                <div  class="card-header">
                    <span class="mission_header ml-2">
                        <!-- 劇本 -->
                        @{{ mission.mission_name }} - {{__('escape.script') }}
                    </span>
                    <span class="mission_header float-right mr-2">
                        <button type="button" class="btn btn-success" @click="newScript">
                            {{__('layout.add')}}{{__('escape.script')}}
                        </button>
                    </span>
                </div>
                <!-- mission table -->
                <div class="card-body">
                    <div class="col-lg-12">
                        <table id ="table5"  class="table table-striped">
                            <thead>
                            <tr>
                                <th >{{__('layout.item') }}</th>
                                <th >{{__('escape.script')}}</th>
                                <th > </th>
                            </tr>
                            </thead>

                            <tbody >

                            <tr v-for="(item, index) in scriptList">
                                <td>@{{index}}</td>
                                <td>@{{item.script_name}}</td>
                                <td>
                                    <button type="button" name="edit" class="btn btn-primary btn-sm" @click="editScript(index)">
                                        {{__('layout.edit')}}
                                    </button>
                                    <button type="button" name="del" class="btn btn-danger btn-sm" @click="delScript(index)">
                                        {{__('layout.delete')}}
                                    </button>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div v-else class="card shadow-lg rounded-lg mt-3">
                <div  class="card-header">
                    <!-- edit script -->
                    <span v-if="script.id>0" class="mission_header ml-2">
                         {{__('layout.edit')}}{{__('escape.script')}}
                    </span>
                    <span v-else class="mission_header ml-2">
                         {{__('layout.add')}}{{__('escape.script')}}
                    </span>
                </div>



                <div class="card-body">
                    <div class="form-row mt-2 mb-2">
                        <div class="input-group mb-3 col-md-6">
                            <div class="input-group-prepend">
                                <label class="input-group-text">{{__('escape.script')}}{{__('layout.name')}}</label>
                            </div>
                            <input type="text" class="form-control" v-model="script.script_name">
                        </div>
                        <div class="input-group mb-3 col-md-6">
                            <div class="input-group-prepend">
                                <label class="input-group-text">{{__('escape.mission')}}{{__('layout.name')}}</label>
                            </div>
                            <input type="text" class="form-control" v-model="mission.mission_name" disabled>
                        </div>
                    </div>
                    任務圖片
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex flex-column align-items-center text-center">

                                <img id="script_img" :src="script.image_url"  width="300" height="240">

                            </div>
                            <form method="post" action="uploadScriptImage" id="uploadScriptImage" enctype="multipart/form-data">
                                {{csrf_field()}}
                                <input type="hidden" name="id" v-model="script.id" />
                                <input type="hidden" name="script_name" v-model="script.script_name" />
                                <input type="hidden" name="mission_id" v-model="script.mission_id" />
                                <input type="hidden" name="room_id" v-model="script.room_id" />
                                <input type="hidden" name="content" v-model="script.content" />
                                <div class="form-row mt-2">
                                    <div class="col-8">
                                        <input name="script_img" type="file" id="imgInp" accept="image/gif, image/jpeg, image/png" onchange="custChange(event);" />
                                    </div>
                                    <div class="col-4">
                                        <span class="float-right">
                                            <button type="button" class="btn btn-primary" @click="toUpload()">上傳</button>
                                        </span>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>

                    <form method="post" action="editScript" id="editScript">
                        <input type="hidden" name="id" v-model="script.id" />
                        <input type="hidden" name="target" v-model="target" />
                        <input type="hidden" name="script_name" v-model="script.script_name" />
                        <input type="hidden" name="mission_id" v-model="script.mission_id" />
                        <input type="hidden" name="room_id" v-model="script.room_id" />
                        <input type="hidden" name="note" v-model="script.note" />
                        <!--<input type="hidden" name = "next_pass" v-model="script.next_pass" >-->
                        {{csrf_field()}}
                        <div class="form-row">

                            <!-- 任務內容 -->
                            <div class="form-group mb-3 col-md-12">
                                <label for="exampleFormControlTextarea1">{{__('escape.mission_content')}}</label>
                                <textarea class="form-control" v-model="script.content" name="content" rows="3"></textarea>
                            </div>

                            <!-- 任務提示 -->
                            <div v-if="mission.sequence!=0" class="input-group mb-3 col-md-12">
                                <div class="input-group-prepend">
                                    <label class="input-group-text">{{__('escape.mission_prompt')}}1</label>
                                </div>
                                <input type="text" class="form-control" v-model="script.prompt1" name="prompt1">
                            </div>
                            <div v-if="mission.sequence!=0" class="input-group mb-3 col-md-12">
                                <div class="input-group-prepend">
                                    <label class="input-group-text">{{__('escape.mission_prompt')}}2</label>
                                </div>
                                <input type="text" class="form-control" v-model="script.prompt2" name="prompt2">
                            </div>
                            <div v-if="mission.sequence!=0" class="input-group mb-3 col-md-12">
                                <div class="input-group-prepend">
                                    <label class="input-group-text">{{__('escape.mission_prompt')}}3</label>
                                </div>
                                <input type="text" class="form-control" v-model="script.prompt3" name="prompt3">
                            </div>

                            <!-- 通關密語 -->
                            <div class="input-group mb-3 col-md-12">
                                <div class="input-group-prepend">
                                    <label class="input-group-text">{{__('escape.pass_value')}}</label>
                                </div>
                                <input type="text" class="form-control" v-model="script.pass" name="pass">
                            </div>
                            <!-- 關卡注意事項 -->
                        <!--<div class="input-group mb-3 col-md-12">
                                <div class="input-group-prepend">
                                    <label class="input-group-text">{{__('escape.mission_note')}}</label>
                                </div>
                                <input type="text" class="form-control" v-model="script.note" name="note">
                            </div>-->

                            <div v-if="mission.sequence!=0" class="input-group mb-3 col-md-12 alert alert-info" role="alert">

                                <!-- 設定下一關任務序號時置換的通關密語 -->
                                <!--{{__('escape.next_pass_notify')}}
                                <br>-->
                                <!-- ** 用於上下關卡密碼有關聯時 ** -->
                                <!--{{__('escape.next_pass_notify2')}} -->
                                <!-- 設定通過任務取物密碼 -->
                                設定通過下一關取物密碼
                            </div>

                            <!--<div v-if="mission.sequence!=0" class="input-group mb-3 col-sm-12 col-xl-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text">下一關{{__('escape.mission_sequence')}}</label>
                                </div>
                                <input type="text" class="form-control" v-model="script.next_sequence" name="next_sequence">
                            </div>-->
                            <div v-if="mission.sequence!=0" class="input-group mb-3 col-sm-12 col-xl-6">
                                <!--<div class="input-group-prepend">
                                    <label class="input-group-text">下一關{{__('escape.pass_value')}}</label>
                                </div>
                                <input type="text" class="form-control" v-model="script.next_pass" name = "next_pass">-->
                                    <div class="input-group-prepend">
                                        <label class="input-group-text">下一關取物密碼</label>
                                    </div>
                                    <input type="text" class="form-control" v-model="script.next_pass" name = "next_pass">
                            </div>



                        </div>
                    </form>

                    <div class="col-md-12">
                        <button type="button" class="btn btn-secondary" @click="back()">{{__('layout.back')}}</button>
                        <button type="button" class="btn btn-primary" @click="toSubmit()">{{__('layout.submit')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Delete alert Modal -->
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

                    <span v-show="target==2">
                        <label v-show="!isDelScript">{{__('layout.delete_confirm')}} @{{mission.mission_name}} ? </label>
                        <label v-show="isDelScript">{{__('layout.delete_confirm')}} @{{script.script_name}} ? </label>
                    </span>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>

                    <div v-if="target==2">
                        <form v-if="!isDelScript" method="post" action="delMission" id="delMission">
                            <input type="hidden" name="_method" value="delete" />
                            <input type="hidden" name="id" v-model="mission.id" />
                            <input type="hidden" name="target" v-model="target" />
                            {{csrf_field()}}
                            <button type="button" @click="toDelete" class="btn btn-danger" >
                                {{__('layout.yes')}}
                            </button>
                        </form>
                        <form v-if="isDelScript" method="post" action="delScript" id="delScript">
                            <input type="hidden" name="_method" value="delete" />
                            <input type="hidden" name="id" v-model="script.id" />
                            <input type="hidden" name="target" v-model="target" />
                            {{csrf_field()}}
                            <button type="button" @click="toDelete" class="btn btn-danger" >
                                {{__('layout.yes')}}
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
    <script>
        @if($missions!=null)
            let missions = {!! $missions !!};
        @else
            let missions = [];
        @endif
        let user = {!! $user !!};
        let devices = {!! $devices !!};
        let available = {!! $available !!};
        let menu1 = "{{__('escape.room') }}";
        let menu2 = "{{__('escape.mission') }}";
        let menu3 = "{{__('escape.security_devices') }}";
        let menu = "{{__('escape.script') }}";
        let data = {!! json_encode($data) !!};
        let scripts = {!! $scripts !!};
        let messages = {!! json_encode($messages) !!};
        let mission_id = {!! $mission_id !!};
        let url = '{{ URL::asset('/Images/script_background.png') }}';
        let cp_id = {!! $cp_id !!};
    </script>
    <script src="//cdn.jsdelivr.net/npm/sortablejs@1.8.4/Sortable.min.js"></script>
    <!-- CDNJS :: Vue.Draggable (https://cdnjs.com/) -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/Vue.Draggable/2.20.0/vuedraggable.umd.min.js"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" charset="utf-8" ></script>
    <script src="{{asset('js/option/tableOption.js')}}"></script>
    <script src="{{asset('js/room/setMission.js')}}"></script>
@endsection
