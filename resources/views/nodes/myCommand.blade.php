@extends('Layout.diy')

@section('content')
    <div class="row breadcrumb mt-1">
        <div class="col-md-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/node/myDevices?link=develop">{{__('device.my_devices') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{__('layout.command_list') }}</li>
            </ol>
        </div>
        <div class="col-md-4 mt-2 text-left">
        <!--{{__('device.select_order_device') }}
            <select onchange="location.href='myCommand?device_id='+this.options[this.selectedIndex].value">

                @foreach ($devices as $device)
                    @if ($device->id === $device_id)
                        <option value="{{$device->id}}" selected="selected">{{$device->device_name}}</option>
                    @else
                        <option value="{{$device->id}}">{{$device->device_name}}</option>
                    @endif
                @endforeach
            </select>-->

        </div>

        <div class="col-md-3 mt-1">
            @if($user->role_id < 3)
            <!--<div class="input-group">
                <div class="input-group rounded">
                    <input type="text" class="form-control rounded" placeholder="查詢裝置輸入裝置名稱" aria-label="Search"
                           aria-describedby="search-addon" v-model="choice"/>
                    <span class="input-group-text border-0" id="search-addon" @click="find()">
                        <i class="fas fa-search"></i>
                    </span>
                </div>
            </div>-->
            @endif
        </div>
        <div class="col-md-2 mt-1 text-right">
            <button type="button" class="btn btn-primary text-right" @click="newCommand()">
                {{__('layout.add')}}命令 <i class="fa fa-plus"></i>
            </button>
        </div>
    </div>
    @if (count($errors) > 0)
        <div class="alert alert-danger" id="message">
            <ul style="color:red;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif



    <div v-show="isNew==false" class="main-content">
        <!-- Show commandObj list -->
        <table id ="table1"  class="table table-striped table-hover">
            <thead>
            <tr>
                <th >{{__('layout.item')}}</th>
            <!--<th >{{__('layout.type_id')}}</th>-->
                <th >{{__('layout.command_name')}}</th>
            <!--<th >{{__('layout.command')}}</th>-->
                <th >{{__('device.device_control')}}{{__('layout.command')}}</th>
                <th ></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($commands as $command)
                <tr>
                    <td> {{$loop->index +1}} </td>
                <!--<td> {{$command->type_id}} </td>-->
                    <td> {{$command->cmd_name}} </td>
                <!--<td> {{$command->command}} </td>-->
                    <td>
                    <!--<label id="{{$loop->index}}">{{$command->ctlKey}}</label>-->
                        <input type="text" value ="{{$command->ctlKey}}" id="key{{$loop->index}}" maxlength="85" size="85" disabled/>
                    </td>
                    <td>
                        <button type="button" @click="editCommand({{$loop->index}})" class="btn btn-outline-primary ml-1" >
                            {{__('layout.edit')}}{{__('layout.command')}}
                        </button>
                        <!--<button type="button" @click="copyUrl({{$loop->index}})" class="btn btn-outline-info ml-1" >
                            {{__('device.copy_code')}}
                        </button>
                        <button type="button" @click="toSendControl({{$loop->index}})" class="btn btn-outline-primary ml-1" >
                            {{__('device.run_code')}}
                        </button>-->
                        <button type="button" @click="sendCmdCheck({{$loop->index}})" class="btn btn-outline-primary ml-1" >
                        發送參數設定
                        </button>
                    </td>

                </tr>
            @endforeach
            </tbody>

        </table>
    </div>

    <!-- Edit Command -->
    <div v-cloak v-show="isNew==true" class="row justify-content-center main-content">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-lg mt-2">
                <div class="card-header">
                    <!-- edit company data -->
                    <h3 v-if="commandObj.id>0" class="text-center font-weight-light my-4">
                        {{__('layout.edit')}}{{__('layout.data')}}
                    </h3>
                    <!-- Add company data -->
                    <h3 v-else class="text-center font-weight-light my-4">
                        {{__('layout.add')}}{{__('layout.data')}}
                    </h3>
                </div>
                <div class="card-body">
                    <form method="post" action="editCommand" id="editCommand">
                        <input type="hidden" name="_method" value="put" />
                        <input type="hidden" name="id" v-model="commandObj.id" />
                        <input type="hidden" name="type_id" v-model="commandObj.type_id"/>
                        <input type="hidden" name="device_id" v-model="commandObj.device_id"/>
                        <input type="hidden" name="sequence" v-model="commandObj.sequence"/>
                        <input type="hidden" name="command" v-model="commandObj.command"/>
                        {{csrf_field()}}
                        <div class="form-row">
                            <!-- command sequence -->
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">{{__('layout.command')}}序號</span>
                                </div>
                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="commandObj.sequence" disabled>
                            </div>
                            <!-- command name -->
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >{{__('layout.command_name')}}</span>
                                </div>
                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="commandObj.cmd_name" name="cmd_name">
                            </div>
                            <!-- command -->
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">{{__('layout.command')}}</span>
                                </div>
                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="commandObj.command"  placeholder="自動產生" disabled>
                            </div>
                            <!-- command Update -->
                            <div v-show="commandObj.id>0" class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">是否更新</span>
                                </div>
                                <div class="input-group-text">
                                    <input type="checkbox" aria-label="Checkbox for following text input" v-model="check" name="check">
                                </div>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="button" class="btn btn-secondary" @click="back()">{{__('layout.back')}}</button>
                            <span>
                                <button type="button" class="btn btn-success" @click="toSubmit()">{{__('layout.submit')}}</button>
                            </span>

                            <span v-show="commandObj.id > 0" class="float-right">
                                <button type="button" class="btn btn-danger" @click="delCheck()">{{__('layout.delete')}}</button>
                            </span>
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
                    <h4 class="modal-title" id="myModalLabel">{{__('layout.waring')}}!</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('layout.delete_confirm')}} @{{commandObj.cmd_name}}?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delMyCommand" id="delMyCommand">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="commandObj.id" />
                        {{csrf_field()}}
                        <button type="button" @Click="toDelete()" class="btn btn-danger">
                            {{__('layout.yes')}}
                        </button>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit attach key with command -->
    <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">發送命令</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body row">

                    <div class="clo-12 ml-3 mb-3">
                        <input type="text" id="cmdId" v-model ="cmd" maxlength="120" size="95" />
                        <div class="mt-3">附加參數：</div>
                    </div>

                    <div v-for="(item, key, index) in keyObj" class="col-3 mb-1">

                        <div class="input-group mb-">
                           <div class="input-group-prepend">
                              <span class="input-group-text" id="inputGroup-sizing-default">@{{ key }}</span>
                           </div>
                           <input type="number" class="form-control" v-model="keyObj[key]">
                        </div>
                    </div>

                    <div v-show="isShow" v-cloak class="col-12 alert alert-info" >
                        @{{ prompt }}
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">離開
                    </button>

                    <button type="button" @click="copyUrl();" class="btn btn-outline-info ml-1" >
                        {{__('device.copy_code')}}
                    </button>
                    <button type="button" @click="toSendControl();" class="btn btn-outline-primary ml-1" >
                        {{__('device.run_code')}}
                    </button>

                </div>
            </div>
        </div>
    </div>


@endsection

@section('footerScripts')
    <script>
        let commands = {!! $commands !!};
        let device_id = {!! $device_id !!};
        let type_id = {!! $type_id !!};
        setTimeout(function () {
            let msg = document.getElementById("message");
            //console.log(msg)
            if(msg !== null)
                document.getElementById("message").remove();
        }, 8000);
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/node/myCommand.js')}}"></script>
@endsection



