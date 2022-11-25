@extends('Layout.diy')
@inject('VideoPresenter', 'App\Presenters\VideoPresenter')

@section('css')

    <link href="{{asset('vender/star-rating1.2/css/star-rating-svg.css')}}" rel="stylesheet" />

@endsection

@section('content')
    <div class="row breadcrumb">
        <div class="col-md-6">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('/learn/allCourses')}}" >所有課程</a></li>
                <li class="breadcrumb-item"><a href="{{url('learn/allCourses?category_id='.$course->category_id)}}">{{$course->category->title}}</a></li>
                <li class="breadcrumb-item active">{{$course->title}}</li>
            </ol>
        </div>

        <div class="col-md-6 text-left">

        </div>


    </div>
    <div class="courseBlock">
        <div class="row">
            <div  class="col-lg-7 col-md-6 col-sm-12">

                <!-- Course image / Chapter video -->
                <div class="mainMedia">
                    <div v-show="!isShow" class="mb-2">
                        <img src="{{$course->image_url}}" style="width: 100%;">
                    </div>

                    <div  v-cloak v-show="isShow" class="mb-2">
                        <video id="videoDemo" controls controlsList="nodownload" oncontextmenu="return false"  width="100%">
                            <source :src="videoUrl" type="video/mp4" />
                        </video>
                    </div>
                </div>

                <!-- Tab menu -->
                <div id="tabMenu">
                    <ul class="tabs">
                        <li id="mainSeats">
                            <a href="#content" class="active">
                                <span v-if="!isShow">課程介紹</span>
                                <span v-if="isShow">單元介紹</span>
                            </a>
                        </li>
                        <li id="125"><a href="#rating">課程評價</a></li>
                        <li id="delivery"><a href="#discussion">問題討論</a></li>
                    </ul>

                </div>
                <!-- Content -->
                <div v-show="tab==1" class="contentBlock">
                    <div v-if="!isShow" >
                        <div>
                            <span class="courseTitle">{{$course->title}}</span>
                        </div>
                        <div>
                            <button v-if="isSmall" type="button" class="btn btn-light float-right ml-5 mb-2" @click="isSmall=false;">詳盡介紹</button>
                            <button v-if="!isSmall" type="button" class="btn btn-light float-right ml-5 mb-2" @click="isSmall=true;">簡單介紹</button>
                        </div>
                        <div v-if="isSmall" >{{$course->content_small}} </div>
                        <div v-cloak v-if="!isSmall">{!! $course->content !!} </div>
                    </div>

                    <div v-cloak v-if="isShow">
                        <div>
                            <span class="courseTitle">@{{chapter.title}}</span>
                        </div>
                        <div>@{{chapter.content}}</div>
                    </div>
                </div>
                <!-- rating -->
                <div v-cloak v-show="tab==2" class="contentBlock">
                    <div class="ml-3 mb-4">
                        <label v-cloak style="font-size: 40px; font-weight:bold;">@{{ courseRating.avg }}  </label>
                        <img :src="courseRating.url" width="20%" >
                        <label  class="ml-2">@{{ scoreList.length }}則評價 </label>
                        <button v-cloak v-if="isScore" type="button" class="btn-outline-dark float-right" @click="toRating();">去評價</button>
                    </div>
                    <div  v-cloak class="container">
                        <div v-for="(item, index) in scoreList" class="row scoreBlock">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div>@{{item.user_name}}</div>
                                    <div>@{{item.date}}</div>
                                </div>
                                <div class="col-lg-9">
                                    <img :src="item.url" width="120px;" >
                                    @{{item.rating}}
                                    <div>
                                        <label>@{{item.comment}}</label>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- comment -->
                <div v-cloak v-show="tab==3" class="contentBlock">
                    <div class="ml-3 mb-4">
                        <img src="{{asset('Images/comment.png')}}" width="30px" >
                        <label class="ml-2">檢視留言( @{{ comments_count }} 個 )</label>
                    </div>
                    <div v-if="isComment" class="mb-4">
                        <div>
                            @include('partial.upload_comment')
                        </div>
                    </div>
                    <div class="mb-4">
                        <div v-for="(item,index) in commentList" class="commentBlock">
                            <!-- Parent comment -->
                            @include('partial.parent_comment')

                            <!-- Children comment -->
                            <div v-show="commentReplyObj[index]['replyShow']">
                                <div v-for="(child, inx) in commentChildren[item.id]" class="replyBlock">
                                    @include('partial.children_comment')
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <div class="col-lg-5 col-md-6 col-sm-12">
                <div class="chapterBlock">
                    @foreach ($chapters as $chapter)
                        <div class="row mb-3" @click="changeChapter({{$chapter->id}}, {{$loop->index}})">

                            <div class="col-lg-4 col-md-5 col-sm-5 col-5">
                                @if($chapter->image_url)
                                    <img src="{{$chapter->image_url}}" width="100%">

                                @else

                                    <img src="{{url('/Images/no-image.png')}}" width="100%">

                                @endif
                                @if($chapter->check==false)
                                    <img src="{{url('/Images/video_lock.png')}}" width="100%"  id="P2">
                                @endif


                                    <div class="videoTime">
                                        @if($chapter->video)
                                            時間:{{$VideoPresenter->convert($chapter->video->duration)}}
                                        @else
                                            <span class="text-danger">無影片</span>
                                        @endif
                                    </div>

                            </div>

                            <div class="col-lg-8 col-md-7 col-sm-7 col-7">

                                <div>
                                    <label style="font-size: 20px;">
                                        單元{{$chapter->sort}} :
                                        {{$chapter->title}}
                                    </label>

                                </div>

                                <div> {{$chapter->content}}</div>


                            </div>
                        </div>
                    @endforeach

                </div>
            </div>


        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">我的評價 : @{{ score.rating }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">

                    <span class="my-rating-9"></span>
                    <span class="live-rating">@{{ ratingString }}</span>
                </div>
                <div class="row">
                    <textarea id="story" name="story" v-model="score.comment"
                          rows="1" style="width: 90%; height: 80px" class="ml-3" placeholder="請寫入評論">
                    </textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">{{__('layout.cancel')}}
                </button>
                <button type="button" @Click="toSendRating()" class="btn btn-primary">
                    {{__('layout.yes')}}
                </button>
            </div>
        </div>
    </div>
    </div>

@endsection

@section('footerScripts')
<script>
    let comments = {!! json_encode($comments) !!};
    let commentChildren = {!! json_encode($commentChildren) !!};
    let chapterVideos = {!! json_encode($chapterVideos) !!};
    let chapters = {!! json_encode($chapters) !!};
    let course =  {!! json_encode($course) !!};
    let scores = {!! json_encode($scores) !!};
    let api_url = '{{ env('API_URL')}}';
    let token = '{{$user->remember_token}}';
    let user_id = {{$user['id']}};
    let data =  {!! json_encode($data) !!};
    let star1 = '{!! url('/Images/star/star1.png')!!}';
    let star2 = '{!! url('/Images/star/star2.png')!!}';
    let star3 = '{!! url('/Images/star/star3.png')!!}';
    let star4 = '{!! url('/Images/star/star4.png')!!}';
    let star5 = '{!! url('/Images/star/star5.png')!!}';
</script>
<script src="{{asset('vender/star-rating1.2/js/jquery.star-rating-svg.js')}}" charset="utf-8" ></script>
<script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" charset="utf-8" ></script>
<script src="{{asset('js/option/tableOption.js')}}"></script>
<script src="{{asset('vender/js-xlsx/xlsx.full.min.js')}}" charset="utf-8" ></script>
<script src="{{asset('js/learn/courseVideo.js')}}"></script>
@endsection
