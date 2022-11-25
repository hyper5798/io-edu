<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\Device;
use App\Models\LineSubscript;
use App\Models\Node;
use App\Models\NodeRule;
use App\Models\NodeScripts;
use App\Models\Report;
use App\Models\Setting;
use App\Models\Type;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class NodeController extends Controller
{
    /**
     * Display a listing of the scripts.
     *@param Request $request
     * @return \Illuminate\Http\Response
     */
    public function script(Request $request)
    {
        $user = session('user');
        $controllers = Device::where('user_id',$user['id'])
            ->where('type_id',99)
            ->get();
        $node_id = $request['node_id'];

        $types = Type::where('category', 1) ->get();

        //$alls = Device::where('user_id',$user['id'])->get();
        $alls = DB::table('devices')
            ->join('types', 'types.type_id', '=', 'devices.type_id')
            ->where('user_id',$user['id'])
            ->select('devices.id', 'devices.device_name', 'devices.macAddr', 'devices.setting_id','devices.type_id', 'types.image_url')
            ->get();
        //dd($alls->toArray());
        $selectNode = null;
        $controller_mac = null;
        $nodes = Node::where('user_id',$user['id'])->get();
        if($nodes->count() == 0) {
            return redirect('module/nodeDevice');
        } else if($node_id == null){
            $selectNode = $nodes[0];
            $controller_mac = $selectNode->node_mac;
            $node_id = $selectNode->id;
        } else {
            foreach ($nodes as $node) {
                if($node->id == $node_id) {
                    $selectNode = $node;
                    $controller_mac = $selectNode->node_mac;
                }
            }
        }
        $rules = NodeRule::where('node_mac', $controller_mac)->get();
        //dd($rules);
        foreach ($rules as $rule) {
            foreach ($alls as $device) {
                if($device->macAddr == $rule->input) {
                    $rule->input_url = $device->image_url;
                    $rule->input_name = $device->device_name;
                }
                if($device->macAddr == $rule->output) {
                    $rule->output_url = $device->image_url;
                    $rule->output_name = $device->device_name;
                }
            }
        }
        if($selectNode->inputs ==null  && $selectNode->outputs == null ) {
            return redirect('module/nodeDevice');
        }
        $arr1 = array();
        $arr2 = array();
        $nodeInputs = "[]";
        $nodeOutputs = "[]";
        if($selectNode!=null && $selectNode->inputs != null) {
            //$nodeInputs = Device::whereIn('macAddr', $selectNode->inputs)->get();
            $nodeInputs = DB::table('devices')
                ->join('types', 'types.type_id', '=', 'devices.type_id')
                ->whereIn('devices.macAddr', $selectNode->inputs)
                ->select( 'devices.device_name', 'devices.macAddr', 'devices.setting_id','devices.type_id', 'types.image_url')
                ->get();
        }
        if($selectNode!=null && $selectNode->outputs != null) {
            //$nodeOutputs = Device::whereIn('macAddr', $selectNode->outputs)->get();
            $nodeOutputs = DB::table('devices')
                ->join('types', 'types.type_id', '=', 'devices.type_id')
                ->whereIn('devices.macAddr', $selectNode->outputs)
                ->select('devices.id', 'devices.device_name', 'devices.macAddr', 'devices.setting_id','devices.type_id', 'types.image_url')
                ->get();
        }
        $token = $user['remember_token'];
        return view('module.nodeScript', compact(['user', 'controllers', 'controller_mac', 'nodes','nodeInputs', 'nodeOutputs','rules', 'token', 'alls', 'node_id', 'types']));
    }

    /**
     * Display a listing of the scripts.
     *@param Request $request
     * @return \Illuminate\Http\Response
     */
    public function flow(Request $request)
    {
        $user = session('user');
        $select_id = $request['select_id'];
        if($select_id == null) {
            if(session('select_id') != null) {
                $select_id = session('select_id');
            } else {
                $select_id = $user['id'];
            }
        } else {
            $select_id = (int)$select_id;
            session(['select_id'=>$select_id]);
        }
        $subscripts = LineSubscript::where('user_id',  $select_id)->get();
        $mac = $request['mac'];

        $node_id = $request['node_id'];
        if($mac != null) {
            //Node用於綁定輸出及輸入模組
            $node = Node::where('node_mac',$mac)->first();
            if($node == null ) {
                return redirect('module/nodeDevice?controller_mac=' . $mac);
            } else if ($node->inputs == null && $node->outputs == null) {
                return redirect('module/nodeDevice?controller_mac=' . $mac);
            } else {
                $node_id = $node->id;
            }
        }
        // $controllers : all of user's module controllers, type ID99 & 200~255
        $controllers = Device::where('user_id',$select_id)
            ->whereBetween('type_id',array(99, 255))
            ->whereNotBetween('type_id', array(100, 199))
            ->get();
        //$alls : all of user's devices
        $alls = DB::table('devices')
            ->join('types', 'types.type_id', '=', 'devices.type_id')
            ->where('user_id',$select_id)
            ->select('devices.id', 'devices.device_name', 'devices.macAddr', 'devices.setting_id','devices.type_id', 'types.image_url')
            ->get();
        //dd($alls->toArray());
        //category 0: controller, 1:input, 2:output
        $types = Type::whereIn('category', [1,2,3,4]) ->get();

        $selectNode = null;
        $controller_mac = null;
        $nodes = Node::where('user_id',$select_id)->get();
        if($nodes->count() == 0) {
            //到nodeDevice設定輸出入裝置
            return redirect('module/nodeDevice');
        } else if($node_id == null){
            $selectNode = $nodes[0];
            $controller_mac = $selectNode->node_mac;
            $node_id = $selectNode->id;
        } else {
            foreach ($nodes as $node) {
                if($node->id == $node_id) {
                    $selectNode = $node;
                    $controller_mac = $selectNode->node_mac;
                }
            }
        }
        $scripts = NodeScripts::where('node_id', $node_id)->get();
        $script_id = $request['script_id'];
        $api_key = '';
        if($script_id == null && $scripts->count()>0) {
            $script = $scripts[0];
            $script_id = $script->id;
            //$script = NodeScripts::where('id', $script_id)->first();

            if($script->api_key == null) {
                $script->api_key = getAPIkey($node_id, $script_id);
                $script->save();
            }
            $api_key = $script->api_key;
        } else if($script_id == null && $scripts->count() == 0) {
            $script_id = 0;

        } else {
            $script_id = (int)$script_id;
        }
        if($selectNode->inputs ==null  && $selectNode->outputs == null ) {
            return redirect('module/nodeDevice?controller_mac='.$controller_mac)->withErrors(['控制器尚未加入裝置!']);
        }

        $arr1 = array();
        $arr2 = array();
        $nodeInputs = "[]";
        $nodeOutputs = "[]";
        //Jason add for all in onew controller
        $selectNodeInputs = array();
        $selectNodeOutputs = array();

        if($selectNode != null && $selectNode->inputs != null) {
            $selectNodeInputs = getRealMacList($selectNode->inputs, $selectNode->node_mac);
        }
        if($selectNode != null && $selectNode->outputs != null) {
            $selectNodeOutputs = getRealMacList($selectNode->outputs, $selectNode->node_mac);
        }

        if($selectNode!=null && $selectNode->inputs != null) {
            //$nodeInputs = Device::whereIn('macAddr', $selectNode->inputs)->get();
            $nodeInputs = DB::table('devices')
                ->join('types', 'types.type_id', '=', 'devices.type_id')
                ->whereIn('devices.macAddr', $selectNodeInputs)
                ->select( 'devices.device_name', 'devices.macAddr', 'devices.setting_id','devices.type_id', 'types.image_url')
                ->get();
        }
        if($selectNode!=null && $selectNode->outputs != null) {
            //$nodeOutputs = Device::whereIn('macAddr', $selectNode->outputs)->get();
            $nodeOutputs = DB::table('devices')
                ->join('types', 'types.type_id', '=', 'devices.type_id')
                ->whereIn('devices.macAddr', $selectNodeOutputs)
                ->select('devices.id', 'devices.device_name', 'devices.macAddr', 'devices.setting_id','devices.type_id', 'types.image_url')
                ->get();
        }

        $token = $user['remember_token'];
        return view('module.nodeFlow', compact(['user', 'controllers', 'controller_mac', 'nodes','nodeInputs', 'nodeOutputs', 'token', 'alls', 'node_id', 'types', 'scripts','script_id','subscripts', 'api_key']));
    }

    /**
     * Display a listing of the devices.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function devices(Request $request)
    {
        $user = session('user');
        $select_id = $request['select_id'];
        if($select_id == null) {
            if(session('select_id') != null) {
                $select_id = session('select_id');
            } else {
                $select_id = $user['id'];
            }
        } else {
            $select_id = (int)$select_id;
            session(['select_id'=>$select_id]);
        }
        //模組控制器，留下type_id 99, 200以上
        $controllers = Device::where('user_id',$select_id)
            ->whereBetween('type_id',array(99, 255))
            ->whereNotBetween('type_id', array(100, 199))
            ->get();
        $controller_mac = $request['controller_mac'];

        //
        if($controllers->count()>0 && $controller_mac == null) {
            $controller_mac = $controllers->first()->macAddr;
        } else if( $controller_mac == null) {
            $controller_mac = 'null';
        }
        $nodes = Node::where('user_id',$select_id)->get();
        $selectNode = null;
        $addInputs = array();
        $addOutputs = array();
        $selectNodeInputs = array();
        $selectNodeOutputs = array();

        foreach ($nodes as $node) {
            if($node->node_mac === $controller_mac) {
                $selectNode = $node;
                if(isset($node->inputs)) {
                    //$addInputs = array_merge($addInputs, $node->inputs);
                    $inputs = getRealMacList($node->inputs, $node->node_mac);
                    $addInputs = array_merge($addInputs, $inputs);
                }
                if(isset($node->outputs)) {
                    $outputs = getRealMacList($node->outputs, $node->node_mac);
                    $addOutputs = array_merge($addOutputs,  $outputs);
                }
            }
        }
        if($selectNode != null && $selectNode->inputs != null) {
            $selectNodeInputs = getRealMacList($selectNode->inputs, $selectNode->node_mac);
        }
        if($selectNode != null && $selectNode->outputs != null) {
            $selectNodeOutputs = getRealMacList($selectNode->outputs, $selectNode->node_mac);
        }


        //dd($selectNode->inputs, $selectNode->outputs);
        //所有已加入輸入裝置
        $arr1 = array();
        $arr2 = array();
        if($selectNode && $selectNode->inputs != null) {
            //$nodeInputs = Device::whereIn('macAddr', $selectNode->inputs)->get();
            $nodeInputs = DB::table('devices')
                ->join('types', 'types.type_id', '=', 'devices.type_id')
                ->whereIn('devices.macAddr', $selectNodeInputs)
                ->select('devices.id', 'devices.device_name', 'devices.macAddr', 'devices.setting_id','devices.type_id', 'types.image_url')
                ->get();

            foreach ($nodeInputs as $device) {
                array_push($arr1, $device->id);
            }
        } else {
            $nodeInputs = "[]";
        }

        if($selectNode && $selectNode->outputs != null) {
            //$nodeInputs = Device::whereIn('macAddr', $selectNode->outputs)->get();
            $nodeOutputs = DB::table('devices')
                ->join('types', 'types.type_id', '=', 'devices.type_id')
                ->whereIn('devices.macAddr', $selectNodeOutputs)
                ->select('devices.id', 'devices.device_name', 'devices.macAddr', 'devices.setting_id','devices.type_id', 'types.image_url')
                ->get();

            foreach ($nodeOutputs as $device) {
                array_push($arr2, $device->id);
            }
        } else {
            $nodeOutputs = "[]";
        }
        //type1 for input controller, type2 for output controller
        $type1 = Type::whereIn('category', [1,3,4])->get();
        $type2 = Type::whereIn('category', [2,4])->get();

        $inputs = DB::table('devices')
            ->join('types', 'types.type_id', '=', 'devices.type_id')
            ->where('devices.user_id',$select_id)
            ->whereIn('devices.type_id', array_column($type1->toArray(), 'type_id'))
            ->whereNotIn('devices.macAddr', $addInputs)
            ->select('devices.id', 'devices.device_name', 'devices.macAddr', 'devices.setting_id','devices.type_id', 'types.image_url')
            ->get();

        $outputs = DB::table('devices')
            ->join('types', 'types.type_id', '=', 'devices.type_id')
            ->where('devices.user_id',$select_id)
            ->whereIn('devices.type_id', array_column($type2->toArray(), 'type_id'))
            ->whereNotIn('devices.macAddr', $addOutputs)
            ->select('devices.id', 'devices.device_name', 'devices.macAddr', 'devices.setting_id','devices.type_id', 'types.image_url')
            ->get();

        return view('module.nodeDevice', compact(['user', 'controllers', 'inputs', 'outputs','controller_mac', 'nodes','nodeInputs', 'nodeOutputs']));
    }

    /**
     * Display a listing of the devices.
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function status(Request $request)
    {
        $redis = app('redis.connection');

        /*$key = 'room1';
        $doorMac = $redis->hgetall($key);*/
        $user = session('user');
        $select_id = $request['select_id'];
        if($select_id == null) {
            if(session('select_id') != null) {
                $select_id = session('select_id');
            } else {
                $select_id = $user['id'];
            }
        } else {
            $select_id = (int)$select_id;
            session(['select_id'=>$select_id]);
        }

        $controllers = DB::table('devices')
            ->join('types', 'types.type_id', '=', 'devices.type_id')
            ->whereBetween('devices.type_id',array(99, 255))
            ->whereNotBetween('devices.type_id',array(100, 199))
            ->where('devices.user_id',$select_id)
            ->select('devices.id', 'devices.device_name', 'devices.macAddr', 'devices.setting_id','devices.type_id', 'types.image_url')
            ->get();
        $controller_mac = $request['controller_mac'];

        if($controllers->count()>0 && $controller_mac == null) {
            $controller_mac = $controllers->first()->macAddr;
        } else if( $controller_mac == null) {
            $controller_mac = 'null';
        }
        $redisTarget = $redis->hgetall($controller_mac);

        foreach ($controllers as $item) {
            if($item->macAddr == $controller_mac && count($redisTarget) !=0) {
                if (array_key_exists('script_id', $redisTarget)) {
                    $item->script_id = $redisTarget['script_id'];
                    $item->script_time = $redisTarget['script_time'];
                } else {
                    $item->script_id = '';
                    $item->script_time = '';
                }
            } else {
                $item->script_id = '';
                $item->script_time = '';
            }
        }


        $nodes = Node::where('user_id',$select_id)->get();
        $selectNode = null;
        $addInputs = array();
        $addOutputs = array();

        foreach ($nodes as $node) {
            if($node->node_mac === $controller_mac) {
                $selectNode = $node;
            }
            if(isset($node->inputs)) {
                $node->inputs = getRealMacList($node->inputs, $node->node_mac);
                $addInputs = array_merge($addInputs, $node->inputs);
            }
            if(isset($node->outputs)) {
                $node->outputs = getRealMacList($node->outputs, $node->node_mac);
                $addOutputs = array_merge($addOutputs, $node->outputs);
            }
        }
        //dd($selectNode->inputs, $selectNode->outputs);
        //所有已加入輸入裝置
        $arr1 = array();
        $arr2 = array();
        if($selectNode && $selectNode->inputs != null) {
            //$nodeInputs = Device::whereIn('macAddr', $selectNode->inputs)->get();
            $nodeInputs = DB::table('devices')
                ->join('types', 'types.type_id', '=', 'devices.type_id')
                ->whereIn('devices.macAddr', $selectNode->inputs)
                ->select('devices.id', 'devices.device_name', 'devices.macAddr', 'devices.setting_id','devices.type_id', 'types.image_url')
                ->get();

            foreach ($nodeInputs as $device) {
                array_push($arr1, $device->id);
                //Jason add for update command
                $target = $redis->hgetall($device->macAddr);
                if($target != null && count($target) >0 ) {
                    $device->command = $target['command'];
                    $device->command_time = $target['command_time'];
                } else {
                    $device->command = '';
                    $device->command_time = '';
                }
            }
        } else {
            $nodeInputs = "[]";
        }

        if($selectNode && $selectNode->outputs != null) {
            //$nodeInputs = Device::whereIn('macAddr', $selectNode->outputs)->get();
            $nodeOutputs = DB::table('devices')
                ->join('types', 'types.type_id', '=', 'devices.type_id')
                ->whereIn('devices.macAddr', $selectNode->outputs)
                ->select('devices.id', 'devices.device_name', 'devices.macAddr', 'devices.setting_id','devices.type_id', 'types.image_url')
                ->get();

            foreach ($nodeOutputs as $device) {
                array_push($arr2, $device->id);
                //Jason add for update command
                $target = $redis->hgetall($device->macAddr);
                if($target != null && count($target) >0 ) {
                    $device->command = $target['command'];
                    $device->command_time = $target['command_time'];
                } else {
                    $device->command = '';
                    $device->command_time = '';
                }
            }
        } else {
            $nodeOutputs = "[]";
        }

        return view('module.nodeStatus', compact(['user', 'controllers', 'controller_mac','nodeInputs', 'nodeOutputs','selectNode']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editNodeDevice(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $id = $input['id'] ? (int)$input['id']: 0;

        if($id>0)
            $node = Node::where('id', $id)->first();
        else {
            $node = new Node;
            $node->user_id = $user['id'];
        }
        if($input['node_name'] != null)
            $node->node_name = $input['node_name'];
        if($input['node_mac'] != null)
            $node->node_mac = $input['node_mac'];

        $node->inputs = isset($input['inputs']) ? json_decode($input['inputs']) : null;
        $node->outputs = isset($input['outputs']) ? json_decode($input['outputs']) : null;
        if($node->inputs == null && $node->outputs == null  ) {
            return back();
        }
        $node->save();
        return back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return RedirectResponse
     */
    public function editNodeFlow(Request $request)
    {
        $input = $request->all();
        $id = $input['id'] ? (int)$input['id']: 0;
        $script = null;
        if($id>0)
            $script = NodeScripts::where('id', $id)->first();
        else {
            $script = new NodeScripts;
        }

        $script->script_name = $input['script_name'];
        $script->node_id = (int)$input['node_id'];
        if(isset($input['node_mac']))
            $script->node_mac = $input['node_mac'];
        //json_decode($input['relation']) OK
        if(isset($input['relation']))
            $script->relation = json_decode($input['relation']);
        else
            $script->relation = null;

        if(isset($input['flow']))
            $script->flow = json_decode($input['flow']);
        else
            $script->flow = null;

        if(isset($input['notify']))
            $script->notify = json_decode($input['notify']);
        else
            $script->notify = null;

        $script->save();
        $path = 'module/nodeFlow?node_id='.$script->node_id.'&script_id='.$script->id;
        return redirect($path);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editNodeRule(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $id = $input['id'] ? (int)$input['id']: 0;


        if($id>0)
            $rule = NodeRule::where('id', $id)->first();
        else {
            $rule = new NodeRule;
        }
        if($input['node_mac'] != null)
            $rule->node_mac = $input['node_mac'];
        if($input['input'] != null)
            $rule->input = $input['input'] ;
        if($input['output'] != null)
            $rule->output = $input['output'];
        if($input['trigger_value'] != null)
            $rule->trigger_value = json_decode($input['trigger_value']) ;
        if($input['action_value'] != null)
            $rule->action_value = (int)($input['action_value']) ;
        if($input['time'] != null)
            $rule->time = (int)($input['time']) ;
        if($input['input_type'] != null)
            $rule->input_type = (int)($input['input_type']) ;
        if($input['output_type'] != null)
            $rule->output_type = (int)($input['output_type']) ;
        if($input['action'] != null)
            $rule->action = ($input['action']) ;
        if($input['rule_order'] != null)
            $rule->rule_order = (int)($input['rule_order']) ;
        if($input['operator'] != null)
            $rule->operator = (int)($input['operator']) ;
        $rule->save();

        return back();
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function editNodeRelation(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $id = $input['id'] ? (int)$input['id']: 0;
        $node = Node::where('id', $id)->first();

        if($input['relation'] != null)
            $node->relation = json_decode($input['relation']) ;
        else
            $node->relation = null;

        $node->save();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delNodeRule(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        NodeRule::where('id', $id)->delete();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function delScript(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        NodeScripts::where('id', $id)->delete();

        return redirect('module/nodeFlow');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function lineSetting(Request $request)
    {
        //$redis = app('redis.connection');

        /*$key = 'room1';
        $doorMac = $redis->hgetall($key);*/
        $user = session('user');
        $target = session('target');
        $url = array_key_exists('url', $target) ? $target['url'] : url("/node/myDevices?link=develop");


        $token = $user['remember_token'];
        $subscripts = LineSubscript::where('user_id',  $user['id'])->get();

        return view('module.lineSetting', compact(['user', 'token', 'subscripts', 'url']));
    }

    public function reports(Request $request)
    {
        $user = session('user');
        $devices = Device::whereBetween('type_id',array(51, 255))
            ->whereNotBetween('type_id', array(99, 199))
            ->where('user_id', $user['id'])
            ->get();
        $device_id = $request['device_id'];
        $myDevice = null;
        $type_id = 0;
        if($device_id != null) {
            $device_id = (int)$device_id;
            foreach ($devices as $device) {
                if($device->id == $device_id) {
                    $myDevice = $device;
                    $type_id = $device->type_id;
                }
            }
        } else if($device_id == null && count($devices)>0 ) {
            $myDevice = $devices->first();
            $device_id = $myDevice->id;
            $type_id = $myDevice->type_id;
        } else {
            return redirect('/node/myDevices?link=module')->withErrors('尚未綁訂或購買上報型控制器，請聯絡歐利科技 03-8575055');
        }
        $start = $request['start'];
        $end = $request['end'];

        if( $start == null ){
            $start = date("Y/m/d");
        }

        if( $end == null ){
            $end = date("Y/m/d");
        }
        $date = new DateTime($end);
        $date->modify('+1 day');
        $date = $date->format('Y-m-d');

        $page = $request['page'];
        $tab = $request['tab'];

        $query = Report::where('macAddr', $myDevice->macAddr)
            ->whereBetween('recv', [$start, $date]);
        $count = $query->count();

        if($tab == null)
            $tab = 1;
        $offset = 0;
        $limit = 500;
        if($page == null) {
            $page = ceil($count/$limit);
        } else {
            $page = (int)$page;
        }
        if($page == 0) {
            $page = 1;
        }

        $offset = ($page-1)*$limit;
        $type =  Type::where('type_id', $type_id)->first();
        $settings = Setting::where('device_id', $device_id)->get();
        $labels = array();
        $dataKeys = array();
        $label = array();
        if($type != null && gettype($type->fields) == 'array') {
            $label = $type->fields;
            if($label != null) {
                list($dataKeys, $labels) = Arr::divide($label);
            }
        }
        //Select macAddr, recv and keys
        $arr1 = array(0 => "macAddr", 1=> "recv");
        $result = array_merge($arr1, $dataKeys);

        $reports = $query->offset($offset)->limit($limit)
            ->select($result)
            ->orderBy('recv', 'ASC')
            ->get();
        $data = [
            'page' => $page,
            'limit' => $limit,
            'tab' => $tab,
            'device' => $myDevice,
            'count' => $count,
            'nextLabel' => trans('intro.nextLabel'),
            'prevLabel' => trans('intro.prevLabel'),
            'skipLabel'=>trans('intro.skipLabel'),
            'doneLabel'=>trans('intro.doneLabel'),
        ];
        $labelObj = json_encode($label);

        return view('module.nodeReports', compact(['user','labelObj','label','dataKeys', 'labels','reports', 'settings', 'data', 'start', 'end', 'type_id', 'devices', 'device_id']));
    }

    /**
     * Update  a setting of the report gauge.
     *
     * @param Request $request
     * @return View
     */
    public function editSetting(Request $request)
    {
        $input = $request->all();
        $set = json_decode($input['set']);
        if($input['id'] == 0) {//New setting
            $setting = new Setting();
            $setting->device_id = (int)$input['device_id'];
            $setting->field = $input['field'];
        } else {
            $setting = Setting::find($input['id']);
        }

        $setting->set = $set;
        $setting->save();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *  @return View
     */
    public function delReports(Request $request)
    {
        $input = $request->all();
        $mac = $input['mac'];
        Report::where('macAddr', $mac)->delete();
        return back();
    }
}

/**
 * Generate api key by node id and script id.
 *
 * @param integer $node_id
 * @param integer $script_id
 *  @return string api key
 */
function getAPIkey($node_id, $script_id){
    $mId = $node_id;
    $nId =  $script_id;
    if(gettype($node_id) == "integer"){
        $mId = (string)$node_id;
    }
    if(gettype($script_id) == "integer"){
        $nId = (string)$script_id;
    }
    $mlen = strlen($mId);
    $nlen = strlen($nId);
    $id_len = 12;//字串長度
    $len1 = rand(1,$id_len-$mlen-$nlen );
    if($len1 ==0) $len1=1;
    $len2 = $id_len-$mlen-$nlen-$len1;

    $key = '';
    $word = 'abcdefghijkmnpqrstuvwxyz23456789';//字典檔 你可以將 數字 0 1 及字母 O L 排除
    $len = strlen($word);//取得字典檔長度

    for($i = 0; $i < $len1; $i++){ //總共取 幾次.
        $key .= $word[rand() % $len];//隨機取得一個字元
    }
    $key = $key.'.'.$nId.'.'.$mId.'.';
    for($i = 0; $i < $len2; $i++){ //總共取 幾次.
        $key .= $word[rand() % $len];//隨機取得一個字元
    }
    return base64_encode($key);//回傳亂數帳號
}

function getRealMacList(Array $list, $mac) {

    for($i=0; $i<count($list); $i++) {
        if($list[$i] == 'self') {
            $list[$i] = $mac;
        }
    }
    return $list;
}
