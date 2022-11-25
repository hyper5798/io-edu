<!DOCTYPE html>
<html lang="tw">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <!-- CSRF Token -->
        <!-- <meta name="csrf-token" content="{{ csrf_token() }}"> -->
        <title>{{__('layout.cp_title')}}</title>
        <link rel="shortcut icon" href="{{ asset('favicon.ico')}}" type="image/x-icon">
        <link href="{{asset('css/styles.css')}}" rel="stylesheet" />
        <link href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css" rel="stylesheet" crossorigin="anonymous" />


    </head>
    <body class="bg-dark bg-auth">
        <div id="layoutAuthentication">
            @yield('content')
        </div>
        <div id="layoutAuthentication_footer">
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; yesio.net </div>
                    <div>
                        <a href="#">Privacy Policy</a>
                        &middot;
                        <a href="#">Terms &amp; Conditions</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <script src="{{asset('js/all.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/jquery-3.4.1.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/bootstrap-4.3.1/js/bootstrap.bundle.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/scripts.js')}}"></script>

    </body>
</html>
