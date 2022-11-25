@extends('Layout.diy')
@inject('VideoPresenter', 'App\Presenters\VideoPresenter')

@section('css')

    <link href="{{asset('vender/star-rating1.2/css/star-rating-svg.css')}}" rel="stylesheet" />

@endsection

@section('content')
    <div class="row breadcrumb">
        <div class="col-md-6">
            <ol class="breadcrumb">
                <li class="breadcrumb-item active">留言回復</li>
            </ol>
        </div>

        <div class="col-md-6 text-left">

        </div>


    </div>
    <div class="courseBlock">
        <table id="table1" border="1" style="width:100%">
            <thead>
            <tr>
                <th width="5%">NO</th>
                <!--<th>父留言</th>-->
                <th width="10%">留言人</th>
                <th width="30%">課程  </th>
                <th width="40%">留言</th>
                <!--<th>時間</th>-->
                <th></th>
            </tr>
            </thead>
            <tbody>


                <tr v-cloak v-for="(item,index) in commentList">
                    <td>@{{index+1}}</td>
                    <!--<td>@{{item.parent_id}}</td>-->
                    <td>@{{item.user_name}}</td>
                    <td>@{{item.course_title}}</td>
                    <td>@{{item.comment}}</td>
                    <!--<td>@{{item.created_at}}</td>-->
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-primary btn-sm" @click="toShowReply(index, item.user_name)">
                            回覆
                        </button>

                        <button type="button" class="btn btn-outline-dark btn-sm" @click="stopReply();">
                            不回覆
                        </button>
                        @if($user->role_id<3)
                        <button type="button" class="btn btn-outline-danger btn-sm" @click="toShowOther(index, item.user_name)">
                            異常
                        </button>
                        @endif
                    </td>
                </tr>

            </tbody>
        </table>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">我的回覆</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-2 smallCard">
                        <span>@{{commentUserName}} : 【@{{ this.selectComment.comment }} 】</span>
                    </div>

                    回覆
                    <textarea id="story" name="story" v-model="reply.comment"
                              rows="1" style="width: 90%; height: 80px" class="ml-3" placeholder="回復">
                    </textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <button type="button" @Click="toSendReply()" class="btn btn-primary">
                        發佈
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal2 -->
    <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">異常處理</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-2 smallCard">
                        <span class="text-danger"> @{{commentUserName}} : </span>
                        <span>【@{{ this.selectComment.comment }} 】</span>
                    </div>

                    <div>
                        <button type="button" class="btn btn-outline-danger" @click="removeAll();">
                            刪除所有留言
                        </button>
                        <button type="button" class="btn btn-outline-danger ml-2" @click="disableComment();">
                            禁止留言
                        </button>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <button type="button" @Click="toSendReply()" class="btn btn-primary">
                        發佈
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
<script>
    let api_url = '{{ env('API_URL')}}';
    let token = '{{$user->remember_token}}';
    let user_id = {{$user['id']}};
    let comments = {!! json_encode($comments) !!};
</script>
<script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" charset="utf-8" ></script>
<script src="{{asset('js/option/tableOption.js')}}"></script>
<script src="{{asset('vender/js-xlsx/xlsx.full.min.js')}}" charset="utf-8" ></script>
<script src="{{asset('js/learn/commentReply.js')}}"></script>
@endsection
