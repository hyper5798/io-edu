@extends('Layout.default')

@section('content')
    <div class="breadcrumb">
        <div class="col-md-5">

        </div>
        <div class="col-md-6  text-left">

        </div>

        <div class="col-md-3 text-right">
        <!--<button type="button" class="btn btn-success text-right" @click="newCheck()">{{__('layout.add')}}</button>-->
        </div>
    </div>
    <div  class="row main-content2">

        <div class="col-md-12 mb-2 mt-3 text-left">
            <div class="btn-group mb-2" role="group" aria-label="Basic example">

            </div>
            <div >



                <div id="container" style="height: 350px"></div>

            </div>
            <div >
                <table id ="table1"  class="table table-striped table-hover">
                    <thead>
                    <tr>
                        <th >項目</th>
                        <th >裝置MAC</th>
                        @foreach($labels as $label)
                            <th >{{$label}}</th>
                        @endforeach
                        <th >日期</th>
                    </tr>

                    </thead>

                    <tbody >
                    @foreach($reports as $report)
                        <tr>

                            <td>{{$loop->index+1}}</td>
                            <td>{{$report->macAddr}}</td>
                            @foreach($dataKeys as $key)
                                <td >
                                    {{ $report->$key}}
                                </td>
                            @endforeach
                            <td>{{$report->recv}}</td>
                        <!--<td>{{$report->extra['frameCnt']}}</td>-->


                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div >

            </div>
        </div>

    </div>

@endsection

@section('footerScripts')
    <script>
        //let types = {!! $types !!};
        //let reports = {!! $reports !!};

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
    <script src="{{asset('js/option/tableOption.js')}}">

@endsection



