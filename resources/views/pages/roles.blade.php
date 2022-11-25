@extends('Layout.default')

@section('content')
    <div class="row breadcrumb">
        <div class="col-md-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/backend">後台儀表板</a></li>
                <!--<li class="breadcrumb-item">{{__('layout.management') }}</li> -->
                <li class="breadcrumb-item active" aria-current="page">{{__('layout.roles') }}</li>
            </ol>
        </div>
        <div class="col-md-6 text-center">
        </div>
        <div class="col-md-3 text-right">
            <button type="button" class="btn btn-success text-right" @click="newCheck()">{{__('layout.add')}}</button>
        </div>
    </div>

<div v-show="!isNew" class="main-content">
    <table id ="table1"  class="table table-striped table-hover">
        <thead>
        <tr>
            <th >{{__('layout.item')}}</th>
            <th >{{__('role.name')}}</th>
            <th >{{__('role.id')}}</th>
            <th >{{__('role.dataset')}}</th>
            <th >{{__('layout.update_at')}}</th>
            <th > </th>
        </tr>

        </thead>

        <tbody>
        @foreach ($roles as $role)
            <tr>
                <td> {{$role->index +1}} </td>
                <td> {{$role->role_name}} </td>
                <td> {{$role->role_id}} </td>
                <td> {{$role->dataset}} </td>
                <td> {{$role->updated_at}} </td>
                <td>
                    <button @v-if="editPoint!={!! $loop->index !!}" type="button" name="edit" class="btn btn-primary btn-sm" @click="editCheck({!! $loop->index !!})">
                        {{__('layout.edit')}}
                    </button>
                    <button type="button" name="del" class="btn btn-danger btn-sm" @click="delCheck({{$loop->index}})">
                        {{__('layout.delete')}}
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
    <!-- Edit Role-->
    <div v-show="isNew" class="row justify-content-center main-content">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-lg mt-2">
                <div  class="card-header">
                    <!-- edit company data -->
                    <h3 v-if="role.id>0" class="text-center font-weight-light my-4">
                        {{__('layout.edit')}}{{__('layout.data')}}
                    </h3>
                    <!-- Add company data -->
                    <h3 v-else class="text-center font-weight-light my-4">
                        {{__('layout.add')}}{{__('layout.data')}}
                    </h3>
                </div>
                <div class="card-body">
                    <form method="post" id="editForm">
                        <input type="hidden" name="_method" value="put" />
                        <input type="hidden" name="id" v-model="role.id" />
                        {{csrf_field()}}
                        <div class="form-row">
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >{{__('role.name')}}</span>
                                </div>
                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="role.role_name" name="role_name">
                            </div>
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">{{__('role.id')}}</span>
                                </div>
                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="role.role_id" name="role_id">
                            </div>
                            <div class="input-group mb-3 col-md-12">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="inputGroupSelect01">{{__('role.dataset')}}</label>
                                </div>
                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="role.dataset" name="dataset">
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
                    <h4 class="modal-title" id="myModalLabel">{{__('layout.waring')}}!</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('layout.delete_confirm')}} @{{role.role_name}} ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" id="delForm">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="role.id" />
                        {{csrf_field()}}
                        <button type="button" onClick="toDelete()" class="btn btn-danger" >
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
        let roles = {!! $roles !!};
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/admin/roles.js')}}"></script>
@endsection
