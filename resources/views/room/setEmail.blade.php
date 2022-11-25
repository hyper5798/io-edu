@extends('Layout.diy')

@section('content')
    <div class="breadcrumb mt-1">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                @if($url != null)
                    <a href="{{$url}}">返回</a>
                @else
                    <a href="javascript:history.back()" onclick="self.location=document.referrer;">返回</a>
                @endif

            </li>
            <li class="breadcrumb-item active" aria-current="page">電子信箱設定 (觸發通知將通知列表中電子信箱) </li>
        </ol>
    </div>

    <div class="row justify-content-center main-content">

        <div class="col-10">
            <div class="card shadow-lg  rounded-lg mt-1">
                <div class="card-header">
                    <div v-cloak class="mission_header ml-2 mt-2">
                        <span class="mr-2">
                            <span v-if="isEdit==0">電子信箱列表</span>
                            <span v-else>電子信箱編輯</span>
                        </span>

                        @if(count($sets) == 0)
                            <span class="text-danger">
                                尚未加入任何電子信箱!
                            </span>
                        @endif
                        <span v-if="isEdit==0 && setList.length>0">
                            <label class="text-info">
                                滑鼠游標移動到列表，點擊後可以對電子信箱進行編輯或刪除
                            </label>
                        </span>
                        <span v-show="isEdit==0 && setList.length<notify_max" class="float-right">
                            <button type="button" class="btn btn-success text-right btn-sm mr-2 mb-1" @click="newSetting">
                                <i class="fas fa-plus"></i>{{__('layout.add')}}
                            </button>

                        </span>
                    </div>
                </div>
                <div  class="card-body">
                    <!-- List rooms -->
                    <div v-cloak v-show="isEdit==0" class="main-content">
                        <table id ="table1"  class="table table-striped table-hover table-content">
                            <thead>
                            <tr>
                                <th width="10%">{{__('layout.item') }}</th> <!-- 項目 -->
                                <td>Index </td>
                                <th>電子信箱(最多{{$NOTIFY_MAX}}組}</th>
                                <th v-if="operateTag==1"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="(myEmail, index) in setList">
                                <td> @{{index +1}} </td>
                                <td> @{{index}} </td>
                                <td> @{{myEmail}} </td>
                                <td v-if="operateTag==1">
                                    <button type="button" name="edit" class="btn btn-primary btn-sm" @click="editSettingByInx(index)">
                                        <i class="fas fa-pen"></i>
                                    </button>
                                    <button type="button" name="del" class="btn btn-danger btn-sm" @click="deleteSettingByInx(index)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Edit setting -->
                    <form method="post" action="editEmail" id="editEmail">
                        <input type="hidden" name="field" value="weight"/>
                        <input type="hidden" name="set" v-model="setString"/>
                        <input type="hidden" name="user_id" v-model="user.id"/>
                        {{csrf_field()}}
                        <div v-cloak v-show="isEdit>0" class="main-content ">
                            <div class="row">
                                <!-- Sequence -->
                                <div class="input-group mb-3 col-md-8 col-lg-6">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" id="inputGroup-sizing-default" >電子信箱</span>
                                    </div>
                                    <input type="email" id="email" class="form-control" v-model="setting">
                                </div>
                                <div class="col-md-auto col-lg-6"></div>



                                <!-- Notify Message -->
                                <div class="col-md-8">
                                    <div v-show="notifyMessage.length != 0" class="alert alert-primary" role="alert">
                                        @{{ notifyMessage }}
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <span class="float-right">
                                        <button type="button" class="btn btn-secondary mr-2" @click="back();">
                                           <i class="fas fa-undo"></i>返回列表

                                        <button v-show="isEdit==2" type="button" class="btn btn-danger btn-sm mr-2" @click="deleteSetting();">
                                            <i class="fas fa-trash"></i>{{__('layout.delete')}}
                                        </button>

                                        <button type="button" class="btn btn-primary" @click="saveSetting()">
                                            <i class="fas fa-pen"></i>{{__('layout.set')}}
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Delete alert Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{__('layout.waring')}}!</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span >{{__('layout.delete_confirm')}} @{{setting}} ? </span>
                </div>

                <div class="modal-footer">
                    <button type="button" @click="toDelete" class="btn btn-danger" >
                        {{__('layout.yes')}}
                    </button>
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
    <script>
        let sets = {!! json_encode($sets)  !!};
        let cp_id = {!! $cp_id !!};
        let user = {!! $user !!};
        let notify_max = {!! $NOTIFY_MAX !!};
    </script>

    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" charset="utf-8" ></script>
    <script src="{{asset('js/option/tableOption.js')}}"></script>
    <script src="{{asset('js/room/setEmail.js')}}"></script>
@endsection

