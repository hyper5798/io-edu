@extends('Layout.default')

@section('css')
    <link href="https://unpkg.com/@wangeditor/editor@latest/dist/css/style.css" rel="stylesheet">
    <style>
        #content { height: 500px; width: 100%; }
        #test { height: 500px; width: 100%; }
    </style>
@endsection

@section('content')

    <div class="row breadcrumb">
        <div class="col-md-9">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/backend">後台儀表板</a></li>
                <li class="breadcrumb-item"><a href="/admin/announce">聲明宣告</a></li>
                <li class="breadcrumb-item active" aria-current="page">更新聲明</li>
            </ol>
        </div>

        <div class="col-md-3 text-right">

        </div>
    </div>

    <!-- Edit announce-->
    <div class="row justify-content-center main-content">
        <div class="col-lg-12">
            <div class="card shadow-lg border-0 rounded-lg mt-2">

                <div class="card-body">
                    <form method="post" action="{{route('admin.announce.update',[$announce->id])}}">
                        @csrf
                        @method('put')
                        <div class="form-row">

                            <div class="input-group mb-1 col-md-9">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >標題</span>
                                </div>
                                <input type="text" class="form-control" name="title" value="{{ $announce->title }}">
                            </div>
                            <div class="input-group mb-1 col-md-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >標記</span>
                                </div>

                                <select name="tag">
                                    @foreach ($tags as $tag)
                                        @if ($loop->index+1 == $announce->tag)
                                            <option value="{{$loop->index+1}}" selected="selected">{{$tag}}</option>
                                        @else
                                            <option value="{{$loop->index+1}}">{{$tag}}</option>
                                        @endif

                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group mb-1 col-md-12">
                                <textarea id="content" name="content">{{ $announce->content }}</textarea>
                            </div>

                            <div class="col-md-4">
                                <button type="button" class="btn btn-secondary" onClick="back()">{{__('layout.back')}}</button>
                                <button type="submit" class="btn btn-primary">{{__('layout.submit')}}</button>
                            </div>
                            <div class="col-md-8">
                                <button type="button" class="btn btn-danger" onclick="showDeleteDialog();">{{__('layout.delete')}}</button>
                            </div>
                        </div>
                    </form>

                    <form id="delForm" method="post" action="{{route('admin.announce.destroy',[$announce->id])}}">
                    @csrf
                    @method('delete')
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footerScripts')
    <!--<script src="https://cdn.ckeditor.com/4.20.0/full/ckeditor.js"></script>-->
    <script src="https://cdn.ckeditor.com/4.20.0/standard/ckeditor.js"></script>
    <script>
        function showDeleteDialog() {
            var yes = confirm('你確定刪除這聲明嗎？');

            if (yes) {
                //alert('你按了確定按鈕');
                toDelete();
            } else {
                //alert('你按了取消按鈕');
            }
        }

        function toDelete() {
            document.getElementById('delForm').submit();
        }


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
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <<script src="https://unpkg.com/@wangeditor/editor@latest/dist/index.js"></script>


    <script src="{{asset('js/admin/announce-edit.js')}}"></script>

@endsection
