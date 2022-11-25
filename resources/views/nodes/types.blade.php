@extends('Layout.default')

@section('content')
    <div class="row breadcrumb">
        <div class="col-md-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/">{{__('layout.index_title') }}</a></li>
                <li class="breadcrumb-item">{{__('layout.devices') }}</li>
                <li class="breadcrumb-item active" aria-current="page">{{__('layout.types') }}</li>
            </ol>
        </div>
        <div class="col-md-6 text-center">
        </div>
        <div class="col-md-3 text-right">
            <button type="button" class="btn btn-success text-right" @click="newTYpe">{{__('layout.add')}}</button>
        </div>
    </div>
    @if (count($errors) > 0)
        <div class="alert alert-danger" id="message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session('message'))
        <div class="alert alert-danger" id="message">
            {{ session('message') }}
        </div>
    @endif

    <div v-show="!isNew" class="main-content">
        <table id ="table1"  class="table table-striped table-hover">
            <thead>
            <tr>
                <th >{{__('layout.item')}}</th>
                <th >{{__('layout.type_id')}}</th>
                <th >{{__('layout.types')}}別名</th>
                <th >{{__('layout.description')}}</th>
                <th> 控制器分類</th>
                <th >{{__('layout.update_at')}}</th>
                <th width="15%"> </th>
            </tr>

            </thead>

            <tbody v-if="typeList.length>0">
            @foreach ($types as $type)
                <tr>
                    <td> {{$loop->index +1}} </td>
                    <td> {{$type->type_id}} </td>

                    <td> {{$type->type_name}} </td>
                    <td> {{$type->description}} </td>
                    <td>
                        @if($type->category == 0)
                            控制型控制器
                        @elseif($type->category == 1)
                            輸入型控制器
                        @elseif($type->category == 2)
                            輸出型控制器
                        @elseif($type->category == 3)
                            輸入型上報控制器
                        @elseif($type->category == 4)
                            All-IN_ONE模組控制器
                        @endif
                    </td>
                    <td> {{$type->updated_at}} </td>
                    <td>
                        <button @v-if="editPoint!={!! $loop->index !!}" type="button" name="edit" class="btn btn-primary btn-sm" @click="editType({!! $loop->index !!})">
                            {{__('layout.edit')}}
                        </button>
                        <button type="button" name="del" class="btn btn-danger btn-sm" @click="delType({{$loop->index}})">
                            {{__('layout.delete')}}
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Edit type-->
    <div v-show="isNew" class="row justify-content-center main-content">
        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-lg mt-2">
                <div class="card-header">
                    <!-- edit type data -->
                    <h3 v-if="type.id>0" class="text-center font-weight-light my-4">
                        {{__('layout.edit')}}{{__('layout.types')}}
                    </h3>
                    <!-- Add type data -->
                    <h3 v-else class="text-center font-weight-light my-4">
                        {{__('layout.add')}}{{__('layout.types')}}
                    </h3>
                </div>
                <div class="card-body">
                        <form method="post" id="editForm">
                            <input type="hidden" name="_method" value="put" />
                            <input type="hidden" name="mytype" v-model="typeString" />
                            <input type="hidden" name="fields" v-model="type.fields" />
                            <input type="hidden" name="rules" v-model="type.rules" />
                            <input type="hidden" name="type_id" v-model="type.type_id" />
                            <input type="hidden" name="type_name" v-model="type.type_name" />
                            <input type="hidden" name="type_id" v-model="type.type_id" />
                            <input type="hidden" name="type_name" v-model="type.type_name" />
                            <input type="hidden" name="category" v-model="type.category" />
                            <input type="hidden" name="work" v-model="type.work" />
                            {{csrf_field()}}
                        </form>
                        <div class="form-row">
                            <!-- type category -->
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >分類</span>
                                </div>
                                <select v-cloak v-model="type.category" class="form-control">
                                    <option v-for="category in categoryList" :value="category.id" >
                                        @{{ category.value }}
                                    </option>
                                </select>
                            </div>
                            <!-- type ID -->
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >{{__('layout.type_id')}}</span>
                                </div>
                                <input type="number" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="type.type_id" name="type_id" @change="chanheItem" @focus="isError=false">
                            </div>
                            <!-- type name -->
                            <div class="input-group mb-3 col-md-6">

                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">{{__('layout.types')}}暱稱</span>
                                </div>
                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="type.type_name" name="type_name" @change="chanheItem" @focus="isError=false">
                            </div>
                            <!-- type work -->
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >工作</span>
                                </div>
                                <select v-model="type.work" class="form-control">

                                    <option v-for="(item, key) in setting"  :value="key" >
                                        @{{ item }}
                                    </option>
                                </select>

                            </div>
                            <!-- type description -->
                            <div class="input-group mb-3 col-md-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >{{__('layout.description')}}</span>
                                </div>
                                <!-- Show device MAC-->
                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="type.description" >

                            </div>
                        </div>
                        <div class="row text-left">
                            <div class="col-md-6">

                                    <!--選擇 --> <!--名稱-->
                                    <h5>{{__('layout.select')}} {{__('layout.name')}}</h5>

                                <div v-for = "(item, index) in fieldList" class="form-row">
                                    <div class="input-group mb-1 col-md-2">
                                        <input type="checkbox" class="form-control" v-model="fieldList[index].check">
                                    </div>
                                    <div class="input-group mb-1 col-md-8">

                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="inputGroup-sizing-default" >key@{{ index+1 }}</span>
                                        </div>
                                        <input type="text" class="form-control"  v-model="item.key" :disabled="!item.check">
                                        <!--<input type="checkbox" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default">-->
                                    </div>
                                    <!-- parsing rule
                                    <div v-if="isParse" class="input-group mb-1 col-md-7">

                                        <span v-for = "(target, index) in item.parse">
                                            <input v-if="index==0" type="text" class="form-control" v-model="item.parse[index]"  :disabled="!item.check" size="2" maxlength="2">
                                            <input v-else-if="index==1" type="text" class="form-control" v-model="item.parse[index]"  :disabled="!item.check" size="2" maxlength="2">
                                            <input v-else type="text" class="form-control"  v-model="item.parse[index]" :disabled="!item.check"  size="20" minlength="20">
                                        </span>
                                    </div>-->


                                </div>
                            </div>
                            <div v-show="type.id>0" class="col-md-6">
                                <h5>裝置類型圖片</h5>
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex flex-column align-items-center text-center">

                                            <img id="type_img" :src="type.image_url"  width="300" height="240">

                                        </div>
                                        <form method="post" action="uploadTypeImage" id="uploadTypeImage" enctype="multipart/form-data">
                                            {{csrf_field()}}
                                            <input type="hidden" name="id" v-model="type.id" />

                                            <div class="form-row mt-2">
                                                <div class="col-8">
                                                    <input name="type_img" type="file" id="imgInp" accept="image/gif, image/jpeg, image/png" onchange="changeImage(event);" />
                                                </div>
                                                <div class="col-4">
                                                <span class="float-right">
                                                    <button type="button" class="btn btn-primary" @click="toUpload()">上傳</button>
                                                </span>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            </div>



                        </div>


                        <div class="col-md-12 mt-2">
                            <button type="button" class="btn btn-secondary" @click="back()">{{__('layout.back')}}</button>
                            <button type="button" class="btn btn-primary" @click="toSubmit()">{{__('layout.submit')}}</button>
                        </div>



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
                    {{__('layout.delete_confirm')}} @{{type.type_name}} ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" id="delForm">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="type.id" />
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
        let setting = {!! json_encode($setting) !!};
        let types = {!! $types !!};
        let url = '{{ URL::asset('/Images/script_background.png') }}';
        function disableMsg() {
            let msg = document.getElementById("message");
            //console.log(msg)
            if(msg !== null)
                document.getElementById("message").remove();
        }
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/node/types.js')}}" crossorigin="anonymous"></script>
@endsection



