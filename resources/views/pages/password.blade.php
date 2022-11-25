@extends('Layout.default')

@section('content')
    <div >
        <main>
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card shadow-lg border-0 rounded-lg ">
                            <div class="card-header">
                                <span class="ml-3" style="font-size: 20px">{{__('passwords.change_password')}}</span>
                                @if($code)
                                    <span class="float-right">
                                        <input type ="button" onclick="history.back()" value="回到上一頁"></input>
                                    </span>
                                @endif
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
        </main>
    </div>
    <script type="text/javascript">
        function disableMsg() {
            document.getElementById("message").style.display = 'none';;
        }
    </script>
@endsection
