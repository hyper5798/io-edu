@extends('Layout.escape')

@section('content')
    <!--tab button -->
    <div v-cloak class="mt-2">
        <button v-if="tab==1" type="button"  class="btn btn-secondary" >
            {{__('layout.personal_info')}}
        </button>
        <button v-else type="button" class="btn btn-outline-secondary" >
            {{__('layout.personal_info')}}
        </button>

        <button v-if="tab==2" type="button" class="btn btn btn-secondary" >
            {{__('layout.change_password')}}
        </button>
        <button v-else type="button" class="btn btn-outline-secondary" >
            {{__('layout.change_password')}}
        </button>
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
                        <span class="float-right mr-3">
                            <button type="button" class="btn btn-primary">
                                {{__('layout.add')}}
                            </button>
                        </span>
                    </div>
                    <div class="card-body">
                        <!-- Edit -->

                        <div class="row justify-content-center main-content">
                            <div class="col-lg-12">
                                <div >

                                    <div class="card-body">
                                        <form method="post" action="edit" id="edit">
                                            <input type="hidden" name="id" />

                                            {{csrf_field()}}
                                            <div class="form-row">
                                                <div class="input-group mb-3 col-md-6">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text" id="inputGroup-sizing-default" >{{__('layout.school')}}</span>
                                                    </div>
                                                    <input type="text" class="form-control" name="cp_name">
                                                </div>
                                                <div class="col-md-12">
                                                    <button type="button" class="btn btn-primary">
                                                        {{__('layout.submit')}}
                                                    </button>
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

    </script>
    <script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/escape/empty.js')}}"></script>
@endsection


