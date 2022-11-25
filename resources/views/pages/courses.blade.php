@extends('Layout.diy')

@section('content')
    <div class="row breadcrumb">
        <div class="col-md-3">
            <ol class="breadcrumb">
                <!--<li class="breadcrumb-item"><a href="/backend">後台儀表板</a></li>-->
                <li class="breadcrumb-item active" aria-current="page">編輯課程</li>
            </ol>
        </div>
        <div  class="col-md-6 mt-1 text-left">
            <span v-show="!isNew">
                 選擇課程類型
                <select v-cloak v-model="course.category_id" @change="onChange($event)">
                    <option value="0">未選擇</option>
                    <option v-for="category in categoryList" :value="category.id">
                        @{{ category.title }}
                    </option>
                </select>
            </span>
        </div>
        <div class="col-md-3 text-right mt-1">
            <button type="button" class="btn btn-success btn-sm text-right" onclick="create()">{{__('layout.add')}}</button>
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
                <th > 課程圖片 </th>
                <th > 講師 </th>
                <th > 課程標題 </th>
                <th > 課程單元 </th>

                <th >{{__('layout.update_at')}}</th>
                <th > </th>
            </tr>

            </thead>

            <tbody>
            @foreach ($courses as $course)
                <tr>
                    <td>
                        @if($course->isShow==1)
                            <button type="button" class="btn btn-success btn-sm btn-block">
                                {{$loop->index +1}}
                            </button>
                        @elseif($course->isShow==2)
                            <button type="button" class="btn btn-warning btn-block">
                                {{$loop->index +1}}
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary btn-block">
                                {{$loop->index +1}}
                            </button>
                        @endif
                    </td>
                    <td> <img src="{{$course->image_url}}" style="width: 40px;height: 30px"> </td>
                    <td>
                        @if($course->user)
                            {{$course->user->name}}
                        @endif
                    </td>
                    <td> {{$course->title}} </td>
                    <th >
                        <span>{{count($course->chapters)}} </span>
                        <span class="ml-3">
                            <button @v-if="editPoint!={!! $loop->index !!}" type="button" name="edit" class="btn btn-info btn-sm" @click="toChapter({!! $loop->index !!})">
                                編輯單元
                            </button>
                        </span>
                    </th>
                    <td> {{$course->updated_at}} </td>
                    <td>


                        <a href="{{ route('admin.course.edit', [$course->id]) }}" class="btn btn-primary btn-sm">{{__('layout.edit')}}</a>
                        @if($course->isShow==0 || $user->role_id<3)
                        <button type="button" name="del" class="btn btn-danger btn-sm" @click="delCheck({{$loop->index}})">
                            {{__('layout.delete')}}
                        </button>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <!-- Edit Course-->
    <div v-show="isNew" class="row justify-content-center main-content">
        <div class="card-body">
            <!-- Edit -->

            <div v-cloak class="row justify-content-center">
                <div class="col-sm-12 col-md-12 col-xl-8">
                    <div class="card mb-3">
                        <div class="card-body">
                            <form method="post" action="editCourse" id="editCourse">
                                <input type="hidden" name="_method" value="put" />
                                <input type="hidden" name="id" v-model="course.id" />
                                <input type="hidden" name="description" v-model="course.description" />
                                <input type="hidden" name="is_show" v-model="course.is_show" />
                                <input type="hidden" name="category_id" v-model="course.category_id" />
                                {{csrf_field()}}
                                <div class="form-row">
                                    <!--Course category ID -->
                                    <div class="input-group mb-3 col-md-12">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">課程類型</span>
                                        </div>
                                        <select class="form-control" v-cloak v-model="course.category_id" @change="onChange($event)">
                                            <option value="0">未選擇</option>
                                            <option v-for="category in categoryList" :value="category.id">
                                                @{{ category.value }}
                                            </option>
                                        </select>

                                    </div>
                                    <!--Course title -->
                                    <div class="input-group mb-3 col-md-12">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">課程名稱</span>
                                        </div>
                                        <input type="text" class="form-control"  v-model="course.title" name="title">
                                    </div>

                                    <div class="col-md-12 mt-2">
                                        <button type="button" class="btn btn-secondary" @click="back()">{{__('layout.back')}}</button>
                                        <button id="step5" type="button" class="btn btn-primary" @click="toSubmit()">{{__('layout.submit')}}</button>
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
                    <h4 class="modal-title" id="myModalLabel">{{__('layout.waring')}}!</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('layout.delete_confirm')}} @{{course.title}} ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delCourse" id="delCourse">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="course.id" />
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
        let courses = {!! $courses !!};
        let category_id = {!! $category_id !!};
        let user = {!! $user !!};
        let free_chapter = {!! $free_chapter !!};
        function create() {
            let newUrl = "/admin/course/create?category_id="+category_id;
            document.location.href = newUrl;
        }
    </script>

    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/admin/courses.js')}}"></script>
@endsection
