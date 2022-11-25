@extends('Layout.default')

@section('content')
    <div class="row breadcrumb">
        <div class="col-md-3 mt-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/backend">{{__('layout.back_title')}}</a></li>
                <!--<li class="breadcrumb-item active" aria-current="page">{{__('layout.devices') }}</li>-->
                <li class="breadcrumb-item active" aria-current="page">{{__('product.products_manager') }}</li>
            </ol>
        </div>
        <div class="col-md-9 mt-2 text-left">
            {{__('layout.category') }}
            <select v-cloak v-model="category" @change="changeCategory()">
                <option v-for="item in categoryList" :value="item.value">
                    @{{ item.name }}
                </option>
            </select>
            {{__('layout.types') }}
            <select v-cloak v-model="type_id" @change="changeType()">
                <option v-for="item in typeList" :value="item.type_id">
                    @{{ item.type_name }}
                </option>
            </select>

        </div>
        <div class="input-group mb-1 col-md-4 justify-content-center">
            <div class="input-group-prepend ">
                <label class="input-group-text">輸入MAC</label>
            </div>

            <input type="text" class="form-control" v-model="targetProduct">
            <button class="btn btn-outline-primary" type="button" @click="searchProduct()"><i class="fas fa-search fa-fw"></i></button>
        </div>

        <div class="col-md-8 mb-1">
            <button type="button" class="btn btn-success float-right ml-2" @click="newCheck()">{{__('layout.add')}}</button>
            <button type="button" class="btn btn-info float-right" @click="importCheck()">{{__('layout.import_add')}}</button>

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

    <div v-show="isNew==0" class="main-content">
        <div id="bar_container" style="height: 200px"></div>
        <table id ="table1"  class="table table-striped table-hover">
            <thead>
            <tr>
                <th width="8%">{{__('layout.item')}}</th>
                <th width="10%">{{__('layout.type_id')}}</th>
                <th width="15%">{{__('device.device_mac')}}</th>
                <th >綁定人</th >
                <th >裝置名稱</th >
                <th width="15%">創建日期</th>
                <th width="10%">備註</th>
                <th width="15%"> </th>
            </tr>

            </thead>

            <tbody >
            @foreach ($products as $product)
                <tr>
                    <td> {{$loop->index +1}} </td>
                    <td> {{$product->type_id}} </td>
                    <td> {{$product->macAddr}} </td>

                    @if($product->device != null)
                        @if($product->device->user != null)
                            <td>{{$product->device->user->name}} </td>
                        @else
                            <td></td>
                        @endif
                        <td>{{$product->device->device_name}} </td>
                    @else
                        <td></td>
                        <td></td>
                    @endif

                    <td> {{$product->created_at}} </td>
                    <td> {{$product->description}} </td>
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
    <!-- Edit product-->
    <div v-cloak v-show="isNew==1" class="row justify-content-center main-content">

        <div class="col-lg-10">
            <div class="card shadow-lg border-0 rounded-lg mt-2">
                <div class="card-header">
                    <!-- edit company data -->
                    <h3 v-if="product.id>0" class="text-center font-weight-light my-4">
                        {{__('layout.edit')}}{{__('product.products')}}
                    </h3>
                    <!-- Add company data -->
                    <h3 v-else class="text-center font-weight-light my-4">
                        {{__('layout.add')}}{{__('product.products')}}
                    </h3>

                </div>
                <div class="card-body">
                    <form method="post" id="editForm">
                        <input type="hidden" name="_method" value="put" />
                        <input type="hidden" name="id" v-model="product.id" />
                        <input type="hidden" name="mac" v-model="product.macAddr"/>
                        <input type="hidden" name="type_id" v-model="product.type_id"/>
                        {{csrf_field()}}
                        <div class="form-row">
                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">{{__('device.device_mac')}}</span>
                                </div>
                                <input v-if="product.id>0" type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="product.macAddr" name="mac" disabled>
                                <input v-else type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="product.macAddr" name="mac">
                            </div>

                            <div class="input-group mb-3 col-md-6">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >{{__('layout.description')}}</span>
                                </div>
                                <input type="text" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-default" v-model="product.description" name="description">
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
    <!-- Import excel -->
    <div v-cloak v-show="isNew==2" class="row justify-content-center main-content">

        <div class="col-lg-12">
            <div class="card shadow-lg border-0 rounded-lg mt-2">
                <div class="card-header">
                    <!-- 匯入Excel新增 -->
                    <h5 class="text-center font-weight-light my-4">
                        {{__('layout.import_add')}}
                        <span class="text-info">請先選擇裝置類型</span>
                    </h5>

                </div>
                <div class="card-body">


                    <form action="import" method="post" id="import">
                        <input type="hidden" name="macs" v-model="macStr" />
                        <input type="hidden" name="type_id" value="{{$type_id}}" />
                        {{csrf_field()}}
                        <div class="form-row">
                            <div class="col-md-9 mt-1">
                                <input type="file" onchange="importf(this)" />
                            </div>
                            <div class="col-md-3 mt-1 text-left">
                                <div>
                                    <!-- 範例下載連結 -->
                                    <a href="{{asset('doc/import_products.xlsx')}}">{{__('layout.example_link')}}</a>
                                    <!-- 自行下載修改 -->

                                    <div>{{__('layout.download_by_yourself')}}</div>
                                </div>
                            </div>
                            <div class="col-md-9 mt-1 text-left">
                                <textarea v-model="macStr" name="macStr" rows="5" cols="100" style="overflow-y:scroll">
                                </textarea>
                            </div>
                            <div class="col-md-3 mt-1 text-left">
                                {{__('layout.format')}}:
                                <img src="{{url('/Images/import_products.png')}}" width="250px">
                            </div>

                            <div class="col-md-8 mt-3">
                                <span v-if="macStr.length<1" class="text-info">
                                    請先下載範例修改批次加入的註冊碼，然後點擊[選擇檔案]去選擇修改的Excel檔案。
                                </span>
                                <span v-else class="text-info">
                                    確認後點擊[提交]按鍵，進行批次新增控制器。
                                </span>
                                <span class="float-right">
                                    <button type="button" class="btn btn-secondary" @click="back()">{{__('layout.back')}}</button>
                                    <button type="button" class="btn btn-primary" @click="toImportSubmit()">{{__('layout.submit')}}</button>
                                </span>

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
                    {{__('layout.delete_confirm')}}@{{product.macAddr}}?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" id="delForm">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="product.id" />
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
        let types = {!! $types !!};
        let products = {!! $products !!};
        let typeId = {!! $type_id !!};
        let category = {!! $category !!};
        let api_url = '{!! env('API_URL') !!}';
        let token = '{{$user->remember_token}}';
        let isNew = {!! $isNew !!};
        let product_group = {!! $product_group !!};
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/js-xlsx/xlsx.full.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/echarts/echarts.min.js')}}" ></script>
    <script src="{{asset('js/node/products.js')}}"></script>
@endsection



