<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Viewer</title>
</head>

<body>

<div>
    @if($size=='small')
        <video muted width="310" height="350" controls autoplay playsinline id="remoteVideo" style="border-color:#000000;border-width:1px;border-style:solid;"></video>
    @else
        <video muted width="100%" controls autoplay playsinline id="remoteVideo" style="border-color:#000000;border-width:1px;border-style:solid;"></video>
    @endif
</div>
<div>
    <!--<button type="button" onclick="test();"> test </button>-->
</div>

<div>
    <a id="link"></a>
    <canvas id="canvas" style="overflow:auto"></canvas>
</div>
<script>
    let api_url = '{!! env('API_URL') !!}';
    let user = {!! $user !!};
    let token = '{!! $user->remember_token !!}';
    let device = {!! $device !!};
    @if($size=='small')
    let option="UAV";
    @else
    let option="USV";
    @endif
</script>
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.0.1/socket.io.js"></script>-->
<script src="{{asset('js/jquery-3.4.1.min.js')}}" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.4.0/socket.io.js"></script>
<script src="https://webrtc.github.io/adapter/adapter-latest.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bluebird/3.7.2/bluebird.min.js"></script>
<script src="{{asset('js/option/webRTCTools.js')}}"></script>
<script src="{{asset('js/room/farm/viewer_multi.js')}}"></script>
</body>
</html>
