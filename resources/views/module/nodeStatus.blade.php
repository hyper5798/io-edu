@extends('Layout.module')



@section('content')
    <!-- Tab -->
    <div class="row mt-2">
        <div class="col-11">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link " data-toggle="tab" href="#1">模組腳本</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link " data-toggle="tab" href="#2">模組控制器</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#3">模組狀態</a>
                </li>

            </ul>
        </div>
        <div class="col-1">
            <!--<button title="{{__('layout.tutorials') }}" type="button" class="btn btn-warning" onclick="window.location='{{ url("/escape/carousel?app=4&item=1") }}'"><i class="fas fa-question"></i></button>-->
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
    <div class="row justify-content-center main-content">
        <div class="col-md-12">
            <div  class="card shadow-lg  rounded-lg mt-3">
                <div  class="card-header">
                    <div class = "row">
                        <div class="col-5 ">
                            <span class="text-info statusText">模組狀態</span>
                        </div>
                        <div class="col-7">
                            <span class="mission_header ml-2">

                                {{__('layout.select') }}邏輯控制器
                                <select onchange="location.href='?controller_mac='+this.options[this.selectedIndex].value">
                                    @foreach ($controllers as $cItem)
                                        @if ($cItem->macAddr == $controller_mac)
                                            <option value="{{$cItem->macAddr}}" selected="selected">{{$cItem->device_name}}</option>
                                        @else
                                            <option value="{{$cItem->macAddr}}">{{$cItem->device_name}}</option>
                                        @endif
                                    @endforeach

                                </select>

                            </span>
                        </div>

                    </div>

                </div>
                <div class="card-body">
                    <div  class="row">
                        <!-- 輸入型控制器 -->
                        <div class="col-4 text-center">

                            <h5>
                                邏輯控制器 <!--<i class="fa fa-arrow-right" aria-hidden="true"></i>-->
                            </h5>

                            <div class="statuslBlock">
                                <div v-cloak class="list-group" :list="list1" group="input">
                                    <div
                                        class="list-group-item"
                                        v-for="(element, index) in list1"
                                        :key="element.device_name"
                                    >
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div>
                                                    <img class="statuslImage" :src="element.image_url"  width="100">
                                                </div>
                                                <div>
                                                    @{{ element.device_name }}
                                                </div>

                                            </div>
                                            <div v-cloak v-if="list1[index].script_id!==''" class="col-md-6">
                                                <div>
                                                    腳本: @{{ element.script_id }}
                                                </div>
                                                <div class="status-time-text">
                                                    @{{ element.script_time }}
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- 加入輸入控制 -->
                        <div class="col-4 text-center">
                            <h5>輸入型控制器</h5>
                            <div class="statuslBlock">
                                <div v-cloak class="list-group">
                                    <div
                                        class="list-group-item"
                                        v-for="(element, key) in list2"
                                        :key="element.device_name"
                                    >
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div>
                                                    <img class="statuslImage" :src="element.image_url"  width="100">
                                                </div>
                                                <div>
                                                    @{{ element.device_name }}
                                                </div>
                                                <!--<div>
                                                    <a class="text-info">
                                                        @{{ element.macAddr }}
                                                    </a>
                                                </div>-->
                                            </div>
                                            <div v-cloak v-if="list2[key].command!==''" class="col-md-6">
                                                <div>
                                                    命令: @{{ element.command }}
                                                </div>
                                                <div class="status-time-text">
                                                    @{{ element.command_time }}
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                        <!-- 加入輸出控制 -->
                        <div class="col-4 text-center">
                            <h5>輸出型控制器</h5>
                            <div class="statuslBlock">
                                <div v-cloak class="list-group">
                                    <div
                                        class="list-group-item"
                                        v-for="(element, key) in list4"
                                        :key="element.device_name"
                                    >
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div>
                                                    <img class="statuslImage" :src="element.image_url"  width="100">
                                                </div>
                                                <div>
                                                    @{{ element.device_name }}
                                                </div>
                                                <!--<div>
                                                    <a class="text-info">
                                                        @{{ element.macAddr }}
                                                    </a>
                                                </div>-->
                                            </div>
                                            <div v-cloak v-if="list4[key].command!==''" class="col-md-6">
                                                <div>
                                                    命令: @{{ element.command }}
                                                </div>
                                                <div class="status-time-text">
                                                    @{{ element.command_time }}
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>


@endsection

@section('footerScripts')
    <script>
        let controllers = {!! $controllers !!};
        let selectNode = {!! $selectNode !!};
        let nodeInputs = {!! $nodeInputs !!};
        let nodeOutputs = {!! $nodeOutputs !!};
        let controller_mac = "{!! $controller_mac !!}";
        let menu1 = "模組腳本";
        let menu2 = "模組控制器";
        let menu3 = "檢視流程";
        let app_url = '{{ env('APP_URL') }}';
    </script>
    <script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/module/nodeStatus.js')}}"></script>

@endsection
