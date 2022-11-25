@extends('Layout.room')

@section('content')
    <!-- Tab -->
    <div class="row mt-2">
        <div class="col-12">
            <ul class="nav nav-tabs">
                @if($user->role_id < 3)
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#1">{{__('layout.cps') }}</a>
                    </li>
                @endif
                @if(env('IS_GROUP')==true)
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#2">{{__('layout.group')}}</a>
                </li>
                @endif

                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#3">{{__('user.account_management') }}</a>
                </li>

            </ul>
        </div>
    <!--<div class="col-1">
            <button title="{{__('layout.tutorials') }}" type="button" class="btn btn-warning" onclick="window.location='{{ url("/escape/carousel?app=4&item=1") }}'"><i class="fas fa-question"></i></button>
        </div>-->

    </div>

    <!-- E-mail exist waring -->
    @if (count($errors) > 0)
        <div class="alert alert-danger mt-3" id="message2">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <!-- Check error waring -->
    <div v-cloak v-if="isError" class="alert alert-danger mt-3" id="message">
        <ul>
            <li v-for="msg in messages">@{{ msg }}</li>
        </ul>
    </div>

    <!-- Box -->
    <div class="row justify-content-center main-content">
        <!-- Edit  -->

        <div class="col-md-12 col-xl-12">
            <div class="card shadow-lg  rounded-lg mt-3">
                <div  class="card-header mission_header">
                    <!-- CP -->
                    <span class="ml-3">
                        @if( $cps!=null && $cps->count()>0 && $user->role_id < 3)
                            {{__('layout.cps') }}
                            <select onchange="location.href='?cp_id='+this.options[this.selectedIndex].value">
                               @foreach ($cps as $item)
                                    @if ($item->id == $cp_id)
                                        <option value="{{$item->id}}" selected="selected">{{$item->cp_name}}</option>
                                    @else
                                        <option value="{{$item->id}}">{{$item->cp_name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        @endif
                    </span>
                    <!-- Group -->
                    @if(env('IS_GROUP')==true)
                    <span class="ml-3 mr-3">
                        @if ($groups && count($groups)>0)
                        {{__('layout.group') }}
                            <select onchange="location.href='accounts?group_id='+this.options[this.selectedIndex].value+'&cp_id='+{{$cp_id}}">

                                @foreach ($groups as $mygroup)

                                    @if ($mygroup->id == $group_id)
                                        <option value="{{$mygroup->id}}" selected="selected">{{$mygroup->name}}</option>
                                    @else
                                        <option value="{{$mygroup->id}}">{{$mygroup->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        @else
                            <label class="text-danger">尚未建立群組</label>
                        @endif
                    </span>
                    @endif
                    <!-- Change members Button -->

                    <!-- Add account Button -->
                    <span class="float-right mr-3">
                            <button type="button" class="btn btn-info btn-sm" @click="importCheck()">{{__('layout.import_add')}}</button>
                            <button type="button" class="btn btn-success btn-sm" @click="newCheck()">{{__('layout.add')}}{{__('layout.accounts')}}</button>
                    </span>
                </div>

                <div class="card-body main-content">
                    <div v-show="isNew==0" class="row justify-content-center">
                    @if(env('IS_GROUP')==true)
                        <!-- 已加入成員列表 -->
                        <div v-cloak class="col-sm-12 col-md-8 col-lg-6  memberBlock">
                            <div v-if="!isGroup" class="row">
                                <div class="col-3">
                                    <label class="font-weight-bold">群組成員列表</label>
                                </div>
                                <div class="col-9">
                                    <input class="form-check-input " type="checkbox" @change="changeAdd($event);">
                                    選擇全部
                                    <button type="button" class="btn btn-danger btn-sm" @click="checkGroup();">勾選帳戶移出群組</button>
                                </div>
                            </div>
                            <div v-cloak>
                                <table id ="table1"  class="table table-striped table-hover table-content">
                                    <thead>
                                        <tr>
                                            <th width="15%">{{__('layout.item') }}</th>
                                            <th width="15%">{{__('layout.select') }}</th>
                                        <!--<th >{{__('layout.date') }}</th>-->
                                            <th width="20%">{{__('layout.name') }}</th> <!-- 名稱 -->
                                            <th width="30%">{{__('layout.accounts') }}</th> <!-- 名稱 -->
                                        <!--<th >{{__('layout.roles') }}</th> 權限 -->
                                            <th width="20%">操作</th>
                                        </tr>

                                    </thead>
                                    <tbody v-if="addList.length>0">
                                        <tr v-for="(mUser, index) in addList">
                                            <td> @{{index +1}} </td>
                                            <td class="text-center">
                                                <input class="form-check-input" type="checkbox" v-model="mUser.check">
                                            </td>
                                            <td> @{{mUser.name}} </td>
                                            <td> @{{mUser.email}} </td>
                                            <!--<td> @{{mUser.role_name}} </td>-->
                                            <td width="25%">
                                                <button type="button" name="edit" class="btn btn-primary btn-sm mr-3" @click="editAddCheck(index)">
                                                    <i class="fas fa-pen"></i><!--{{__('layout.edit')}}-->
                                                </button>
                                            <!--<button type="button" name="del" class="btn btn-danger btn-sm" @click="delMemberCheck(index, mUser.name)">
                                                    <i class="fa fa-trash"></i>{{__('layout.delete')}}
                                                </button>-->
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- 未加入帳戶列表 -->
                        <div class="col-sm-12 col-md-8 col-lg-6  memberBlock">
                            <div v-if="!isGroup" class="row">
                                <div class="col-3">
                                    <label class="font-weight-bold">未加入帳戶列表</label>
                                </div>
                                <div class="col-9">
                                    <input class="form-check-input" type="checkbox" @change="changeUser($event);">
                                    選擇全部
                                    <button type="button" class="btn btn-primary btn-sm" @click="addToGroup();">勾選帳戶加入群組</button>
                                </div>
                            </div>

                            <!-- 未加入帳戶列表 -->
                            <div v-cloak v-show="!isGroup" >
                                <table id ="table12"  class="table table-striped table-hover table-content">
                                    <thead>
                                    <tr>
                                        <th width="15%">{{__('layout.item') }}</th>
                                        <th width="15%">{{__('layout.select') }}</th>
                                    <!--<th >{{__('layout.date') }}</th>-->
                                        <th width="20%">{{__('layout.name') }}</th> <!-- 名稱 -->
                                        <th width="30%">{{__('layout.accounts') }}</th> <!-- 名稱 -->
                                    <!--<th >{{__('layout.roles') }}</th> 權限 -->
                                        <th width="20%">操作</th>
                                    </tr>
                                    </thead>
                                    <tbody v-if="userList.length>0">
                                    <tr v-for="(mUser, index) in userList">
                                        <td> @{{index +1}} </td>
                                        <td class="text-center">
                                            <input class="form-check-input" type="checkbox" v-model="mUser.check">
                                        </td>
                                        <!--<td> @{{mUser.updated_at}} </td>-->
                                        <td> @{{mUser.name}} </td>
                                        <td> @{{mUser.email}} </td>
                                        <!--<td> @{{mUser.role_name}} </td>-->
                                        <td width="25%">
                                            <button type="button" name="edit" class="btn btn-primary btn-sm mr-3" @click="editUserCheck(index)">
                                                <i class="fas fa-pen"></i><!--{{__('layout.edit')}}-->
                                            </button>
                                            <button type="button" name="del" class="btn btn-danger btn-sm" @click="delUserCheck(index, mUser.name)">
                                                <i class="fa fa-trash"></i><!--{{__('layout.delete')}}-->
                                            </button>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        @if(count($groups)>0)
                        <div v-cloak v-show="isNew==1">
                            <form method="post" action="editGroupUser" id="editGroupUser">
                                <input type="hidden" name="id" v-model="group.id" />
                                <input type="hidden" name="member" v-model="member" />
                                <input type="hidden" name="cp_id" v-model="group.cp_id" />
                                {{csrf_field()}}
                            </form>
                        </div>
                        @endif
                    @else
                        <!-- 無關群組帳戶列表 -->
                            <div class="col-sm-12 col-md-12 col-lg-12  memberBlock">
                                <div v-if="!isGroup" class="row">
                                    <div class="col-3">
                                        <label class="font-weight-bold">帳戶列表</label>
                                    </div>

                                </div>
                                <div v-cloak v-show="!isGroup" >
                                    <table id ="table12"  class="table table-striped table-hover table-content">
                                        <thead>
                                        <tr>
                                            <th width="15%">{{__('layout.item') }}</th>

                                        <!--<th >{{__('layout.date') }}</th>-->
                                            <th width="20%">{{__('layout.name') }}</th> <!-- 名稱 -->
                                            <th width="30%">電子信箱或電話</th> <!-- 名稱 -->
                                        <!--<th >{{__('layout.roles') }}</th> 權限 -->
                                            <th width="20%">操作</th>
                                        </tr>
                                        </thead>
                                        <tbody v-if="userList.length>0">
                                        <tr v-for="(mUser, index) in userList">
                                            <td> @{{index +1}} </td>

                                            <!--<td> @{{mUser.updated_at}} </td>-->
                                            <td> @{{mUser.name}} </td>
                                            <td>
                                                <span v-if="mUser.email != null"> @{{mUser.email}}  </span>
                                                <span v-else> @{{mUser.phone}}  </span>
                                            </td>
                                            <!--<td> @{{mUser.role_name}} </td>-->
                                            <td width="25%">
                                                <button type="button" name="edit" class="btn btn-primary btn-sm mr-3" @click="editUserCheck(index)">
                                                    <i class="fas fa-pen"></i><!--{{__('layout.edit')}}-->
                                                </button>
                                                <button type="button" name="del" class="btn btn-danger btn-sm" @click="delUserCheck(index, mUser.name)">
                                                    <i class="fa fa-trash"></i><!--{{__('layout.delete')}}-->
                                                </button>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                    @endif
                    </div>

                    <!-- Edit user-->
                    <div v-cloak v-show="isNew==1">
                        <form method="post" action="editAccount" id="editAccount">
                            <input type="hidden" name="id" v-model="user.id" />
                            <input type="hidden" name="password" v-model="user.password" />
                            <input type="hidden" name="email" v-model="user.email" />
                            <input type="hidden" name="phone" v-model="user.phone" />
                            <input type="hidden" name="cp_id" v-model="user.cp_id" />
                            <input type="hidden" name="role_id" v-model="user.role_id" />
                            <input v-if="user.group_role_id != null" type="hidden" name="group_role_id" v-model="user.group_role_id" />

                            {{csrf_field()}}
                            <div class="form-row justify-content-center">
                                <div class="col-sm-12 col-md-6">
                                    <!--User Name -->
                                    <div class="input-group mb-2">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">{{__('user.name') }}</span>
                                        </div>
                                        <input type="text" class="form-control" v-model="user.name" name="name" @focus="onFocusEvent">
                                    </div>
                                    <!--User Email -->
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">{{__('user.email') }}</span>
                                        </div>
                                        <input v-if="user.id==0" type="text" class="form-control"  v-model="user.email"  @focus="isError=false">
                                        <input v-else type="email" class="form-control"  v-model="user.email" disabled>
                                    </div>
                                    <!--User Phone -->
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">電話</span>
                                        </div>
                                        <input v-if="user.id==0" type="text" class="form-control"  v-model="user.phone"  @focus="isError=false">
                                        <input v-else type="text" class="form-control"  v-model="user.phone" disabled>
                                    </div>
                                    <!--User Roles -->
                                    <div v-if="user.hasOwnProperty('group_role_id')" class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">{{__('layout.group') }}{{__('layout.roles') }}</span>
                                        </div>
                                        <select v-model="user.group_role_id">
                                            <option v-for="role in roleList" :value="role.role_id">@{{role.role_name}}</option>
                                        </select>
                                        <span class="mt-2 ml-3"><label class="text-success font-weight-bold">已加入群組</label></span>
                                    </div>
                                    @if($user->role_id < 2)
                                    <div v-if="user.hasOwnProperty('role_id')" class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">{{__('layout.accounts') }}{{__('layout.management') }}{{__('layout.roles') }}</span>
                                        </div>
                                        <select v-model="user.role_id">
                                            <option v-for="role in userRoleList" :value="role.role_id">@{{role.role_name}}</option>
                                        </select>
                                        @if(env('IS_GROUP')==true)
                                            @if(count($groups)>0)
                                            <span class="mt-2 ml-3"><label class="text-danger font-weight-bold">未加入群組</label></span>
                                            @endif
                                        @endif
                                    </div>
                                    @endif
                                    <div>
                                        {{__('user.warning6')}}
                                        <span class="float-right">
                                            <button type="button" class="btn btn-secondary" @click="back()">{{__('layout.back')}}</button>
                                            <button type="button" class="btn btn-primary" @click="toSubmit()">{{__('layout.submit')}}</button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Import excel -->
                    <div v-cloak v-show="isNew==2" >
                        <form method="post" action="editBatchAccount" id="editBatchAccount">
                            <input type="hidden" name="password" v-model="user.password" />
                            <input type="hidden" name="cp_id" v-model="user.cp_id" />
                            <input type="hidden" name="role_id" v-model="user.role_id" />
                            <input type="hidden" name="class_id" v-model="user.class_id" />
                            {{csrf_field()}}
                            <div class="form-row">
                                <div class="col-md-9 mt-1">
                                    <input type="file" onchange="importf(this)" />
                                </div>
                                <div class="col-md-3 mt-1 text-left">
                                    <div>
                                        <!-- 範例下載連結 -->
                                        <a href="{{asset('doc/import_accounts.xlsx')}}">{{__('layout.example_link')}}</a>
                                        <!-- 自行下載修改 -->

                                        <div>{{__('layout.download_by_yourself')}}</div>
                                    </div>
                                </div>
                                <div class="col-md-9 mt-1 text-left">
                                <textarea v-model="accountStr" name="accountStr" rows="5" cols="100" style="overflow-y:scroll">
                                </textarea>
                                </div>
                                <div class="col-md-3 mt-1 text-left">
                                    {{__('layout.format')}}:
                                    <img src="{{url('/Images/import_excel.png')}}" width="250px">
                                </div>

                                <div class="col-md-12 mt-3">
                                    <button type="button" class="btn btn-secondary" @click="back()">{{__('layout.back')}}</button>
                                    <button type="button" class="btn btn-primary" @click="toImportSubmit()">{{__('layout.submit')}}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Delete Dialog-->
    <!-- Modal delete account-->
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
                    {{__('layout.delete_confirm')}} @{{user.name}}?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delAccount" id="delAccount">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="user.id" />
                        @if(count($groups)>0)
                        <input v-if="isGroup" type="hidden" name="group_id" v-model="group.id" />
                        @endif
                        {{csrf_field()}}
                        <button type="button" onClick="toDelete()" class="btn btn-danger">
                            {{__('layout.yes')}}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Group User-->
    @if(count($groups)>0)
    <div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{__('layout.waring')}}!</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    你確認要移出勾選的成員?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delGroupUser" id="delGroupUser">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="group.id" />
                        <input type="hidden" name="cp_id" v-model="group.cp_id" />
                        <input type="hidden" name="member" v-model="member" />
                        {{csrf_field()}}
                        <button type="button" @Click="removeFromGroup();" class="btn btn-danger">
                            {{__('layout.yes')}}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
@endsection

@section('footerScripts')
    <script>
        let user = {!! $user !!};
        let data = {!! json_encode($data) !!};
        let users = {!! $users !!};
        let adds = {!! $adds !!};
        let group_id = {!! $group_id !!};
        let groups = {!! $groups !!};
        let cps = {!! $cps !!};
        let role_id = {!! $user['role_id'] !!};
            @if($cp_id == null)
        let cp_id = null;
            @else
        let cp_id = {!! $cp_id !!};
            @endif

        let menu1 = "{{__('layout.cps') }}";
        let menu2 = "{{__('layout.group') }}";
        let menu3 = "{{__('user.account_management') }}";
        let roles = {!! $roles !!};
        let user_roles = {!! $user_roles !!};
    </script>

    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" charset="utf-8" ></script>
    <script src="{{asset('js/option/tableOption.js')}}"></script>
    <script src="{{asset('vender/js-xlsx/xlsx.full.min.js')}}" charset="utf-8" ></script>
    <script src="{{asset('js/room/accounts.js')}}"></script>
@endsection
