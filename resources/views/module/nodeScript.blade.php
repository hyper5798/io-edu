@extends('Layout.escape')

@section('content')
    <!-- Tab -->
    <div class="row mt-2">
        <div class="col-11">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link active" data-toggle="tab" href="#1">設定腳本</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-toggle="tab" href="#2">模組裝置</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link " data-toggle="tab" href="#3">檢視流程</a>
                </li>

            </ul>
        </div>
        <div class="col-1">
            <button title="{{__('layout.tutorials') }}" type="button" class="btn btn-warning" onclick="window.location='{{ url("/escape/carousel?app=4&item=1") }}'"><i class="fas fa-question"></i></button>
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
    <div class="row justify-content-center main-content">
        <!-- Edit  -->
        <div class="col-md-12 col-xl-12">
            <div class="card shadow-lg  rounded-lg mt-3">
                <div  class="card-header mission_header">
                    <div class="row">
                        <div class="col-lg-4">
                            {{__('layout.select') }}控制器
                            <select onchange="location.href='?node_id='+this.options[this.selectedIndex].value">
                                @foreach ($nodes as $cItem)
                                    @if ($cItem->id == $node_id)
                                        <option value="{{$cItem->id}}" selected="selected">{{$cItem->node_name}}</option>
                                    @else
                                        <option value="{{$cItem->id}}">{{$cItem->node_name}}</option>
                                    @endif
                                @endforeach

                            </select>
                        </div>
                        <div class="col-lg-5">
                            <button type="button" class="btn btn-outline-primary" @click="setController()">設定控制器</button>

                        </div>
                        <div class="col-lg-3">
                            <button type="button" class="btn btn-primary text-right" @click="showRelation()">關聯規則</button>
                            <button type="button" class="btn btn-success text-right" @click="newCheck()">新增規則</button>

                        </div>
                    </div>

                    </span>


                </div>
                <div class="card-body main-content">
                    <!-- List rules -->
                    <!-- List Rules -->
                    <div v-cloak v-show="isNew==0">
                        <table id ="table1"  class="table table-striped table-hover table-content">
                            <thead>
                            <tr>
                                <th >{{__('layout.item') }}</th>
                                <th >輸入裝置</th>
                                <th >操作</th>
                                <th >觸發欄位</th>
                                <th >觸發值</th> <!-- 名稱 -->
                                <th >輸出裝置</th>
                                <th >輸出值</th>
                                <th >輸出時間</th>
                                <!--<th >輸出裝置類型</th>-->
                                <th ></th>
                                <th v-show="isShow" ></th>
                            </tr>
                            </thead>

                            <tbody>

                                <tr v-for="(item, key) in ruleList" :key="key">
                                    <td> @{{key+1}} </td>
                                    <td> <!--@{{item.input}}-->
                                        <span v-if="item.input === 'server'">
                                            <img src="{{url('/Images/computer.png')}}"  width="40" height="40">
                                            平台
                                        </span>
                                        <span v-if="item.input === 'time'">
                                            <img  src="{{url('/Images/clock.jpg')}}"  width="40" height="40">
                                            時間
                                        </span>
                                        <span v-else>
                                            <img  :src="item.input_url"  width="40" height="40">
                                            @{{  item.input_name }}
                                        </span>
                                     </td>
                                     <td>

                                         <label v-if="item.operator == 1"> > </label>
                                         <label v-else-if="item.operator == 2"> >= </label>
                                         <label v-else-if="item.operator == 3"> = </label>
                                         <label v-else-if="item.operator == 4"> <= </label>
                                         <label v-else-if="item.operator == 5"> < </label>
                                         <label v-else-if="item.operator == 6"> <> </label>

                                     </td>
                                     <td>
                                         @{{ Object.keys(item.trigger_value)['0'] }}
                                     </td>
                                    <td>
                                        <span v-show="Object.keys(item.trigger_value)['0']!=='pass'">@{{ Object.values(item.trigger_value)['0'] }}</span>
                                    </td>
                                     <td>
                                         <!--@{{item.output}}-->
                                         <span v-if="item.output === 'server'">
                                            <img src="{{url('/Images/computer.png')}}"  width="40" height="40">
                                            平台
                                         </span>
                                         <span v-if="item.output === 'time'">
                                            <img  src="{{url('/Images/clock.jpg')}}"  width="40" height="40">
                                            時間
                                        </span>
                                         <span v-else>
                                            <img  :src="item.output_url"  width="40" height="40">
                                            @{{  item.output_name }}
                                        </span>

                                     </td>
                                     <td>@{{item.action_value}}</td>
                                     <td>@{{item.time}}</td>
                                     <!--<td>
                                         <label v-if="item.output_type ==1"> 開關 </label>
                                         <label v-else-if="item.output_type == 2"> DC馬達 </label>
                                         <label v-else-if="item.output_type == 3"> 伺服馬達 </label>
                                         <label v-else-if="item.output_type == 4"> 步進馬達 </label>
                                         <label v-else-if="item.output_type == 5"> PWM </label>
                                         <label v-else-if="item.output_type == 6">三色燈 </label>

                                     </td>-->
                                     <td>
                                         <button type="button" name="edit" class="btn btn-primary btn-sm" @click="editCheck(key)">
                                             {{__('layout.edit')}}
                                         </button>
                                         <button type="button" name="del" class="btn btn-danger btn-sm" @click="delCheck(key, item.name)">
                                             {{__('layout.delete')}}
                                         </button>
                                     </td>
                                        <td v-show="isShow">
                                            @{{ ruleList[key]['id'] }}
                                        </td>
                                 </tr>

                             </tbody>
                         </table>
                     </div>
                     <!-- Edit rule-->
                     <form method="post" action="editNodeRule" id="editNodeRule">
                         <input type="hidden" name="id" v-model="rule.id" />
                         <input type="hidden" name="node_mac" v-model="rule.node_mac" />
                         <input type="hidden" name="rule_order" v-model="rule.rule_order" />
                         <input type="hidden" name="action" v-model="rule.action" />
                         <input type="hidden" name="input_type" v-model="rule.input_type" />
                         <input type="hidden" name="output_type" v-model="rule.output_type" />
                         <input type="hidden" name="action_value" v-model="rule.action_value">
                         <input type="hidden" name="trigger_value" v-model="rule.trigger_value">
                         <input type="hidden" name="operator" v-model="rule.operator">
                         <input type="hidden" name="time" v-model="rule.time">
                         {{csrf_field()}}
                         <div v-cloak v-show="isNew==1" class="row">

                             <div class="col-5">
                                 <h5>輸入觸發設定</h5>
                                 <div class="inputBlock">
                                     <div class="workBlock">
                                         <label for="validationDefault01">選擇輸入裝置</label>
                                         <select v-cloak v-model="rule.input" @change="inChange($event)" name="input">
                                             <option v-for="inDevice in list2" :value="inDevice.macAddr">
                                                 @{{ inDevice.device_name }} ( @{{ inDevice.macAddr }} )
                                             </option>
                                         </select>

                                     </div>
                                     <div class="workBlock">
                                         <div>

                                             <label for="validationDefault01">選擇欄位</label>
                                             <select v-cloak v-model="trigger.field">
                                                 <option v-for="(item, key, index) in selectInput" :value="key">
                                                     @{{ item }}
                                                 </option>
                                             </select>

                                         </div>
                                         <div v-if="trigger.field !== 'pass'">
                                             <span>
                                             <label for="validationDefault01">觸發設定</label>
                                             <select v-cloak v-model="rule.operator" name="operator">
                                                 <option v-for="operator in operatorList" :value="operator.id">
                                                     @{{ operator.value }}
                                                 </option>
                                             </select>
                                         </span>

                                             <span >
                                             <label for="validationDefault01">觸發值</label>
                                             <input type="number" v-model="trigger.value" min="1" max="1000">
                                         </span>
                                         </div>

                                     </div>

                                 </div>
                             </div>
                             <div class="col-1 align-self-center">
                                 <div class="row justify-content-center ">
                                     <i class="fa fa-arrow-right fa-5x" aria-hidden="true"></i>
                                 </div>
                             </div>
                             <!-- 加入輸出控制 -->
                             <div class="col-5">
                                 <h5>觸發後輸出控制</h5>
                                 <div class="outputBlock">
                                     <div class="workBlock">
                                         <label for="validationDefault01">選擇輸出裝置</label>
                                         <select v-cloak v-model="rule.output" @change="outChange($event)" name="output">
                                             <option v-for="outDevice in list4" :value="outDevice.macAddr">
                                                 @{{ outDevice.device_name }} ( @{{ outDevice.macAddr }} )
                                             </option>
                                         </select>

                                     </div>
                                     <div class="workBlock">
                                         <div v-if="rule.output_type === 7">
                                             <label for="validationDefault01">輸出埠</label>
                                             <span >
                                                     <span v-for="(item, index) in type7PortList">
                                                         <input type="text" name="action_value"  min="0" max="1" v-model="type7PortList[index]" style="width: 35px" placeholder="(index+1)" disabled>
                                                     </span>
                                                 </span>
                                         </div>
                                         <span>



                                             <label for="validationDefault01">輸出值</label>
                                             <input v-if="rule.output_type !== 7" type="number" name="action_value"  min="-10000" max="16777215" v-model="rule.action_value">
                                             <span input v-if="rule.output_type === 7">
                                                 <span v-for="(item, index) in type7List">
                                                     <input type="number" min="0" max="1" v-model="type7List[index]" style="width: 35px" placeholder="(index+1)">
                                                 </span>
                                             </span>

                                         </span>
                                         <span v-if="rule.output_type === 1 || rule.output_type === 2 || rule.output_type === 7">
                                             <label for="validationDefault01">輸出時間(秒)</label>
                                             <input type="number" min="1" max="200" v-model="rule.time">
                                         </span>
                                         <div v-cloak>
                                             <label v-if="rule.output_type === 1">輸出值範圍(0/1)</label>
                                             <label v-else-if="rule.output_type === 2">輸出值範圍(-1023~0~1023)</label>
                                             <label v-else-if="rule.output_type === 3">輸出值範圍(0~160)</label>
                                             <label v-else-if="rule.output_type === 4">輸出值範圍(-10000~0~10000)</label>
                                             <label v-else-if="rule.output_type === 5">輸出值範圍(0~4096)</label>
                                             <label v-else-if="rule.output_type === 6">輸出值範圍:(0~16,777,215)</label>
                                             <label v-else-if="rule.output_type === 7">輸出值範圍:(0:關閉 1:開啟)</label>
                                             <label v-else>輸出值範圍</label>
                                         </div>
                                     </div>

                                 </div>
                             </div>
                             <div class="col-md-3 mt-2">
                                 <button type="button" class="btn btn-secondary" @click="back()">{{__('layout.back')}}</button>
                                 <button type="button" class="btn btn-primary" @click="toSubmit()">{{__('layout.submit')}}</button>
                             </div>
                         </div>

                     </form>
                 </div>
             </div>
         </div>

     </div>

     <!-- Dialog-->
     <!-- Modal -->
     <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
         <div class="modal-dialog" role="document">
             <div v-if="isDelete" class="modal-content">
                 <div class="modal-header">
                     <h4 class="modal-title" id="myModalLabel">{{__('layout.waring')}}!</h4>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     {{__('layout.delete_confirm')}} 規則@{{ index }}?
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-default"
                             data-dismiss="modal">{{__('layout.cancel')}}
                     </button>
                     <form method="post" action="delNodeRule" id="delNodeRule">
                         <input type="hidden" name="_method" value="delete" />
                         <input type="hidden" name="id" v-model="rule.id" />
                         {{csrf_field()}}
                         <button type="button" @click="toDelete()" class="btn btn-danger">
                             {{__('layout.yes')}}
                         </button>
                     </form>
                 </div>
             </div>
             <div v-else class="modal-content">
                 <div class="modal-header">
                     <h4 class="modal-title" id="myModalLabel">設定規則關聯</h4>

                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     <div class="row">
                         <div v-if="andList.length>0 "class="col-md-12">
                             <div>
                                 <label class="text-primary" id="myModalLabel">以下規則的輸出裝置及輸出值設定相同，你要將它們的觸發都成立時再做輸出控制請勾選關聯</label>
                             </div>

                             <p v-for="(nItem,index) in andList">
                                 <input type="checkbox" id="checkbox" v-model="nItem.check">
                                 關聯規則: @{{nItem.number}}
                             </p>
                         </div>
                         <div v-else class="col-md-12">

                             <label class="text-danger" id="myModalLabel">沒有規則的輸出裝置及輸出值設定相同，按下確定移除關聯</label>

                         </div>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-default"
                             data-dismiss="modal">{{__('layout.cancel')}}
                     </button>
                     <form method="post" action="editNodeRelation" id="editNodeRelation">
                         <input type="hidden" name="id" v-model="node.id" />
                         <input type="hidden" name="relation" v-model="node.relation" />
                         {{csrf_field()}}
                         <button type="button" @click="toEditRelation()" class="btn btn-primary">
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
         let controllers = {!! $controllers !!};
         let alls = {!! $alls !!};
         let rules = {!! $rules !!};
         let nodes = [];
         @if($nodes->count()>0)
             nodes = {!! $nodes !!};
         @endif
         let nodeInputs = {!! $nodeInputs !!};
         let nodeOutputs = {!! $nodeOutputs !!};
         let controller_mac = "{!! $controller_mac !!}";
         let token = "{!! $token !!}";
         let app_url = '{{ env('APP_URL') }}';
         let menu1 = "設定腳本";
         let menu2 = "設定裝置";
         let menu3 = "檢視流程";
         let types = {!! $types !!};
         nodeInputs.push({device_name:'未選擇', macAddr: '', });
         /*nodeInputs.push({device_name:'平台', macAddr: 'server', });
         nodeInputs.push({device_name:'時間', macAddr: 'time'});
         nodeOutputs.push({device_name:'平台', macAddr: 'server'});*/

     </script>

     <script src="{{asset('vender/DataTables-1.10.20/js/jquery.dataTables.min.js')}}" charset="utf-8" ></script>
     <script src="{{asset('js/option/tableOption.js')}}"></script>
     <script src="{{asset('vender/js-xlsx/xlsx.full.min.js')}}" charset="utf-8" ></script>
     <script src="{{asset('js/module/nodeScript.js')}}"></script>
 @endsection
