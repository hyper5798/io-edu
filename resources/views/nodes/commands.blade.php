@extends('Layout.diy')

@section('content')
    <div class="row breadcrumb mt-1">
        <div class="col-md-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/node/apps">{{__('device.device_app') }}</a></li>
            <!--<li class="breadcrumb-item">{{__('layout.devices') }}</li>-->
                <li class="breadcrumb-item active" aria-current="page">{{__('layout.command_manager') }}</li>
            </ol>
        </div>
        <div class="col-md-6 mt-2 text-left">

            @if($types != null)
                {{__('layout.types') }}
                <select onchange="location.href='commands?type_id='+this.options[this.selectedIndex].value">
                    @foreach ($types as $type)
                        @if ($type->type_id == $type_id)
                            <option value="{{$type->type_id}}" selected="selected">{{$type->type_name}}</option>
                        @else
                            <option value="{{$type->type_id}}">{{$type->type_name}}</option>
                        @endif
                    @endforeach
                </select>
            @endif
            @if($devices != null)
                {{__('layout.devices') }}
                <select onchange="location.href='commands?device_id='+this.options[this.selectedIndex].value">
                    @foreach ($devices as $device)
                        @if ($device->id == $device_id)
                            <option value="{{$device->id}}" selected="selected">{{$device->device_name}}</option>
                        @else
                            <option value="{{$device->id}}">{{$device->device_name}}</option>
                        @endif
                    @endforeach
                </select>
            @endif
        </div>
        <div class="col-md-3 mt-1 text-right">
            <button type="button" class="btn btn-success text-right" @click="newCommand">{{__('layout.add')}}</button>
        </div>
    </div>
    @if (session('message'))
        <div class="alert alert-danger" id="message">
            {{ session('message') }}
        </div>
    @endif

    <div v-show="!isNew" class="main-content">
        <!-- Show commandObj list -->
        <table id ="table1"  class="table table-striped table-hover">
            <thead>
            <tr>
                <th >{{__('layout.item')}}</th>
                <th >{{__('layout.type_id')}}</th>
                <th >{{__('layout.command_name')}}</th>
                <th >{{__('layout.command')}}</th>
                <th >{{__('layout.update_at')}}</th>
                <th > </th>
            </tr>
            </thead>
            <tbody >
            @foreach ($commands as $command)
                <tr>
                    <td> {{$loop->index +1}} </td>
                    <td> {{$command->type_id}} </td>
                    <td> {{$command->cmd_name}} </td>
                    <td> {{$command->command}} </td>
                    <td> {{$command->updated_at}} </td>
                    <td>
                        <button @v-if="editPoint!={!! $loop->index !!}" type="button" name="edit" class="btn btn-primary btn-sm" @click="editCommand({!! $loop->index !!})">
                            {{__('layout.edit')}}
                        </button>
                        <button type="button" name="del" class="btn btn-danger btn-sm" @click="delCommand({{$loop->index}})">
                            {{__('layout.delete')}}
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>

        </table>
    </div>

    <!-- Edit command-->
    <div v-show="isNew" class="row justify-content-center main-content">

        <div class="col-lg-12">
            <div class="card shadow-lg border-0 rounded-lg mt-2">
                <div  class="card-header">
                    <!-- edit data -->
                    <h3 v-if="commandObj.id>0" class="text-center font-weight-light my-4">
                        {{__('layout.edit')}}{{__('layout.command')}}
                    </h3>
                    <!-- Add data -->
                    <h3 v-else class="text-center font-weight-light my-4">
                        {{__('layout.add')}}{{__('layout.command')}}
                    </h3>
                </div>

                <div class="card-body">
                    <form method="post" id="editForm">
                        <input type="hidden" name="_method" value="put" />
                        <input type="hidden" name="setCommand" v-model="commandObjString" />
                        {{csrf_field()}}
                        <div class="form-row">
                            <div class="col-md-12 ">
                                <div class="row">
                                    <!--  Show type id -->

                                        <div class="input-group mb-3 col-md-12">
                                            <input type="hidden"  v-model="commandObj.type_id" name="type_id">
                                            <input type="hidden"  v-model="commandObj.type_id" name="device_id">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-default">{{__('layout.command_name')}}</span>
                                            </div>
                                            <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="commandObj.cmd_name" name="cmd_name" @change="changeItem" @focus="isError=false">
                                        </div>
                                        <!--  Show cmd name -->
                                        <div class="input-group mb-3 col-md-6">

                                        </div>

                                    <!--  Show cmd name -->
                                    <div class="input-group col-md-12">

                                            <label >{{__('layout.command')}}</label>

                                        <!--<input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="commandObj.command" name="description" @change="changeItem" @focus="isError=false">-->
                                        <table >
                                            <tr >
                                                <td ><input type="text" value="fc00" maxlength="4" size="4"></td>
                                                <td v-for="(option,index) in cmdOptions">
                                                    <input type="text" v-model="option.value" maxlength="16" size="16">
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                    <!-- Selected back or submit -->
                                    <div class="col-md-12 text-right mb-2">
                                        <button type="button" class="btn btn-secondary" @click="backCommandObjList">{{__('layout.back')}}</button>
                                        <button type="button" class="btn btn-primary" @click="toSubmit()">{{__('layout.submit')}}</button>
                                    </div>

                                    <!-- Show command list -->
                                    <div class="col-md-6" >
                                        <div class="mt-2">
                                            編輯第
                                            <select v-model="cmdIndex" @change="onChange($event)">

                                                <option v-for="(item, index) in cmdOptions" v-bind:value="index" :key="index" >
                                                    @{{ item.title }}
                                                </option>
                                            </select>
                                            <span class="ml-2"> @{{currentCmd}} </span>

                                            <div class="text-left mt-2">
                                                <button type="button" class="btn btn-success" @click="addCommand">{{__('layout.add')}}</button>
                                                <button type="button" class="btn btn-danger" @click="deleteCommand">{{__('layout.delete')}}</button>
                                                <button type="button" class="btn btn-primary" @click="saveCommand">{{__('layout.yes')}}</button>
                                            </div>
                                            <div class="row mt-3">
                                                <div class="input-group mb-3 col-md-12">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="inputGroup-sizing-default" >10進位</span>
                                                    </div>
                                                    <input type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="checkValue" >
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="inputGroup-sizing-default" >16進位</span>
                                                    </div>
                                                    <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="computedValue">
                                                </div>
                                                <div class="input-group mb-3 col-md-6">

                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <!-- pin mode: input -->
                                        <span v-if="byteObj.p2=='01'">
                                            pin mode: input<br>
                                            Data Byte1: 00:None / 01:Pin.PULL_DOWN / 02:Pin.PULL_UP
                                        </span>
                                        <!-- pin mode: output -->
                                        <span v-if="byteObj.p2=='02'">
                                            pin mode: output<br>
                                            34,35,36,39不可選<br>
                                            Data Byte1: 00:Low / 01:High
                                        </span>
                                        <!-- pin mode: OPEN_DRAIN -->
                                        <span v-if="byteObj.p2=='03'">
                                            pin mode: OPEN_DRAIN<br>
                                            保留
                                        </span>
                                        <!-- pin mode: pwm -->
                                        <span v-else-if="byteObj.p2=='04'">
                                            pin mode: pwm<br>
                                            34,35,36,39不可選<br>
                                            Data Byte1~Data Byte4: freq (1Hz~65535Hz~40MHz)<br>
                                            Data Byte5~Data Byte6: duty (0~1023)
                                            </span>
                                        <!-- pin mode: ADC -->
                                        <span v-else-if="byteObj.p2=='05'">
                                            pin mode: ADC<br>
                                            Data Byte1: atten<br>
                                            Data Byte2: width
                                        </span>
                                        </span>
                                        <!-- pin mode: DAC -->
                                        <span v-else-if="byteObj.p2=='06'">
                                            pin mode: DAC<br>
                                            Data Byte1: output value (0~255)
                                        </span>
                                        <!-- pin mode: NeoPixel -->
                                        <span v-else-if="byteObj.p2=='07'">
                                            pin mode: NeoPixel<br>
                                            Data Byte1: 燈珠數 (0~254)<br>
                                            Data Byte2: 欲控制之燈珠索引 (0~254 / 255:ALL)<br>
                                            Data Byte3: R (0~255)<br>
                                            Data Byte4: G (0~255)<br>
                                            Data Byte5: B (0~255)
                                        </span>
                                        <span v-else></span>
                                    </div>

                                </div>
                            </div>

                            <!--  Add / Edit command's selection -->
                            <div class="col-md-12 mt-3 type-table-content">
                                <table style="width:100%;border:3px #cccccc solid;" cellpadding="10" border='1'>
                                    <!--  Command table head  -->
                                    <thead>
                                    <tr>
                                        <th >Pin NO</th>
                                        <th >Pin Mode</th>
                                        <th >Pin Byte1</th>
                                        <th >Pin Byte2</th>
                                        <th >Pin Byte3</th>
                                        <th >Pin Byte4</th>
                                        <th >Pin Byte5</th>
                                        <th >Pin Byte6</th>
                                    </tr>
                                    </thead>
                                    <!--  Show command   -->
                                    <tbody>
                                    <tr >
                                        <th>
                                            <!-- Pin No -->
                                            <select v-model="byteObj.p1">
                                                <option v-for="pin in pin1" v-bind:value="pin.value" :key="pin.value">
                                                    @{{ pin.title }}
                                                </option>
                                            </select>
                                        </th>
                                        <th>
                                            <!-- Pin Mode -->
                                            <select v-model="byteObj.p2">
                                                <option v-for="pin in pin2" v-bind:value="pin.value" :key="pin.value">
                                                    @{{ pin.title }}
                                                </option>
                                            </select>
                                        </th>
                                        <th>
                                            <!-- For pin mode in -->
                                            <select v-if="byteObj.p2=='01'" v-model="byteObj.p3">
                                                <option v-for="pin in pin3" v-bind:value="pin.value" :key="pin.value">
                                                    @{{ pin.title }}
                                                </option>
                                            </select>
                                            <!-- For pin mode out -->
                                            <select v-else-if="byteObj.p2=='02'" v-model="byteObj.p3">
                                                <option v-for="pin in pin3_out" v-bind:value="pin.value" :key="pin.value">
                                                    @{{ pin.title }}
                                                </option>
                                            </select>
                                            <!-- For pin mode ADC -->
                                            <select v-else-if="byteObj.p2=='05'" v-model="byteObj.p3">
                                                <option v-for="pin in pin3_atten" v-bind:value="pin.value" :key="pin.value">
                                                    @{{ pin.title }}
                                                </option>
                                            </select>
                                            <input v-else type="text"  v-model="byteObj.p3" maxlength="2" size="2"/>
                                        </th>
                                        <th>
                                            <select v-if="byteObj.p2=='05'" v-model="byteObj.p4">
                                                <option v-for="pin in pin4_width" v-bind:value="pin.value" :key="pin.value">
                                                    @{{ pin.title }}
                                                </option>
                                            </select>
                                            <input v-else type="text"  v-model="byteObj.p4"  maxlength="2" size="2"/>
                                        </th>
                                        <th>
                                            <input type="text"  v-model="byteObj.p5"  maxlength="2" size="2"/>
                                        </th>
                                        <th>
                                            <input type="text"  v-model="byteObj.p6" maxlength="2" size="2"/>
                                        </th>
                                        <th>
                                            <input type="text"  v-model="byteObj.p7" maxlength="2" size="2"/>
                                        </th>
                                        <th>
                                            <input type="text"  v-model="byteObj.p8"  maxlength="2" size="2"/>
                                        </th>
                                    </tr>
                                    </tbody>
                                </table>
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
                    {{__('layout.delete_confirm')}} @{{commandObj.cmd_name}} ?
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

        let device_id = null;
        @if($device_id != null)
            device_id = {!! $device_id !!};
        @endif
        let type_id = {!! $type_id !!};
        let commands = {!! $commands !!};

        function disableMsg() {
            let msg = document.getElementById("message");
            //console.log(msg)
            if(msg !== null)
                document.getElementById("message").remove();
        }
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/node/commands.js')}}"></script>
@endsection



