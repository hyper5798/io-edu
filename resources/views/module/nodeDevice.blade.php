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
                    <a class="nav-link active" data-toggle="tab" href="#2">模組控制器</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link " data-toggle="tab" href="#3">模組狀態</a>
                </li>

            </ul>
        </div>
        <div class="col-1">
            <!--<button title="{{__('layout.tutorials') }}" type="button" class="btn btn-warning" onclick="window.location='{{ url("/escape/carousel?app=4&item=1") }}'"><i class="fas fa-question"></i></button>-->
        </div>

    </div>
    @if (count($errors) > 0)
        <div class="alert alert-danger mt-2" id="message">

            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach

            <button type="button" class="close" onClick="clearError();">
                <span >&times;</span>
            </button>
        </div>
    @endif
    <div class="row justify-content-center main-content">
        <div class="col-md-12">
            <div  class="card shadow-lg  rounded-lg mt-3">
                <div  class="card-header">
                    <div class = "row">
                        <div class="col-5">
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
                        <div class="col-7 mt-3">
                            @if($controllers->count() === 0)
                                <p class="text-danger">
                                    {{__('node.controller_required')}}
                                </p>
                            @endif
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <div  class="row">
                        <!-- 輸入型控制器 -->
                        <div class="col-3 text-center">

                            <h5>可選擇輸入控制 <i class="fa fa-arrow-right" aria-hidden="true"></i></h5>

                            <div class="normalBlock">
                                <draggable v-cloak class="list-group" :list="list1" group="input">
                                    <div
                                        class="list-group-item"
                                        v-for="(element, index) in list1"
                                        :key="element.device_name"
                                    >
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div>
                                                    @{{ element.device_name }}
                                                </div>
                                                <div>
                                                    <a class="text-info">
                                                        @{{ element.macAddr }}
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <img id="script_img" :src="element.image_url"  width="60" height="60">
                                            </div>
                                        </div>

                                    </div>
                                </draggable>
                            </div>
                        </div>
                        <!-- 加入輸入控制 -->
                        <div class="col-3 text-center">
                            <h5>加入輸入控制</h5>
                            <div class="freeBlock">
                                <draggable v-cloak class="list-group" :list="list2" group="input">
                                    <div
                                        class="list-group-item"
                                        v-for="(element, index) in list2"
                                        :key="element.device_name"
                                    >
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div>
                                                    @{{ element.device_name }}
                                                </div>
                                                <div>
                                                    <a class="text-info">
                                                        @{{ element.macAddr }}
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <img id="script_img" :src="element.image_url"  width="60" height="60">
                                            </div>
                                        </div>

                                    </div>
                                </draggable>
                            </div>
                        </div>
                        <!-- 加入輸出控制 -->
                        <div class="col-3 text-center">
                            <h5>加入輸出控制</h5>
                            <div class="membersBlock">
                                <draggable v-cloak class="list-group" :list="list4" group="people">
                                    <div
                                        class="list-group-item"
                                        v-for="(element, index) in list4"
                                        :key="element.device_name"
                                    >
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div>
                                                    @{{ element.device_name }}
                                                </div>
                                                <div>
                                                    <a class="text-info">
                                                        @{{ element.macAddr }}
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <img id="script_img" :src="element.image_url"  width="60" height="60">
                                            </div>
                                        </div>
                                    </div>
                                </draggable>
                            </div>
                        </div>
                        <!-- 輸出型控制器 -->
                        <div class="col-3 text-center">
                            <h5><i class="fa fa-arrow-left" aria-hidden="true"></i>可選擇輸出型控制器</h5>
                            <div class="normalBlock">
                                <draggable v-cloak class="list-group" :list="list3" group="people">
                                    <div
                                        class="list-group-item"
                                        v-for="(element, index) in list3"
                                        :key="element.device_name"
                                    >
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div>
                                                    @{{ element.device_name }}
                                                </div>
                                                <div>
                                                    <a class="text-info">
                                                        @{{ element.macAddr }}
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <img id="script_img" :src="element.image_url"  width="60" height="60">
                                            </div>
                                        </div>
                                    </div>
                                </draggable>
                            </div>
                        </div>
                        <form method="post" action="editNodeDevice" id="editNodeDevice">
                            {{csrf_field()}}
                            <input type="hidden" name="id" v-model="node.id" />
                            <input type="hidden" name="inputs" v-model="node.inputs" />
                            <input type="hidden" name="outputs" v-model="node.outputs" />
                            <input type="hidden" name="node_name" v-model="node.node_name" />
                            <input type="hidden" name="node_mac" v-model="node.node_mac" />
                        </form>
                        <div class="col-12 mt-3">
                            <label class="text-primary">移動滑鼠到目標控制器拖曳到要加入(移出)的控制區</label>
                            <span class="float-right">
                            <button type="button" class="btn btn-primary" @click="toSubmit">{{__('node.control_device_setting')}}
                            </button>
                        </span>
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
        let nodes = [];
        @if($nodes->count()>0)
            nodes = {!! $nodes !!};
        @endif
        let inputs = {!! $inputs !!};
        let outputs = {!! $outputs !!};
        let nodeInputs = {!! $nodeInputs !!};
        let nodeOutputs = {!! $nodeOutputs !!};
        let controller_mac = "{!! $controller_mac !!}";
        let menu1 = "模組腳本";
        let menu2 = "模組控制器";
        let menu3 = "模組狀態";
    </script>
    <script src="//cdn.jsdelivr.net/npm/sortablejs@1.8.4/Sortable.min.js"></script>
    <!-- CDNJS :: Vue.Draggable (https://cdnjs.com/) -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/Vue.Draggable/2.20.0/vuedraggable.umd.min.js"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" charset="utf-8" ></script>
    <script src="{{asset('js/option/tableOption.js')}}"></script>
    <script src="{{asset('js/module/nodeDevice.js')}}"></script>

@endsection
