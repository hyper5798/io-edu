@extends('Layout.diy')
@inject('VideoPresenter', 'App\Presenters\VideoPresenter')

@section('content')

    <div class="row breadcrumb">
        <div class="col-6 col-sm-6 col-md-3 col-lg-2">
            <ol class="breadcrumb">
                <!--<li class="breadcrumb-item"><a href="/backend">後台儀表板</a></li>-->
            <!--<li class="breadcrumb-item">{{__('layout.management') }}</li> -->
                <li class="breadcrumb-item active" aria-current="page">{{__('layout.upload_video') }}</li>
            </ol>
        </div>
        <div class="col-6 col-sm-6 col-md-3 col-lg-3 mt-1 text-left">
            <span v-show="!isNew">
                 類型:
                <select onchange="location.href='?category_id='+this.options[this.selectedIndex].value" class="mr-5">

                    @foreach ($categories as $category)
                        @if ($category->id == $category_id)
                            <option value="{{$category->id}}" selected="selected">{{$category->title}}</option>
                        @else
                            <option value="{{$category->id}}">{{$category->title}}</option>
                        @endif

                    @endforeach

                </select>

            </span>
        </div>
        <div class="col-12 col-sm-12 col-md-4 col-lg-4 mt-1 text-left">
            課程:
            <select onchange="location.href='?course_id='+this.options[this.selectedIndex].value+'&category_id={{$category_id}}' ">

                @foreach ($courses as $course)
                    @if ($course->id == $course_id)
                        <option value="{{$course->id}}" selected="selected">{{$course->title}}</option>
                    @else
                        <option value="{{$course->id}}">{{$course->title}}</option>
                    @endif

                @endforeach

            </select>
        </div>
        <div class="col-md-2 text-right">
            <button v-show="!isNew" type="button" class="btn btn-success btn-sm text-right" @click="newCheck()">{{__('layout.add')}}</button>
        </div>
    </div>
    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">

            <h4>{{$errors->first()}}</h4>

        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif


    <div v-cloak v-show="!isNew" class="main-content">
        <table id ="table1"  class="table table-striped table-hover">
            <thead>
            <tr>
                <!--<th >{{__('layout.item')}}</th>-->
                <th width="10%">單元</th>
                <th >{{__('video.name')}}</th>
                <th >影片</th>
                <th >播放時間</th>

            <!--<th >{{__('video.content')}}</th>-->
            <!--<th >{{__('layout.update_at')}}</th>-->
                <th > </th>
            </tr>

            </thead>

            <tbody>
            @foreach ($videos as $video)
                <tr>
                    <!--<td> {{$loop->index +1}} </td>-->
                    <td>
                        @if($course->isShow==1)
                            <span class="btn btn-success btn-block btn-sm">{{$video->sort}} </span>
                        @elseif($course->isShow==2)
                            <span class="btn btn-warning btn-block btn-sm">{{$video->sort}} </span>
                        @else
                            <span class="btn btn-secondary btn-block btn-sm">{{$video->sort}} </span>
                        @endif
                    </td>
                    <td> {{$video->title}} </td>
                    <td>
                        <video controls width="150" height="100">
                            <source src="{{$video->video_url}}" type="video/mp4" />
                        </video>

                    </td>
                    <td> {{$VideoPresenter->convert($video->duration)}} </td>
                <!--<td> {{$video->content}} </td>-->
                <!--<td> {{$video->updated_at}} </td>-->
                    <td>
                        <button @v-if="editPoint!={!! $loop->index !!}" type="button" name="edit" class="btn btn-primary btn-sm" @click="editCheck({!! $loop->index !!})">
                            {{__('layout.edit')}}
                        </button>
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
    <!-- Edit Role-->
    <div v-show="isNew" class="row justify-content-center main-content">
        <div class="card-body">
            <!-- Edit -->

            <div v-cloak class="row justify-content-center">
                <!-- upload video -->
                <div class="col-sm-12 col-md-12 col-lg-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <form method="post" id="uploadVideo" action="uploadVideo" id="uploadVideo" enctype="multipart/form-data">
                                {{csrf_field()}}

                                    <input type="hidden" name="id" v-model="video.id" />
                                    <input type="hidden" name="category_id" value="{{$category_id}}" />
                                    <input type="hidden" name="course_id" value="{{$course_id}}" />
                                    <input type="hidden" v-model="video.title" name="title">
                                    <input type="hidden" v-model="video.sort" name="sort">
                                    <input type="hidden" v-model="video.duration" name="duration">
                                    <input type="hidden" class="form-control"  v-model="video.video_name" name="video_name">
                                    <input type="hidden" v-model="video.video_url" name="video_url">
                                    <input type="file" id="uploadVideoFile" name="file" onChange="fileChange()"/>


                            </form>
                            <div v-if="isNeedUpload==true" class="d-flex flex-column align-items-center text-center">
                                <div id="videoSourceWrapper">
                                        <video id="upload" class="mt-2" style="width: 100%;" controls>
                                            <source id="videoSource"/>
                                        </video>
                                        <span v-if="video.video_name.length==0" class="text-danger">
                                            請先選擇影片檔案
                                        </span>
                                        <!--<span class="float-right">
                                            <input class="btn btn-primary" name="submit" type="submit" value="上傳" @click="toUpload()"/>
                                        </span>-->
                                </div>

                            </div>
                            <div v-else>
                                <div class="mb-1">
                                    <span>@{{ video.video_name }}</span>
                                    <span class="float-right">
                                        <button type="button" class="btn btn-primary btn-sm" @click="updateVideo();">更新</button>
                                    </span>
                                </div>
                                <div class="mt-3">
                                    <!--<video id="movie" autoplay controls width="250" height="150">-->
                                    <video id="movie" controls width="250" height="150">
                                        <source :src="video.video_url" type="video/mp4" />
                                    </video>
                                </div>

                            </div>
                        </div>
                    </div>


                </div>

                <div class="col-sm-12 col-md-12 col-lg-8">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="input-group mb-3 col-md-6">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">課程</span>
                                    </div>
                                    @foreach ($courses as $course)
                                        @if ($course->id == $course_id)
                                            <input type="text" class="form-control"  value="{{$course->title}}" size="10" disabled>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="input-group mb-3 col-md-6">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">課程分類</span>
                                    </div>
                                    @foreach ($categories as $category)
                                        @if ($category->id == $category_id)
                                            <input type="text" class="form-control"  value="{{$category->title}}" size="10" disabled>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="input-group mb-3 col-4 col-sm-3 col-md-4 col-lg-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">單元</span>
                                    </div>
                                    <input type="number" class="form-control"  v-model="video.sort"  min="1" max="20">
                                </div>

                                <div class="input-group mb-3 col-8 col-sm-9 col-md-8 col-lg-9">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{__('video.name') }}</span>
                                    </div>
                                    <input type="text" class="form-control"  v-model="video.video_name" name="video_name" size="50" disabled>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-sm-4">
                                    <p class="profile-title">{{__('video.title') }}</p>
                                </div>
                                <div v-cloak class="col-sm-8 text-secondary">

                                    <input type="text" v-model="video.title" name="title"  size="50">
                                </div>


                            </div>
                        <!-- <hr>
                               <div class="row">
                                    <div class="col-sm-4">
                                        <p class="profile-title">{{__('video.content') }}</p>
                                    </div>
                                    <div v-cloak class="col-sm-8 text-secondary">
                                        <textarea  v-model="video.content" name="content" rows="4" cols="50"></textarea>
                                    </div>

                                </div>-->
                            <hr>
                        <!--<div class="row">
                                    <div class="col-sm-4">
                                        <p class="profile-title">{{__('video.storage_path') }}</p>
                                    </div>
                                    <div v-cloak class="col-sm-8 text-secondary">
                                        <input type="text" v-model="video.storage_path" name="storage_path" size="50">
                                    </div>
                                </div>
                                <hr>-->
                            <div v-if="video.id>0" class="row">
                                <div class="col-sm-4">
                                    <p class="profile-title">{{__('video.video_url') }}</p>
                                </div>
                                <div  class="col-sm-8 text-secondary">
                                    <input type="text" v-model="video.video_url" name="video_url" size="50" disabled>
                                </div>
                            </div>

                            <div class ="row">
                                <hr>
                                <div v-cloak class="col-sm-12 text-secondary">
                                        <span class="float-right mr-3">
                                            <button type="button" class="btn btn-secondary" @click="back()">{{__('layout.back')}}</button>
                                            <button type="button" class="btn btn-primary" @click="toUpload()">{{__('layout.submit')}}</button>
                                        </span>
                                </div>
                            </div>
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
                    {{__('layout.delete_confirm')}} @{{video.video_name}} ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delVideo" id="delVideo">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="video.id" />
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
        let video_id = {!! $video_id !!};
        let videos = {!! $videos !!};
        let category_id = {!! $category_id !!};
        let course_id = {!! $course_id !!};
        let user = {!! $user !!};
    </script>

    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/admin/videos.js')}}"></script>
@endsection
