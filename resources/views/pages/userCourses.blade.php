@extends('Layout.default')

@section('content')
    <div class="row breadcrumb">
        <div class="col-md-12">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/backend">後台儀表盤</a></li>
                <li class="breadcrumb-item"><a href="/admin/users">用戶</a></li>
                <li class="breadcrumb-item active" aria-current="page">用戶課程選擇</li>
            </ol>
        </div>

    </div>
    <div v-cloak class="main-content mt-3">
        <form method="post" id="updateUserCourses" action="{{url('/admin/updateUserCourses')}}">
            <input type="hidden" name="_method" value="put" />
            <input type="hidden" name="target_id" value="{{$target_id}}" />
            <input type="hidden" name="optionString" v-model="optionString" />
            {{csrf_field()}}
            <div  v-for="(item, index) in categoryList" class="homeBlock">
                <div class="panel panel-primary">
                    <div class="panel-heading">

                        <h5 class="panel-title">
                            <input type="checkbox" v-model="categoryCheckList[item.id]" @change="changeOption(item.id);">全選
                            <span class="ml-3">@{{ item.title }}</span>
                        </h5>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div v-for="(course, key, inx) in categoryCourseObj[item.id]" class="col-lg-3">
                                <input type="checkbox" :name="item.id[course.id]" v-model="course.check">
                                 @{{ course.title }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button  type="button" class="float-right mr-5 btn btn-primary" @click="toSubmit();">提交</button>
        </form>
    </div>



@endsection

@section('footerScripts')
    <script>
        let categories = {!! json_encode($categories)  !!};
        let categoryCourses = {!! json_encode($categoryCourses) !!};
        let categoryChecks = {!! json_encode($categoryChecks) !!};
        let user = {!! json_encode($user) !!};
    </script>

    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/dataTables.bootstrap4.min.js')}}" crossorigin="anonymous"></script>
    <script src="{{asset('js/admin/userCourses.js')}}"></script>
@endsection
