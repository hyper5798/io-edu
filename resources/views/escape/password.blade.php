@extends('Layout.escape')

@section('content')
    <!--tab button -->
    <div class="mt-2">
        <button type="button"  onClick="toChangePass()" class="btn btn-outline-secondary" >
            {{__('layout.personal_info')}}
        </button>

        <button type="button"  class="btn btn-secondary" >
            {{__('layout.change_password')}}
        </button>

    </div>
    <div >

            <div class="main-container">
                <div class="row">
                    <div class="col-lg-12 mt-3">
                        <div class="card shadow-lg border-0 rounded-lg ">
                            <div class="card-header mission_header">
                                <span class="ml-4">
                                    {{__('passwords.change_password')}}
                                </span>
                            </div>
                            <div class="card-body">
                                @if(isset($success))
                                    <div class="alert alert-success" id="message">
                                        <ul>
                                            <li>{{ $success }}</li>
                                        </ul>
                                    </div>
                                @endif
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
                                    @csrf
                                    <!--
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputEmailAddress" >{{__('passwords.old_pass')}}</label>
                                        <input class="form-control py-4" name="old_pass" type="password" aria-describedby="emailHelp" value="{{ old('old_pass')}}" placeholder="{{__('passwords.old_hold')}}" onfocus="disableMsg()"/>
                                    </div>-->

                                    <input class="form-control py-4" name="old_pass" type="hidden" value="{{ old('old_pass')}}"/>
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputEmailAddress">{{__('passwords.new_pass')}}</label>
                                        <input class="form-control py-4" name="new_pass" type="password" aria-describedby="emailHelp" value="{{ old('new_pass')}}" placeholder="{{__('passwords.new_hold')}}" onfocus="disableMsg()"/>
                                    </div>
                                    <div class="form-group">
                                        <label class="small mb-1" for="inputEmailAddress">{{__('passwords.confirm_pass')}}</label>
                                        <input class="form-control py-4" name="new_pass_confirmation" type="password" aria-describedby="emailHelp" value="{{ old('confirm_pass')}}" placeholder="{{__('passwords.confirm_hold')}}" onfocus="disableMsg()"/>
                                    </div>


                                    <button type="button" class="btn btn-secondary" onclick="history.back()">{{__('layout.cancel')}}</button>
                                    <button type="submit" class="btn btn-primary">{{__('passwords.reset_pass')}}</button>


                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

    </div>
    <script type="text/javascript">

        let newUrl = "{{url('/escape/profile')}}";
        function toChangePass() {
            //alert(newUrl);
            document.location.href = newUrl;
        }
        function disableMsg() {
            document.getElementById("message").style.display = 'none';
        }
    </script>
@endsection
