@extends('Layout.diy')
@inject('VideoPresenter', 'App\Presenters\VideoPresenter')

@section('content')
    <div class="row breadcrumb">
        <div class="col-md-6">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/learn/allCourses')}}">所有課程</a></li>
                <li class="breadcrumb-item"><a href="{{url('learn/allCourses?category_id='.$course->category_id)}}">{{$course->category->title}}</a></li>
                <li class="breadcrumb-item active">{{$course->title}}</li>
            </ol>
        </div>

        <div class="col-md-6 text-left">

        </div>


    </div>
    <div class="courseBlock">
        <div class="row">

            <div class="col-lg-7 col-md-6 col-sm-12">
                <video id="videoDemo" autoplay controls preload="" controlsList="nodownload" oncontextmenu="return false"  width="100%" >
                    <source :src="videoUrl" type="video/mp4" />
                </video>

                <div class="contentBlock">
                    <div>
                        <span v-cloak class="courseTitle">@{{chapter.title}}</span>
                    </div>
                    <div v-cloak >@{{chapter.content}}</div>
                </div>

            </div>

            <div class="col-lg-5 col-md-6 col-sm-12">
                <div class="chapterBlock">
                    @foreach ($chapters as $chapter)
                        <div class="row mb-3" @click="changeChapter({{$chapter->id}}, {{$loop->index}})">

                            <div class="col-lg-4 col-md-5 col-sm-5 col-5">
                                @if($chapter->video)
                                    <video id="p1" oncontextmenu="return false" width="100%"  >
                                        <source src="{{$chapter->video->video_url}}" type="video/mp4" />
                                    </video>

                                @else

                                    <img src="{{url('/Images/no-video.png')}}" width="100%">

                                @endif
                                @if($chapter->check==false)
                                    <img src="{{url('/Images/video_lock.png')}}" width="100%"  id="P2">
                                @endif
                            </div>

                            <div class="col-lg-8 col-md-7 col-sm-7 col-7">

                                <div>
                                    <label style="font-size: 20px;">
                                        單元{{$chapter->sort}} :
                                        {{$chapter->title}}
                                    </label>

                                </div>

                                <div> {{$chapter->content}}</div>
                                <div> 時間: {{$VideoPresenter->convert($chapter->video->duration)}}</div>

                            </div>
                        </div>
                    @endforeach

                </div>
            </div>


        </div>
    </div>



@endsection

@section('footerScripts')
    <script>
        let chapterVideos = {!! json_encode($chapterVideos) !!};
        let chapters = {!! json_encode($chapters) !!};
        let course_id = {!! $course_id !!};
        let chapter_id = {!! $chapter_id !!};
        let chapter_index = {!! $chapter_index !!};
    </script>

    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" charset="utf-8" ></script>
    <script src="{{asset('js/option/tableOption.js')}}"></script>
    <script src="{{asset('vender/js-xlsx/xlsx.full.min.js')}}" charset="utf-8" ></script>
    <script src="{{asset('js/learn/chapterVideo.js')}}"></script>
@endsection
