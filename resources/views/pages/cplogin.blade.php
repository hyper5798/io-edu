@extends('Layout.normal')
@section('content')
    <div id="layoutAuthentication_content">
        <main>
            <div class="container">
                <div class="row justify-content-center loginBlock">
                    <div class="col-lg-5">
                        <div class="text-center mt-5 mb-3"></div>
                        <div class="card shadow-lg border-0 rounded-lg mt-2">
                            <div class="card-header">
                                <h3 class="text-center font-weight-light">
                                        {{__('auth.login_title') }}
                                </h3>
                            </div>
                            <div class="card-body">
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger" id="message">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="row mb-4">

                                </div>


                                <form method="post" action="postLogin" id="postLogin">

                                    {{csrf_field()}}


                                    <div class="form-group"><label class="small mb-1" for="inputEmailAddress">{{__('auth.user_email')}}</label><input class="form-control py-4" name="email" type="email" value="{{ old('email') }}" placeholder="{{__('auth.email_hold')}}" onfocus="disableMsg()" /></div>
                                    <div class="form-group"><label class="small mb-1" for="inputPassword">{{__('auth.user_pass')}}</label><input class="form-control py-4" name="password" type="password" value="{{ old('password') }}" placeholder="{{__('auth.pass_hold')}}" onfocus="disableMsg()" /></div>
                                <!--<div class="form-group">
                                            <div class="custom-control custom-checkbox"><input class="custom-control-input" id="rememberPasswordCheck" type="checkbox" /><label class="custom-control-label" for="rememberPasswordCheck">{{__('auth.remember_pass')}}</label></div>
                                        </div>-->

                                    @if (session('message'))
                                        <div class="alert alert-danger" id="message">
                                            {{ session('message') }}
                                        </div>
                                    @endif
                                    <div class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">
                                        @if(env('IS_IOFLEX')==true)
                                            <p><a href="{{asset('doc/ioFLEX使用手冊.pdf')}}">ioFLEX使用手冊</a></p>
                                    @endif
                                    <!--<a class="small" href="password.blade.php">{{__('auth.forget_pass')}}</a>-->
                                        <a class="small" href="password.blade.php"></a>
                                        <button type="submit" class="btn btn-primary float-right">{{__('auth.login')}}</button>

                                    </div>

                                </form>
                            </div>
                            <div class="card-footer text-center">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">掃描QRCode下載密室 APP </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <img src="<?php echo e(url('/Images/qrcode.png')); ?>" width="150px">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">OK
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        //Fix facebook return hash
        if (window.location.hash && window.location.hash == '#_=_') {
            if (window.history && history.pushState) {
                window.history.pushState("", document.title, window.location.pathname);
            } else {
                // Prevent scrolling by storing the page's current scroll offset
                let scroll = {
                    top: document.body.scrollTop,
                    left: document.body.scrollLeft
                };
                window.location.hash = '';
                // Restore the scroll offset, should be flicker free
                document.body.scrollTop = scroll.top;
                document.body.scrollLeft = scroll.left;
            }
        }

        function disableMsg() {
            let msg = document.getElementById("message");
            //console.log(msg)
            if(msg !== null)
                document.getElementById("message").remove();
        }

        function showQRCode() {
            $('#myModal').modal('show');
        }
    </script>
@endsection
