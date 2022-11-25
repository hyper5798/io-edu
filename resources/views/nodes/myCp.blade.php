@extends('Layout.diy')

@section('content')
    <div class="row breadcrumb">
        <div class="col-md-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/node/myDevices">{{__('device.title') }}</a></li>
                <li class="breadcrumb-item active">{{__('layout.my_cp') }}</li>
            </ol>
        </div>

        <div class="col-md-7 text-left">
            @if($cps->count()>0 && $user->cp_id == 1)
                {{__('layout.select') }}{{__('layout.school') }}
                <select v-cloak v-model="selected" @change="onChange($event)">
                    <option v-for="(cp, index) in cpList" :value="index" >@{{cp.cp_name}}</option>
                </select>
            @elseif($cps != null && $user->cp_id != 1)
                {{__('layout.school') }}
                @foreach ($cps as $cp)
                    @if ($cp->id === $cp_id)
                        <input type="text" value="{{$cp->cp_name}}" disabled>
                    @endif
                @endforeach
            @endif
        </div>

        <div class="col-md-2 text-right">
            @if($cps->count()>0 && $user->cp_id != 1)
                <button type="button" class="btn btn-success text-right" @click="newCheck()">{{__('layout.add')}}{{__('layout.class')}}</button>
            @endif
        </div>
    </div>

    <!-- Edit cp-->
    @if($user->cp_id == 1)
    <div v-cloak class="row justify-content-center main-content">
        <div class="col-lg-12">
            <div class="card shadow-lg border-0 rounded-lg mt-3">
                <div  class="card-header">
                    <!-- edit company data -->
                    <h5 class="text-center my-4">
                        {{__('layout.my_cp')}}
                    </h5>
                </div>
                <div class="card-body">
                    <form method="post" action="editCp" id="editCp">
                        <input type="hidden" name="id" v-model="cp.id" />
                        <input type="hidden" name="role_id" v-model="cp.role_id" />
                        {{csrf_field()}}
                        <div class="form-row">
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >{{__('layout.school')}}</span>
                                </div>
                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="cp.cp_name" name="cp_name">
                            </div>
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">{{__('layout.telephone')}}</span>
                                </div>
                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="cp.phone" name="phone">
                            </div>
                            <div class="input-group mb-3 col-md-12">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="inputGroupSelect01">{{__('layout.address')}}</label>
                                </div>
                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="cp.address" name="address">
                            </div>

                            <div class="col-md-12"><button type="button" class="btn btn-primary" @click="setSchool()">
                                        {{__('layout.yes')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
    <!-- Class List -->
    @if($cps->count()>0 && $user->cp_id != 1)
    <div v-show="!isEdit" class="row main-content">
        <div class="col-lg-12">
            <table id ="table1"  class="table table-striped table-hover">
                <thead>
                <tr>
                    <th width="10%">{{__('layout.item') }}</th>
                    <th >{{__('layout.date')}}</th>
                    <th >{{__('layout.class')}}</th>
                    <th > </th>
                </tr>
                </thead>

                <tbody >
                @foreach($classes as $class)
                    <tr>
                        <td>{{$loop->index+1}}</td>
                        <td>{{$class->updated_at}}</td>
                        <td>{{$class->class_name}}</td>
                        <td>
                            <button @v-if="editPoint!={!! $loop->index !!}" type="button" name="edit" class="btn btn-primary btn-sm" @click="editClass({!! $loop->index !!})">
                                {{__('layout.edit')}}
                            </button>
                            <button type="button" name="del" class="btn btn-danger btn-sm" @click="delClass({{$loop->index}})">
                                {{__('layout.delete')}}
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
    <!-- Edit myClass-->
    <div v-cloak v-show="isEdit" class="row justify-content-center main-content">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-lg mt-2">
                <div  class="card-header">
                    <!-- edit company data -->
                    <h3 v-if="myClass.id>0" class="text-center font-weight-light my-4">
                        {{__('layout.edit')}}{{__('layout.data')}}
                    </h3>
                    <!-- Add company data -->
                    <h3 v-else class="text-center font-weight-light my-4">
                        {{__('layout.add')}}{{__('layout.data')}}
                    </h3>
                </div>
                <div class="card-body">
                    <form method="post" action="editClass" id="editClass">
                        <input type="hidden" name="id" v-model="myClass.id" />
                        <input type="hidden" name="class_option" v-model="myClass.class_option" />
                        <input type="hidden" name="cp_id" v-model="myClass.cp_id" />
                        {{csrf_field()}}
                        <div class="form-row">

                            <div class="input-group mb-3 col-md-12">
                                <div class="input-group-prepend">
                                    <label class="input-group-text" for="inputGroupSelect01">{{__('layout.class')}}{{__('layout.name')}}</label>
                                </div>
                                <input type="text" class="form-control" v-model="myClass.class_name" name="name">
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
                    {{__('layout.delete_confirm')}} @{{myClass.class_name}} ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delClass" id="delClass">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="myClass.id" />
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
        let cps = {!! $cps !!};
        let cp_id = {!! $cp_id !!};
        let classes = {!! $classes !!};
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" charset="utf-8" ></script>
    <script src="{{asset('js/option/tableOption.js')}}"></script>
    <script src="{{asset('js/node/setCp.js')}}"></script>
@endsection
