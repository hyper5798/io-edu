@extends('Layout.diy')

@section('css')
    <link href="https://unpkg.com/@wangeditor/editor@latest/dist/css/style.css" rel="stylesheet">
    <style>
        #content { height: 500px; width: 100%; }
    </style>
@endsection

@section('content')

    <div class="row breadcrumb">
        <div class="col-md-9">
            <ol class="breadcrumb">
                <!--<li class="breadcrumb-item"><a href="/backend">後台儀表板</a></li>-->
                <li class="breadcrumb-item"><a href="/admin/chapter?category_id={{$category_id}}&course_id={{$course_id}}">課程單元</a></li>
                <li class="breadcrumb-item active" aria-current="page">更新單元</li>
            </ol>
        </div>

        <div  class="col-md-3 mt-1 text-left">

        </div>
    </div>

    <!-- Create Chapter -->
    <div class="row justify-content-center main-content">
        <div class="input-group mb-1 col-6 col-sm-6 col-md-4 col-lg-3">
            <div class="card mt-2">

                <form method="post" action="uploadImage" id="uploadImage" enctype="multipart/form-data">

                    {{csrf_field()}}
                    <div class="col-12">
                        <input name="progressbarTW_img" type="file" id="imgInp" accept="image/gif, image/jpeg, image/png" multiple class="mt-2 mb-2">
                    </div>
                </form>

                <button type="button">
                    @if($chapter->image_url)
                        <img id="preview_progressbarTW_img" src="{{$chapter->image_url}}"  style="width: 240px;height: 180px;">
                    @else
                        <img id="preview_progressbarTW_img" src="{{url('/Images/no_image.png')}}"  style="width: 240px;height: 180px;">
                    @endif

                </button>

                <div class="mt-2">
                    <span class="float-right">
                        <button type="button" class="btn btn-primary mb-4 mr-2" onclick="uploadImage();">
                            上傳
                        </button>
                    </span>
                </div>

            </div>
        </div>
        <div class="col-6 col-sm-6 col-md-8 col-lg-9">
            <div class="card shadow-lg border-0 rounded-lg mt-2">

                <div class="card-body">
                    <form method="post" id="chapter_update"action="{{route('admin.chapter.update',[$chapter->id])}}">
                        <input type="hidden" name="category_id" value="{{$category_id}}" />
                        <input type="hidden" name="course_id" value="{{$course_id}}" />
                        <input type="hidden" id="image_url" name="image_url" value="{{ $course->image_url }}">

                        {{csrf_field()}}
                        @method('put')
                        <div class="form-row">
                            <div class="input-group mb-1 col-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >課程</span>
                                </div>
                                <label class="form-control" >{{$course->title}}</label>

                            </div>
                            <div class="input-group mb-1 col-6 col-sm-6 col-md-4 col-lg-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >單元</span>
                                </div>
                                <input type="number" class="form-control" name="sort" value="{{$chapter->sort}}" min="1" max="20">

                            </div>
                            <div class="input-group mb-1 col-6 col-sm-6 col-md-8 col-lg-9">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >
                                        <span class="mr-3">
                                             影片
                                        </span>


                                    </span>
                                </div>
                                <select name="video_id" >
                                    @if(0 == $chapter->video_id)
                                        <option value="0" selected>不選擇</option>
                                    @else
                                        <option value="0" >不選擇</option>
                                    @endif
                                    @foreach($videos as $video)
                                        @if($video->id ==$chapter->video_id)
                                            <option value="{{$video->id}}" selected>{{$video->title}}</option>
                                        @else
                                            <option value="{{$video->id}}" >{{$video->title}}</option>
                                        @endif
                                    @endforeach

                                </select>
                                <a class="form-control" href="{{ url('admin/video/create?category_id='.$category_id.'&course_id='.$course_id.'&sort='.$chapter->sort) }}" class="btn btn-primary btn-sm">新增影片</a>

                            </div>
                            <div class="input-group mb-1 col-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >標題</span>
                                </div>
                                <input type="text" class="form-control" name="title" value="{{$chapter->title}}">
                            </div>


                            <!--<div class="input-group mb-1 col-6  col-sm-6 col-md-3 col-lg-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >是否上架</span>
                                </div>

                                <select name="isShow" class="form-control">
                                        <option value="0" selected>備課中</option>
                                        <option value="1">上架</option>
                                </select>
                            </div>-->
                            <div class="input-group mb-1 col-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >簡介</span>
                                </div>
                                <input type="text" class="form-control" name="content" value="{{$chapter->content}}">
                            </div>

                            <div class="input-group mb-1 col-sm-12 col-md-12 col-lg-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >圖片網址</span>
                                </div>
                                <input type="text" class="form-control"id="image_url2" name="image_url" value="{{$chapter->image_url}}" disabled>
                            </div>


                            <div class="col-md-12">
                                <button type="button" class="btn btn-secondary" onClick="location.href='{{url('/admin/chapter?category_id='.$category_id.'&course_id='.$course_id)}}';">{{__('layout.back')}}</button>
                                <button type="submit" class="btn btn-primary" onclick="toSubmit();">{{__('layout.submit')}}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footerScripts')

    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <<script src="https://unpkg.com/@wangeditor/editor@latest/dist/index.js"></script>

    <script>
        let api_url = '{!! env('API_URL') !!}';
        let token = '{!! $user->remember_token !!}';
        let course = {!! json_encode($course) !!};
        function back() {
            history.go(-1);
        }
        $(document).ready(function() {
            $("#imgInp").change(function(){
                //當檔案改變後，做一些事
                readURL(this);   // this代表<input id="imgInp">
                document.getElementById('image_url').value = '';
                document.getElementById('image_url2').value = '';
            });
        } );
        let upload_url = null;
        let file_name = null;

        function readURL(input){
            if(input.files && input.files[0]){
                let reader = new FileReader();
                reader.onload = function (e) {
                    upload_url  = e.target.result;
                    $("#preview_progressbarTW_img").attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);

            }
        }

        function getFileName() {
            var fullPath = document.getElementById('imgInp').value;
            if (fullPath) {
                var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
                var name = fullPath.substring(startIndex);
                if (name.indexOf('\\') === 0 || name.indexOf('/') === 0) {
                    name = name.substring(1);
                }
                //alert(filename);
                return name;
            }
        }

        function uploadImage() {
            file_name = getFileName();
            let url = api_url+'/api/upload-chapter-image';
            //let data = {img: upload_url, option: ('c'+course.category_id+'/o'+course.id) , token:token, file_name:file_name, XDEBUG_SESSION_START:'PHPSTORM'};
            let data = {img: upload_url, category_id: course.category_id,course_id:course.id, token:token, file_name:file_name, XDEBUG_SESSION_START:'PHPSTORM'};
            sendToApi(url,data);
        }

        function sendToApi(url,data) {

            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                /*beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer '+data.token);
                },*/
                success: function (result) {
                    console.log(result);
                    document.getElementById('image_url').value = result.file;
                    document.getElementById('image_url2').value = result.file;
                },
                error:function(err){
                    console.log(err);
                },
            });
        }

        function toSubmit(){
            let value = document.getElementById('image_url').value;
            if(value === '') {
                return alert('尚未上傳圖片!')
            }
            value = document.getElementById('title').value;
            if(value === '') {
                return alert('尚未填寫標題!')
            }
            document.getElementById('chapter_update').submit();
        }
    </script>
@endsection
