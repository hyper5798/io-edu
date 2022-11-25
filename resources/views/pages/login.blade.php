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
                                    {{__('layout.system_title')}}
                                </h3>
                            </div>
                            <div class="card-body">

                                <div class="row mb-4">
                                    <!--
                                    <div class="col-lg-6 col-md-6 col-xs-12 col-sm-6"> <a href="{{url('/redirect/facebook')}}" class="btn btn-primary facebook"> <span>FB 帳號登入</span> <img src="{{url('/Images/fb.png')}}" width="20px"> </a> </div>
                                    <div class="col-lg-6 col-md-6 col-xs-12 col-sm-6"> <a href="{{url('/redirect/google')}}" class="btn btn-info facebook"> <span>Google 帳號登入</span> <img src="{{url('/Images/google.png')}}" width="20px"> </a> </div>
                                    -->
                                </div>


                                <form method="post" action="postLogin" id="postLogin">

                                    {{csrf_field()}}


                                    <div class="form-group">
                                        <label class="small mb-1" for="inputEmailAddress">
                                            <h5>{{__('auth.user_email')}}</h5>
                                        </label>
                                        <input class="form-control py-4" name="email" id="email" type="email" value="{{ old('email') }}" placeholder="{{__('auth.email_hold')}}" onfocus="disableMsg()"/></div>
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputPassword">
                                            <h5>{{__('auth.user_pass')}}</h5>
                                        </label>
                                        <input class="form-control py-4" name="password" id="password" type="password" value="{{ old('password') }}" placeholder="{{__('auth.pass_hold')}}" onfocus="checkMail()" />
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


                                    <div v-cloak v-if="message.length>0" class="alert alert-danger" id="message">
                                        @{{message}}
                                    </div>

                                    <div v-cloak v-if="!isShowResend" class="form-group d-flex align-items-center justify-content-between mt-4 mb-0">

                                        <a class="small" href="{{url('/forgot-password')}}"><h5>{{__('auth.forget_pass')}}</h5></a>

                                        <button type="submit" class="btn btn-primary float-right" @click="isLogin=true;">{{__('auth.login')}}</button>

                                    </div>

                                </form>
                            </div>
                            <div class="card-footer text-center">

                                <button v-cloak v-if="isShowResend" type="button" class="btn btn-outline-success" onclick="toResendMail();">
                                    重送認證信
                                </button>
                                <button v-cloak v-if="isShowResend" type="button" class="btn btn-outline-dark" @click="cancelResendMail();">
                                    取消
                                </button>

                                <p class="float-right"><a href="/register">{{__('auth.register_account')}}</a></p>



                                @if(env('ACCOUNT_MANAGER')==1)
                                    <!--<p class="float-left">
                                        <button type="button" class="btn btn-outline-secondary" onclick="showQRCode()">
                                            顯示 APP QRCode
                                        </button>
                                    </p>-->

                                @else
                                    <!--<p>
                                        <button type="button" class="btn btn-outline-secondary" onclick="showQRCode()">
                                            顯示 APP QRCode
                                        </button>
                                    </p>-->
                                @endif


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
                    <!--<img src="<?php echo e(url('/Images/qrcode.png')); ?>" width="150px">-->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">OK
                    </button>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('footerScripts')
    <script>
        let api_url = '{{ env('API_URL')}}';
        let errors = {!! $errors !!};
    </script>
    <script src="{{asset('js/login.js')}}"></script>
@endsection
