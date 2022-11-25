@extends('Layout.diy')
@inject('VideoPresenter', 'App\Presenters\VideoPresenter')

@section('content')
    <div class="row breadcrumb">
        <div class="col-lg-9 col-md-9 col-sm-9 col-9">
            <ol class="breadcrumb">
                <!--<li class="breadcrumb-item"><a href="/backend">後台儀表板</a></li>-->
                <li class="breadcrumb-item" aria-current="page"><a href="/admin/courses?category_id={{$category_id}}&course_id={{$course_id}}">編輯課程</a></li>
                <li class="breadcrumb-item active" aria-current="page">課程單元
            </ol>
        </div>


        @if($course_id >0)
        <div class="col-lg-3 col-md-3 col-sm-3 col-3">
            <button type="button" class="btn btn-success btn-sm float-right" onclick="create()">{{__('layout.add')}}</button>
        </div>
        @endif

        <div  class="col-lg-6 col-md-6 col-12 col-md-12 mb-1 pl-5">
            選擇課程類型
            <select onchange="location.href='?category_id='+this.options[this.selectedIndex].value" class="mr-5">
                @if($category_id==0)
                    <option value="0" selected>未選擇</option>
                @else
                    <option value="0">未選擇</option>
                @endif
                @foreach ($categories as $category)
                    @if ($category->id == $category_id)
                        <option value="{{$category->id}}" selected="selected">{{$category->title}}</option>
                    @else
                        <option value="{{$category->id}}">{{$category->title}}</option>
                    @endif

                @endforeach

            </select>
        </div>
        <div  class="col-lg-6 col-md-6 col-6 col-md-6 mb-1 text-left">

                選擇課程
                <select onchange="location.href='?course_id='+this.options[this.selectedIndex].value+'&category_id={{$category_id}}' ">
                    @if($course_id==0)
                        <option value="0" selected>未選擇</option>
                    @else
                        <option value="0">未選擇</option>
                    @endif
                    @foreach ($courses as $item)
                        @if ($item->id == $course_id)
                            <option value="{{$item->id}}" selected="selected">{{$item->title}}</option>
                        @else
                            <option value="{{$item->id}}">{{$item->title}}</option>
                        @endif

                    @endforeach

                </select>
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

    <div class="main-content">
        <table id ="table1"  class="table table-striped table-hover">
            <thead>
            <tr>
                <!--<th >{{__('layout.item')}}</th>-->
                <th width="6%"> 單元 </th>
                <th width="15%"> 圖片 </th>
                <th width="15%"> 影片 </th>
                <th width="35%"> 標題 </th>
                <th > 影片時間 </th>
                <!--<th >{{__('layout.update_at')}}</th>-->
                <th > </th>
            </tr>

            </thead>

            <tbody>
            @foreach ($chapters as $chapter)
                <tr>
                    <!--<td> {{$loop->index +1}} </td>-->
                    <td> {{$chapter->sort}} </td>
                    <td>
                        @if($chapter->image_url)
                            <img src="{{$chapter->image_url}}" width="150" height="100" >

                        @else

                            <img src="{{url('/Images/no_image.png')}}" width="150" height="100" >

                        @endif
                    </td>
                    <th >
                        @if($chapter->video)
                            <video controls width="150" height="100">
                                <source src="{{$chapter->video->video_url}}" type="video/mp4" />
                            </video>

                        @else

                            <img src="{{url('/Images/no-video.png')}}" width="150" height="100" >

                        @endif
                    </th>
                    <td> {{$chapter->title}} </td>
                    <!--<td> {{$chapter->updated_at}} </td>-->
                    <th >
                        @if($chapter->video)

                            {{$VideoPresenter->convert($chapter->video->duration)}}


                        @else



                        @endif
                    </th>
                    <td>
                        <a href="{{ route('admin.chapter.edit', [$chapter->id]) }}" class="btn btn-primary btn-sm">{{__('layout.edit')}}</a>
                        @if($course->isShow==0 || $user->role_id <3)
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
                    {{__('layout.delete_confirm')}} @{{chapter.title}} ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delChapter" id="delChapter">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="chapter.id" />
                        <input type="hidden" name="category_id" value="{{$category_id}}" />
                        <input type="hidden" name="course_id" value="{{$course_id}}" />
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
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>

    <script>
        let categories = {!! $categories !!};
        let courses = {!! $courses !!};
        let chapters = {!! $chapters !!};
        let category_id = {!! $category_id !!};
        let course_id = {!! $course_id !!};
        let user = {!! $user !!};

    </script>
    <script src="{{asset('js/admin/chapter.js')}}"></script>
@endsection
