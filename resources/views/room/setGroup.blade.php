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
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#2">{{__('layout.group')}}</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#2">{{__('user.account_management') }}</a>
                </li>

            </ul>
        </div>
        <!--<div class="col-1">
            <button title="{{__('layout.tutorials') }}" type="button" class="btn btn-warning" onclick="window.location='{{ url("/escape/carousel?app=4&item=2") }}'"><i class="fas fa-question"></i></button>
        </div>-->

    </div>

    <!-- Box -->
    <div v-cloak class="row justify-content-center mb-3">
        <!-- Edit cps & class -->
        <div class="col-md-6 col-xl-6">
            <div class="card rounded-lg mt-2">
                <div  class="card-header mission_header">
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
                        @elseif ( $cps!=null && $cps->count()>0)
                            @foreach ($cps as $item)
                                @if ($item->id == $cp_id)
                                    <label class="text-primary">{{$item->cp_name}}</label>
                                @endif
                            @endforeach
                        @endif
                    </span>

                    <span class="ml-3">
                        @if( $groups!=null && $groups->count()>0)
                            {{__('layout.select') }}{{__('layout.group') }}
                            <select onchange="location.href='?group_id='+this.options[this.selectedIndex].value+'&cp_id='+{{$cp_id}}">
                               @foreach ($groups as $item)
                                    @if ($item->id == $group_id)
                                        <option value="{{$item->id}}" selected="selected">{{$item->name}}</option>
                                    @else
                                        <option value="{{$item->id}}">{{$item->name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        @endif
                    </span>
                    <!--<span class="ml-3">
                        ps:{{__('layout.class_alert') }}
                    </span>-->
                    <span v-if="!isEdit" class="float-right mr-3">
                        <button  type="button" class="btn btn-success text-right" @click="newCheck()">{{__('layout.add')}}{{__('layout.group')}}</button>
                    </span>
                </div>
                <div class="card-body bodyBlock">
                    <div class="input-group input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-default">群組</span>
                        </div>
                        <input type="text" v-model="group.name" class="form-control" aria-label="Sizing example input" aria-describedby="inputGroup-sizing-sm">
                    </div>

                    <div class="input-group input-group mb-3">
                        管理 &ensp;&ensp;&ensp;
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" v-model="isRoom" id="defaultCheck1">
                            <label class="form-check-label" for="defaultCheck1">
                                場域&ensp;&ensp;
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" v-model="isMission" id="defaultCheck2">
                            <label class="form-check-label" for="defaultCheck2">
                                任務
                            </label>
                        </div>
                    </div>
                    @if(count($rooms)>0)
                        <!-- 選擇場域 -->
                        <div v-if="isRoom">
                            選擇場域
                            <div v-for="room in roomList">
                                <input type="radio" v-model="group.room_id" :value="room.id">
                                <label>@{{ room.room_name }}</label>
                                <br>
                            </div>
                        </div>
                        <!-- 選擇任務 -->
                        <div v-if="isMission">

                            @if( $rooms!=null && $rooms->count()>0)
                                選擇場域
                                <select onchange="location.href='?room_id='+this.options[this.selectedIndex].value">
                                    @foreach ($rooms as $item)
                                        @if ($item->id == $room_id)
                                            <option value="{{$item->id}}" selected="selected">{{$item->room_name}}</option>
                                        @else
                                            <option value="{{$item->id}}">{{$item->room_name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            @endif
                            <hr>
                                <div v-if="missionList.length>0">
                                    選擇任務
                                    <div v-for="mission in missionList">
                                        <input type="radio" v-model="group.mission_id" :value="mission.id">
                                        <label>@{{ mission.mission_name }}</label>
                                        <br>
                                    </div>
                                </div>
                                <div v-else>
                                    <label class="text-danger">尚未建立任務</label>
                                    <a href="{{url('/room/setMission?target=2&cp_id=')}}{{$cp_id}}&room_id={{$room_id}}" title="{{__('escape.mission')}}{{__('layout.edit')}}">{{__('escape.mission')}}{{__('layout.edit')}}</a>
                                </div>
                        </div>

                        <div v-if="isEdit">
                            <button v-if="isCancel" type="button" class="btn btn-secondary" @click="cancel()">{{__('layout.cancel') }}</button>
                            <button type="button" class="btn btn-primary" @click="toSubmit()">{{__('layout.set') }}</button>
                        </div>
                        <div v-if="!isEdit">
                            <button type="button" class="btn btn-danger" @click="delGroup()">{{__('layout.delete') }}</button>
                            <button type="button" class="btn btn-primary" @click="toSubmit()">{{__('layout.reset') }}</button>
                        </div>
                    @else
                        <label class="text-danger">尚未建立場域</label>
                            <a href="{{url('/escape/setRoom?cp_id=')}}{{$cp_id}}" title="{{__('escape.room_edit')}}">{{__('escape.room_edit')}}</a>
                    @endif
                </div>
            </div>
        </div>
        <br>
    </div>

    <form method="post" action="editGroup" id="editGroup">
        <input type="hidden" name="id" v-model="group.id" />
        <input type="hidden" name="name" v-model="group.name"/>
        <input type="hidden" name="cp_id" v-model="group.cp_id" />
        <input type="hidden" name="room_id" v-model="group.room_id"/>
        <input type="hidden" name="mission_id" v-model="group.mission_id" />
        {{csrf_field()}}
    </form>

    <!-- Modal -->
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
                    {{__('layout.delete_confirm')}}{{__('layout.group')}} @{{ group.name }} ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delGroup" id="delGroup">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="group.id" />

                        {{csrf_field()}}
                        <button type="button" @click="toDelete()" class="btn btn-danger" >
                            {{__('layout.yes')}}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
    <script>
        let group_id = {!! $group_id !!};
        let cp_id = {!! $cp_id !!};
        let groups = {!! $groups !!};
        let rooms = {!! $rooms !!};
        let missions = {!! $missions !!};
        let menu1 = "{{__('layout.cps') }}";
        let menu2 = "{{__('layout.group') }}";
        let menu3 = "{{__('user.account_management') }}";
    </script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" charset="utf-8" ></script>
    <script src="{{asset('js/option/tableOption.js')}}"></script>
    <script src="{{asset('js/room/setGroup.js')}}"></script>
@endsection
