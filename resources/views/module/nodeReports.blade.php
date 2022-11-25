@extends('Layout.module')

@section('content')

    <div class="breadcrumb">
        <div class="col-sm-6 col-md-6 ">
            <ol class="breadcrumb mt-2">
                <li class="breadcrumb-item"><a href="/node/myDevices?link=module">{{__('device.my_devices') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{$data['device']['device_name']}}</li>
            </ol>
        </div>
        <div class="col-sm-6 col-md-6">
            <div class="mt-1">
                <span>
                    <label class="mr-1">日期:</label>
                </span>
                <span>
                    <label class="ml-2 mr-1">開始</label>
                </span>
                <span>
                    <input id="start" type="text" v-model="start" class="datepicker" maxlength="10" size="10">
                </span>
                <span>
                    <label class="ml-2 mr-1">結束</label>
                </span>
                <span>
                    <input id="end" type="text" v-model="end" class="datepicker" maxlength="10" size="10">
                </span>
                <span class="float-right">
                    <button class="btn btn-primary" @click="search()" data-toggle="tooltip" data-placement="top" title="查詢">
                        查詢 <i class="fas fa-search"></i>
                    </button>
                </span>
            </div>
        </div>

        <div class="col-md-6 text-right">
        <!--<button type="button" class="btn btn-success text-right" @click="newCheck()">{{__('layout.add')}}</button>-->
        </div>
    </div>
    <div v-cloak v-if="showRefreshMsg" class="alert alert-info" role="alert">
        {{__('layout.auto_refresh') }}
    </div>
    <!-- Data management -->
    <div class="row main-content2">
        <div class="col-md-2 mt-3">
            <div class="list-group">
                <!-- 控制器選擇 -->
                <div class="list-group-item bg-dark text-white">
                    {{__('device.select_device') }}
                </div>

                @foreach ($devices as $device)
                    @if($device->id == $device_id)
                        <li class="list-group-item list-group-item-action active" >
                            <a href="{{url('/module/nodeReports?device_id=')}}{{$device->id}}">
                                <label class="text-white">{{$device->device_name}}</label></h5>
                            </a>
                        </li>
                    @else
                        <li class="list-group-item list-group-item-action" >
                            <a href="{{url('/module/nodeReports?device_id=')}}{{$device->id}}">
                                <label class="text-dark">{{$device->device_name}}</label></h5>
                            </a>
                        </li>
                    @endif

                @endforeach
            </div>
        </div>
        <div v-cloak v-show="!isNew" class="col-md-9 mb-2 mt-3 text-left">
            <div class="btn-group mb-2" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-secondary" @click="switchTab(1)">{{__('app.data_chart') }}</button>
                <button type="button" class="btn btn-secondary" @click="switchTab(2)">{{__('app.data_table') }}</button>
                <span><button type="button" id="export" class="btn btn-secondary" >{{__('app.export_csv') }}</button></span>
            <!--<button id="step10" type="button" id="export" class="btn btn-secondary" @click="reload">{{__('app.manual_update') }}</button>-->
                <!--<button type="button" id="export" class="btn btn-secondary" @click="switchRefresh">
                    <span v-if="!isRefresh">{{__('app.start_auto_refresh') }}</span>
                    <span v-else>{{__('app.stop_auto_refresh') }}</span>
                </button>-->
            </div>
            <div v-cloak v-if="gaugeList.length>0" class="btn-group mb-2" role="group">
                <button v-if="chartOption==2" type="button" id="export" class="btn btn-outline-primary" @click="chartOption=1;tab=1;">{{__('app.gauge_chart') }}</button>
                <button v-else type="button" id="export" class="btn btn-outline-primary" @click="chartOption=2;tab=1;">{{__('app.line_chart') }}</button>
            </div>

            <div v-cloak class="btn-group mb-2" role="group">
                <!-- 前500筆數據 -->
                <button  v-if="data.page>1" type="button" id="export" class="btn btn-secondary" @click="prePage">{{__('app.previous_page') }}</button>
                <button type="button" id="export" class="btn btn-secondary" disabled>目前第@{{ data.page }}頁</button>
                <!-- 後500筆數據 -->
                <button v-if="data.limit == reportList.length" type="button" id="export" @click="nextPage" class="btn btn-secondary">{{__('app.next_page') }}</button>
            </div>

            <button type="button" name="del" class="float-right btn btn-danger" @click="delDataCheck()">
                {{__('layout.delete')}}上報資料 <i class="fa fa-trash"></i>
            </button>

            <div v-show="tab==1">
                <div v-if="reportList.length >0">
                    <div v-show="chartOption==0||chartOption==1">
                        <div class="row">
                            <!--Gauge list -->
                            <div v-for="(item,index) in gaugeList" class="col-md-6 mb-3">
                                <div class="card">
                                    <div v-cloak class="card-header">
                                        <div class="d-flex justify-content-between">
                                        <span class="ml-1">
                                            @{{ item.name }}
                                        </span>
                                            <span >
                                             @{{ item.recv }}
                                        </span>
                                            <span v-if="index==0">
                                            <button class="btn btn-secondary" @click="set(index)" data-toggle="tooltip" data-placement="top" title="{{__('app.edit_settings') }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </span>
                                            <span v-else>
                                            <button class="btn btn-secondary" @click="set(index)" data-toggle="tooltip" data-placement="top" title="{{__('app.edit_settings') }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </span>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div :id="item.id" style="height: 350px"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- map start-->
                            <div v-if="isShowMap" class="col-md-12 mb-3">
                                <div class="card">
                                    <div v-cloak class="card-header">
                                        <span class="ml-1">
                                            地圖
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <div id="map" style="height: 500px;width: 100%;"></div>
                                    </div>
                                </div>
                             </div>
                            <!-- map end-->
                        </div>
                    </div>
                    <div v-show="chartOption==0||chartOption==2" id="container" style="height: 350px"></div>
                </div>
                <div v-if="reportList.length == 0">
                    <div class="alert alert-danger" id="message">
                        <h3>{{__('app.no_data_prompt') }}</h3>
                    </div>
                </div>
            </div>
            <div v-show="tab==2">
                <table id ="table1"  class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th >{{__('layout.item') }}</th>
                        <th >{{__('layout.date') }}</th>
                        <!--<th >{{__('device.device_mac') }}</th>-->
                        @foreach($labels as $label)
                            <th >{{$label}}</th>
                        @endforeach
                    </tr>

                    </thead>

                    <tbody >

                    @foreach($reports as $report)
                        <tr>
                            <td>{{$loop->index+1}}</td>
                            <td>{{$report->recv}}</td>
                            <!--<td>{{$report->macAddr}}</td>-->
                            @foreach($dataKeys as $key)
                                <td >
                                    {{ $report->$key}}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            </div>
            <div v-show="tab==3">

            </div>
        </div>
        <!-- Edit setting -->
        <div v-cloak v-show="isNew" class="col-md-10 mb-2 mt-3 text-left">
            <div class="col-md-6 mt-1">
                <div class="card shadow-lg border-0 rounded-lg mt-2">
                    <div class="card-header">
                        <!-- edit setting -->
                        <h3  class="text-center font-weight-light my-4">
                            {{__('app.edit_settings') }}
                        </h3>
                    </div>
                    <div class="card-body">
                        <form method="post" action="editSetting" id="editSetting">
                            <input type="hidden" name="_method" value="put" />
                            <input type="hidden" name="id" v-model="setting.id" />
                            <input type="hidden" name="device_id" v-model="setting.device_id"/>
                            <input type="hidden" name="field" v-model="setting.field"/>
                            <input type="hidden" name="set" v-model="setting.set"/>
                            <input type="hidden" name="page" v-model="data.page"/>
                            <input type="hidden" name="tab" v-model="tab"/>
                            {{csrf_field()}}
                            <div class="form-row">
                                <!--<div class="input-group mb-3 col-md-6">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-default" >選擇類型</span>
                                    </div>
                                    <select v-cloak v-model="setting.set.choice" @change="onChangeChoice($event)">
                                        <option v-for="item in gaugeChoiceList" :value="item.id">
                                            @{{ item.value }}
                                        </option>
                                    </select>
                                </div>-->
                                <div class="input-group mb-3 col-md-6">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-default" >{{__('app.min') }}</span>
                                    </div>
                                    <input type="number" class="form-control" aria-label="Sizing example input"  v-model="setting.set.min">
                                </div>
                                <div class="input-group mb-3 col-md-6">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-default" >{{__('app.max') }}</span>
                                    </div>
                                    <input type="number" class="form-control" aria-label="Sizing example input"  v-model="setting.set.max">
                                </div>
                                <div class="input-group mb-3 col-md-6">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-default" >{{__('app.unit') }}</span>
                                    </div>
                                    <input type="text" class="form-control" aria-label="Sizing example input"  v-model="setting.set.unit">
                                </div>
                                <div>
                                    <div v-for="(item,index) in setting.set.range" class="input-group mb-3 ">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-default" >{{__('app.range') }}</span>
                                        </div>
                                        <select  v-model="setting.set.range[index][0]" class="custom-select" name="status">
                                            <option v-for="option in percentList" :value="option.value" :key="option.name">
                                                @{{ option.name }}
                                            </option>
                                        </select>
                                        <input  :id="'range'+index" class="form-control" data-jscolor="{position:'right'}" v-model = "setting.set.range[index][1]">
                                    </div>
                                </div>

                            </div>
                        </form>
                        <div class="form-row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-secondary" @click="back()">{{__('layout.cancel')}}</button>
                                <button id="step7" type="button" class="btn btn-primary" @click="toSubmit()">{{__('layout.submit')}}</button>
                            </div>
                        </div>
                    </div>
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
                    {{__('layout.delete_confirm')}} {{$data['device']['device_name']}} {{__('layout.reports')}}?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delReports"  id="delReports">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="mac" v-model="data.device.macAddr" />
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
        let reports = {!! $reports !!};
        let settings = {!! $settings !!};
        let labelObj = {!! $labelObj !!};
        let field_required = "{{__('app.field_required') }}";
        let user = {!! $user !!};
        let data = {!! json_encode($data) !!};
        let app_url = '{{ env('APP_URL') }}';
        let start = '{!! $start !!}';
        let type_id = {!! $type_id !!};
        let end = '{!! $end !!}';
        let point_url = "{!! url('/Images/point.png')!!}";
    </script>
    <script src="{{asset('vender/bootstrap-4.3.1/js/bootstrap-datepicker.min.js')}}" ></script>
    <script src="{{asset('vender/bootstrap-4.3.1/js/bootstrap-datepicker.zh-TW.min.js')}}" ></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" charset="utf-8" ></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" ></script>
    <script src="{{asset('vender/DataTables-1.10.20/extensions/Buttons-1.6.2/js/dataTables.buttons.min.js')}}" ></script>
    <!-- Datatable HTML5 button -->
    <script src="{{asset('vender/DataTables-1.10.20/extensions/Buttons-1.6.2/js/buttons.html5.min.js')}}" ></script>
    <script src="{{asset('vender/jscolor/jscolor.js')}}" ></script>
     <script src="{{asset('vender/echarts/echarts.min.js')}}" ></script>
    <script src="{{asset('vender/echarts/theme/roma.js')}}" ></script>
    <script src="{{asset('js/option/chartOption.js')}}"></script>
    <script src="{{asset('js/option/tableOption.js')}}"></script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP_KEY')}}">
    </script>
    <script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/module/nodeReports.js')}}"></script>
@endsection



