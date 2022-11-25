@extends('Layout.escape')

@section('content')
    <div class="mt-2 mb-2">
        @if($app==1)
            <button class="btn btn-secondary" type="button" onclick="location.href='{{url("/escape/admin")}}'">{{__('layout.back')}}</button>
        @endif
        @if($app==2)
            <button class="btn btn-secondary" type="button" onclick="location.href='{{url("/escape/personal")}}'">{{__('layout.back')}}</button>
        @endif
        @if($app==3)
            <button class="btn btn-secondary" type="button" onclick="location.href='{{url("/escape/teamRecords")}}'">{{__('layout.back')}}</button>
        @endif
        @if($app==4)
            <button class="btn btn-secondary" type="button" onclick="location.href='{{url("/escape/setCp")}}'">{{__('layout.back')}}</button>
        @endif
        @if($app==5)
            <button class="btn btn-secondary" type="button" onclick="location.href='{{url("/escape/teams")}}'">{{__('layout.back')}}</button>
        @endif
        @if($app==7)
            <button class="btn btn-secondary" type="button" onclick="location.href='{{url("/escape/setRoom")}}'">{{__('layout.back')}}</button>
        @endif
    </div>
    <div class="container">
        <div class="row">
            <div class = 'col-sm-12 col-md-3 clo-lg-9'>
                <div class="list-group">

                    <div v-cloak v-for="(value, index) in itemList">


                        <button v-if="index==myItem" type="button" class="list-group-item list-group-item-action active" @click="changeItem(index)">
                            @{{value}}
                        </button>

                        <button v-else type="button" class="list-group-item list-group-item-action" @click="changeItem(index)">
                            @{{value}}
                        </button>

                    </div>

                </div>
            </div>
            <div class = 'col-sm-12 col-md-9 col-lg-9'>
                <div class="medium">
                    <img :src="image" />
                </div>
                <div v-cloak class="row">
                    <div  class = 'col-sm-12 col-md-10 col-lg-10'>
                        <div><h3>@{{ topic }}</h3></div>
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
        let apply = {!! $app !!};
        let images = {!! json_encode($images) !!};
        let topics = {!! json_encode($topics) !!};
        let contents = {!! json_encode($contents) !!};
        let items = {!! json_encode($items) !!};
        let item = {!! $item !!};
        let marr = {!! json_encode($marr) !!};

    </script>
    <script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/escape/carousel.js')}}"></script>
@endsection


