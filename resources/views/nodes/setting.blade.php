@extends('Layout.diy')

@section('content')
    <div class="row breadcrumb mt-1">
        <div class="col-md-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/node/apps">{{__('device.device_app') }}</a></li>
            <!--<li class="breadcrumb-item">{{__('layout.devices') }}</li>-->
                <li class="breadcrumb-item active" aria-current="page">{{__('device.device_setting') }}</li>
            </ol>
        </div>
        <div class="col-md-3 mt-2 text-left">
            {{__('layout.devices') }}
            <select onchange="location.href='/node/setting/'+this.options[this.selectedIndex].value">
                @foreach ($devices as $device)
                    @if ($device->id == $device_id)
                        <option value="{{$device->id}}" selected="selected">{{$device->device_name}}</option>
                    @else
                        <option value="{{$device->id}}">{{$device->device_name}}</option>
                    @endif

                @endforeach
            </select>

        </div>

    </div>

    <div class="col-md-12 mt-1 text-center">
        @if($setting->make_command == null)
            <div class="alert alert-danger" id="message">
                {{__('device.no_make_command') }}
            </div>
        @endif
            <div class="alert alert-info">
                <div>勾選自己創建命令: 需自行到命令管理,編輯自己的控制命令</div>
                <div>不勾選自己創建命令: 系統為你的裝置預設了一些控制命令</div>
                <div>請確定是否自行創建命令後按[儲存設定]按鍵離開設定</div>
            </div>
    </div>

    <!-- Edit command-->
    <div class="row justify-content-center main-content">

        <div class="col-lg-12">
            <div class="card shadow-lg border-0 rounded-lg mt-2">
                <div  class="card-header">
                    <!-- edit data -->
                    <h3 class="text-center font-weight-light my-4">
                        {{__('layout.edit')}}{{__('device.device_setting')}}
                    </h3>
                </div>

                <div class="card-body">
                    <form method="post" id="editForm">
                        <input type="hidden" name="_method" value="put" />
                        <input type="hidden" name="device_id" value="{{__('device.device_id')}}" />
                        {{csrf_field()}}
                        <div class="form-row">
                            <div class="col-md-12 ">
                                <div class="row">
                                    <!--  Show device id -->
                                    <div class="input-group mb-3 col-md-6">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-default" >{{__('device.device_id')}}</span>
                                        </div>
                                        <input type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" value="{{$setting->device_id}}" disabled="">
                                    </div>
                                    <!--  Show command is diy or not -->
                                    <div class="input-group mb-3 col-md-6">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-default" >{{__('device.make_command') }}</span>
                                        </div>
                                        @if($setting->make_command == null || $setting->make_command == false)
                                            <input type="checkbox" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="make_command" onfocus="disableMsg()">
                                        @else
                                            <input type="checkbox" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" name="make_command" checked="checked" onfocus="disableMsg()">
                                        @endif
                                    </div>



                                    <!-- Selected back or submit -->
                                    <div class="col-md-12 text-right mb-2">
                                        <!--<button type="button" class="btn btn-secondary" @click="backCommandObjList">{{__('layout.back')}}</button>-->
                                        <!--<button type="button" class="btn btn-primary" @click="toSubmit()">{{__('layout.submit')}}</button>-->
                                        <input type="submit" value="儲存設定">
                                    </div>


                                </div>
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
                    <h4 class="modal-title" id="myModalLabel">{{__('layout.waring')}}!</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('layout.delete_confirm')}} @{{setting.id}} ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" id="delForm">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="commandObj.id" />
                        {{csrf_field()}}
                        <button type="button" onClick="toDelete()" class="btn btn-danger" >
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
        let devices = {!! $devices !!};
        let user = {!! $user !!};
        let device_id = {!! $device_id !!};
        function disableMsg() {
            let msg = document.getElementById("message");
            //console.log(msg)
            if(msg !== null)
                document.getElementById("message").remove();
        }
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <!--<script src="{{asset('js/node/commands.js')}}"></script>-->
@endsection



