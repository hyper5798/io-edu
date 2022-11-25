@extends('Layout.room')


@section('content')
    <div class="room_header mb-3">
            <!--<span class="ml-3"> 現在位置：</span>
            <span class="breadcrumb-item">
                <a href="/room/index?room_id={{$room_id}}">
                    {{$room->room_name}}
                </a>
            </span>
            <span> / </span>-->
            <span>無人船選擇</span>
    </div>
    <div class="card rounded-lg">
        <div class="card-body homeBlock">
            <div class="row justify-content-center">
                @if(count($devices)>0)
                  @foreach($devices as $item)

                    <div class="col-3 justify-content-center" onclick="toMission({{$item->id}});">
                        <div class="card roomBlock" >
                            <div class="card-body" >
                                <div class="text-center">
                                    <!-- Product name-->
                                    <i class="fa fa-podcast fa-5x mr-2" aria-hidden="true"></i>
                                </div>
                            </div>
                            <!-- Product actions-->
                            <div class="card-footer p-4 pt-0 border-top-0 bg-transparent text-center">
                                <h4>{{$item->device_name}}</h4>
                            </div>
                        </div>
                    </div>

                  @endforeach
                @else
                   <div class="ml-2 text-danger"><h1>尚未設定任務</h1></div>
                @endif
            </div>
        </div>
    </div>

@endsection

@section('footerScripts')
    <script>
        let room = {!! $room !!};
        let missions = {!! json_encode($missions) !!};

        function toMission(id) {
            $.LoadingOverlay("show");
            let newUrl = '/room/'+room.work+'?device_id='+id;
            document.location.href = newUrl;
        }
    </script>

@endsection


