@extends('Layout.diy')

@section('content')
    <div class="breadcrumb">
        <div class="col-md-6">
            <ol class="breadcrumb">
                <ol class="breadcrumb">
                    <li id="step6" class="breadcrumb-item"><a href="/node/myDevices?link=develop">{{__('device.my_devices') }}</a></li>
                    <li class="breadcrumb-item"><a href="{{url('/node/apps/?device_id=')}}{{$device->id}}">{{$device->device_name}} - 應用列表</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$app->name}}</li>
                </ol>
            </ol>
        </div>
        <div class="col-md-6 mt-2 text-left">

        </div>

        <div class="col-md-3 text-right">
        <!--<button type="button" class="btn btn-success text-right" @click="newCheck()">{{__('layout.add')}}</button>-->
        </div>
    </div>

    <div v-show="!isNew" class="row main-content">
        <!-- Sidebar Menu -->
        <div class="col-md-2 mt-3">
            <div class="list-group">
                <!-- Application option -->
                <div class="list-group-item bg-light text-black-50 font-weight-bold">
                    {{__('app.app_option') }}
                </div>
               <!-- Data management -->
                <div class="list-group-item">
                    <a href="{{url('/node/apps/reports?app_id=')}}{{$app->id}}">{{__('app.data_management') }}</a>
                </div>

                <div class="list-group-item">
                    <a href="{{url('/node/apps/channel?app_id=')}}{{$app->id}}">{{__('app.edit_channel') }}</a>
                </div>
                <!-- API key -->

                    <a id="step1" href="{{url('/node/apps/APIkey?app_id=')}}{{$app->id}}" class="list-group-item list-group-item-action active">{{__('app.api_key') }}></a>

            </div>

        </div>
        <!--Generate API Key -->
        <div class="col-md-10 mt-3 text-left">
            <div id="timeselector" class="btn-group btn-group-toggle mb-1" data-toggle="buttons">
                <label class="btn btn-secondary active">
                    <input type="radio" name="options" id="1" autocomplete="off" >{{__('app.set_write_field')}}
                </label>
                <label class="btn btn-secondary ">
                    <input type="radio" name="options" id="2" autocomplete="off" checked>{{__('app.set_read_field')}}
                </label>
                <label class="btn btn-secondary">
                    <input type="radio" name="options" id="3" autocomplete="off">{{__('app.regenerate_api_key')}}
                </label>
            </div>
            <div class="col-md-9">
                <!--app detail -->
                <div class="form-group">
                    <!-- Application detail -->
                    <div><label >{{__('app.app_id')}} : {{$app->id}}</label></div>
                    <div><label >{{__('app.name')}} : {{$app->name}}</label></div>
                    <div><label >{{__('device.device_mac')}} : {{$app->macAddr}}</label></div>
                </div>

                <!-- 設定寫入資料欄位值 -->
                <div v-cloak v-show="tab==1" class="col-md-12 mt-3 text-left">
                    <div class="form-group">
                        <label>
                            {{__('app.set_write_field')}}
                        </label>
                        <div>
                            <table>
                                <tr>
                                    <td>欄位</td>
                                    <td>名稱</td>
                                    <td>設定值</td>
                                </tr>
                                <tr v-cloak v-for="(item, key, index) in labelList">
                                    <td><input  text="text" v-model="item.key" disabled></td>
                                    <td><input  text="text" v-model="item.name" disabled></td>
                                    <td >
                                        <input v-if="item.key=='lat' || item.key=='lng'" text="text" v-model="item.value" maxlength="10" size="10">
                                        <input v-else text="number" v-model="item.value" size="4">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="form-group">
                        <div>
                            <label>
                                API KEY
                            </label>
                            <input type="text" v-model="mykey" id="api_key" maxlength="20" size="20" disabled/>
                            <input type="button" class="btn btn-info " @click="copyKey" value="複製 API KEY" />
                        </div>
                        <div >
                            <label>
                                {{__('app.write_api_url')}}
                            </label>

                            <input type="text" v-model="write_url" id="write_url" maxlength="100" size="100" disabled/>
                        </div>
                       <label>

                        </label>

                        <div class="mt-1">

                            <input type="button" class="btn btn-info " @click="copyUrl" value="{{__('app.copy_api')}}" />
                            <input id="step4" type="button" class="btn btn-primary" @click="toSendControl" value="{{__('app.run_api')}}" />
                        </div>

                    </div>
                </div>

                <!-- 設定讀取資料欄位值 -->
                <div v-cloak v-show="tab==2" class="col-md-12 mt-3 text-left">
                    <div class="form-group">
                        <label>
                            {{__('app.set_read_field')}}
                        </label>
                        <div>
                            <table>
                                <tr>
                                    <td>{{__('layout.field_name')}}</td>
                                    <td>{{__('layout.name')}}</td>
                                    <td>{{__('app.set_value')}}</td>
                                </tr>
                                <tr v-for="(item, index) in findList">
                                    <td><input v-cloak text="text" v-model="item.key" disabled></td>
                                    <td><input v-cloak text="text" v-model="item.name" disabled></td>
                                    <td>
                                        <input v-if="index==0" v-cloak type="number" v-model="item.value" min="1" max="500" size="4">
                                        <input v-else v-cloak type="number" v-model="item.value" min="1" max="5" size="1">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>
                            {{__('app.read_api_url')}}
                        </label>
                        <input type="text" v-model="read_url" id="read_url" maxlength="100" size="100"/>
                        <div class="mt-2">
                            <input type="button" class="btn btn-info " @click="copyUrl2" value="{{__('app.copy_api')}}" />
                            <input type="button" class="btn btn-primary" @click="toSendControl2" value="{{__('app.run_api')}}" />
                        </div>


                    </div>
                </div>

                <!-- api key-->
                <form v-cloak v-show="tab==3" action="{{url('/node/apps/change')}}" method="get" id="editForm">
                    <input type="hidden" name="app_id" value="{{$app->id}}" />
                    <div class="form-group">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-sizing-default">{{__('app.api_key')}}</span>
                            </div>
                            <input type="text" value="{{$app->api_key}}" maxlength="30" size="30" disabled>
                        </div>
                    </div>
                    <div class="form-group">

                        <button type="button" class="btn btn-warning" @click="toSubmit()"> {{__('app.regenerate_api_key')}}</button>

                    </div>
                </form>

                <div v-show="isShow" v-cloak class="alert alert-info mt-3 mb-3" >
                    @{{ prompt }}
                    <button type="button" class="close" @click="clean()" title="清除訊息">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>






    </div>

@endsection

@section('footerScripts')
    <script>
        let keys = [];
        let app_url = '{{ env('APP_URL')}}';
        let api_key = '{{$app->api_key}}';
        let labels = {!! json_encode($labels) !!};
        let token = '{{$user->remember_token}}';
        let tab = {!! $tab !!};
        let user = {!! $user !!};
        let data = {!! json_encode($data) !!};
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/node/apikey.js')}}"></script>
@endsection



