<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>Dashboard - Admin</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico')}}" type="image/x-icon">
    <link href="{{asset('css/styles.css')}}" rel="stylesheet" />
    <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />
    @yield('css')
</head>
<body  class="sb-nav-fixed">
<div id="app">
<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <span class="title-block">
        <a class="title-escape" href="#">{{__('layout.system_title')}}</a>
        <label class="ml-2 text-primary fon">{{__('layout.cp_title')}}</label>
    </span>
    <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle"><i class="fas fa-bars"></i></button>

    <!-- Navbar Search-->
    <form class="d-none d-md-inline-block form-inline ml-auto mr-0 mr-md-3 my-2 my-md-0">
        <!-- <div class="input-group">
            <input class="form-control" type="text" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" />
            <div class="input-group-append">
                <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
            </div>
        </div> -->
    </form>
    <!-- Navbar-->
    <ul class="navbar-nav ml-auto ml-md-0">
        <li class="nav-item dropdown">
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
            <a class="nav-link dropdown-toggle" id="userDropdown" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="{{url('/pass?page=admin')}}">{{__('layout.change_password')}}</a>
                <a class="dropdown-item" href="{{url('/admin/cps')}}">{{__('layout.cps')}}</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{url('/logout')}}">{{__('layout.logout')}}</a>
            </div>
        </li>
    </ul>
</nav>
<div id="layoutSidenav">
    @include('Layout/route-menu')
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid">
                @yield('content')
            </div>
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; yesio.net</div>
                    <div>
                        <a href="#">Privacy Policy</a>
                        &middot;
                        <a href="#">Terms &amp; Conditions</a>
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
@section('footerScripts')
@show
</html>
