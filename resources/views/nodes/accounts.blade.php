@extends('Layout.diy')

@section('content')
    <div class="row breadcrumb">
        <div class="col-md-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/node/myDevices')}}">{{__('device.title') }}</a></li>
                <li class="breadcrumb-item active">{{__('user.account_management') }}</li>
            </ol>
        </div>

        <div class="col-md-6 text-left">
            @if($classes->count()>0)
                {{__('layout.select') }}{{__('layout.class') }}
                <select onchange="location.href='accounts?class_id='+this.options[this.selectedIndex].value">
                    @foreach ($classes as $class)
                        @if ($class->id == $class_id)
                            <option value="{{$class->id}}" selected="selected">{{$class->class_name}}</option>
                        @else
                            <option value="{{$class->id}}">{{$class->class_name}}</option>
                        @endif
                    @endforeach
                </select>
            @endif
        </div>

        <div class="col-md-3 text-right">
            @if($classes->count()>0 && $user->cp_id > 1)
                <button type="button" class="btn btn-info text-right" @click="importCheck()">{{__('layout.import_add')}}</button>
                <button type="button" class="btn btn-success text-right" @click="newCheck()">{{__('layout.add')}}{{__('layout.accounts')}}</button>
            @endif
        </div>
    </div>
    <!-- E-mail exist waring -->
    @if (count($errors) > 0)
        <div class="alert alert-danger mt-3" id="message2">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <!-- Check error waring -->
    <div v-cloak v-if="isError" class="alert alert-danger mt-3" id="message">
        <ul>
            <li v-for="msg in messages">@{{ msg }}</li>
        </ul>
    </div>



    <!-- List users -->
    <div v-show="isNew==0" class="main-content">
        <table id ="table1"  class="table table-striped table-hover table-content">
            <thead>
            <tr>
                <th >{{__('layout.item') }}</th> <!-- 項目 -->
                <th >{{__('layout.date') }}</th> <!-- 日期 -->
                <th >{{__('layout.name') }}</th> <!-- 名稱 -->
                <th >{{__('layout.accounts') }}</th> <!-- 名稱 -->
                <th ></th>
            </tr>
            </thead>

            <tbody>
            @foreach ($users as $mUser)
                <tr>
                    <td> {{$loop->index +1}} </td>
                    <td> {{$mUser->updated_at}} </td>
                    <td> {{$mUser->name}} </td>
                    <td> {{$mUser->email}} </td>
                    <td>
                        <button type="button" name="edit" class="btn btn-primary btn-sm" @click="editCheck({!! $loop->index !!})">
                            {{__('layout.edit')}}
                        </button>
                        <button type="button" name="del" class="btn btn-danger btn-sm" @click="delCheck({{$loop->index}}, '{{$mUser->name}}')">
                            {{__('layout.delete')}}
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Edit user-->
    <div v-cloak v-show="isNew==1" class="row justify-content-center main-content">

        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-lg mt-2">
                <div class="card-header">
                    <!-- edit company data -->
                    <h3 v-if="user.id>0" class="text-center font-weight-light my-4">
                        {{__('layout.edit')}}{{__('user.data')}}
                    </h3>
                    <!-- Add company data -->
                    <h3 v-else class="text-center font-weight-light my-4">
                        {{__('layout.add')}}{{__('user.data')}}
                    </h3>

                </div>
                <div class="card-body">
                    <form method="post" action="editAccount" id="editAccount">
                        <input type="hidden" name="id" v-model="user.id" />
                        <input type="hidden" name="password" v-model="user.password" />
                        <input type="hidden" name="email" v-model="user.email" />
                        <input type="hidden" name="cp_id" v-model="user.cp_id" />
                        <input type="hidden" name="role_id" v-model="user.role_id" />
                        <input type="hidden" name="class_id" v-model="user.class_id" />
                        {{csrf_field()}}
                        <div class="form-row">
                            <!--User Name -->
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{__('user.name') }}</span>
                                </div>
                                <input type="text" class="form-control" v-model="user.name" name="name" @focus="onFocusEvent">
                            </div>
                            <!--User Email -->
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{__('user.email') }}</span>
                                </div>
                                <input v-if="user.id==0" type="text" class="form-control"  v-model="user.email" @focus="isError=false">
                                <input v-else type="email" class="form-control"  v-model="user.email" disabled>
                            </div>
                            <!--User Roles -->
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">{{__('layout.roles') }}</span>
                                </div>
                                <!-- 一般用戶 -->
                                <input v-if="user.role_id==11" type="text" class="form-control"  value="{{__('user.normal_user') }}" disabled>
                                <!-- 管理用戶 -->
                                <input v-else type="text" class="form-control" value="{{__('user.admin_user') }}" disabled>
                            </div>
                            <!--User Class -->
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <label class="input-group-text">{{__('layout.class') }}</label>
                                </div>
                                <input type="text" class="form-control" v-model="user.class_name" disabled>
                            </div>

                            <div class="col-md-3">
                                <button type="button" class="btn btn-secondary" @click="back()">{{__('layout.back')}}</button>
                                <button type="button" class="btn btn-primary" @click="toSubmit()">{{__('layout.submit')}}</button>
                            </div>
                            <div  class="col-md-6">

                                <span v-if="user.role_id==10">{{__('user.warning4')}}</span><!-- 帳戶會從班級移出 -->
                                <span v-else>{{__('user.warning5')}}</span><!-- 帳戶會加入到班級中 -->
                                <br>{{__('user.warning6')}}
                            </div>
                            <div class="col-md-3 text-right">
                                <span >
                                    <!-- 切換為管理用戶 -->
                                    <button v-if = "user.role_id==11" type="button" class="btn btn-warning" @click="toAdmin()">{{__('user.switch_admin')}}</button>
                                    <!-- 切換為一般用戶 -->
                                    <button v-else type="button" class="btn btn-warning" @click="toUser()">{{__('user.switch_user')}}</button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Import excel -->
    <div v-cloak v-show="isNew==2" class="row justify-content-center main-content">

        <div class="col-lg-12">
            <div class="card shadow-lg border-0 rounded-lg mt-2">
                <div class="card-header">
                    <!-- Import excel data -->
                    <h3 class="text-center font-weight-light my-4">
                        {{__('layout.import_add')}}
                    </h3>
                </div>
                <div class="card-body">


                    <form method="post" action="editBatchAccount" id="editBatchAccount">
                        <input type="hidden" name="password" v-model="user.password" />
                        <input type="hidden" name="cp_id" v-model="user.cp_id" />
                        <input type="hidden" name="role_id" v-model="user.role_id" />
                        <input type="hidden" name="class_id" v-model="user.class_id" />
                        {{csrf_field()}}
                        <div class="form-row">
                            <div class="col-md-9 mt-1">
                                <input type="file" onchange="importf(this)" />
                            </div>
                            <div class="col-md-3 mt-1 text-left">
                                <div>
                                    <!-- 範例下載連結 -->
                                    <a href="{{asset('doc/import_accounts.xlsx')}}">{{__('layout.example_link')}}</a>
                                    <!-- 自行下載修改 -->

                                    <div>{{__('layout.download_by_yourself')}}</div>
                                </div>
                            </div>
                            <div class="col-md-9 mt-1 text-left">
                                <textarea v-model="accountStr" name="accountStr" rows="5" cols="100" style="overflow-y:scroll">
                                </textarea>
                            </div>
                            <div class="col-md-3 mt-1 text-left">
                                {{__('layout.format')}}:
                                <img src="{{url('/Images/import_excel.png')}}" width="250px">
                            </div>

                            <div class="col-md-12 mt-3">
                                <button type="button" class="btn btn-secondary" @click="back()">{{__('layout.back')}}</button>
                                <button type="button" class="btn btn-primary" @click="toImportSubmit()">{{__('layout.submit')}}</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Delete Dialog-->
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
                    {{__('layout.delete_confirm')}} @{{user.name}}?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delAccount" id="delAccount">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="user.id" />
                        {{csrf_field()}}
                        <button type="button" onClick="toDelete()" class="btn btn-danger">
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
        let user = {!! $user !!};
        let data = {!! json_encode($data) !!};
        let users = {!! $users !!};
        let class_id = {!! $class_id !!};
        let classes = {!! $classes !!};
        let role_id = {!! $user['role_id'] !!};
        let cp_id = {!! $user['cp_id'] !!};

    </script>

    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" charset="utf-8" ></script>
    <script src="{{asset('js/option/tableOption.js')}}"></script>
    <script src="{{asset('vender/js-xlsx/xlsx.full.min.js')}}" charset="utf-8" ></script>
    <script src="{{asset('js/node/accounts.js')}}"></script>
@endsection
