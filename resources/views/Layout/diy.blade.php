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
    <link rel="shortcut icon" href="{{ asset('favicon.ico')}}" type="image/x-icon">
    <link href="{{asset('css/styles.css')}}" rel="stylesheet" />
    <!--<link href="{{asset('vender/bootstrap-4.3.1/css/bootstrap.css')}}" rel="stylesheet" />-->
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    <link href="{{asset('vender/bootstrap-4.3.1/css/bootstrap-datepicker.min.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <style type="text/css">
        li.jstree-open  {background:url("/Images/minus.png") 0px 0px no-repeat !important;}
        li.jstree-closed  {background:url("/Images/plus.png") 0px 0px no-repeat !important;}
    </style>
    @yield('css')
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
                        <!--({{$user->role->role_name}})-->
                    @endif
                </span>
            @endif
            <ul class="navbar-nav ml-auto ml-md-0">
                <li class="nav-item dropdown">

                    <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">

                    <a target="_blank" class="dropdown-item" href="https://yesio.net/docs/ioedu-statement/">聲明與規範</a>


                    <a target="_blank" class="dropdown-item" href="https://yesio.net/yesiodocs">學習中心</a>
                    <div class="dropdown-divider"></div>

                    <a class="dropdown-item" href="{{url('/pass?page=node')}}">{{__('layout.change_password')}}</a>
                    <div class="dropdown-divider"></div>

                    <!-- Email 設定 -->
                    <a class="dropdown-item" href="<?php echo e(url('/room/setEmail')); ?>">觸發通知:電子信箱設定</a>


                    <a class="dropdown-item" href="<?php echo e(url('/module/lineSetting')); ?>">觸發通知:Line Notify設定</a>
                    <div class="dropdown-divider"></div>



                    <a class="dropdown-item" href="{{url('/node/myDevices?link=develop')}}">{{__('device.my_devices') }}</a>
                    <div class="dropdown-divider"></div>

                    <!--<a class="dropdown-item" href="{{url('/node/apps')}}">{{__('app.http_command_management')}}</a>
                    <div class="dropdown-divider"></div>-->



                    @if($user->role_id < 8)
                        <a class="dropdown-item" href="{{url('/learn/allCourses')}}">所有課程</a>
                        <a class="dropdown-item" href="{{url('/learn/test-create')}}">自我評量</a>
                        <a class="dropdown-item" href="{{url('/learn/test-analyze')}}">評量結果分析</a>
                        <div class="dropdown-divider"></div>

                        <a class="dropdown-item" href="/backend">{{__('layout.back_title')}} <span class="sr-only">(current)</span></a>
                        <div class="dropdown-divider"></div>
                        <!--我的命令-->
                        <!--<a class="dropdown-item" href="/node/myCommand">我的命令 <span class="sr-only">(current)</span></a>
                        <div class="dropdown-divider"></div>-->
                    @endif

                    @if($user->role_id < 8)
                        <a class="dropdown-item" href="{{url('/admin/courses')}}">編輯課程</a>
                        <a class="dropdown-item" href="{{url('/admin/videos')}}">上傳影片</a>
                        <a class="dropdown-item" href="{{url('/learn/comment-replay')}}">課程留言</a>
                        <div class="dropdown-divider"></div>
                    @endif

                    <a class="dropdown-item" href="{{url('/logout')}}">{{__('layout.logout')}}</a>

                </li>
            </ul>
        </div>
    </nav>
<div class="container-fluid">
    <div id="row">
        <main>
            <div >
                @yield('content')
            </div>
        </main>
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


</div>
</body>
<script src="{{asset('js/all.min.js')}}" crossorigin="anonymous"></script>
<script src="{{asset('js/jquery-3.4.1.min.js')}}" crossorigin="anonymous"></script>
<script src="{{asset('vender/bootstrap-4.3.1/js/bootstrap.bundle.min.js')}}" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
<!--<script src="https://cdn.jsdelivr.net/npm/vue@2.6.11"></script>-->
<!--<script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.6.11/vue.js"></script>-->
<script src="{{asset('vender/vue2.6.11/vue.js')}}"></script>
<!--<script src="{{asset('vender/vue2.6.11/vue.min.js')}}"></script>-->
<!--<script src="{{asset('js/scripts.js')}}"></script>-->
<script src="{{asset('vender/jquery.loadingoverlay/loadingoverlay.min.js')}}" crossorigin="anonymous"></script>
<script src="{{asset('vender/jquery.loadingoverlay/loadingoverlay_progress.min.js')}}" crossorigin="anonymous"></script>
<!--<script src="{{asset('vender/intro.js-2.9.3/intro.js')}}"></script>-->
<script src="{{asset('vender/bootstrap-4.3.1/js/bootstrap-datepicker.min.js')}}" ></script>
<script src="{{asset('vender/bootstrap-4.3.1/js/bootstrap-datepicker.zh-TW.min.js')}}" ></script>
@yield('footerScripts')
@show
</html>
