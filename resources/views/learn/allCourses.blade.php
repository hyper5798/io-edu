@extends('Layout.diy')


@section('content')
    <div class="row breadcrumb">
        <!--<div class="col-md-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">所有課程</li>
            </ol>
        </div>-->

        <div class="col-md-6 text-left">

        </div>

        <div class="col-md-3 text-right">

        </div>
    </div>

    <div class="categoryBtnBlock">

        @foreach($categories as $item)
            <button type="button" class="btn btn-secondary btn-sm ml-3" onclick="toNewCategory({{$item->id}})">
                {{$item->title}} ( 單元數: {{$item->courses_count}} )
            </button>
        @endforeach


    </div>

    @if (count($errors) > 0)
        <div id="message" class="alert alert-danger alert-dismissible fade show mt-3 mb-3" role="alert">
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

    <div class="categoryBlock">
        <div class="col-lg-12 col-md-12 col-sm-12  col-12 text-left mt-2">
            <span>
                {{$category->title}}

            </span>
            @if(count($courses)>0)
                <span class="ml-5">課程數量 : {{ count($courses) }}</span>
            @else
                <span class="text-danger ml-5">
                    尚無課程 !
                </span>
            @endif
        </div>
        <div class="row mt-4">
            @foreach($courses as $course)
                <div class="col-lg-3 col-md-4 col-sm-4 col-6 mb-3">
                    <div class="card smallCard" onclick="location.href='{{url('/learn/courseVideo?course_id=')}}{{$course->id}}'">
                        <div class="text-center">
                            @if($course->image_url)
                                <img class="categoryBlockImg" src="{{$course->image_url}}">
                            @else
                                <img class="categoryBlockImg" src="{{url('/Images/no_image.png')}}">
                            @endif
                        </div>


                        <div class="card-body">
                            <h5 class="card-title ellipsis2">{{$course->title}}</h5>
                            @if($course->content_small)
                                <p class="card-text ellipsis2">{{$course->content_small}}</p>
                            @else
                                <p class="card-text ellipsis2"> &nbsp; </p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>







    </div>






@endsection

@section('footerScripts')
    <script>
        function toNewCategory(id) {
            //alert(id);
            let newUrl = 'allCourses?category_id='+id;
            document.location.href = newUrl;
        }



        setTimeout(function () {
            document.getElementById("message").hidden = true;
        }, 5000);


    </script>

    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" charset="utf-8" ></script>
    <script src="{{asset('js/option/tableOption.js')}}"></script>
    <script src="{{asset('vender/js-xlsx/xlsx.full.min.js')}}" charset="utf-8" ></script>
    <!--<script src="{{asset('js/node/accounts.js')}}"></script>-->
@endsection
