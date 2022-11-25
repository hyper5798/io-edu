@extends('Layout.module')

@section('content')
    <div v-cloak v-if="message.length>0" class="col-12 alert alert-success" role="alert">
        @{{ message }}
        <button type="button" class="close" @click="message=''">
            <span >&times;</span>
        </button>
    </div>
    <div v-cloak v-if="error.length>0" class="col-12 alert alert-danger" role="alert">
        @{{ error }}
        <button type="button" class="close" @click="error=''">
            <span >&times;</span>
        </button>
    </div>
    <!-- Tab -->
    <div class="row mt-2">
        <div class="col-11">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#1">模組腳本</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#2">模組控制器</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#3">模組狀態</a>
                </li>
            </ul>
        </div>
        <div class="col-1">
        <!--<button title="{{__('layout.tutorials') }}" type="button" class="btn btn-warning" onclick="window.location='{{ url("/escape/carousel?app=4&item=1") }}'"><i class="fas fa-question"></i></button>-->
        </div>

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

    <!-- Box -->
    <div class="row mt-2 mb-2">
        <div class="col-lg-4">
            {{__('layout.select') }}邏輯控制器
            <select onchange="location.href='?node_id='+this.options[this.selectedIndex].value+'&script_id={{$script_id}}'">
                @foreach ($nodes as $cItem)
                    @if ($cItem->id == $node_id)
                        <option value="{{$cItem->id}}" selected="selected">{{$cItem->node_name}}</option>
                    @else
                        <option value="{{$cItem->id}}">{{$cItem->node_name}}</option>
                    @endif
                @endforeach

            </select>
        </div>
        <div class="col-lg-8">
            將輸出/入控制方塊拉至顯示螢幕中,<label class="text-danger">輸入與輸出控制方塊連線數最多10條,每個邏輯控制器腳本最多5個</label>
        </div>

        <form method="post" action="editNodeFlow" id="editNodeFlow">
            {{csrf_field()}}
            <input type="hidden" name="id" v-model="script.id" />
            <input type="hidden" name="node_id" v-model="script.node_id" />
            <input type="hidden" name="node_mac" v-model="script.node_mac" />
            <input type="hidden" name="script_name" v-model="script.script_name" />
            <input type="hidden" name="relation" v-model="script.relation" />
            <input type="hidden" name="flow" v-model="script.flow" />
            <input type="hidden" name="notify" v-model="script.notify" />
        </form>
    </div>

    <!-- Menu -->
    <div id="menuContainer" class="menu">

        <div class="text-center"><h5>腳本</h5></div>

        <div v-cloak v-if="isShowNew==true" class="input-group" >
            <div class="input-group-prepend">
                <div class="input-group-text">{{__('layout.select') }}</div>
            </div>

            <select class="custom-select" onchange="location.href='?node_id={{$node_id}}&script_id='+this.options[this.selectedIndex].value">
                @foreach ($scripts as $sItem)
                    @if ($sItem->id == $script_id)
                        <option value="{{$sItem->id}}" selected="selected">{{$sItem->id}} - {{$sItem->script_name}}</option>
                    @else
                        <option value="{{$sItem->id}}">{{$sItem->id}} - {{$sItem->script_name}}</option>
                    @endif
                @endforeach

            </select>
        </div>

        <div v-cloak v-else class="input-group">

            <div class="input-group-prepend">
                <div class="input-group-text">{{__('layout.name')}}</div>
            </div>
            <input type="text" class="form-control" v-model="script.script_name" maxlength="10" size="10">
        </div>

        <!--<p class="text-center mt-1">裝置</p>-->
        <div class="menu-button-container mt-3">
            <div class="button-add-task button inputBlock ele-draggable" id="button-add-task" draggable="true">輸入控制</div>
            <div class="button-add-decision button outputBlock ele-draggable" id="button-add-decision" draggable="flase">輸出控制</div>
            <hr>

            <div >
                <button id="loadButton" class="btn btn-outline-warning " >重載腳本</button>
                <button type="button" class="btn btn-outline-success float-right" @click="saveFlow()">儲存腳本</button>
            </div>

            <div class="mt-2">
                <button  id="resetButton" class="btn btn-outline-danger">
                    清除螢幕
                </button>
                <button type="button" class="btn btn-primary float-right" @click="setController()" :disabled="isShowNew!=true">
                    傳送腳本
                </button>
            </div>
            <div class="mt-2">
                <button v-cloak v-if="isShowNew==true" class="btn btn-success " @click="addNewScript()">另存腳本</button>
                <button v-cloak v-if="isShowNew==false&&scripts.length>0" class="btn btn-secondary" @click="cancleNew()">取消另存</button>
                <button v-cloak v-if="scripts.length>0" id="loadButton" class="btn btn-danger float-right" @click="delScript()" :disabled="isShowNew!=true">刪除腳本</button>
            </div>
        </div>
    </div>

    <div
        id="canvas"
        style="width:100%; height:1000px; background: #d6d6d6; margin-bottom: 6px;"
    >
    </div>

    <textarea
        v-show="isShowFlowJson"
        id="jsonOutput"
        style="width:100%; height:140px;"
    >
        {"nodes":[],"connections":[],"numberOfElements":0}
  </textarea>

    <!-- Dialog-->
    <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
        <div class="modal-dialog  modal-lg" role="document">
            <div v-if="isChoice==0" class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{__('layout.waring')}}!</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{__('layout.delete_confirm')}} @{{script.script_name}} ?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <form method="post" action="delScript" id="delScript">
                        <input type="hidden" name="_method" value="delete" />
                        <input type="hidden" name="id" v-model="script.id" />

                        {{csrf_field()}}

                        <button type="button" @click="toDelete()" class="btn btn-danger" >
                            {{__('layout.yes')}}
                        </button>
                    </form>
                </div>
            </div>
            <!-- Script setting -->
            <div v-if="isChoice>1" class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">
                        <p v-if="isChoice === 2">設定輸入</p>
                        <p v-else-if="isChoice === 3">設定輸出</p>
                    </h4>-
                </div>
                <div v-cloak class="modal-body">
                    <div class="row">
                        <!-- setting input device -->
                        <div v-if="isChoice === 2" class="col-md-12">
                            <div class="inputBlock">
                                <!-- Choice input source -->
                                <div class="workBlock">
                                    <label for="validationDefault01">選擇輸入</label>
                                    <select v-cloak v-model="currentInput.data.mac" @change="inChange($event)" name="input">
                                        <option v-for="inDevice in list2" :value="inDevice.macAddr">
                                            @{{ inDevice.device_name }} <!--( @{{ inDevice.macAddr }} )-->

                                        </option>
                                    </select>
                                </div>
                                <div v-if="currentInput.data.mac != ''" class="workBlock">
                                    <!-- devices setting -->
                                    <div v-if="currentInput.data.type>0">
                                        <div>
                                            <label for="validationDefault01">選擇欄位</label>
                                            <select v-cloak v-model="currentInput.data.field">
                                                <option v-for="(item, key, index) in selectInput" :value="key">
                                                    @{{ item }}
                                                </option>
                                            </select>

                                        </div >
                                        <div v-if="isChangePass==1">
                                            <span v-if="currentInput.data.type==25 || currentInput.data.type==26">
                                                <input type="checkbox" id="checkbox" v-model="currentInput.data.change">
                                                <label for="checkbox">套用情境劇本密碼</label>
                                            </span>
                                        </div>

                                        <div>
                                            <label for="validationDefault01">觸發設定</label>
                                            <select v-cloak v-model="currentInput.data.operator" name="operator">
                                                <option v-for="operator in operatorList" :value="operator.id">
                                                    @{{ operator.value }}
                                                </option>
                                            </select>
                                            <span>
                                                 <label for="validationDefault01">觸發值</label>
                                                 <input type="number" v-model="currentInput.data.value" min="1" max="5000">
                                             </span>
                                        </div>
                                        <div>
                                            觸發範圍:@{{ description }}
                                        </div>
                                    </div>
                                    <!-- time & server -->
                                    <div v-else>
                                        <div v-if="currentInput.data.mac == 'time'">
                                            <div>
                                                <label for="validationDefault01">觸發設定</label>
                                                <select v-cloak v-model="currentInput.data.operator" name="operator">
                                                    <option v-for="operator in operatorList" :value="operator.id">
                                                        @{{ operator.value }}
                                                    </option>
                                                </select>
                                            </div>

                                            <span>
                                                 <label for="validationDefault01">時間</label>
                                                 <input  type="number" v-model="time.hour" min="0" max="23" >
                                                 時
                                                 <input  type="number" v-model="time.minute" min="0" max="59" >
                                                 分
                                            </span>
                                            <!--<span>
                                                 <label for="validationDefault01">觸發值</label>
                                                 <input v-show="currentInput.data.type > 0" type="number" v-model="currentInput.data.value" min="1" max="5000">
                                            </span>-->
                                            <div>
                                                觸發範圍:@{{ description }}
                                            </div>
                                        </div>
                                        <!-- Input by server-->
                                        <div v-if="currentInput.data.mac == 'server'">
                                            <div v-cloak v-show="prompt.length>0" class="alert alert-info" >
                                                @{{ prompt }}
                                                <button type="button" class="close" @click="clean()" title="清除訊息">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div>
                                                <label for="validationDefault01">選擇控制命令</label>
                                                <select v-cloak v-model="currentInput.data.value" @change="commandChange($event)">
                                                    <option v-for="(item, key, index) in commandList" :value="item">
                                                        @{{ key }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div>
                                                <label>
                                                    控制命令 API 網址
                                                </label>
                                                <span class="float-right">
                                                    <input id="step4" type="button" class="btn btn-primary btn-sm" @click="toSendCommand" value="測試 API" />
                                                    <input type="button" class="btn btn-info btn-sm "  @click="copyUrl" value="{{__('app.copy_api')}}" />
                                                </span>
                                                <input type="text" v-model="api_url" id="api_url" maxlength="90" size="90" :title="api_url"/>
                                            </div>
                                            <div>
                                                命令範圍:@{{ description }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- setting output device -->
                        <div v-else-if="isChoice === 3" class="col-md-12">
                            <div class="outputBlock">
                                <div class="workBlock">
                                    <label for="validationDefault01">選擇輸出</label>
                                    <select v-cloak v-model="currentOutput.data.mac" @change="outChange($event)" name="output">
                                        <option v-for="outDevice in list4" :value="outDevice.macAddr">
                                            @{{ outDevice.device_name }} <!--( @{{ outDevice.macAddr }} )-->
                                        </option>
                                    </select>

                                </div>
                                <div v-if="currentOutput.data.mac != ''" class="workBlock">
                                    <div v-if="currentOutput.data.type === 7">
                                        <label for="validationDefault01">輸出埠</label>
                                        <span >
                                             <span v-for="(item, index) in type7PortList">
                                                 <input type="text" name="action_value"  min="0" max="1" v-model="type7PortList[index]" style="width: 35px" placeholder="(index+1)" disabled>
                                             </span>
                                         </span>
                                    </div>
                                    <div v-if="currentOutput.data.type !== 0">
                                        <div >
                                            <label for="validationDefault01">動作值</label>
                                            <input v-if="currentOutput.data.type !== 7" type="number" min="-10000" max="16777215" v-model="currentOutput.data.value">
                                            <span input v-if="currentOutput.data.type === 7">
                                             <span v-for="(item, index) in type7List">
                                                 <input type="number" min="0" max="1" v-model="type7List[index]" style="width: 35px" placeholder="(index+1)">
                                             </span>
                                         </span>
                                        </div>
                                        <div v-if="currentOutput.data.type === 1 || currentOutput.data.type === 2 || currentOutput.data.type === 7">
                                            <label for="validationDefault01">動作時間(秒)</label>
                                            <input type="number" min="1" max="200" v-model="currentOutput.data.time">
                                        </div>
                                        <div v-cloak>
                                            動作值範圍:@{{ description }}
                                        </div>
                                    </div>
                                    <div v-cloak v-if="currentOutput.data.type == 0">
                                        <div v-if="currentOutput.data.mac == 'mail'">
                                            <label for="validationDefault01">主旨</label>
                                            <input type="text" name="action_value"  v-model="currentNotify.topic" maxlength="35" size="35">
                                        </div>
                                        <div v-if="currentOutput.data.mac !== 'server'">
                                            <label >觸發裝置</label>
                                            <select  v-model="currentNotify.content.mac" @change="targetChange($event)">
                                                <option v-for="nDevice in triggerList" :value="nDevice.macAddr">
                                                    @{{ nDevice.device_name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div v-else>
                                            <label >上報裝置</label>
                                            <select  v-model="currentOutput.data.value" @change="reportChange($event)">
                                                <option v-for="rDevice in reportList" :value="rDevice.macAddr">
                                                    @{{ rDevice.device_name }}
                                                </option>
                                            </select>
                                        </div>

                                        <div v-show="currentOutput.data.mac == 'mail' || currentOutput.data.mac == 'line' || currentOutput.data.mac == 'both'">
                                            <div>
                                              <label >加入訊息</label>
                                              <input type="text" v-model="currentNotify.content.value" maxlength="50" size="50">
                                            </div>
                                            <div>假設觸發值為@{{ example }}</div>
                                            <div>顯示訊息: @{{ info }}</div>
                                            <hr>
                                        </div>

                                        <div v-if="currentOutput.data.mac == 'mail' ||  currentOutput.data.mac == 'both'">
                                            <label >收件者</label>
                                            <div v-for="(item, index) in currentNotify.friends">
                                                <div class="mt-1">
                                                    <input  type="text" v-model="currentNotify.friends[index]" maxlength="35" size="35">

                                                    <span class="float-right">
                                                        <button v-show="currentNotify.friends.length !=1" type="button" class="btn btn-danger btn-sm" @click="delFriend(index)" >
                                                            <i class="fa fa-minus"></i>
                                                        </button>
                                                        <button v-show="index==(currentNotify.friends.length-1)" type="button" class="btn btn-primary btn-sm" @click="addFriend()" >
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div v-show="currentOutput.data.mac == 'line' || currentOutput.data.mac == 'both'">
                                            <div v-show="lineList.length==0" >
                                                <label class="text-danger">尚未設定 Line Notify</label>
                                                <button onclick="Auth();">設定Line Notify</button>
                                            </div>
                                            <div v-show="lineList.length>0" >
                                                選擇已建立Line Notify連動的群組
                                                    <div v-for="(lineItem, index) in lineList">
                                                        @{{ lineItem.line_group }}
                                                        <input type="checkbox" v-model="lineItem.check" @change="editFriends">
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">{{__('layout.cancel')}}
                    </button>
                    <button type="button" @click="toSetNode()" class="btn btn-primary">
                        {{__('layout.yes')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
    <script>
        let node_id = {!! $node_id !!};
        let script_id = {!! $script_id !!};
        let controllers = {!! $controllers !!};
        let alls = {!! $alls !!};
        let scripts = {!! $scripts !!};
        let nodes = [];
        @if($nodes->count()>0)
            nodes = {!! $nodes !!};
        @endif

        let nodeInputs = {!! $nodeInputs !!};
        let nodeOutputs = {!! $nodeOutputs !!};
        let controller_mac = "{!! $controller_mac !!}";
        let subscripts = {!! $subscripts !!};
        let token = "{!! $token !!}";
        let app_url = '{{ env('APP_URL') }}';

        let menu1 = "模組腳本";
        let menu2 = "模組控制器";
        let menu3 = "模組狀態";
        let types = {!! $types !!};
        let api_key = "{!! $api_key !!}";
        let clock_url = "{!! url('/Images/clock.jpg')!!}";
        let computer_url = "{!! url('/Images/computer.png')!!}";
        let gmail_url = "{!! url('/Images/gmail.png')!!}";
        let line_url = "{!! url('/Images/line.png')!!}";
        @if($user->room_id == null)
            let isChangePass = 0;
        @else
            let isChangePass = 1;
        @endif

        URL += 'response_type=code';
        URL += '&client_id='+'{{ env('LINE_NOTIFY_CLIENT_ID') }}';
        URL += '&redirect_uri='+'{{ env('REDIRECT_URI') }}';
        URL += '&scope=notify';
        URL += '&state=abcde';

        @if($user->role_id <5)
        //nodeInputs.push({device_name:'命令控制', macAddr: 'server', });
        //nodeOutputs.push({device_name:'平台', macAddr: 'server'});
        @endif

    </script>
    <script src="{{asset('vender/socket.io/socket.io.js')}}" crossorigin="anonymous"></script>
    <script src="https://cdn.bootcss.com/jsPlumb/2.9.0/js/jsplumb.js"></script>
    <script src="{{asset('js/module/nodeFlow.js')}}"></script>
@endsection
