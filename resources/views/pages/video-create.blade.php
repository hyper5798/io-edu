@extends('Layout.diy')
@inject('VideoPresenter', 'App\Presenters\VideoPresenter')

@section('content')
    <div class="row breadcrumb">
        <div class="col-md-12">
            <ol class="breadcrumb">
                <!--<li class="breadcrumb-item"><a href="/backend">後台儀表板</a></li>-->
                <li class="breadcrumb-item"><a href="/admin/chapter?category_id={{$category_id}}&course_id={{$course_id}}">課程單元</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{__('layout.upload_video') }}</li>
            </ol>
        </div>

    </div>

    <div class="row justify-content-center main-content">
        <div class="card-body">
            <!-- Edit -->

            <div v-cloak class="row justify-content-center">
                <!-- upload video -->
                <div class="col-sm-12 col-md-12 col-lg-4 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <form method="post" id="uploadVideo" action="{{url('/admin/uploadVideo')}}" id="uploadVideo" enctype="multipart/form-data">
                                {{csrf_field()}}

                                <input type="hidden" name="id" v-model="video.id" />
                                <input type="hidden" name="category_id" value="{{$category_id}}" />
                                <input type="hidden" name="course_id" value="{{$course_id}}" />
                                <input type="hidden" v-model="video.title" name="title">
                                <input type="hidden" v-model="video.sort" name="sort">
                                <input type="hidden" v-model="video.duration" name="duration">
                                <input type="hidden" value="chapter" name="from">
                                <input type="hidden" class="form-control"  v-model="video.video_name" name="video_name">
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
                                        <button type="button" class="btn btn-primary btn-sm" @click="isNeedUpload = true;">更新</button>
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

                                    <input type="text" class="form-control"  value="{{$course->title}}" size="10" disabled>

                                </div>

                                <div class="input-group mb-3 col-md-6">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">課程分類</span>
                                    </div>
                                    <input type="text" class="form-control"  value="{{$category->title}}" size="10" disabled>
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

                            <hr>


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


@endsection

@section('footerScripts')
    <script>
        let sort = {!! $sort !!};
        let category_id = {!! $category_id !!};
        let course_id = {!! $course_id !!};
    </script>

    <script src="{{asset('js/admin/video-create.js')}}"></script>
@endsection
