@extends('Layout.room')

@section('content')
    <!-- Tab -->
    <div class="row mt-3">
        <div class="col-11">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#1">{{__('escape.room') }}</a>
                </li>
                @if(count($rooms) > 0)
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#2">{{__('escape.mission') }}</a>
                    </li>
                    @if($user->role_id < 7)
                        <!--<li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#3">{{__('escape.security_devices') }}</a>
                        </li>-->
                    @endif
                @endif
            </ul>
        </div>
        <div class="col-1">
            <button  title="{{__('layout.tutorials') }}" type="button" class="btn btn-warning" onclick="window.location='{{ url("/escape/carousel?app=7") }}'"><i class="fas fa-question"></i></button>
        </div>
    </div>

    <div v-cloak v-show="target==1" class="row justify-content-center main-content">
        <!-- Edit room -->
        <div class="col-md-12 col-xl-6">
            <div class="card shadow-lg  rounded-lg mt-3">
                <div class="card-header">
                    <div class="mission_header ml-2 mt-2">
                        @if($user->role_id < 3)
                            <select onchange="location.href='?cp_id='+this.options[this.selectedIndex].value">
                                @foreach ($cps as $cp)
                                    @if ($cp->id == $cp_id)
                                        <option value="{{$cp->id}}" selected="selected">{{$cp->cp_name}}</option>
                                    @else
                                        <option value="{{$cp->id}}">{{$cp->cp_name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        @else
                            {{__('layout.select') }}{{__('escape.room') }}
                        @endif
                        @if(count($rooms) > 0)

                            <!-- 選擇遊戲場域 -->

                            <select onchange="location.href='?cp_id='+{{$cp_id}}+'&room_id='+this.options[this.selectedIndex].value">
                                @foreach ($rooms as $room)
                                    @if ($room->id == $room_id)
                                        <option value="{{$room->id}}" selected="selected">{{$room->room_name}}</option>
                                    @else
                                        <option value="{{$room->id}}">{{$room->room_name}}</option>
                                    @endif
                                @endforeach
                            </select>
                            <span class="float-right">
                                <button type="button" class="btn btn-success text-right mr-2" @click="newRoom">
                                    {{__('layout.add')}}
                                </button>
                            </span>

                        @else
                            <span class="text-danger">
                                尚未建立任何遊戲場域!
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" action="editRoom" id="editRoom">
                        <input type="hidden" name="id" v-model="room.id" />
                        <input type="hidden" name="mac" v-model="room.mac" />
                        <input type="hidden" name="device_id" v-model="room.device_id" />
                        <input type="hidden" name="target" v-model="target" />
                        <input type="hidden" name="pass_time" v-model="room.pass_time" />
                        <input type="hidden" name="cp_id" v-model="room.cp_id" />
                        <input type="hidden" name="room_type" v-model="room.type" />
                        {{csrf_field()}}
                        <div class="form-row">
                            <div class="input-group mb-3 col-md-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default" >{{__('escape.room')}}</span>
                                </div>
                                <input type="text" class="form-control" v-model="room.room_name" name="room_name">
                            </div>
                            @if($user->role_id <3)
                            <div class="input-group mb-3 col-md-12">
                                <div class="input-group-prepend">
                                    <label class="input-group-text">工作類型</label>
                                </div>

                                <select v-model="room.work" name="room_work">
                                    <option v-for="(item, index) in workList" :value="item.key" >@{{item.value}}</option>
                                </select>

                            </div>


                            <div class="input-group mb-3 col-md-12">
                                <div class="input-group-prepend">
                                    <label class="input-group-text">控制器類型</label>
                                </div>

                                <select v-model="room.type" name="room_type">
                                    <option v-for="(item, index) in typeList" :value="item.key" >@{{item.value}}</option>
                                </select>

                            </div>

                            <div class="input-group mb-3 col-md-12">
                                <div class="input-group-prepend">
                                    <label class="input-group-text">是否提供認養</label>
                                </div>
                                <input type="checkbox" v-model="room.isSale" name="isSale" checked data-toggle="toggle" data-on="是" data-off="否" data-size="lg" >

                            </div>
                            @endif
                            <!-- 闖關時間 -->
                            <!--<div class="input-group mb-3 col-md-12">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="inputGroup-sizing-default">{{__('escape.pass_time')}}</span>
                                </div>
                                <input type="text" class="form-control" v-model="room.pass_time" name="pass_time">
                            </div>-->

                            <div class="col-md-12">
                                <span class="float-right">
                                    @if(count($rooms) > 0)
                                    <button type="button" class="btn btn-danger text-right mr-1" @click="deleteRoom">
                                        {{__('layout.delete')}}
                                    </button>
                                    @endif
                                    <button type="button" class="btn btn-primary" @click="setRoom()">
                                        <span v-if="room.id==0">{{__('layout.set')}}</span>
                                        <span v-else>{{__('layout.reset')}}</span>
                                    </button>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- 調整序號 -->
        @if(count($missions) > 0)
            <!--<div class="col-md-12 col-xl-8">
            <div  class="card shadow-lg  rounded-lg mt-3">
                <div  class="card-header">

                    <span class="mission_header ml-2">
                            {{__('escape.adjust_sequence') }}
                    </span>
                </div>
                <div class="card-body">
                    <div >

                            <div  class="alert alert-primary" role="alert">
                                {{__('escape.sequence_waring') }}
                            </div>

                        <div class="col-12">
                            <draggable
                                    :list="list"
                                    :disabled="!enabled"
                                    class="list-group"
                                    ghost-class="ghost"
                                    :move="checkMove"
                                    @start="dragging = true"
                                    @end="dragging = false"
                            >
                                <div
                                        class="list-group-item"
                                        v-for="element in list"
                                        :key="element.name"
                                >
                                    @{{ element.sequence }} -> @{{ element.mission_name }}
                                </div>
                            </draggable>
                        </div>
                        <form method="post" action="editSequence" id="editSequence">
                            <input type="hidden" name="sequence" v-model="sequence" />
                            <input type="hidden" name="target" v-model="target" />
                            {{csrf_field()}}
                        </form>
                        <span class="float-lg-right mr-2">

                            <button type="button" class="btn btn-primary text-right mr-1 mt-3" @click="resetSequence">
                            {{__('escape.reset_sequence')}}
                            </button>

                        </span>

                    </div>

                </div>
            </div>

        </div>-->
        @endif
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
                    <span >{{__('layout.delete_confirm')}} @{{room.room_name}} ? </span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delRoom" id="delRoom">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="room.id" />
                        <input type="hidden" name="target" v-model="target" />
                        {{csrf_field()}}
                        <button type="button" @click="toDelete" class="btn btn-danger" >
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
        let rooms = {!! $rooms !!};
        let room_id = {!! $room_id !!};
        let cp_id = {!! $cp_id !!};
        let missions = {!! $missions !!};
        let user = {!! $user !!};
        let devices = {!! $devices !!};
        let menu1 = "{{__('escape.room') }}";
        let menu2 = "{{__('escape.mission') }}";
        let menu3 = "{{__('escape.security_devices') }}";
        let data = {!! json_encode($data) !!};
        let messages = {!! json_encode($messages) !!};

        console.log('***** : '+window.location.hash);
        //Fix facebook return hash
        if (window.location.hash && window.location.hash == '#_=_') {
            if (window.history && history.pushState) {
                window.history.pushState("", document.title, window.location.pathname);
            } else {
                // Prevent scrolling by storing the page's current scroll offset
                let scroll = {
                    top: document.body.scrollTop,
                    left: document.body.scrollLeft
                };
                window.location.hash = '';
                // Restore the scroll offset, should be flicker free
                document.body.scrollTop = scroll.top;
                document.body.scrollLeft = scroll.left;
            }
        }
    </script>
    <script src="//cdn.jsdelivr.net/npm/sortablejs@1.8.4/Sortable.min.js"></script>
    <!-- CDNJS :: Vue.Draggable (https://cdnjs.com/) -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/Vue.Draggable/2.20.0/vuedraggable.umd.min.js"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" charset="utf-8" ></script>
    <script src="{{asset('js/option/tableOption.js')}}"></script>
    <script src="{{asset('js/room/setRoom.js')}}"></script>
@endsection
