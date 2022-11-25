@extends('Layout.diy')

@section('content')
    <div class="breadcrumb">
        <div class="col-md-5">
                <ol class="breadcrumb">
                    <li id="step4" class="breadcrumb-item"><a href="/node/myDevices">{{__('device.my_devices') }}</a></li>
                    @foreach ($devices as $item)
                        @if($item->id == $device_id)
                            <li id="step2" class="breadcrumb-item active" aria-current="page">{{$item->device_name}}</li>
                        @endif
                    @endforeach
                </ol>
        </div>
        <div class="col-md-4 mt-2 text-left">

        </div>

        <div class="col-md-3 text-right">

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

    <div v-show="!isNew" class="row main-content">
        <div class="col-md-3 mt-3">
            <ul class="list-group">
                <li class="list-group-item">
                    <h4>{{__('device.switch_my_device') }}</h4>
                </li>

                @foreach ($devices as $item)
                    <li class="list-group-item" >
                        <a href="{{url('/node/admin/?device_id=')}}{{$item->id}}">{{$item->device_name}}</a>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="col-md-9 mb-2 mt-2 text-left">
            <table  style="border:3px #cccccc solid;" cellpadding="10" border='1'>
                <thead>
                <tr>
                    <th id="step3" width="150px">
                        @if($user->myIntro != 2)
                            <a href="{{url('/node/apps/?device_id=')}}{{$device_id}}">
                        @else
                            <a href="#" onClick="toIntroUrl({{$device_id}})">
                        @endif
                        <img src="{{url('/Images/http.png')}}" width="150px">
                        </a>
                    </th>
                    <th ><h1>{{__('app.http_command_management')}}</h1></th>
                </tr>

                </thead>

                <tbody >
                    <tr>
                        <td width="200px">
                            <!--<a href="{{url('/node/commands/?device_id=')}}{{$device_id}}">
                                <img id="img_fan" src="{{url('/Images/order.png')}}" width="150px">
                            </a>-->
                            <img id="img_fan" src="{{url('/Images/order.png')}}" width="150px">
                        </td>
                        <td >
                            <h1>
                                <div><h1>{{__('app.defined_command_management')}}</h1></div>
                                <div><h1>{{__('app.test_app_prompt')}}</h1></div>
                            </h1>
                        </td>
                    </tr>
                    <tr>
                        <td >
                            <!--<a href="{{url('/node/commands/?device_id=')}}{{$device_id}}">
                                <img id="img_fan" src="{{url('/Images/api.png')}}" width="150px">
                            </a>-->
                            <img id="img_fan" src="{{url('/Images/api.png')}}" width="150px">
                        </td>
                        <td >
                            <h1>
                                <div><h1>{{__('app.default_command_management')}}</h1></div>
                                <div><h1>{{__('app.test_app_prompt')}}</h1></div>
                            </h1>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>


@endsection

@section('footerScripts')
    <script>
        let data = {!! json_encode($data) !!};
        let intro = introJs();
        @if($user->myIntro == 2)
        intro.setOptions({
            nextLabel: data.nextLabel, //'下一步'
            prevLabel: data.prevLabel, //'上一步',
            skipLabel: data.skipLabel, //'跳過',
            doneLabel: data.doneLabel, //'完成',
            hidePrev: true,
            //showButtons: false,
            showBullets: false,
            steps: [
                {
                    intro: data.command_page,
                },
                {
                    element: document.querySelectorAll('#step2')[0],
                    intro: data.command_step2,
                    position: 'bottom'
                },
                {
                    element: '#step3',
                    intro: data.command_step3,
                    position: 'left'
                },
                {
                    element: '#step4',
                    intro: data.back_step,
                    position: 'bottom'
                }
            ]
        });

        intro.start();
        @endif

        function toIntroUrl(id) {
            let newUrl = "/node/apps/?device_id="+id+'&myIntro=3';
            //alert(newUrl);
            document.location.href = newUrl;
        }
    </script>
@endsection



