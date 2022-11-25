@extends('Layout.default')

@section('content')
    <div class="row breadcrumb">
        <div class="col-md-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/backend">後台儀表板</a></li>
                <li class="breadcrumb-item active" aria-current="page">課程分類</li>
            </ol>
        </div>
        <div class="col-md-6 mt-1 text-left">

        </div>
        <div class="col-md-3 text-right mt-1">
            <button type="button" class="btn btn-success btn-sm text-right" @click="newCheck()">{{__('layout.add')}}</button>
        </div>
    </div>

    @if (count($errors) > 0)
    <div class="alert alert-danger alert-dismissible fade show mt-3 mb-3" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div v-cloak v-show="!isNew" class="main-content">
        <table id ="table1"  class="table table-striped table-hover">
            <thead>
            <tr>
                <th >{{__('layout.item')}}</th>
                <th > 分類名稱</th>
                <!--<th > 分類標籤</th>-->
                <th >{{__('layout.update_at')}}</th>
                <th > </th>
            </tr>

            </thead>

            <tbody>
            @foreach ($categories as $item)
                <tr>
                    <td> {{$loop->index +1}} </td>
                    <td> {{$item->title}} </td>
                    <!--<td> {{$item->tag}} </td>-->
                    <td> {{$item->updated_at}} </td>
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
    <!-- Edit Category-->
    <div v-show="isNew" class="row justify-content-center main-content">
        <div class="card-body">
            <!-- Edit -->

            <div v-cloak class="row justify-content-center">
                <div class="col-sm-12 col-md-12 col-xl-8">
                    <div class="card mb-3">
                        <div class="card-body">
                            <form method="post" action="editCategory" id="editCategory">
                                <input type="hidden" name="_method" value="put" />
                                <input type="hidden" name="id" v-model="category.id" />
                                <input type="hidden" name="tag" v-model="category.tag" />
                                {{csrf_field()}}
                                <div class="form-row">

                                    <!--Category title -->
                                    <div class="input-group mb-3 col-md-12">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">分類名稱</span>
                                        </div>
                                        <input type="text" class="form-control"  v-model="category.title" name="title">
                                    </div>

                                    <!--<div class="input-group mb-3 col-md-12">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">分類標籤</span>
                                        </div>
                                        <input type="text" class="form-control"  v-model="category.tag" name="tag" placeholder="影片上傳資料夾標籤，如更換會影響原本上傳的影片">
                                    </div>-->



                                    <div class="col-md-12 mt-2">
                                        <button type="button" class="btn btn-secondary" @click="back()">{{__('layout.back')}}</button>
                                        <button id="step5" type="button" class="btn btn-primary" @click="toSubmit()">{{__('layout.submit')}}</button>
                                        <!--<label class="ml-3 text-danger">分類標籤: 影片上傳資料夾標籤，如更換會影響原本上傳的影片</label>-->
                                    </div>
                                </diV>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" video="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" video="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">
                        警告! 刪除此分類會一併刪除相關的課程及影片,刪除前請謹慎考慮
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('layout.delete_confirm')}} @{{category.title}} ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delCategory" id="delCategory">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="category.id" />
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
        let categories = {!! $categories !!};
        let user = {!! $user !!};
    </script>

    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/admin/categories.js')}}"></script>
@endsection
