@extends('Layout.diy')

@section('content')
    <div class="breadcrumb">
        <div class="col-md-8">
            <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page"> <a href="/node/myDevices?link=develop">
                        我的控制器</a>
                </li>

                <li class="breadcrumb-item active" aria-current="page">{{$myTarget->device_name}} - 應用列表</li>
            </ol>

        </div>


        <div v-cloak class="col-md-4">
            @if($apps->count() < $app_limit || $user->role_id <3)
                <button v-show="!isNew" id="step1" type="button" class="btn btn-primary text-right ml-2 float-lg-right" @click="newCheck()">
                    {{__('app.add_http_app')}} <i class="fa fa-plus"></i>
                </button>
            @endif
            @if($data['type_id'] > 101)
                <button v-show="!isNew" id="step1" type="button" class="btn btn-info text-right float-lg-right" @click="updateApiKey()">
                    更新控制器所有API KEY
                </button>
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
    @if ($app_id == 0)
    <div v-show="!isNew" class="row main-content">

        <div class="col-md-3 mt-3">
            <ul class="list-group">
                <li class="list-group-item">
                    <h5>{{__('device.select_device') }}</h5>
                </li>

                @foreach ($devices as $device)
                    <li class="list-group-item" >
                        <a href="{{url('/node/apps/?device_id='.$device->id.'&user_id='.$user_id)}}"><h5>{{$device->device_name}}</h5></a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="col-md-9 mb-2 mt-2 text-left">
            <table id ="table1"  class="table table-striped table-hover">
                <thead>
                <tr>
                    <th >{{__('layout.item')}}</th>
                    <th >{{__('app.name')}}</th>
                    @if($user->role_id<2)
                    <!--<th >Sequence</th>-->
                    @endif
                    <!--<th >{{__('device.device_mac')}}</th>-->
                    <th >{{__('layout.update_at')}}</th>
                    <th > </th>
                </tr>

                </thead>

                <tbody >
                @foreach ($apps as $app)
                    <tr>
                        <td> {{$loop->index +1}} </td>

                        <td width="40%">
                            <div>{{$app->name}}</div>
                            <div>
                                <button class="btn btn-info btn-sm" type="button" onclick="location.href='{{url('/node/apps/reports?app_id=')}}{{$app->id}}'">
                                    {{__('app.app_management') }} <i class="fa fa-database"></i>
                                </button>
                                <!--<button type="button" name="del" class="btn btn-danger btn-sm" @click="delDataCheck({{$loop->index}})">
                                    {{__('layout.delete')}}上報資料 <i class="fa fa-trash"></i>
                                </button>-->
                            </div>
                            </div>
                        </td>
                        @if($user->role_id<2)
                        <!--<td> {{$app->sequence}} </td>-->
                        @endif
                        <!--<td> {{$app->macAddr}} </td>-->
                        <td> {{$app->updated_at}} </td>
                        <td>

                            <button type="button" name="del" class="btn btn-danger btn-sm" @click="delAppCheck({{$loop->index}})">
                                {{__('layout.delete')}}應用 <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach

                </tbody>
            </table>
        </div>

    </div>
    @endif
    <!-- Edit App-->
    <div v-cloak v-show="isNew" class="row justify-content-center main-content">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-lg mt-2">
                <div class="card-header">
                    <!-- edit app data -->
                    <h3 v-if="appObj.id>0" class="text-center font-weight-light my-4">
                        {{__('layout.set')}}{{$data['device_name']}}{{__('device.device_app')}}
                    </h3>
                    <!-- Add app data -->
                    <h3 v-else class="text-center font-weight-light my-4">
                        {{__('layout.add')}}{{$data['device_name']}}{{__('device.device_app')}}
                    </h3>
                </div>
                <div class="card-body">
                    <form method="post" id="editForm">
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

                            <!-- app device mac -->
                            @if($user->role_id<2)
                            <div class="input-group mb-3 col-md-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >Sequence</span>
                                </div>
                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="appObj.sequence">
                            </div>
                            <div class="input-group mb-3 col-md-3">
                                @if($type_id==102)
                                <div class="input-group-prepend block">
                                    <span class="form-control "> 1~5:航訊或測試, 6^:上報</span>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                        <div class="row text-center">
                            <!-- app name -->
                            <div class=" mb-3 col-md-2 text-center">
                                <h4  class="text-center">{{__('layout.select')}}</h4>
                            </div>
                            <div class=" mb-3 col-md-5">
                                <h4>{{__('layout.field_name')}}</h4>
                            </div>
                            <!--<div class="mb-3 col-md-5">
                                <h4 v-if="isParse">{{__('layout.parse_rule')}}</h4>
                            </div>-->

                        </div>

                        <div v-for = "(item, index) in appObj.fieldList" class="form-row">
                            <div class="input-group mb-1 col-md-2">
                                <input v-if="index==0" id="step3" type="checkbox" class="form-control" v-model="item.check">
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
                            <!--<div v-if="isParse" class="input-group mb-1 col-md-5">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >parse@{{ index+1 }}</span>
                                </div>
                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="item.parse" :disabled="!item.check">
                                <!--<input type="checkbox" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">-->
                            <!--</div>-->

                        </div>
                        <div class="col-md-12 mt-2">
                            <button type="button" class="btn btn-secondary" @click="back()">{{__('layout.back')}}</button>
                            <button id="step5" type="button" class="btn btn-primary" @click="toSubmit()">{{__('layout.submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal delete App -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{__('app.delete_waring')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    {{__('layout.delete_confirm')}} [@{{appObj.name}}] ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" id="delForm">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="appObj.id" />
                        {{csrf_field()}}
                        <button type="button" onClick="toDelete()" class="btn btn-danger" >
                            {{__('layout.yes')}}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal delete App data-->
    <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">
                        {{__('layout.delete')}}{{__('layout.waring')}}
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    {{__('layout.delete_confirm')}} [@{{appObj.name}}] {{__('layout.reports')}}?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delReports"  id="delReports">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="appObj.id" />
                        {{csrf_field()}}
                        <button type="button" onClick="toDeleteReports()" class="btn btn-danger" >
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
        let apps = {!! $apps !!};
        let devices = {!! $devices !!};
        let device_id = {!! $device_id !!};
        let app_id = {!! $app_id !!};
        let target = {!! $myTarget !!};
        let user = {!! $user !!};
        let url = "{{$_SERVER['HTTP_HOST']}}";
        let field_required = "{{__('app.field_required') }}";
        let name_required = "{{__('app.name_required') }}";
        let data = {!! json_encode($data) !!};
        let app_url = '{{ env('APP_URL')}}';
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/node/apps.js')}}"></script>
@endsection



