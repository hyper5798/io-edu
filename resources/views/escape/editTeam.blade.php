@extends('Layout.escape')

@section('content')
    <!-- breadcrumb -->
    <div class="breadcrumb row mt-2">
        <div class="col-11">
                {{__('layout.edit_team') }}
        </div>

        <div class="col-1">
            <button  title="{{__('layout.tutorials') }}" type="button" class="btn btn-warning" onclick="window.location='{{ url("/escape/carousel?app=5") }}'"><i class="fas fa-question"></i></button>
        </div>
    </div>
    @if (count($errors) > 0)
        <div class="alert alert-danger mt-2" id="message">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row justify-content-center main-content">
        <!-- Edit teams -->
        <div class="col-md-4">
            <div class="card shadow-lg  rounded-lg mt-3">
                <div  class="card-header">
                    <span class="mission_header ml-2">
                        <!-- 選擇團隊 -->
                        {{__('layout.select') }}{{__('layout.teams')}}
                        @if($teams->count()>0)
                        <select onchange="location.href='?team_id='+this.options[this.selectedIndex].value+'&class_id={{$class_id}}'">

                            @foreach ($teams as $item)
                                @if ($item->id == $team_id)
                                    <option value="{{$item->id}}" selected="selected">{{$item->name}}</option>
                                @else
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endif
                            @endforeach
                        </select>
                        @endif
                    </span>
                    <span class="float-right mt-2 mr-3">
                        @if($teams->count()>0)
                        <button type="button" class="btn btn-success text-right mr-1" @click="newTeam">
                            {{__('layout.add')}}
                        </button>
                        @endif
                    </span>

                </div>
                <div class="card-body">
                    <form method="post" action="editTeam" id="editTeam">
                        <input type="hidden" name="id" v-model="team.id" />
                        {{csrf_field()}}
                        <div class="form-row">
                            <div class="input-group mb-3 col-12">
                                <label class="input-group-text">{{__('layout.teams')}}</label>
                                <input type="text" class="form-control" v-model="team.name" name="name">
                            </div>
                            <div class="col-12">
                                <span class="float-right">
                                    <button type="button" class="btn btn-danger text-right mr-1" @click="deleteTeam">
                                        {{__('layout.delete')}}
                                    </button>
                                    <button type="button" class="btn btn-primary" @click="setTeam()">
                                        {{__('layout.submit')}}{{__('layout.teams')}}
                                    </button>
                                </span>
                            </div>
                        </div>
                    </form>
            </div>
            </div>
        </div>
        <!-- teamUsers -->
        <div class="col-md-8">
            <div  class="card shadow-lg  rounded-lg mt-3">
                <div  class="card-header">
                    <div class = "row">
                        <div class="col-5">
                            <span class="mission_header ml-2">
                                {{__('layout.select') }}{{__('layout.class') }}
                                <select onchange="location.href='?class_id='+this.options[this.selectedIndex].value+'&team_id={{$team_id}}'">
                                    @foreach ($classes as $cItem)
                                        @if ($cItem->id == $class_id)
                                            <option value="{{$cItem->id}}" selected="selected">{{$cItem->class_name}}</option>
                                        @else
                                            <option value="{{$cItem->id}}">{{$cItem->class_name}}</option>
                                        @endif
                                    @endforeach
                                        @if ($class_id == 0)
                                            <option value="0" selected="selected">{{__('escape.not_joined')}}</option>
                                        @else
                                            <option value="0">{{__('escape.not_joined')}}</option>
                                        @endif
                                </select>

                            </span>
                        </div>
                        <div class="col-7 mt-3">
                            @if($teams->count() === 0)
                                <p class="text-danger">
                                    {{__('team.team_required')}}
                                </p>
                            @endif
                        </div>
                    </div>

                </div>
                <div class="card-body">
                    <div  class="row">
                        <!-- team members -->
                        <div class="col-6">
                            <h3> {{__('team.join_members') }}</h3>
                            <div class="membersBlock">
                                <draggable v-cloak class="list-group" :list="list1" group="people">
                                    <div
                                            class="list-group-item"
                                            v-for="(element, index) in list1"
                                            :key="element.name"
                                    >
                                         @{{ element.name }}
                                    </div>
                                </draggable>
                            </div>
                        </div>
                        <!-- available members -->
                        <div class="col-6">
                            <h3>{{__('team.available_members') }}</h3>
                            <div class="freeBlock">
                                <draggable v-cloak class="list-group" :list="list2" group="people">
                                    <div
                                            class="list-group-item"
                                            v-for="(element, index) in list2"
                                            :key="element.name"
                                    >
                                         @{{ element.name }}
                                    </div>
                                </draggable>
                            </div>
                        </div>
                        <form method="post" action="editTeamUsers" id="editTeamUsers">
                            <input type="hidden" name="add_members" v-model="add_members" />
                            <input type="hidden" name="remove_members" v-model="remove_members" />
                            <input type="hidden" name="team_id" v-model="team.id" />
                            {{csrf_field()}}
                        </form>
                        <div class="col-12 mt-3">
                            <span class="float-right">
                            <button type="button" class="btn btn-primary" @click="toSubmit">{{__('layout.submit')}}{{__('team.members')}}
                            </button>
                        </span>
                        </div>



                    </div>
                </div>
            </div>

        </div>
    </div>

    <! delete team modal -->
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
                    {{__('team.delete_team_waring')}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delTeam" id="delTeam">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="team.id" />

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
        let teams = {!! $teams !!};
        let classes = {!! $classes !!};
        let users = {!! $users !!};
        let members = {!! $members !!};
        let team_id = {!! $team_id !!};
        let class_id = {!! $class_id !!};
        let members_limit = "{{__('team.members_limit')}}";
        let no_team_selected = "{{__('team.no_team_selected')}}";
        let name_required = "{{__('team.name_required')}}";
    </script>
    <script src="//cdn.jsdelivr.net/npm/sortablejs@1.8.4/Sortable.min.js"></script>
    <!-- CDNJS :: Vue.Draggable (https://cdnjs.com/) -->
    <script src="//cdnjs.cloudflare.com/ajax/libs/Vue.Draggable/2.20.0/vuedraggable.umd.min.js"></script>
    <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" charset="utf-8" ></script>
    <script src="{{asset('js/option/tableOption.js')}}"></script>
    <script src="{{asset('js/escape/editTeam.js')}}"></script>

@endsection
