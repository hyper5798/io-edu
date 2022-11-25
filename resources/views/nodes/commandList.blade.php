@extends('Layout.diy')

@section('content')
    <div class="row breadcrumb mt-1">
        <div class="col-md-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/node/apps">{{__('device.device_app') }}</a></li>
            <!--<li class="breadcrumb-item">{{__('layout.devices') }}</li>-->
                <li class="breadcrumb-item active" aria-current="page">{{__('layout.command_list') }}</li>
            </ol>
        </div>
        <div class="col-md-3 mt-2 text-left">
            {{__('device.select_order_device') }}
            <select onchange="location.href='commandList?command='+this.options[this.selectedIndex].value">
                <<!-- common command -->
                <option value="0" disabled>--{{__('device.common_commands')}}--</option>

                @foreach ($devices as $device)
                    @if ($device->id === $device_id)
                        <option value="0.{{$device->id}}" selected="selected">{{$device->device_name}}</option>
                    @else
                        <option value="0.{{$device->id}}">{{$device->device_name}}</option>
                    @endif
                @endforeach
            </select>

        </div>
        <div class="col-md-3 mt-1 text-right">

        </div>
    </div>
    @if (count($errors) > 0)
        <div class="alert alert-danger" id="message">
            <ul style="color:red;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div v-show="isShow" v-cloak class="alert alert-info" >
        @{{ prompt }}
    </div>

    <div class="main-content">
        <!-- Show commandObj list -->
        <table id ="table1"  class="table table-striped table-hover">
            <thead>
            <tr>
                <th >{{__('layout.item')}}</th>
            <!--<th >{{__('layout.type_id')}}</th>-->
                <th >{{__('layout.command_name')}}</th>
            <!--<th >{{__('layout.command')}}</th>-->
                <th >{{__('device.device_control')}}{{__('layout.command')}}</th>
                <th ></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($commands as $command)
                <tr>
                    <td> {{$loop->index +1}} </td>
                <!--<td> {{$command->type_id}} </td>-->
                    <td> {{$command->cmd_name}} </td>
                <!--<td> {{$command->command}} </td>-->
                    <td>
                    <!--<label id="{{$loop->index}}">{{$command->ctlKey}}</label>-->
                        <input type="text" value ="{{$command->ctlKey}}" id="{{$loop->index}}" maxlength="75" size="75"/>
                    </td>
                    <td>
                        <button type="button" @click="copyUrl({{$loop->index}})" class="btn btn-info ml-1" >
                            {{__('device.copy_code')}}
                        </button>
                        <button type="button" @click="toSendControl({{$loop->index}})" class="btn btn-primary ml-1" >
                            {{__('device.run_code')}}
                        </button>
                    </td>

                </tr>
            @endforeach
            </tbody>

        </table>
    </div>

@endsection

@section('footerScripts')
    <script>
        let commands = {!! $commands !!};

        setTimeout(function () {
            let msg = document.getElementById("message");
            //console.log(msg)
            if(msg !== null)
                document.getElementById("message").remove();
        }, 8000);
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/node/commandList.js')}}"></script>
@endsection



