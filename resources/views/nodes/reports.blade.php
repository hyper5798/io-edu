@extends('Layout.diy')

@section('content')

    <div class="breadcrumb">
        <div class="col-sm-6 col-md-6 ">
            <ol class="breadcrumb mt-2">
                @if(Session::get('link') != 'room')
                    <li class="breadcrumb-item"><a href="/node/myDevices?link=develop">{{__('device.my_devices') }}</a></li>

                    <li class="breadcrumb-item"><a href="{{url('/node/apps/?device_id=')}}{{$data['device']['id']}}">{{$data['device']['device_name']}} -  應用列表 </a></li>
                @else
                    <li class="breadcrumb-item"><a href="javascript:history.back()" onclick="self.location=document.referrer;">回上一頁</a></li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">{{$data['myApp']['name']}}</li>
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
            @if( Session::get('link') != 'room')
                <div class="list-group">
                    <!-- 應用選項 -->
                    <div class="list-group-item bg-light text-black-50 font-weight-bold">
                        {{__('app.app_option') }}
                    </div>
                    <!-- Data management -->
                    <a href="{{url(\App\Constant\AppConstant::APP_REPORTS_PATH).'?app_id='}}{{$data['myApp']['id']}}" class="list-group-item list-group-item-action active">{{__('app.data_management') }}</a>
                    <!-- Edit channel -->
                    <div class="list-group-item">
                        <a href="{{url(\App\Constant\AppConstant::APP_CHANNEL_PATH).'?app_id='}}{{$data['myApp']['id']}}">{{__('app.edit_channel') }}</a>
                    </div>
                    <!-- API key -->
                    <div class="list-group-item">
                        <a href="{{url(\App\Constant\AppConstant::APP_API_KEY_PATH).'?app_id='}}{{$data['myApp']['id']}}">{{__('app.api_key') }}</a>
                    </div>
                </div>
            @endif
        </div>
        <span v-cloak v-show="!isNew" class="col-md-9 mb-2 mt-3 text-left">
            <div class="mb-2">
                 <span>
                    <button v-if="tab==1" type="button" class="btn btn-secondary">{{__('app.gauge_chart') }}</button>
                    <button v-else type="button" class="btn btn-outline-secondary" @click="switchTab(1)">{{__('app.gauge_chart') }}</button>
                    <button v-if="tab==2" type="button" class="btn btn-secondary">{{__('app.line_chart') }}</button>
                    <button v-else type="button" class="btn btn-outline-secondary" @click="switchTab(2)">{{__('app.line_chart') }}</button>
                    <button v-if="tab==3" type="button" class="btn btn-secondary" >{{__('app.data_table') }}</button>
                    <button v-else type="button" class="btn btn-outline-secondary" @click="switchTab(3)">{{__('app.data_table') }}</button>

                </span>
                <span v-show="tab==3" class="float-right">
                     <button type="button" id="export" class="btn btn-primary" >{{__('app.export_csv') }}</button>
                     <button type="button" name="del" class="btn btn-danger" @click="delDataCheck()">
                        {{__('layout.delete')}}全部資料 <i class="fa fa-trash"></i>
                    </button>
                </span>
            </div>

            <div v-if="reportList.length == 0">
                <div class="alert alert-danger" id="message">
                    <h3>{{__('app.no_data_prompt') }}</h3>
                </div>
            </div>

             <div v-show="tab==3">
                <table id ="table1"  class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th >{{__('layout.item') }}</th>
                        <th >{{__('layout.date') }}</th>
                        <!--<th >{{__('device.device_mac') }}</th>-->
                        @foreach($labels as $label)
                            <th >{{$label}}</th>
                        @endforeach
                        <th ></th>
                    </tr>

                    </thead>

                    <tbody >



                    </tbody>
                </table>
            </div>

            <!-- gauge & chart -->
            <div v-show="reportList.length >0" >
                <div >
                    <div v-show="tab==1">
                        <div class="row">
                            <!--Gauge list -->
                            <div v-for="(item,index) in gaugeList" class="col-md-6 mb-3">
                                <div class="card">
                                    <div v-cloak class="card-header">
                                        <div class="d-flex justify-content-between">
                                        <span class="ml-1 mt-1">
                                            @{{ item.name }}
                                        </span>
                                        <span class="mt-1">
                                            <input type="text" v-model="item.recv" disabled/>
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
                    <div class="card">
                        <div v-cloak class="card-header">
                            <span style="font-size: 20px;" class="ml-3">
                                @{{ title }}
                            </span>
                            @if($user->role_id<3)
                            <span class="float-right mr-5">
                                <a href="{{url('/node/lineChart?app_id='.$app_id)}}" target="_blank">Line Chart</a>
                            </span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div v-show="tab==2||chartOption==0" id="container" style="height: 350px"></div>
                        </div>
                    </div>

                </div>

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
                    <form method="post" id="editForm">
                        <input type="hidden" name="_method" value="put" />
                        <input type="hidden" name="id" v-model="setting.id" />
                        <input type="hidden" name="app_id" v-model="setting.app_id"/>
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

                <div  class="modal-body">
                    <span v-if="isDelAll">{{__('layout.delete_confirm')}} {{$data['myApp']['name']}} 全部資料?</span>
                    <span v-else>{{__('layout.delete_confirm')}}  這筆資料?</span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delReports"  id="delReports">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="data.myApp.id" />
                        {{csrf_field()}}
                    </form>
                    <form method="post" action="{{url('/node/delReport')}}"  id="delReport">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="report_id" />
                        {{csrf_field()}}
                    </form>
                    <span v-if="isDelAll">
                            <button type="button" onClick="toDeleteReports()" class="btn btn-danger" >
                                {{__('layout.yes')}}
                            </button>
                        </span>
                    <span v-else>
                            <button type="button" onClick="toDeleteReport()" class="btn btn-danger" >
                                {{__('layout.yes')}}
                            </button>
                        </span>


                </div>
            </div>
        </div>
    </div>


@endsection

@section('footerScripts')
    <script>
        let types = {!! $data['types'] !!};
        let reports = {!! $reports !!};
        let settings = {!! $settings !!};
        let labelObj = {!! $labelObj !!};
        let field_required = "{{__('app.field_required') }}";
        let user = {!! $user !!};
        let data = {!! json_encode($data) !!};
        let app_url = '{{ env('APP_URL') }}';
        let start = '{!! $start !!}';
        let app_id = {!! $app_id !!};
        let end = '{!! $end !!}';
        let point_url = "{!! url('/Images/point.png')!!}";
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" charset="utf-8" ></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" ></script>
    <script src="{{asset('vender/DataTables-1.10.20/extensions/Buttons-1.6.2/js/dataTables.buttons.min.js')}}" ></script>
    <!-- Datatable HTML5 button -->
    <script src="{{asset('vender/DataTables-1.10.20/extensions/Buttons-1.6.2/js/buttons.html5.min.js')}}" ></script>
    <script src="{{asset('vender/jscolor/jscolor.js')}}" ></script>
    <!-- Datatable print button-->
    <!--<script src="{{asset('vender/DataTables-1.10.20/extensions/Buttons-1.6.2/js/buttons.print.min.js')}}" ></script>-->

    <!-- Datatable pdf button -->
    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>-->

    <!-- echarts -->
    <!--<script src="https://cdn.jsdelivr.net/npm/echarts@4.1.0/dist/echarts.js"></script>-->
    <script src="{{asset('vender/echarts/echarts.min.js')}}" ></script>
    <script src="{{asset('vender/echarts/theme/roma.js')}}" ></script>
    <script src="{{asset('js/option/chartOption.js')}}"></script>
    <script src="{{asset('js/option/tableOption.js')}}"></script>
    <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={{env('GOOGLE_MAP_KEY')}}">
    </script>
    <script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script><script src="{{asset('js/node/reports.js')}}"></script>
@endsection



