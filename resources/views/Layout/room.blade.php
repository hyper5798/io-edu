<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{__('layout.cp_title')}}</title>
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <link href="{{asset('vender/bootstrap-4.3.1/css/bootstrap-datepicker.min.css')}}" rel="stylesheet" crossorigin="anonymous" />
    <link href="{{asset('vender/bootstrap4-toggle/css/bootstrap4-toggle.min.css')}}" rel="stylesheet" crossorigin="anonymous" />
    <link href="{{asset('vender/bootstrap4-datetimepicker/css//bootstrap-datetimepicker.min.css')}}" rel="stylesheet" crossorigin="anonymous" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/css/bootstrap-colorpicker.css" rel="stylesheet" crossorigin="anonymous" />
    <link href="{{asset('css/styles.css')}}" rel="stylesheet" />
    <link href="{{asset('css/index.css')}}" rel="stylesheet" />
</head>
<body class="sb-nav-fixed" >
<div id="app" >
    <nav class="navbar navbar-expand-lg navbar-light bg-diy">
       <span class="title-block">
            <a target="_blank" href="https://yesio.net/"><img src="{{url('/Images/yesio-ICON-2022.png')}}"  class="urlIcon"></a>
            <a class="title-escape" href="{{url('/')}}">{{__('layout.system_title')}}</a>
        </span>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">

            <ul class="navbar-nav mr-auto">
                <!--<li class="nav-item"><a class="nav-link active" aria-current="page" href="{{url('/')}}">首頁</a></li>
                <li class="nav-item"><a class="nav-link" href="/#about">關於我們</a></li>
                <li class="nav-item"><a class="nav-link" href="/#contact">聯絡客服</a></li>-->
            </ul>
            @if(session('user') != null)
                @php
                    $user = session('user');
                @endphp
                <span class="mt-1">
                    {{$user['name']}}
                    @if($user->role)
                        ({{$user->role->role_name}})
                    @endif
                </span>
            @endif
            <ul class="navbar-nav ml-auto ml-md-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        @if($user->image_url == null)
                            <i class="fas fa-user fa-fw"></i>
                        @else
                            <img id="preview_progressbarTW_img" src="{{$user->image_url}}" alt="Admin" class="rounded-circle" width="30" height="30">
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">

                        <!-- 個人資訊 -->
                        <a class="dropdown-item" href="{{url('/room/profile')}}">{{__('layout.personal_info')}}</a>
                        <div class="dropdown-divider"></div>

                        <!-- Email 設定 -->
                        @if($user->isNotify)
                            <a class="dropdown-item" href="{{url('/room/setEmail')}}">觸發通知:電子信箱設定</a>
                            <div class="dropdown-divider"></div>
                            <!-- Line 設定 -->
                            <a class="dropdown-item" href="{{url('/module/lineSetting')}}">觸發通知:Line Notify設定</a>
                            <div class="dropdown-divider"></div>
                        @endif

                        <!-- 我的場域 -->
                        <a class="dropdown-item" href="{{url('/room/index')}}">{{__('layout.my_room')}}</a>
                        <div class="dropdown-divider"></div>

                        @if( $user->role_id < 3)

                            <a class="dropdown-item" href="{{url('/node/myDevices?link=develop')}}">開發版應用</a>
                            <div class="dropdown-divider"></div>

                        <!-- 綁定控制器 -->
                            <a class="dropdown-item" href="{{url('/room/userBinding')}}">綁定控制器</a>
                            <div class="dropdown-divider"></div>

                        <!-- 帳戶管理 -->
                            <a class="dropdown-item" href="{{url('/room/setCp')}}">{{__('user.account_management')}}</a>
                            <div class="dropdown-divider"></div>
                        <!-- 場域管理 -->
                            <a class="dropdown-item" href="{{url('/room/setRoom')}}">{{__('escape.room_edit')}}</a>
                            <div class="dropdown-divider"></div>
                        <!-- 後台 -->
                            <a class="dropdown-item" href="/backend">{{__('layout.back_title')}} <span class="sr-only">(current)</span></a>
                            <div class="dropdown-divider"></div>
                        @endif

                        <a class="dropdown-item" href="{{url('/logout')}}">{{__('layout.logout')}}</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>

    @yield('title-image')

    <div class="container-fluid">
        <div id="row">
            <main>
                <div >
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <div class="mt-3">
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; yesio.net</div>
                    <div>
                        <a href="http://yesio.net">歐利科技 All rights reserved.</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>

</div>
</body>
<script src="{{asset('js/all.min.js')}}" crossorigin="anonymous"></script>
<script src="{{asset('js/jquery-3.4.1.min.js')}}" crossorigin="anonymous"></script>
<script src="{{asset('vender/bootstrap-4.3.1/js/bootstrap.bundle.min.js')}}" crossorigin="anonymous"></script>
<script src="{{asset('vender/bootstrap4-toggle/js/bootstrap4-toggle.min.js')}}"></script>
<script src="{{asset('vender/bootstrap4-datetimepicker/js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{asset('vender/bootstrap4-datetimepicker/js/bootstrap-datetimepicker.zh-TW.js')}}"></script>
<script crossorigin src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/3.4.0/js/bootstrap-colorpicker.min.js"></script>
<!--<script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script>-->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.11/vue.js"></script>-->
<script src="{{asset('vender/vue2.6.11/vue.js')}}"></script>
<!--<script src="{{asset('vender/vue2.6.11/vue.min.js')}}"></script>-->
<!--<script src="{{asset('js/scripts.js')}}"></script>-->
<script src="{{asset('vender/jquery.loadingoverlay/loadingoverlay.min.js')}}" crossorigin="anonymous"></script>
<script src="{{asset('vender/jquery.loadingoverlay/loadingoverlay_progress.min.js')}}" crossorigin="anonymous"></script>

@section('footerScripts')
@show
</html>
