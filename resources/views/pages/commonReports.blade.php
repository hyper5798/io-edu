@extends('Layout.default')

@section('content')
    <div class="breadcrumb">
        <div class="col-md-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">{{__('layout.index_title') }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{__('layout.reports')}}</li>
            </ol>
        </div>
        <div class="col-md-3  text-left">
            @if($types != null)
                {{__('layout.types') }}
                <select onchange="location.href='reports?type_id='+this.options[this.selectedIndex].value">
                    @foreach ($types as $type)
                        @if ($type->type_id == $type_id)
                            <option value="{{$type->type_id}}" selected="selected">{{$type->type_name}}</option>
                        @else
                            <option value="{{$type->type_id}}">{{$type->type_name}}</option>
                        @endif
                    @endforeach
                </select>
            @endif
        </div>

        <div class="col-md-3  text-left">
            @if($types != null)
                {{__('layout.devices') }}
                <select onchange="location.href='reports?type_id='+{{$type_id}}+'&device_id='+this.options[this.selectedIndex].value">
                    @foreach ($devices as $device)
                        @if ($device->id == $device_id)
                            <option value="{{$device->id}}" selected="selected">{{$device->device_name}}({{$device->macAddr}})</option>
                        @else
                            <option value="{{$device->id}}">{{$device->device_name}}({{$device->macAddr}})</option>
                        @endif
                    @endforeach
                </select>
            @endif
        </div>
        <div v-cloak class="col-md-3  text-left">
            查詢全部
            <input type="checkbox" id="checkbox" v-model="checked" @Click="findAll">
        </div>

    </div>
    <div v-show="!isNew" class="row main-content2">
        <div class="col-md-10 mb-2 mt-3 text-left">
            <div class="btn-group mb-2" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-secondary" @click="tab=1">{{__('app.line_chart') }}</button>
                <button type="button" class="btn btn-secondary" @click="tab=2">{{__('app.data_table') }}</button>
                <button type="button" id="export" class="btn btn-secondary">{{__('app.export_csv') }}</button>
            </div>

            <div class="btn-group mb-2" role="group" aria-label="Basic example">
                <button type="button" class="btn btn-primary" onClick="test()">
                    測試結果
                </button>
            </div>

            <div class="btn-group mb-2" role="group" aria-label="Basic example">
                <form method="post" action="delReports" id="delReports">
                    {{csrf_field()}}
                    <input type="hidden" name="_method" value="delete" />
                    <button type="button" class="btn btn-danger" onClick="toDelete()">
                        {{__('layout.delete')}}
                    </button>
                </form>
            </div>


            <div v-show="tab==1">

                <div class="row">

                    <div v-for="(item,index) in gaugeList" class="col-md-6 mb-3">
                        <div class="card">
                            <div v-cloak class="card-header">
                                @{{ item.name }}
                            </div>
                            <div class="card-body">
                                <div :id="item.id" style="height: 300px"></div>
                            </div>
                        </div>

                    </div>
                </div>

                <div id="container" style="height: 350px"></div>

            </div>
            <div v-show="tab==2">
                <table id ="table1"  class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th >{{__('layout.item') }}</th>
                        <th >{{__('layout.date') }}</th>
                        <th >{{__('device.device_mac') }}</th>
                        <th >key1</th>
                        <th >key2</th>

                    </tr>

                    </thead>

                    <tbody >
                    @foreach($reports as $report)
                        <tr>

                            <td>{{$loop->index+1}}</td>
                            <td>{{$report->recv}}</td>
                            <td>{{$report->macAddr}}</td>
                            <td >{{ $report->key1}}</td>
                            @if($report->key2)
                                <td >{{ $report->key2}}</td>
                            @else
                                <td ></td>
                            @endif

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div v-show="tab==3">

            </div>
        </div>

    </div>
@endsection

@section('footerScripts')
    <script>
        let types = {!! $types !!};
        let reports = {!! $reports !!};
        let keys = {!! json_encode($dataKeys) !!};
        let values = {!! json_encode($labels) !!};
        let findAll = {!! json_encode($findAll) !!};
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" charset="utf-8" ></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" ></script>
    <script src="{{asset('vender/DataTables-1.10.20/extensions/Buttons-1.6.2/js/dataTables.buttons.min.js')}}" ></script>
    <!-- Datatable HTML5 button -->
    <script src="{{asset('vender/DataTables-1.10.20/extensions/Buttons-1.6.2/js/buttons.html5.min.js')}}" ></script>

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
    <script src="{{asset('js/admin/commonReports.js')}}"></script>
@endsection



