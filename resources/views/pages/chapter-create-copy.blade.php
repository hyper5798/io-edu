@extends('Layout.diy')

@section('css')

@endsection

@section('content')

    <div class="row breadcrumb">
        <div class="col-md-9">
            <ol class="breadcrumb">
                <!--<li class="breadcrumb-item"><a href="/backend">後台儀表板</a></li>-->
                <li class="breadcrumb-item"><a href="/admin/chapter?category_id={{$category_id}}&course_id={{$course_id}}">課程單元</a></li>
                <li class="breadcrumb-item active" aria-current="page">新增單元</li>
            </ol>
        </div>

        <div  class="col-md-3 mt-1 text-left">

        </div>
    </div>

    <!-- Create Chapter -->
    <div class="row justify-content-center main-content">
        <div class="col-lg-12">
            <div class="card shadow-lg border-0 rounded-lg mt-2">

                <div class="card-body">
                    <form method="post" action="/admin/chapter/store">
                        <input type="hidden" name="category_id" value="{{$category_id}}" />
                        <input type="hidden" name="course_id" value="{{$course_id}}" />

                        {{csrf_field()}}
                        <div class="form-row">
                            <div class="input-group mb-1 col-6 col-sm-6 col-md-3 col-lg-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >課程</span>
                                </div>
                                <label class="form-control" >{{$course->title}}</label>

                            </div>
                            <div class="input-group mb-1 col-6 col-sm-6 col-md-3 col-lg-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >單元</span>
                                </div>
                                <input type="number" class="form-control" name="sort" value="{{$sort}}" min="1" max="20">

                            </div>
                            <div class="input-group mb-1 col-12 col-sm-12 col-md-6 col-lg-6">
                                <div class="input-group-prepend">
                                     <span class="input-group-text" id="inputGroup-sizing-default" >
                                        <span class="mr-3">
                                             影片
                                        </span>

                                        <a href="{{ url('admin/video/create?category_id='.$category_id.'&course_id='.$course_id.'&sort='.$sort) }}" class="btn btn-primary btn-sm">新增</a>
                                    </span>
                                </div>
                                <select name="video_id">
                                    @if(0 == $sort)
                                        <option value="0" selected>不選擇</option>
                                    @else
                                        <option value="0" >不選擇</option>
                                    @endif
                                    @foreach($videos as $video)
                                        @if($video->sort == $sort)
                                            <option value="{{$video->id}}" selected>{{$video->title}}</option>
                                        @else
                                            <option value="{{$video->id}}" >{{$video->title}}</option>
                                        @endif
                                    @endforeach
                                </select>

                            </div>
                            <div class="input-group mb-1 col-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >標題</span>
                                </div>
                                <input type="text" class="form-control" name="title">
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
                                <textarea id="content" name="content" style="width: 100%;height: 500px;"></textarea>
                            </div>



                            <div class="col-md-12">
                                <button type="button" class="btn btn-secondary" onClick="location.href='{{url('/admin/courses?category_id='.$category_id)}}';">{{__('layout.back')}}</button>
                                <button type="submit" class="btn btn-primary">{{__('layout.submit')}}</button>
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
    <script src="https://cdn.ckeditor.com/4.19.1/standard-all/ckeditor.js"></script>
    <!--<script src="https://cdn.ckeditor.com/ckeditor5/35.1.0/classic/ckeditor.js"></script>-->
    <script>
        function back() {
            history.go(-1);
        }
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


    </script>
@endsection
