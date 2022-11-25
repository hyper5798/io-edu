@extends('Layout.room')

@section('content')
    <!-- Tab -->
    <div class="row mt-2">
        <div class="col-12">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#1">{{__('layout.cps') }}</a>
                </li>
                @if(env('IS_GROUP')==true)
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#2">{{__('layout.group')}}</a>
                    </li>
                @endif


                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#3">{{__('user.account_management') }}</a>
                </li>

            </ul>
        </div>
        <!--<div class="col-1">
            <button  title="{{__('layout.tutorials') }}" type="button" class="btn btn-warning" onclick="window.location='{{ url("/escape/carousel?app=4") }}'"><i class="fas fa-question"></i></button>
        </div>-->

    </div>
    @if (count($errors) > 0)
        <div class="alert alert-danger mt- 2" id="message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <!-- Edit company -->
    <div v-cloak class="row justify-content-center">

        <div class="col-md-12 col-xl-12">
            <div class="card shadow-lg  rounded-lg mt-3">
                <div  class="card-header mission_header">
                    <span class= "ml-3">
                        {{__('layout.school_alert') }}
                    </span>
                    <span class="float-right mr-3">

                        <button  type="button" class="btn btn-success text-right" @click="newCheck()">{{__('layout.add')}}</button>

                    </span>
                </div>
                <div class="card-body main-content">
                    <!-- cp list -->

                    <div v-cloak v-show="!isEdit">
                        <table id ="table1"  class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th width="10%">{{__('layout.item') }}</th>
                                <th >{{__('layout.date')}}</th>
                                <th >{{__('layout.cps')}}</th>
                                <th >{{__('layout.creator')}}</th>
                                <th > </th>
                            </tr>
                            </thead>

                            <tbody >
                            @foreach($cps as $item)
                            <tr>
                                <td>{{$loop->index+1}}</td>
                                <td>{{$item->updated_at}}</td>
                                <td>{{$item->cp_name}}</td>
                                <td>{{$item->name}}</td>

                                <td>
                                    @if($item->user_id == $user->id)
                                    <button @v-if="editPoint!={!! $loop->index !!}" type="button" name="edit" class="btn btn-primary btn-sm" @click="editCp({!! $loop->index !!})">
                                        {{__('layout.edit')}}
                                    </button>
                                    <button type="button" name="del" class="btn btn-danger btn-sm" @click="delCp({{$loop->index}})">
                                        {{__('layout.delete')}}
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- edit cp -->
                    <div v-cloak v-show="isEdit">
                        <form method="post" action="editCp" id="editCp">
                            <input type="hidden" name="id" v-model="cp.id" />
                            <input type="hidden" name="role_id" v-model="cp.role_id" />
                            <input type="hidden" name="phone" v-model="cp.phone" />
                            <input type="hidden" name="address" v-model="cp.address" />
                            {{csrf_field()}}
                            <div class="form-row">
                                <!-- 是否有母公司 -->
                                <div class="input-group mb-3 col-md-2">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text">是否有母公司</label>
                                    </div>
                                    <input type="checkbox" v-model="isParent" name="isParent" id="isParent" class="form-control">

                                </div>
                                <div class="col-md-10"></div>
                                <!-- 選擇母公司 -->
                                <div v-if="isParent==1" class="input-group mb-3 col-md-5 justify-content-md-center">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-default" >選擇母公司</span>
                                    </div>

                                    <select  v-model="cp.parent_id" name="parent_id" class="form-control">
                                        <option v-for="item in parentList" :value="item.id" :key="item.id">
                                            @{{ item.cp_name }}
                                        </option>
                                    </select>
                                </div>

                                <div v-if="isParent==1" class="col-md-auto"></div>

                                <div class="input-group mb-3 col-md-7">

                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-default" >{{__('layout.cps')}}</span>
                                    </div>
                                    <input type="text" class="form-control"  v-model="cp.cp_name" name="cp_name">
                                </div>
                                <div class="col-md-12">
                                    <button type="button" class="btn btn-secondary" @click="back">{{__('layout.back')}}</button>

                                    <button type="button" class="btn btn-primary" @click="toSubmit">
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



    <!-- Delete waring modal-->
    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{__('layout.waring')}}!</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('escape.delete_cp_waring')}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delCp" id="delCp">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="cp.id" />

                        {{csrf_field()}}
                        <button type="button" @click="toDelete()" class="btn btn-danger" >
                            {{__('layout.yes')}}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footerScripts')
    <script>
        let parent_cps =  {!! $parent_cps !!};
        let parent_id =  {!! $parent_id !!};
        let cps = {!! $cps !!};
        let cp_id = {!! $cp_id !!};
        let role_id = {!! $user->role_id !!};
        let menu1 = "{{__('layout.cps') }}";
        let menu2 = "{{__('layout.group') }}";
        let menu3 = "{{__('user.account_management') }}";
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" charset="utf-8" ></script>
    <script src="{{asset('js/option/tableOption.js')}}"></script>
    <script src="{{asset('js/room/setCp.js')}}"></script>
@endsection
