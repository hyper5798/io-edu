<?php

namespace App\Repositories;

use App\Models\App;
use App\Models\Device;
use App\Models\DeviceMission;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\GroupMission;
use App\Models\Mission;
use App\Models\Node;
use App\Models\NodeRule;
use App\Models\NodeScripts;
use App\Models\Product;
use App\Models\Report;
use App\Models\Room;
use App\Models\Setting;
use App\Models\User;

class MemberRepository
{
    protected $user;
    protected $room;
    /**
     * UserRepository constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function all()
    {
        return $this->user->all();
    }

    public function getByUserId($id)
    {
        return $this->user::where('user_id',$id)->get();
    }

    //使用者得到群組及場域使用權限room_limit, 例如 場域id = 1,權限為 room_limit[1]
    public function getGroupRoomByUserId($id)
    {
        $user = session('user');

        $arr = array();
        $room_limit = array();

        foreach ($user->groups as $group) {
            array_push($arr, $group->room_id);
            $room_limit[$group->room_id] = $group->pivot->group_role_id;
        }

        //dd($arr, $room_limit);

        $rooms = Room::whereIn('id', $arr)->get();
        //$user->groups = $user->groups;
        $user->room_limit =  $room_limit;
        $user->room_lenth = count($rooms);
        session(['user' => $user]);

        return $rooms;
    }

    public function getRoomByCpId($cp_id)
    {
        $rooms = null;
        if($cp_id == null) {
            $rooms = Room::all();
        } else {
            $rooms = Room::where('cp_id',$cp_id)->get();
        }
        $user = session('user');
        $user->room_lenth = count($rooms);
        session(['user' => $user]);
        return $rooms;
    }

    public function getRoomMissionByRoom($rooms)
    {
        $arr = array();
        foreach ($rooms as $room) {
            //$m = Mission::where('room_id', $rooms[$i]->id)->get();
            $m = $this->getGroupMissionByRoomId($room->id);
            if($room->work == 'demo') {
                $arr[$room->id] = $m;
            } else {
                $arr1 = array_column($m->toArray(), 'macAddr');
                $devices = Device::whereIn('macAddr', $arr1)->get();
                $arr[$room->id] = $devices;
            }
        }
        return $arr;
    }

    /*
     * To get mission
     * */
    public function getGroupMissionByRoomId($id)
    {
        $user = session('user');
        if($user->role_id > 7) {
            $groupUser = GroupUser::where('user_id', $user['id'])->get();
            $idArr =array_column($groupUser->toArray(), 'group_id');
            $groups = Group::whereIn('id', $idArr)->get();
            $arr = array_column($groups->toArray(), 'mission_id');
            $missions = Mission::where('room_id', $id)
                ->whereIn('id', $arr)
                ->orderby('sequence', 'asc')
                ->get();
        }  else {
            $missions = Mission::where('room_id', $id)
                ->orderby('sequence', 'asc')
                ->get();
        }

        return $missions;
    }

    //裝置mac做群組及任務名稱
    public function bindingGroupDriver($device, $room_id) {

        $missions = Mission::where('room_id', $room_id)->get();
        $check = Mission::where('macAddr', $device->macAddr)->count();
        if($check > 0) {
            return;
        }

        //Add mission
        $mission = new Mission;
        //$mission->mission_name = $device->macAddr;
        $mission->mission_name = $device->device_name;
        $mission->room_id = $room_id;
        $mission->sequence = count($missions)+1;
        $mission->device_id = $device->id;
        $mission->macAddr = $device->macAddr;
        $mission->user_id = $device->user_id;
        $mission->save();

        //Add group and user
        $group = new Group;
        $group->name = $device->macAddr;
        $group->cp_id = $device->cp_id;
        $group->mission_id = $mission->id;
        $group->room_id = $room_id;
        $group->save();

        $groupUser = new GroupUser;
        $groupUser->group_id = $group->id;
        $groupUser->user_id = $device->user_id;
        $groupUser->cp_id = $device->cp_id;
        $groupUser->group_role_id = 8;//Group admin
        $groupUser->save();

        $groupMission = new GroupMission;
        $groupMission->group_id = $group->id;
        $groupMission->mission_id = $mission->id;
        $groupMission->save();

        $deviceMission = new DeviceMission;
        $deviceMission->device_id = $device->id;
        $deviceMission->mission_id = $mission->id;
        $deviceMission->save();
    }

    public static function removeGroupDevice($device_id) {

        $device = Device::find($device_id);
        $mac = $device->macAddr;

        $Mission = Mission::where('macAddr', $mac)->first();
        if($Mission) {
            $groups = Group::where('mission_id', $Mission->id)->get();
            DeviceMission::where('mission_id', $Mission->id)->delete();
            $Mission->delete();
            //Delete group and user
            //群組以裝置mac自動命名
            //$groups = Group::where('name', $mac)->get();
            foreach ($groups as $group) {
                GroupUser::where('group_id', $group->id)->delete();
                GroupMission::where('group_id', $group->id)->delete();
                $group->delete();
            }
        }

        if($device->type_id > 100 && $device->type_id < 200) {
            Report::where('macAddr', $device->macAddr)->delete();
        }
        if($device->type_id === 99 || $device->type_id > 199) {//控制模組
            $node = Node::where('node_mac', $device->macAddr)->first();
            if($node!=null && array_key_exists('change_mac',$input)) {
                Node::where('node_mac', $input['change_mac'])->delete();
                $change_device = Device::where('macAddr', $input['change_mac'])->first();
                $node->node_mac = $change_device->macAddr;
                $node->node_name = $change_device->device_name;
                $node->save();
            } else if($node!=null) {
                NodeScripts::where('node_id', $node->id)->delete();
                $node->delete();
            }
        }

        if($device->type_id < 99) {
            $user = session('user');
            $nodes = Node::where('user_id', $user['id'])->get();
            foreach ($nodes as $node) {
                $inputs = $node->inputs;
                $outputs = $node->outputs;
                if ($inputs!= null && ($key = array_search($mac, $inputs)) !== false) {
                    unset($inputs[$key]);
                    $node->inputs = $inputs;
                    $node->save();
                }
                if ($outputs !=null && ($key = array_search($mac, $outputs)) !== false) {
                    unset($outputs[$key]);
                    $node->outputs = $outputs;
                    $node->save();
                }
            }
        }

        $device->delete();
    }

    public function bindingDriverUser($device_id, $user_id) {
        $device = Device::find($device_id);

        $mission =  $device->mission;
        if($mission != null)
            $group = $mission->groups->first();
        $check = GroupUser::where('group_id', $group->id)
        ->where('user_id', $user_id)->get();

        if(count($check)>0) {
            return;
        }

        $editUser = User::find($user_id);
        $groupUser = new GroupUser;
        $groupUser->group_id = $group->id;
        $groupUser->user_id = $editUser->id;
        $groupUser->cp_id = $device->cp_id;
        $groupUser->group_role_id = 9;//Group user
        $groupUser->save();
    }

    public function removeBindUser($device_id, $user_id) {

        $del_user = User::find($user_id);
        $mission = Device::find($device_id)->mission;
        $group = $mission->groups()->first();
        GroupUser::where('group_id', $group->id)
            ->where('user_id', $user_id)
            ->delete();

        $user = session('user');
        if($del_user->provider_user_id != null) {
            $provider = (int)$del_user->provider_user_id;
            if($provider == $user['id'])
                $del_user->delete();
        }
    }

    public static function copyPublicSetting($type_id, $device_id) {
        //Check New device setting is exist or not
        $checks = Setting::where("device_id", $device_id)->count();
        if($checks>0) {
            return;
        }

        $device = Device::where("type_id", $type_id)
            ->where("isPublic",1)
            ->first();
        if($device != null) {
            $sets = Setting::where("device_id", $device->id)->get();
            foreach ($sets as $set) {
                $setting = new Setting;
                if($device_id != null) {
                    $setting->device_id = $device_id;
                }

                $setting->field = $set->field;
                $setting->set = $set->set;
                if(property_exists($set, "app_id"))  {
                    $setting->app_id = $set->app_id;
                }
                $setting->save();
            }
        }
    }

    public function copyPublicApp($type_id, $device_id) {
        //Check New device setting is exist or not
        $checks = App::where("device_id", $device_id)->count();
        if($checks>0) {
            return;
        }
        $public = Device::where("type_id", $type_id)
            ->where("isPublic",1)
            ->first();
        if($public != null) {

            $apps = App::where("device_id", $public->id)->get();
            if($apps->count()>0) {
                //$device = Device::where("device_id", $device->id)->first();
                $device = $this->getDeviceById($device_id);

                foreach ($apps as $oldApp) {
                    $newApp = new App;
                    $newApp->name =  $oldApp->name;
                    $newApp->device_id =  $device_id;
                    $newApp->macAddr =  $device->macAddr;
                    $newApp->key_label =  $oldApp->key_label;
                    $newApp->key_parse =  $oldApp->key_parse;
                    $newApp->sequence =  $oldApp->sequence;
                    $newApp->save();
                    $newApp->api_key = getAPIkey($newApp->id, $device->user_id);
                    $newApp->save();
                }
            }

        }
    }

    public function getDeviceById($id)  {
        return Device::find($id);
    }
}

/**
 * Generate api key by app id and team id.
 *
 * @param integer $app_id
 * @param integer id
 *  @return string api key
 */
function getAPIkey($app_id, $id){
    $mId = $app_id;
    $nId =  $id;
    if(gettype($app_id) == "integer"){
        $mId = (string)$app_id;
    }
    if(gettype($id) == "integer"){
        $nId = (string)$id;
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
