@extends('Layout.diy')

@section('content')

    <div class="container mt-2">
        <div class="row">
            <div class = 'col-sm-12 col-md-3 clo-lg-9 mb-2'>
                <div class="list-group">

                    <div v-cloak v-for="(item, index) in itemList">

                        <button v-if="index==(sort-1)" type="button" class="list-group-item list-group-item-action active" @click="changeItem(index)">
                            @{{item.title}}
                        </button>

                        <button v-else type="button" class="list-group-item list-group-item-action" @click="changeItem(index)">
                            @{{item.title}}
                        </button>

                    </div>

                </div>
            </div>
            <div class = 'col-sm-12 col-md-9 col-lg-9'>
                <div class="medium">
                    <video id="movie" autoplay controls width="800" height="450">
                        <source :src="video" type="video/mp4" />
                    </video>
                </div>
                <div v-cloak class="row">
                    <div  class = 'col-sm-12 col-md-10 col-lg-10'>
                        <div><h3>@{{ title }}</h3></div>
                        <div><h5>@{{ content }}</h5></div>
                    </div>
                    <div class = 'col-sm-12 col-md-2 col-lg-2'>
                        <button type="button" class="btn btn-primary" @click="previous">
                            <i class="fas fa-angle-left"></i>
                        </button>
                        <button type="button" class="btn btn-primary" @click="next">
                            <i class="fas fa-angle-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footerScripts')
    <script>
        let category_id  = {!! $category_id !!};
        let sort = {!! $sort !!};
        let link = '{!! $link !!}';
        let items = {!! json_encode($items) !!};
        let chapterList = {!! $chapterList !!};

    </script>
    <script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/admin/tutorial.js')}}"></script>
@endsection


