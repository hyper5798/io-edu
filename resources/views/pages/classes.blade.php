@extends('Layout.default')

@section('content')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/">{{__('layout.index_title') }}</a></li>
        <li class="breadcrumb-item">{{__('layout.management') }}</li>
        <li class="breadcrumb-item active" aria-current="page">{{__('layout.classes') }}</li>
    </ol>
    <div v-show="!isNew">
        <table id ="table1"  class="table table-striped table-hover">
            <thead>
            <tr>
                <th >項目</th>
                <th >分類名稱</th>
                <th >公司ID</th>
                <th >選項ID</th>
                <th >日期</th>
                <th > </th>
            </tr>

            </thead>

            <tbody>
            @foreach ($classes as $class)
                <tr>
                    <td> {{$loop->index +1}} </td>
                    <td> {{$class->class_name}} </td>
                    <td> {{$class->cp_id}} </td>
                    <td> {{$class->class_option}} </td>
                    <td> {{$class->updated_at}} </td>
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
            <!--<tr v-for="(class, index) in classList">
                <td>@{{index+1}}</td>
                <td>
                    <input  type="text" v-model="class.class_name" :disabled="editPoint!=index">
                </td>
                <td>
                    <input  type="text" v-model="class.cp_id" :disabled="editPoint!=index">
                </td>
                <td>
                    <input  type="text" v-model="class.option_id" :disabled="editPoint!=index">
                </td>

                <td>@{{class.created_at}}</td>
                <td>
                    <button v-if="editPoint!=index" type="button" name="edit" class="btn btn-primary btn-sm" @click="editCheck(index)">
                        編輯
                    </button>
                    <button v-else type="button" name="edit" class="btn btn-success btn-sm" @click="saveEdit(index)">
                        儲存
                    </button>
                    <button type="button" name="del" class="btn btn-danger btn-sm" @click="delCheck(index, user.userName)">
                        刪除
                    </button>
                </td>
            </tr>-->
            </tbody>
        </table>
    </div>

@endsection

@section('footerScripts')
    <script>
        let classes = {!! $classes !!};
        let classOptions = {!! $classOptions !!};
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('classes/cps.js')}}"></script>
@endsection



