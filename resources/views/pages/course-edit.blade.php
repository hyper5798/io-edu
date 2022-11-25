@extends('Layout.diy')

@section('css')

@endsection

@section('content')

    <div class="row breadcrumb">
        <div class="col-md-9">
            <ol class="breadcrumb">
                <!--<li class="breadcrumb-item"><a href="/backend">後台儀表板</a></li>-->
                <li class="breadcrumb-item"><a href="/admin/courses?category_id={{$course->category_id}}">編輯課程</a></li>
                <li class="breadcrumb-item active" aria-current="page">更新課程</li>
            </ol>
        </div>

        <div  class="col-md-3 mt-1 text-left">

        </div>
    </div>

    <!-- Create question-->
    <div v-show="isNew" class="row justify-content-center main-content">
        <div class="col-lg-12">
            <div class="card shadow-lg border-0 rounded-lg mt-2">
                <form method="post" action="uploadImage" id="uploadImage" enctype="multipart/form-data">

                    {{csrf_field()}}
                    <div class="col-12">
                        <input name="progressbarTW_img" type="file" id="imgInp" accept="image/gif, image/jpeg, image/png" multiple>
                    </div>
                </form>
                <form method="post"  id="course_update" action="{{route('admin.course.update',[$course->id])}}">
                    <input type="hidden" id="image_url" name="image_url" value="{{ $course->image_url }}">
                    {{csrf_field()}}
                    @method('put')
                    <div class="card-body">

                        <div class="form-row">
                            <div class="input-group mb-1 col-6 col-sm-6 col-md-4 col-lg-3">
                                <button type="button">
                                    @if($course->image_url)
                                        <img id="preview_progressbarTW_img" src="{{$course->image_url}}"  style="width: 240px;height: 180px;">
                                    @else
                                        <img id="preview_progressbarTW_img" src="{{url('/Images/no_image.png')}}"  style="width: 240px;height: 180px;">
                                    @endif
                                </button>

                                <div class="col-12 mt-2">
                                    <span class="float-right">
                                        <button type="button" class="btn btn-primary" onclick="uploadImage();">
                                            上傳
                                        </button>
                                    </span>
                                </div>


                            </div>


                            <div class="input-group mb-1 col-6 col-sm-6 col-md-8 col-lg-9">
                                <div class="container">
                                    <div class="row">
                                        <div class="input-group mb-1 col-12 col-sm-12 col-md-12 col-lg-12">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-default" >課程類型</span>
                                            </div>

                                            <select class="form-control" name="category_id">

                                                @foreach($categories as $category)
                                                    @if($course->category_id == $category->id)
                                                        <option value="{{$category->id}}" selected>{{$category->title}}</option>
                                                    @else
                                                        <option value="{{$category->id}}">{{$category->title}}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="input-group mb-1 col-6 col-sm-6 col-md-6 col-lg-6">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-default" >最大免費單元</span>
                                            </div>

                                            <input type="text" class="form-control" name="freeChapterMax" value="{{$course->freeChapterMax}}">
                                        </div>
                                        <div class="input-group mb-1 col-6  col-sm-6 col-md-6 col-lg-6">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-default" >是否上架</span>
                                                <select name="isShow" class="form-control">
                                                    @if($course->isShow == 0)
                                                        <option value="0" selected>備課中</option>
                                                    @else
                                                        <option value="0">備課中</option>
                                                    @endif
                                                    @if($course->isShow == 1)
                                                        <option value="1" selected>上架</option>
                                                    @else
                                                        <option value="1">上架</option>
                                                    @endif

                                                </select>
                                            </div>
                                        </div>
                                        <div class="input-group mb-1 col-sm-12 col-md-12 col-lg-12">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-default" >標題</span>
                                            </div>
                                            <input type="text" class="form-control" id="title" name="title" value="{{ $course->title }}">
                                        </div>

                                        <div class="input-group mb-1 col-sm-12 col-md-12 col-lg-12">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-default" >簡介</span>
                                            </div>
                                            <input type="text" class="form-control" name="content_small" value="{{ $course->content_small }}">
                                        </div>

                                        <div class="input-group mb-1 col-sm-12 col-md-12 col-lg-12">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="inputGroup-sizing-default" >圖片網址</span>
                                            </div>
                                            <input type="text" class="form-control" id="image_url2" name="image_url" value="{{ $course->image_url }}" disabled>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="input-group mb-1 col-12 col-sm-12 col-md-12 col-lg-12">
                                <textarea id="content" name="content" style="width: 100%;height: 500px;">{{ $course->content }}</textarea>
                            </div>

                            <div class="col-md-12">
                                <button type="button" class="btn btn-secondary" onClick="location.href='{{url('/admin/courses?category_id='.$course->category_id)}}';">{{__('layout.back')}}</button>
                                <button type="button" class="btn btn-primary" onclick="toSubmit();">{{__('layout.submit')}}</button>
                            </div>

                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('footerScripts')

    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <<script src="https://unpkg.com/@wangeditor/editor@latest/dist/index.js"></script>
    <script src="https://cdn.ckeditor.com/4.19.1/standard-all/ckeditor.js"></script>
    <!--<script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>-->
    <script>
        function back() {
            history.go(-1);
        }
        let category_id = '{!! $course->category_id !!}';
        let api_url = '{!! env('API_URL') !!}';
        let token = '{!! $user->remember_token !!}';
        let ytUrl = '{!! url('/vender/CKEditor/youtube/plugin.js') !!}';
        let panelbuttonUrl = '{!! url('/vender/CKEditor/panelbutton/plugin.js') !!}';
        let colorbuttonUrl = '{!! url('/vender/CKEditor/colorbutton/plugin.js') !!}';
        let imageUrl = '{!! url('/vender/CKEditor/filebrowser/plugin.js') !!}';
        let uploadUrl = '{!! url('/api/upload-image?type=multipart/form-data&_token=') !!}' + $('meta[name=csrf-token]').attr("content");
        CKEDITOR.plugins.addExternal( 'youtube', ytUrl, '' );
        CKEDITOR.plugins.addExternal( 'uploadimage', imageUrl, '' );
        CKEDITOR.plugins.addExternal( 'panelbutton', panelbuttonUrl, '' );
        CKEDITOR.plugins.addExternal( 'colorbutton', colorbuttonUrl, '' );
        CKEDITOR.on( 'fileUploadResponse', function( evt ) {
            console.log(evt);
        });

        //CKEDITOR.plugins.addExternal("codesnippet", "https://cdnjs.cloudflare.com/ajax/libs/ckeditor/4.7.3/plugins/codesnippet/plugin.js", "");
        CKEDITOR.replace("content", {
            extraPlugins: "youtube,uploadimage, panelbutton, colorbutton",
            filebrowserUploadUrl: uploadUrl,
            language: 'zh-TW',
            width: '100%',
            height: '500px',
            overflow: 'auto',
            csrfProtection: false,
            //enterMode: CKEDITOR.ENTER_BR,
            //removeButtons: "Image,Scayt,PasteText,PasteFromWord,Outdent,Indent",
            //uiColor: '#3c75e5'
            //codeSnippet_theme: "solarized_dark"
        });

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
            let url = api_url+'/api/upload-course-image';
            let data = {img: upload_url, category_id: category_id , token:token, file_name:file_name, XDEBUG_SESSION_START:'PHPSTORM'};
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
            document.getElementById('course_update').submit();
        }

    </script>
@endsection
