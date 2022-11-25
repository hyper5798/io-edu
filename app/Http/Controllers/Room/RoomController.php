<?php

namespace App\Http\Controllers\Room;

use App\Constant\UserConstant;
use App\Http\Controllers\Admin\CommonController;
use App\Models\Cp;
use App\Models\Device;
use App\Models\Network;
use App\Models\Room;
use App\Models\Setting;
use App\Models\Type;
use App\Models\User;
use App\Repositories\CpRepository;
use App\Repositories\MemberRepository;
use App\Repositories\SettingRepository;
use App\Services\AppService;
use App\Services\DeviceService;
use App\Services\SettingService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RoomController extends CommonController
{
    private $memberRepository, $settingRepository,$deviceService, $appService,
        $cpRepository,$settingService,$userService;

    public function __construct(
        MemberRepository  $memberRepository,
        SettingRepository $settingRepository,
        CpRepository      $cpRepository,
        DeviceService     $deviceService,
        AppService        $appService,
        SettingService    $settingService,
        UserService       $userService
    )
    {
        $this->memberRepository  = $memberRepository;
        $this->settingRepository = $settingRepository;
        $this->cpRepository      = $cpRepository;
        $this->deviceService     = $deviceService;
        $this->appService        = $appService;
        $this->settingService    = $settingService;
        $this->userService       = $userService;
    }

    /**
     * Home of room.
     * @param Request $request
     * @return \Illuminate\Routing\Redirector|View
     */
    public function index(Request $request)
    {
        $input = $request->all();
        session(['link'=> 'room']);
        //Jason add for record current url

        $user = session('user');
        $target = session('target');
        $target['url'] = url()->full();
        //Jason add cp & user -- start
        //路由參數
        $cp_id = (int)$request->input('cp_id', 0);
        $user_id = (int)$request->input('user_id', 0);
        //公司下拉選單(Super Admin切換公司)
        $cps = $this->cpRepository->all();
        //helper function checkCpId返回array : cp_id & isChange
        $check = checkCpId($cp_id);
        $cp_id = $check['cp_id'];
        //帳戶下拉選單(Super Admin切換帳戶)
        $users =$this->userService->getBy('cp_id', $cp_id);
        //helper function getUserId
        $user_id = getUserId($user_id, $users ,$check['isChange']);

        //Jason add cp & user -- end


        $rooms = null;
        //Add user_id for super admin to selected.
        if ($user->role_id == 8 || $user->role_id ==9) {//Group user
            //取得 rooms belong group
            $rooms = $this->memberRepository->getGroupRoomByUserId($user_id);
            $target['rooms'] = $rooms;
            session(['target'=> $target]);
        } else {//Admin user
            $rooms = Room::where('user_id', $user_id) -> get();
            $target['rooms'] = $rooms;
            session(['target'=> $target]);
            if(count($rooms)==0) {
                //return redirect('/room/userBinding');
                session(['room' => null]);
                $devices = $this->deviceService->getBy('user_id', $user_id);
                if($devices->count()>0) {
                    $device = $devices->first();
                    $tmp = 'device_id='.$device->id;
                    if( $device->type_id == 103 ) {//農業機器人
                        return redirect('/room/agriBot?' . $tmp);
                    }else if($device->type_id == 102 ) {//無人船
                        return redirect('/room/usv?' . $tmp);
                    }else if($device->type_id == 104 ) {//
                        return redirect('/room/thruster?' . $tmp);
                    }
                } else {
                    return redirect('/room/userBinding');
                }
            }
        }

        //測試用不可刪除
        $develops = null;
        $modules = null;
        //測試用不可刪除

        //取得用戶所有場地的任務
        $arr = $this->memberRepository->getRoomMissionByRoom($rooms);
        return view('room.index', compact(['users','user_id','user', 'rooms', 'arr', 'cps', 'cp_id', 'develops', 'modules']));
    }

    /**
     * Display the specified resource. (來自我的場地選擇後)
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|View
     */
    public function developShow(int $id)
    {
        $user = session('user');
        $groups = $user['groups'];
        $room_id = $id;
        $room = Room::find($id);
        //$test = $room->toArray();
        $missions = $this->memberRepository->getGroupMissionByRoomId($id);
        $devices = Device::whereIn('macAddr',array_column($missions->toArray(), 'macAddr'))->get();

        //$room_length = $user['room_length'];
        $target = session('target');
        $target['room'] = $room;
        $target['missions'] = $missions;
        $target['devices'] = $devices;
        $target['mac'] = $devices[0]->macAddr;
        session(['target' => $target]);
        session(['room' => $room]);
        //102: USV
        if(count($devices) > 0) {
            $device = $devices[0];
            $tmp = 'room_id='.$room_id.'&device_id='.$device->id;
            if( $device->type_id == 103 ) {//農業機器人
                return redirect('/room/agriBot?' . $tmp);
            }else if($device->type_id == 102 ) {//無人船
                return redirect('/room/usv?' . $tmp);
            }else if($device->type_id == 104 ) {//
                return redirect('/room/thruster?' . $tmp);
            }
        }
        return redirect('/room/index')->withErrors('無法到指定頁面，請檢查裝置類型!');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function moduleShow(int $id)
    {
        $user = session('user');
        $groups = $user['groups'];
        $room_id = $id;
        $room = Room::find($id);
        $test = $room->toArray();
        $missions = $this->memberRepository->getGroupMissionByRoomId($id);
        $user->room = $room;
        $user->missions = $missions;
        session(['user' => $user]);

        return view('room.module.showMissions', compact(['user', 'missions', 'room_id', 'room', 'test']));
    }

    public function userBinding(Request $request)
    {
        $input = $request->all();
        //Jason add for record current url

        $user = session('user');
        $target = session('target');
        $target['url'] = url()->full();
        //Jason add cp & user -- start
        //路由參數
        $cp_id = (int)$request->input('cp_id', 0);
        $user_id = (int)$request->input('user_id', 0);
        //公司下拉選單(Super Admin切換公司)
        $cps = $this->cpRepository->all();
        //helper function checkCpId返回array : cp_id & isChange
        $check = checkCpId($cp_id);
        $cp_id = $check['cp_id'];
        //帳戶下拉選單(Super Admin切換帳戶)
        $users =$this->userService->getBy('cp_id', $cp_id);
        //helper function getUserId
        $user_id = getUserId($user_id, $users ,$check['isChange']);

        //Jason add cp & user -- end
        $rooms = $target['rooms'];
        $room_id = array_key_exists('room_id', $input) ? (int)$input['room_id'] : null;

        if(count($rooms) == 0) {//User bind room check
            //User bind room only one.
            $room = Room::where('user_id', $user_id)->first();
        } else if(count($rooms) > 0 && $room_id != null) {
            $room = Room::find($room_id);
        } else {
            $room = $rooms[0];
        }

        $user = session('user');
        $networks = Network::get(['id', 'network_name']);
        $groups = $user->group;

        $devices = DB::table('devices')
            ->join('users', 'devices.user_id', '=', 'users.id')
            ->select('devices.*', 'users.name', 'users.email')
            ->where('devices.user_id', $user_id)->get();

        //裝置透過關聯取出任務，再取出群組，再取出用戶
        $deviceUsers = array();
        $deviceRooms = array();
        $group = null;
        foreach($devices as $device) {
            $device = Device::find($device->id);
            $gUsers = array();
            $mission =  $device->mission;
            if($mission != null) {
                $group = $mission->groups->first();
                $deviceRooms[$device->macAddr] = $mission->room_id;
            }

            if($group != null) {
                $gUsers = $group->users;
                $deviceUsers[$device->macAddr] = $gUsers;
            }
         }
        if($user->role_id < 3 || count($rooms) > 1) {
            $cps = Cp::all();
        } else {
            $cps = Cp::where('id',$user['cp_id'])->get();
        }

        if(count($devices) == 0) {
            return view('room.userBinding', compact(['deviceRooms','rooms','users', 'user_id','cps', 'cp_id', 'user', 'room', 'devices', 'networks', 'groups', 'deviceUsers']))->withErrors(['尚未綁定裝置!']);
        }



        // dd($devices);
        return view('room.userBinding', compact(['deviceRooms','rooms','users', 'user_id','cps', 'cp_id','user', 'room', 'devices', 'networks', 'groups', 'deviceUsers']));
    }

    public function editUserRoom(Request $request)
    {
        $user = session('user');
        $input = $request->all();

        $cp_id = $user->cp_id;
        $id = (int)$input['id'];

        if($id>0)
            $room = Room::where('id', $id)->first();
        else {
            $room = new Room;
            $room->cp_id = $cp_id;
            $room->user_id = $user['id'];
        }
        if(array_key_exists('room_name', $input))
            $room->room_name = $input['room_name'];

        $room->save();

        return back();
    }

    public function editUserDevice(Request $request)
    {
        $input = $request->all();
        $validator = $this->deviceService->getDeviceValidator($input);
        if(count($validator->errors()->all()) > 0){
            session(['error'=> $validator->errors()]);
            return back()->withErrors($validator);
        }
        $cp_id = getTargetCpId();
        $user_id = getTargetUserId();
        $device = $this->deviceService->createOrUpdate($input, $cp_id);
        //檢查是否為開發版&是否已複製應用及設定
        $publicDevice = $this->deviceService->findPublicDeviceByTypeId($device->type_id);
        $needCopy = $this->deviceService->checkForDevelopController($device, $publicDevice);
        if($needCopy) {
            $this->appService->checkAndCopyPublicApps($publicDevice, $device, $user_id);
            $this->settingService->copyPublicSetting($publicDevice, $device->id);
        }

        $type_id = (int)$input['type_id'];
        $room_id = (int)$input['room_id'];

        $user = session('user');

        $type = Type::where('type_id',$type_id)->first();
        if($user_id == 0) {
            $user_id = $user['id'];
        }
        if($room_id == 0) {
            $room = new Room;
            $room->cp_id = $user->cp_id;
            $room->user_id = $user_id;
            $room->room_name = $input['room_name'];
            if($type_id < 100 || $type_id >200) {
                $room->type = 'module';
            } else {
                $room->type = 'develop';
            }
            if(isset($type->work) == true) {
                $room->work = $type->work;
            } else {
                $room->work = 'demo';
            }

            $room->save();
            $room_id = $room->id;
        } /*else { //暫停更新用戶場域功能
            $room = Room::find($room_id);
            $room->room_name = $input['room_name'];
            $room->save();
        }*/
        //升級用戶權限(有購買控制器用戶)
        if($user->role_id == 11) {
            $user = User::find($user_id);
            $user->role_id = 10;
            $user->save();
            session(['user' => $user]);
        }

        //Create device,group, mission, group_user, group_mission, device_mission

        $this->memberRepository->bindingGroupDriver($device, $room_id);

        return back();
    }

    public function delUserDevice(Request $request)
    {
        $input = $request->all();
        $device_id = (int)$input['id'];
        $device = $this->deviceService->find($device_id);
        //移除裝置的應用(服務會做刪除判斷)
        $this->appService->destroyOfDevice($device);
        //移除裝置的設定
        $this->settingService->destroyOfDevice($device);
        $this->memberRepository->removeGroupDevice($device_id);
        return back();
    }

    public function bindingUser(Request $request)
    {
        $user = session('user');//Admin user
        $input = $request->all();
        $user_id = (int)$input['id'];
        $user_name = $input['name'];
        $device_id = (int)$input['device_id'];
        $email = $input['email'];
        $password = $input['password'];

        $editUser = null;

        if($user_id>0)
            $editUser = User::where('id',  $user_id)->first();
        else {
            $editUser = User::where('email', $email) -> first();
            if($editUser == null) {
                $editUser = new User;//Share user
                $editUser->active = 1;
                $editUser->provider_user_id = $user['id'];
                $editUser->cp_id =  $user->cp_id;
                $editUser->password = bcrypt($password);
                $editUser->email = $email;
                $editUser->role_id = 11;
            }
        }
        $editUser->name =  $user_name;
        $editUser->save();

        $this->memberRepository->bindingDriverUser($device_id, $editUser->id);

        return back();
    }

    public function delBindUser(Request $request)
    {
        $input = $request->all();
        $user_id = (int)$input['id'];
        $device_id = (int)$input['device_id'];
        $this->memberRepository->removeBindUser($device_id, $user_id);
        return back();
    }

    /**
     * Display a listing of the gas bottle.
     * @param Request $request
     * @return View
     */
    public function setEmail(Request $request)
    {
        $input = $request->all();

        $user = session('user');
        $target = session('target');
        $user_id = $target['user_id'];

        $url = array_key_exists('url', $target) ? $target['url'] : url("/node/myDevices?link=develop");


        //用來之後若須根據不同公司進行設定使用
        $cp_id = array_key_exists('cp_id', $input) ? (int)$input['cp_id'] : $user->cp_id;

        $NOTIFY_MAX = UserConstant::NOTIFY_EMAIL_MAX;
        if($user->role_id < 3) {
            $cps = Cp::all();
        } else {
            $cps = Cp::where('id',$user['cp_id'])->get();
        }
        $setting = $this->settingRepository->getUserSetting($user_id, 'email');
        if($setting == null) {
            $sets = array();
        } else {
            $sets = $setting->set;
        }

        return view('room.setEmail', compact(['user', 'sets', 'cps', 'cp_id', 'url', 'NOTIFY_MAX']));
    }

    /**
     * Update gas bottle with admin user in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function editEmail(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $target = session('target');
        $user_id = $target['user_id'];
        $user = session('user');
        $setting = $this->settingRepository->getUserSetting($user_id, 'email');

        if($setting == null ) {
            $setting = new Setting;
            $setting->user_id = $user_id;
            $setting->field = 'email';
        }

        if($input['set'] != null) {
            if($input['set'] == '[]') {
                $setting->set = json_decode($input['set']);
            } else {
                $setting->set = json_decode($input['set']);
            }
        }

        $setting->save();

        return back();
    }
}
