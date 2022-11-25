@extends('Layout.escape')

@section('content')
    <!-- Tab -->
    <div class="mt-3">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#1">{{__('escape.room') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#2">{{__('escape.mission') }}</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" data-toggle="tab" href="#3">{{__('escape.security_devices') }}</a>
            </li>
        </ul>
    </div>
    <!-- Add security device -->
    <div v-cloak class="row justify-content-center main-content">
        <!-- Edit room -->
        <div class="col-md-8">
            <div class="card shadow-lg  rounded-lg mt-3">
                <div  class="card-header">
                    <span class="mission_header ml-2">
                        <!-- 選擇密室 -->
                        {{__('layout.select') }}{{__('escape.room') }}
                        <select onchange="location.href='?room_id='+this.options[this.selectedIndex].value">
                           @foreach ($rooms as $room)
                                @if ($room->id == $room_id)
                                    <option value="{{$room->id}}" selected="selected">{{$room->room_name}}</option>
                                @else
                                    <option value="{{$room->id}}">{{$room->room_name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </span>

                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h3> {{__('escape.security_devices') }}</h3>
                            <div class="securityBlock">
                                <draggable class="list-group" :list="list1" group="people">
                                    <div
                                            class="list-group-item"
                                            v-for="(element, index) in list1"
                                            :key="element.name"
                                    >
                                        @{{ element.device_name }} @{{  element.macAddr }}
                                    </div>
                                </draggable>
                            </div>

                        </div>

                        <div class="col-6">
                            <h3>{{__('escape.available_devices') }}</h3>
                            <div class="enabledBlock">
                                <draggable class="list-group" :list="list2" group="people">
                                    <div
                                            class="list-group-item"
                                            v-for="(element, index) in list2"
                                            :key="element.name"
                                    >
                                        @{{ element.device_name }} @{{  element.macAddr }}
                                    </div>
                                </draggable>
                            </div>

                        </div>
                    </div>
                    <form method="post" action="editSecurity" id="editSecurity">
                        <input type="hidden" name="security_devices" v-model="security_devices" />
                        <input type="hidden" name="available_devices" v-model="available_devices" />
                        <input type="hidden" name="room_id" v-model="room.id" />
                        <input type="hidden" name="target" v-model="target" />
                        {{csrf_field()}}
                    </form>
                    <div class="col-md-12 mt-3">
                        <button type="button" class="btn btn-primary" @click="toSubmit">{{__('layout.submit')}}</button>
                    </div>

                </div>
            </div>
        </div>
    </div>




@endsection

@section('footerScripts')
    <script>
        let rooms = {!! $rooms !!};
        let room = {!! $my_room !!};
        let room_id = {!! $room_id !!};
        let user = {!! $user !!};
        let devices = {!! $devices !!};
        let menu1 = "{{__('escape.room') }}";
        let menu2 = "{{__('escape.mission') }}";
        let menu3 = "{{__('escape.security_devices') }}";
        let menu = "{{__('escape.script') }}";
        let data = {!! json_encode($data) !!};
        let securityNodes = {!! $securityNodes !!};
    </script>
    <script src="//cdn.jsdelivr.net/npm/sortablejs@1.8.4/Sortable.min.js"></script>
    <!-- CDNJS :: Vue.Draggable (https://cdnjs.com/) -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/Vue.Draggable/2.20.0/vuedraggable.umd.min.js"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" charset="utf-8" ></script>
    <script src="{{asset('js/option/tableOption.js')}}"></script>
    <script src="{{asset('js/escape/securityNodes.js')}}"></script>
@endsection
