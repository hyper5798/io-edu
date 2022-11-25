<?php

namespace App\Http\Controllers\Node;

use App\Models\Command;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Type;
use Illuminate\View\View;
use App\Models\Setting;

class CommandController extends Common4Controller
{
    /**
     * Display a listing of the command list.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $user = session('user');
        $devices = null;
        $types = null;
        $device_id = null;
        if($user['role_id'] == 11) {
            //For controller user choice by device
            $user_id = $user ['id'];
            $device_id = $request['device_id'];
            //1. Check binding device
            $devices = User::find($user_id )
                -> devices;
            if($devices->count() == 0) {
                $message = trans('device.no_binding_waring');
                return redirect('/node/devices')->withErrors([$message]);
            }

            //2.Check make command device
            $devices = $devices->where('make_command', 1);
            if($devices->count() == 0) {
                //return redirect('/node/devices')->withErrors(['尚未綁定裝置, 自動轉址到裝置管理！']);
                $message = trans('device.no_binding_waring');
                return redirect('/node/devices')->withErrors(['你的裝置沒有選擇自定義命令,不可以進行命令管理！']);
            } else if($devices->count() > 0  && $device_id == null){
                $device = $devices -> first();
                $device_id = $device->id;
            } else {
                $device = Device::find($device_id);
            }
            $device_id = (int)($device_id) ;
            $type_id = $device->type_id;
            $commands = DB::table('commands')
                ->where('type_id', $type_id)
                ->where('device_id', $device_id)
                ->get();
            //dd($commands);
            return view('nodes.commands', compact(['user', 'types','devices', 'commands', 'type_id', 'device_id']));
        } else {
            //For admin user choice by type
            $type_id = $request['type_id'];
            $types =  Type::all();
            if($types->count() > 0  && $type_id == null){
                $type = $types -> first();
                $type_id = $type->type_id;
            }
            $type_id = (int)($type_id) ;
            $commands = DB::table('commands')
                ->where('type_id', $type_id)
                ->whereNull('device_id')
                ->get();
            //dd($commands);
            return view('nodes.commands', compact(['user', 'types', 'devices', 'commands', 'type_id', 'device_id']));
        }
    }

    /**
     * Display a listing of the command  with control key
     *
     * @param Request $request
     * @return View
     */
    public function commandList(Request $request)
    {
        $user = session('user');
        $id = $user['id'];
        $userDevices = User::find($id )
            ->devices;
        $devices = $userDevices -> where('type_id', '>',100);
        $filterDevices = $devices->where('make_command', 1);

        $device = null;
        $device_id = null;

        if($devices->count() == 0 ){
            return redirect('/node/devices')->withErrors(['尚未註冊裝置, 請按[新增]註冊']);
        }
        $command = $request['command'];
        if($command == null) {
            $make_command = 0;//Default for common command
            $device = $devices ->first();
            $device_id = $device->id;
        } else {
            //$arr = preg_split('.', $command, -1, PREG_SPLIT_NO_EMPTY);
            $arr  = explode('.',$command);
            $make_command = $arr[0];
            $device_id = $arr[1];
            $device = Device::find($device_id );
        }
        $make_command = (int)$make_command;

        if($make_command == 0) {
            $commands = DB::table('commands')
                ->where('type_id', '>',100)
                ->whereNull('device_id')
                ->get();
        } else {
            $commands = DB::table('commands')
                ->where('type_id', 11)
                ->where('device_id', $device_id)
                ->get();
            //Get devices with private command
        }

        $types =  Type::all();

        //Jason modify for get setting relation on 2020.7.3

        $url = env('APP_URL').'/send_control?key=';
        //$url = 'http://localhost:8080/send_control?key=';
        //將命令轉為control key
        if($device != null) {
            $mac = $device->macAddr;
            foreach ($commands  as $command) {
                $secret = $mac.':'.$command->id;
                $tmp = base64_encode($secret);
                $command->ctlKey = $url.$tmp;
                //dd($tmp);
                //dd(base64_decode($tmp));
            }
        }
        /*if($device_id != null) {
            return redirect('/node/commandList/'.$device_id);
        }*/
        return view('nodes.commandList', compact(['user', 'devices', 'filterDevices', 'types', 'commands', 'device_id']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function update(Request $request)
    {
        $input = $request->all();
        $setCommand = $input['setCommand'];
        if($setCommand == null) {
            return back();
        }
        $data = json_decode($setCommand);

        if($data->id == 0) {
            $command = new Command();
        } else {
            $id = $data->id;
            $command = Command::find($id);
            $command->updated_at = now();
        }

        $command->type_id = $data->type_id;
        $command->cmd_name = $data->cmd_name;
        $command->command = $data->command;
        if($data->device_id)
            $command->device_id = $data->device_id;
        $command->save();

        return back();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *  @return View
     */
    public function destroy(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        Command::where('id', $id)->delete();
        return back();
    }

    public function getCtlkeyList($mac, $cmdList)
    {
        $app_url = env('APP_URL');
        $url = $app_url.'/send_control?key=';
        foreach ($cmdList  as $cmd) {
            $secret = $mac.':'.$cmd->id;
            $tmp = base64_encode($secret);
            $cmd->ctlKey = $url.$tmp;
        }
    }

    /**
     * Display a listing of the command list.
     *
     * @param Request $request
     * @return View
     */
    public function myCommand(Request $request)
    {
        $user = session('user');
        /*$commands = Command::all();
        foreach ($commands as $command) {
            if($command->device == null) {
                $command->delete();
            }
        }*/


        $id = $user['id'];
        $userDevices = null;
        $devices = null;
        if($user->role_id<3) {
            if(isset($request['search'])) {
                $search = $request['search'];
                $devices = Device::Where('device_name', 'like', '%' . $search . '%')
                    ->where('type_id', '>',100)
                    ->get();
            } else {
                $devices = Device:: where('type_id', '>',100)
                    ->get();
            }
        } else {
            if(isset($request['search'])) {
                $search = $request['search'];
                $devices = Device::Where('device_name', 'like', '%' . $search . '%')
                    ->where('user_id', $user['id'])
                    ->where('type_id', '>',100)
                    ->get();
            } else {
                $devices = Device:: where('type_id', '>',100)
                    ->where('user_id', $user['id'])
                    ->get();
            }
        }

        $device = null;

        if($devices->count() == 0 ){
            return redirect('/node/devices')->withErrors(['尚未註冊裝置, 請按[新增]註冊']);
        }
        $device_id = $request['device_id'];

        if($device_id == null) {
            $device = $devices ->first();
            $device_id = $device->id;
        } else {
            $device_id = (int)$device_id;
            $device = Device::find($device_id );
        }
        $type_id = $device->type_id;

        $commands = DB::table('commands')
            ->where('type_id', '>',100)
            ->where('device_id', $device_id)
            ->whereNotNull('sequence')
            ->orderBy('sequence','asc')
            ->get();

        $types =  Type::all();

        //Jason modify for get setting relation on 2020.7.3

        $url = env('APP_URL').'/send_control?command=';
        //$url = 'http://localhost:8080/send_control?key=';
        //將命令轉為control key
        if($device != null) {
            $mac = $device->macAddr;
            foreach ($commands  as $command) {
                $command->ctlKey = $url.$command->command;
            }
        }
        /*if($device_id != null) {
            return redirect('/node/commandList/'.$device_id);
        }*/
        return view('nodes.myCommand', compact(['user', 'devices', 'types', 'commands', 'device_id', 'type_id']));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @return mixed
     */
    public function editCommand(Request $request)
    {
        $input = $request->all();
        $id =  (int)$input['id'];
        if($id == 0) {//New setting
            $command = new Command();
            $command->device_id = (int)$input['device_id'];
            $command->type_id = (int)$input['type_id'];
            $command->sequence = (int)$input['sequence'];
        } else {
            $command = Command::find($id);
        }
        if(isset($input['check']) ) {
            $command->command = getCommand($command->device_id, $command->sequence);
        }

        $command->cmd_name = $input['cmd_name'];
        $command->save();


        return redirect('/node/myCommand?device_id='.$command->device_id);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     *  @return View
     */
    public function delMyCommand(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        Command::where('id', $id)->delete();
        return back();
    }
}

function getCommand($device_id, $cmd){
    $mId = $device_id;
    $nId =  $cmd;
    if(gettype($device_id) == "integer"){
        $mId = (string)$device_id;
    }
    if(gettype($cmd) == "integer"){
        $nId = (string)$cmd;
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
    $key = $key.':'.$nId.':'.$mId.':';
    for($i = 0; $i < $len2; $i++){ //總共取 幾次.
        $key .= $word[rand() % $len];//隨機取得一個字元
    }
    return base64_encode($key);//回傳亂數帳號
}
