@extends('Layout.escape')

@section('content')
    <!--tab button -->
    <div class="row mt-2">
        <div class="col-11">
            <button type="button"  class="btn btn-secondary" >
                {{__('layout.personal_info')}}
            </button>

            <button type="button" @Click="toChangePass" class="btn btn-outline-secondary" >
                {{__('layout.change_password')}}
            </button>
        </div>

        <div class="col-1">
            <button  title="{{__('layout.tutorials') }}" type="button" class="btn btn-warning" onclick="window.location='{{ url("/escape/carousel?app=6") }}'"><i class="fas fa-question"></i></button>
        </div>
    </div>
    <div class="main-content">
        <div class="row justify-content-center">
            <!-- Edit -->
            <div class="col-12">
                <div class="card shadow-lg  rounded-lg mt-3">
                    <div  class="card-header mission_header">

                        <span class="ml-3">
                            {{__('layout.personal_info')}}
                        </span>

                    </div>
                    <div class="card-body">
                        <!-- Edit -->

                        <div class="row justify-content-center">
                            <div class="col-sm-12 col-md-12 col-xl-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex flex-column align-items-center text-center">
                                            <button type="button" class="rounded-circle">
                                                <img id="preview_progressbarTW_img" :src="image_url" class="rounded-circle" width="150" height="150">
                                            </button>

                                            <div class="mt-3">
                                                <h4>{{$user->name}}</h4>
                                                <p class="text-secondary mb-1">{{$user->email}}</p>
                                                <p class="text-muted font-size-sm">
                                                    @if($profile && $profile->address)
                                                        {{$profile->address}}
                                                    @endif
                                                </p>

                                            </div>
                                            <hr>


                                            <form method="post" action="uploadImage" id="uploadImage" enctype="multipart/form-data">
                                                {{csrf_field()}}
                                                <div class="row">
                                                    <div class="col-12">
                                                        <input name="progressbarTW_img" type="file" id="imgInp" accept="image/gif, image/jpeg, image/png"/ >
                                                    </div>
                                                    <div class="col-12">
                                                        <span class="float-right">
                                                            <input class="btn btn-primary" name="" type="submit" value="上傳"/>
                                                        </span>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-sm-12 col-md-12 col-xl-8">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <form method="post" action="editProfile" id="editProfile">
                                            <input type="hidden" name="id" v-model="profile.id" />
                                            {{csrf_field()}}
                                            <div class="row">
                                            <div class="col-sm-2">
                                                <span class="profile-title">{{__('user.name') }}</span>
                                            </div>
                                            <div class="col-sm-10 text-secondary">
                                                <input type="text" v-model="user.name" name="name" size="10" >
                                            </div>

                                        </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <p class="profile-title">{{__('user.cellphone') }}</p>
                                                </div>
                                                <div v-cloak class="col-sm-10 text-secondary">

                                                    <input type="text" v-model="profile.cellphone" name="cellphone" size="10" >
                                                </div>


                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <p class="profile-title">{{__('user.telephone') }}</p>
                                                </div>
                                                <div v-cloak class="col-sm-8 text-secondary">

                                                    <input type="text" v-model="profile.telephone" name="telephone" size="10" >
                                                </div>

                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <p class="profile-title">{{__('user.birthday') }}</p>
                                                </div>
                                                <div v-cloak class="col-sm-10 text-secondary">
                                                    <input type="text" v-model="profile.birthday" name="birthday" size="10">
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-sm-2">
                                                    <p class="profile-title">{{__('user.address') }}</p>
                                                </div>
                                                <div v-cloak class="col-sm-10 text-secondary">
                                                    <input type="text" v-model="profile.address" name="address" size="40" >
                                                </div>
                                            </div>
                                            <hr>
                                            <div class ="row">

                                                <div v-cloak class="col-sm-12 text-secondary">
                                                    <span class="float-right mr-3">
                                                        <button type="button" class="btn btn-primary" @click="toSubmit">
                                                            {{__('layout.submit')}}
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('footerScripts')
    <script>
                @if($profile == null)
        let profile = null;
                @else
        let profile = {!! $profile !!};
                @endif
        let user = {!! $user !!};
        let newUrl = "{{url('/pass?page=escape')}}";
        let name_required = "{{__('user.name_required') }}";
    </script>
    <script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/escape/profile.js')}}"></script>
@endsection


