@extends('Layout.normal')

@section('content')
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <div class="card shadow-lg border-0 rounded-lg mt-3">
                                <div class="card-header text-center">
                                    <label class="font-weight-bold ">註冊</label>
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
                                    <form>
                                        <input type="hidden" name="link" value="{{$link}}" />
                                        {{csrf_field()}}
                                        <div class="form-row">
                                            <div class="col-md-12">
                                                <div class="form-group"><label class="small mb-1" for="inputFirstName">{{__('auth.user_name')}}</label><input class="form-control py-4" name="name" type="text" value="{{ old('name') }}" placeholder="{{__('auth.name_hold')}}" onfocus="disableMsg()"/></div>
                                            </div>

                                        </div>

                                        <div class="form-group"><label class="small mb-1" for="inputEmailAddress">{{__('auth.user_email')}}</label><input class="form-control py-4" name="email" type="email" aria-describedby="emailHelp" value="{{ old('email') }}" placeholder="{{__('auth.email_hold')}}" onfocus="disableMsg()" /></div>
                                        <div class="form-row">
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputPassword">{{__('auth.user_pass')}}</label><input class="form-control py-4" name="password" type="password" value="{{ old('password') }}" placeholder="{{__('auth.pass_hold')}}" onfocus="disableMsg()" /></div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group"><label class="small mb-1" for="inputConfirmPassword">{{__('auth.pass_confirm')}}</label><input class="form-control py-4" name="password_confirmation" type="password" value="{{ old('password_confirmation') }}" placeholder="{{__('auth.confirm_hold')}}" onfocus="disableMsg()" /></div>
                                            </div>
                                            <div class="col-md-12">
                                                請閱讀下述聲明與規範，同意後方能啟動註冊程序。
                                            </div>
                                            <div class="col-md-12">
                                                <div class="announce-Content">{!! $announce->content !!}</div>
                                            </div>
                                            <div class="input-group col-sm-5 col-md-3 mt-3">

                                                    <input type="checkbox" class="checkboxBlock" v-model="isAgree">
                                                    <span class="ml-2"><h5>同意</h5></span>

                                            </div>
                                            <div class="col-sm-7 col-md-9 mt-3">

                                               <span class="float-right">
                                                   <button v-if="!isAgree" type="button" class="btn btn-secondary btn-lg" disabled>{{__('auth.register')}}</button>
                                                   <button v-else type="submit" class="btn btn-primary btn-lg">{{__('auth.register')}}</button>
                                                </span>
                                            </div>
                                        </div>




                            </form>
                        </div>
                    <!--<div class="card-footer text-center">
                                    <div class="small"><a href="{{url('/login?link=')}}{{$link}}">{{__('auth.has_account')}}</a></div>
                                </div>-->
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footerScripts')
    <script type="text/javascript">
        function disableMsg() {
            let msg = document.getElementById("message");
            //console.log(msg)
            if(msg !== null)
                document.getElementById("message").remove();
        }

        let app = new Vue({
            el: '#app',
            data: {
                isAgree: false
            },
            methods: {
            }
        });
    </script>
@endsection
