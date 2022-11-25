@extends('Layout.default')

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
                <li class="breadcrumb-item"><a href="/backend">後台儀表板</a></li>
                <li class="breadcrumb-item"><a href="/learn/question">評量考題</a></li>
                <li class="breadcrumb-item active" aria-current="page">新增考題</li>
            </ol>
        </div>

        <div class="col-md-3 text-right">

        </div>
    </div>

    <!-- Create question-->
    <div v-show="isNew" class="row justify-content-center main-content">
        <div class="col-lg-12">
            <div class="card shadow-lg border-0 rounded-lg mt-2">

                <div class="card-body">
                    <form method="post" action="/learn/question">
                        <input type="hidden" name="id" v-model="question.id" />
                        {{csrf_field()}}
                        <div class="form-row">
                            <div class="input-group mb-3 col-md-3">
                                領域 : &emsp;
                                <select name="field_id">
                                    @foreach ($fields as $field)
                                        @if ($field->id == $field_id)
                                            <option value="{{$field->id}}" selected="selected">{{$field->title}}</option>
                                        @else
                                            <option value="{{$field->id}}">{{$field->title}}</option>
                                        @endif

                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group mb-3 col-md-3">
                                等級 : &emsp;
                                <select name="level_id">
                                    @foreach ($levels as $level)
                                        @if ($level->id == $level_id)
                                            <option value="{{$level->id}}" selected="selected">{{$level->title}}</option>
                                        @else
                                            <option value="{{$level->id}}">{{$level->title}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="input-group mb-1 col-md-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >標題</span>
                                </div>
                                <input type="text" class="form-control" name="title">
                            </div>
                            <div class="input-group mb-1 col-md-12">
                                <textarea id="content" name="content"></textarea>
                            </div>

                            <div class="input-group mb-1 col-md-10">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">正確答案選擇(1)</span>
                                </div>
                                <input type="text" class="form-control" name="option_a">
                            </div>
                            <div class="input-group mb-1 col-md-2">
                                <input type="checkbox" class="form-control" name="a"><h3>正確答案</h3>
                            </div>

                            <div class="input-group mb-1 col-md-10">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">正確答案選擇(2)</span>
                                </div>
                                <input type="text" class="form-control" name="option_b">
                            </div>
                            <div class="input-group mb-1 col-md-2">
                                <input type="checkbox" class="form-control" name="b"><h3>正確答案</h3>
                            </div>

                            <div class="input-group mb-1 col-md-10">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">正確答案選擇(3)</span>
                                </div>
                                <input type="text" class="form-control" name="option_c">
                            </div>
                            <div class="input-group mb-1 col-md-2">
                                <input type="checkbox" class="form-control" name="c"><h3>正確答案</h3>
                            </div>

                            <div class="input-group mb-1 col-md-10">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">正確答案選擇(4)</span>
                                </div>
                                <input type="text" class="form-control" name="option_d">
                            </div>
                            <div class="input-group mb-1 col-md-2">
                                <input type="checkbox" class="form-control" name="d"><h3>正確答案</h3>
                            </div>

                            <div class="input-group mb-1 col-md-10">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">正確答案選擇(5)</span>
                                </div>
                                <input type="text" class="form-control" name="option_e">
                            </div>
                            <div class="input-group mb-1 col-md-2">
                                <input type="checkbox" class="form-control" name="e"><h3>正確答案</h3>
                            </div>

                            <div class="col-md-12">
                                <button type="button" class="btn btn-secondary" onClick="back()">{{__('layout.back')}}</button>
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
        let panelbuttonUrl = '{!! url('/vender/CKEditor/panelbutton/plugin.js') !!}';
        let colorbuttonUrl = '{!! url('/vender/CKEditor/colorbutton/plugin.js') !!}';
        let ytUrl = '{!! url('/vender/CKEditor/youtube/plugin.js') !!}';
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
