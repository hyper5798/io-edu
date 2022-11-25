@extends('Layout.default')
@inject('DatePresenter', 'App\Presenters\DatePresenter')

@section('content')
    <div class="row breadcrumb">
        <div class="col-md-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/backend">後台儀表板</a></li>
                <li class="breadcrumb-item">{{__('layout.management') }}</li>
                <li class="breadcrumb-item active" aria-current="page">{{__('layout.accounts') }}</li>
            </ol>
        </div>
        <div class="col-md-6 text-center">
        </div>
        <div class="col-md-3 text-right">
            <!--<button type="button" class="btn btn-success text-right" @click="newCheck()">{{__('layout.add')}}</button>-->
        </div>
    </div>

<div v-show="!isNew" class="main-content">
    <table id ="table1"  class="table table-striped table-hover table-content">
        <thead>
        <tr>
            <th >ID</th>
            <th >{{__('layout.date') }}</th>
            <th >{{__('layout.name') }}</th> <!-- 名稱 -->
            <th >{{__('layout.accounts') }}</th> <!-- 名稱 -->
            <th >{{__('layout.roles') }}</th> <!-- 權限 -->
            <!-- <th >單位</th>單位 -->
            <!-- <th >啟用</th> 啟用 -->
            <th>選課</th>
            <th ></th>
        </tr>
        </thead>

        <tbody>
        @foreach ($users as $user)
            <tr>
                <td> {{$user->id}} </td>
                <td> {{$DatePresenter->getDate($user->updated_at)}} </td>
                <td> {{$user->name}} </td>
                <td> {{$user->email}} </td>
                <td> {{$user->role_name}} </td>
                <!--<td> {{$user->cp_name}} </td>-->
                <!--<td> {{$user->active}} </td>-->
                <td>
                    <button type="button" name="edit" class="btn btn-outline-primary btn-sm" @click="toChoiceCourse({!! $loop->index !!})">
                        變更
                    </button>
                </td>
                <td>
                    <button type="button" name="edit" class="btn btn-primary btn-sm" @click="editCheck({!! $loop->index !!})">
                        {{__('layout.edit')}}
                    </button>
                    <button type="button" name="del" class="btn btn-danger btn-sm" @click="delCheck({{$loop->index}}, '{{$user->name}}')">
                        {{__('layout.delete')}}
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<!-- Edit user-->
<div v-show="isNew" class="row justify-content-center main-content">
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
                <form method="post" id="editForm">
                    <input type="hidden" name="_method" value="put" />
                    <input type="hidden" name="id" v-model="user.id" />
                    <input type="hidden" name="password" v-model="user.password" />
                    <input type="hidden" name="email" v-model="user.email" />
                    {{csrf_field()}}
                    <div class="form-row">
                        <div class="input-group mb-3 col-md-6">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-sizing-default" >{{__('user.name') }}</span>
                            </div>
                            <input type="text" class="form-control" v-model="user.name" name="name">
                        </div>
                        <div class="input-group mb-3 col-md-6">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-sizing-default">{{__('user.email') }}</span>
                            </div>
                            <input v-if="user.id==0" type="text" class="form-control"  v-model="user.email" >
                            <input v-else type="text" class="form-control"  v-model="user.email" disabled>
                        </div>
                        <div class="input-group mb-3 col-md-6">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputGroupSelect01">{{__('user.company') }}</label>
                            </div>
                           <select  v-model="user.cp_id" class="custom-select" name="cp_id">
                               <option v-for="cp in cpList" :value="cp.id" :key="cp.id">
                                   @{{ cp.cp_name }}
                               </option>
                           </select>
                        </div>
                        <div class="input-group mb-3 col-md-6">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputGroupSelect01">{{__('layout.roles') }}</label>
                            </div>
                            <select  v-model="user.role_id" class="custom-select" name="role_id">
                                <option v-for="role in roleList" :value="role.role_id" :key="role.role_id">
                                    @{{ role.role_name }}
                                </option>
                            </select>
                        </div>

                        <div class="input-group mb-3 col-md-6">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="inputGroupSelect01">{{$user->active}}</label>
                            </div>
                            <select  v-model="user.active" class="custom-select" name="active">
                                <option v-for="act in actList" :value="act.id" :key="act.id">
                                    @{{ act.value }}
                                </option>
                            </select>
                        </div>

                        <div class="input-group mb-3 col-md-6">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-sizing-default">{{__('layout.accounts')}}ID</span>
                            </div>
                            <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="user.id" disabled>
                        </div>
                        <div class="col-md-12">
                            <button type="button" class="btn btn-secondary" @click="back()">{{__('layout.back')}}</button>
                            <button type="button" class="btn btn-primary" @click="toSubmit()">{{__('layout.submit')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">警告!</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    你確認要刪除用戶@{{user.name}}?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">取消
                    </button>
                    <form method="post" id="delForm">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="user.id" />
                        {{csrf_field()}}
                        <button type="button" onClick="toDelete()" class="btn btn-danger">
                            確定刪除
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footerScripts')
    <script>
        let users = {!! $users!!};
        let cps = {!! $cps!!};
        let roles = {!! $roles!!};
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/option/tableOption.js')}}"></script>
    <script src="{{asset('js/admin/users.js')}}"></script>
@endsection
