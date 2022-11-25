<?php

namespace App\Http\Controllers\Api;

use App\Models\App;
use App\Models\Device;
use App\Models\DeviceMission;
use App\Models\Group;
use App\Models\GroupMission;
use App\Models\GroupRoom;
use App\Models\GroupUser;
use App\Models\Mission;
use App\Models\Product;
use App\Models\Room;
use App\Models\Setting;
use App\Models\Type;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeviceApiController extends Controller
{
    public function removeRoom(Request $request)
    {
        $input = $request->all();
        if (array_key_exists('token', $input)) {
            $token = $input['token'];
            $user = User::where('remember_token', $token)->get();
            if (count($user) == 0) {
                return response('驗證失敗!', 401);
            }
        } else {
            return response('驗證失敗!', 401);
        }
        $device_id = (int)$input['device_id'];
        $room_id = (int)$input['room_id'];
        $missions = Mission::where('room_id', $room_id)->get();
        //group_rooms & group_missions 可刪除
        $group_rooms = GroupRoom::where('room_id', $room_id)->delete();
        $group_missions = GroupMission::whereIn('mission_id', array_column($missions->toArray(), 'id'))->delete();
        $groups = Group::where('room_id',$room_id)->get();

        if($groups!=null && count($groups)>0) {
            $t3 =  array_column($groups->toArray(), 'id');
            $group_users = GroupUser::whereIn('group_id', $t3)->delete();
            foreach($groups as $item) {
                $item->delete();
            }
        }
        if($missions !=null && count($missions)>0) {
            $t4 =  array_column($missions->toArray(), 'id');
            $device_missions = DeviceMission::whereIn('mission_id', $t4)->delete();

            foreach($missions as $item) {
                $item->delete();
            }
        }
        $room = Room::find($room_id)->delete();
        $devices = Device::where('id', $device_id)->delete();

        //delete $group_users->$groups->$group_missions->$group_rooms->missions->room

        return response('刪除成功!', 200);
    }

    public function searchProduct(Request $request)
    {
        $input = $request->all();
        if (array_key_exists('token', $input)) {
            $token = $input['token'];
            $user = User::where('remember_token', $token)->get();
            if (count($user) == 0) {
                return response('驗證失敗!', 401);
            }
        } else {
            return response('驗證失敗!', 401);
        }
        $mac = $input['mac'];
        $product = Product::Where('macAddr', 'like', '%' . $mac . '%')->first();
        if($product != null) {
            $type = Type::where("type_id", $product->type_id)->first();
            $product->category = $type->category;
        }

        return response($product , 200);
    }

    public function searchDevice(Request $request)
    {
        $input = $request->all();
        if (array_key_exists('token', $input)) {
            $token = $input['token'];
            $user = User::where('remember_token', $token)->get();
            if (count($user) == 0) {
                return response('驗證失敗!', 401);
            }
        } else {
            return response('驗證失敗!', 401);
        }
        $mac = $input['mac'];

        $device = Device::Where('macAddr', 'like', '%' . $mac . '%')->first();
        if($device != null) {
            $type = Type::where("type_id", $device->type_id)->first();
            $device->category = $type->category;
        }
        $result = array("route"=>$input['route'] , "value"=> $device);

        return response($result , 200);
    }

    public function searchRoom(Request $request)
    {
        $input = $request->all();
        if (array_key_exists('token', $input)) {
            $token = $input['token'];
            $user = User::where('remember_token', $token)->get();
            if (count($user) == 0) {
                return response('驗證失敗!', 401);
            }
        } else {
            return response('驗證失敗!', 401);
        }
        $user_id = $input['user_id'];
        $mac = $input['mac'];

        $rooms = Room::where('user_id', $user_id)->get();
        $mission = Mission::where("user_id", $user_id)
            ->where("macAddr", $mac)
            ->first();
        $room_id = 0;
        if($mission != null) {
            $room_id = $mission->room_id;
        }

        $result = array("route"=>$input['route'] , "rooms"=> $rooms, "room_id"=>$room_id);

        return response($result , 200);
    }

    public function editDeviceSetting(Request $request)
    {
        $input = $request->all();
        if (array_key_exists('token', $input)) {
            $token = $input['token'];
            $user = User::where('remember_token', $token)->get();
            if (count($user) == 0) {
                return response('驗證失敗!', 401);
            }
        } else {
            return response('驗證失敗!', 401);
        }
        $user_id = $input['user_id'];
        $mac = $input['mac'];

        $rooms = Room::where('user_id', $user_id)->get();
        $mission = Mission::where("user_id", $user_id)
            ->where("macAddr", $mac)
            ->first();
        $room_id = 0;
        if($mission != null) {
            $room_id = $mission->room_id;
        }

        $result = array("route"=>$input['route'] , "rooms"=> $rooms, "room_id"=>$room_id);

        return response($result , 200);
    }

    public function editSetting(Request $request)
    {
        $input = $request->all();
        if (array_key_exists('token', $input)) {
            $token = $input['token'];
            $user = User::where('remember_token', $token)->get();
            if (count($user) == 0) {
                return response('驗證失敗!', 401);
            }
        } else {
            return response('驗證失敗!', 401);
        }

        $field = $input['field'];

        $setting = null;

        if (array_key_exists('app_id',$input ) ){
            $setting = Setting::where('app_id',  $input['app_id'])
                ->where('field', $field)->first();
        } else  if(array_key_exists('device_id',$input )) {
            $setting = Setting::where('device_id', $input['device_id'])
                ->where('field', $field)->first();
        }
        if($setting == null) {

            $setting = new Setting;
            $setting->field = $input['field'];

            if (array_key_exists('app_id',$input ) ){
                $setting->app_id =  (int)$input['app_id'];
                $app = App::find($input['app_id']);
                $setting->device_id = $app->device_id;
            } else {
                $setting->device_id = (int)$input['device_id'];
            }

            $setting->field = $field;
        }

        $setting->set = json_decode($input['setStr']);
        $setting->save();

        return response( 'Success', 200);
    }

    public function deviceVerify(Request $request)
    {
        $input = $request->all();
        if(!checkToken($input)) {
            return response()->json(['code' => 401, 'message' => "Auth fail"], 200);
        }
        $mac = $input['mac'];
        $product = Product::where('macAddr', $mac)->first();
        if($product == null) {
            return response()->json(['code' => 403, 'message' => "Not find"], 200);
        }
        $device = Device::where('macAddr', $mac)->first();
        if($device != null) {
            $user = User::find($device->user_id);
            $user->date = $device->updated_at;
            //return response( $user, 405);
            return response()->json(['code' => 405, 'message' => "Not allowed",'user'=>$user], 200);
        }
        return response()->json(['code' => 200, 'result' => "Not allowed", 'product'=>$product ], 200);
    }
}

function checkToken($input) {
    if (array_key_exists('token', $input)) {
        $token = $input['token'];
        $user = User::where('remember_token', $token)->get();
        if (count($user) == 0) {
            return false;
        }
    } else {
        return false;
    }
    return true;
}
