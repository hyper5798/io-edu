<?php

namespace App\Http\Controllers\Node;


use App\Constant\UserConstant;
use App\Models\Product;
use App\Models\Room;
use App\Models\User;
use App\Repositories\AnnounceRepository;
use App\Repositories\CpRepository;
use App\Repositories\SettingRepository;
use App\Services\AppService;
use App\Services\DeviceService;
use App\Services\NodeScriptService;
use App\Services\NodeService;
use App\Services\SettingService;
use App\Services\TypeService;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\Repositories\MemberRepository;
use App\Models\Network;
use Illuminate\View\View;

class DeviceController extends Common4Controller
{
    private $memberRepository, $settingRepository, $userService, $typeService, $deviceService,
        $appService, $nodeService, $nodeScriptService, $cpRepository, $settingService;

    public function __construct(
        TypeService       $typeService,
        DeviceService     $deviceService,
        NodeService       $nodeService,
        NodeScriptService $nodeScriptService,
        UserService       $userService,
        AppService        $appService,
        SettingRepository $settingRepository,
        SettingService    $settingService,
        CpRepository      $cpRepository,
        MemberRepository  $memberRepository
    )
    {
        $this->typeService       = $typeService;
        $this->deviceService     = $deviceService;
        $this->nodeService       = $nodeService;
        $this->nodeScriptService = $nodeScriptService;
        $this->userService       = $userService;
        $this->appService        = $appService;
        $this->cpRepository      = $cpRepository;
        $this->settingService    = $settingService;
        $this->memberRepository  = $memberRepository;
        $this->settingRepository = $settingRepository;
    }
    /**
     * Display all of user's devices.
     *     * @param Request $request

     * @return View
     */
    public function myDevices(Request $request)
    {
        $input = $request->all();

        $link = getLink($input);
        //登入者
        $user = session('user');
        //公司下拉選單(Super Admin切換公司)
        $cps = $this->cpRepository->all();
        //helper function checkCpId返回array : cp_id & isChange
        $cp_id = (int)$request->input('cp_id', 0);
        $user_id = (int)$request->input('user_id', 0);
        $check = checkCpId($cp_id);
        $cp_id = $check['cp_id'];
        //帳戶下拉選單(Super Admin切換帳戶)
        $users =$this->userService->getBy('cp_id', $cp_id);
        //helper function getUserId
        $user_id = getUserId($user_id, $users ,$check['isChange']);
        $bindUser = $this->userService->find($user_id);
        //網路下拉列表
        $networks = Network::get(['id', 'network_name']);
        //裝置類型下拉列表
        $types =  $this->typeService->all();

        //搜尋控制器名稱
        $device_name = $request->input('device_name', null);
        //開發版列表
        $devices = $this->deviceService->getUserDevelops($user_id, $device_name);
        //控制模組列表
        $controllers  = $this->deviceService->getUserModules($user_id, $device_name);
        //輸入模組
        $inputs = $this->deviceService->getUserInputs($user_id);
        //輸出模組
        $outputs = $this->deviceService->getUserOutputs($user_id);
        //判斷控制模組的node不存在就新增(node: 控制模組輸出入裝置設置)
        $this->deviceService->checkUserNodes($controllers, $user_id);
        //取得控制模組的所有node，convert "self" to real mac 多合一模組
        $nodes = $this->deviceService->getUserNodes($user_id);

        $token = $user->remember_token;
        return view('nodes.myDevices', compact(['bindUser','users','user_id','cps','cp_id','user','types', 'devices', 'token', 'controllers', 'inputs', 'outputs', 'nodes', 'link']));
    }

    /**
     * Edit user's device
     *     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        //For my devices page
        $input = $request->all();
        $validator = $this->deviceService->getDeviceValidator($input);
        //判斷是否輸入欄是否錯誤
        if(count($validator->errors()->all()) > 0){
            session(['error'=> $validator->errors()]);
            return back()->withErrors($validator);
        }

        $user_id = getTargetUserId();
        $cp_id = getTargetCpId();
        //創建或更新 (更新有changeMac會更新macAddr & production Id)
        $device = $this->deviceService->createOrUpdate($input, $cp_id);
        //檢查是否為模組
        $this->deviceService->checkForModuleController($device);
        //編輯的裝置才有changeMac(更換註冊碼用)
        $changeMac = $request->input('changeMac', null);//
        if($changeMac) {
            $this->appService->changeMacToApps($device);
        } else {
            //檢查是否為開發版&是否已複製應用及設定
            $publicDevice = $this->deviceService->findPublicDeviceByTypeId($device->type_id);
            $check = $this->deviceService->checkForDevelopController($device, $publicDevice);
            if($check) {
                $this->appService->checkAndCopyPublicApps($publicDevice, $device, $user_id);
                $this->settingService->copyPublicSetting($publicDevice, $device->id);
            }
        }



        return back();
    }

    /**
     * Super admin all of devices in the backend
     *     * @param Request $request
     * @return View
     */
    public function devices(Request $request)
    {
        //category 0:controller, 1:input, 2:output, 3:output&report, 4:all in one
        //大分類輸入 0:控制型， 1:輸入型，2:輸出型，3:輸出上報型，4:多合一控制模組(含輸出入功能)
        $category = (int)$request->input('category', 0);
        //裝置Id輸入
        $type_id = (int)$request->input('type_id', 0);
        $mac = $request->input('mac', null);
        //類型下拉選單
        $types =  $this->typeService->getTypesByCategory($category);
        //若無輸入就用類型列表第一個Id
        $type_id = $this->typeService->checkTypeId($type_id, $types);
        //檢查是否有公共的裝置(譬如:無人船，農業機器人，推進器等，公共裝置主要用來複製其設定或應用).
        //$checkPublic如果為0，就不顯示公共裝置選擇避免誤設。
        $checkPublic = ($type_id>101) ? 1 : 0;

        $cp_id = (int)$request->input('cp_id', 0);
        $check = checkCpId($cp_id);
        //公司下拉選單
        $cps = $this->cpRepository->all();
        $cp_id = $check['cp_id'];

        $user = session('user');
        $rooms = Room::where("user_id", $user['id'])->get();
        //$userRooms:用戶的場域陣列
        //裝置選擇加入場域下拉選單，初始置入Super Admin場域，勾選加入場域時從API取得其他用戶場域
        //勾選加入場域對應到setting_id = 1
        //範例:Super Admin有場域$rooms => userRooms[1] = $rooms
        $userRooms = array($user['id']=>$rooms);
        $users = User::where('cp_id', $cp_id)->get();
        $networks = Network::get(['id', 'network_name']);
        //取得公司指定類型所有裝置
        $devices = $this->deviceService->getDevices($cp_id, $type_id, $mac);

        //未綁定產品列表供綁定時選擇
        $products = Product::where('type_id', $type_id)
            ->whereNotIn('macAddr', array_column($devices->toArray(), 'macAddr'))
            ->get();

        //外掛下拉選單(目前:未支援 ， 無人機)，目前支援外掛的只有無人船 type_id : 102
        //$supports = Setting::where('field','support')->first()->set;
        $supports = $this->settingRepository->firstBy('field', 'support')->set;

        // dd($devices);
        return view('nodes.devices', compact(['userRooms', 'cps', 'cp_id', 'users', 'types', 'devices', 'networks', 'type_id','category' , 'products', 'supports']));
    }
    /**
     * Edit device in the backend.
     *     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editDevice(Request $request) //Backend edit device
    {
        //For device page
        $input = $request->all();
        //$id = (int)$request->input('id');
        //$user = session('user');
        $validator = $this->deviceService->getDeviceValidator($input);
        if(count($validator->errors()->all()) > 0){
            session(['error'=> $validator->errors()]);
            return back()->withErrors($validator);
        }
        $user_id = getTargetUserId();
        $cp_id = getTargetCpId();
        $device = $this->deviceService->createOrUpdate($input, $cp_id);

        if(array_key_exists('room_id', $input)) {//後台才有綁定場域&群組等功能
            $room_id = (int)$input['room_id'];
            if($room_id>0) {
                $this->memberRepository->bindingGroupDriver($device, $room_id);
            }
        }

        //檢查是否為模組
        $this->deviceService->checkForModuleController($device);
        //檢查是否為開發版&是否已複製應用及設定
        $publicDevice = $this->deviceService->findPublicDeviceByTypeId($device->type_id);
        $needCopy = $this->deviceService->checkForDevelopController($device, $publicDevice);
        if($needCopy) {
            $this->appService->checkAndCopyPublicApps($publicDevice, $device, $user_id);
            $this->settingService->copyPublicSetting($publicDevice, $device->id);
        }

        return back();
    }

    /**
     * Destroy device
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        $input = $request->all();
        //刪除的裝置Id
        $id = (int)$request->input('id');;
        $device = $this->deviceService->find($id);
        if(!$device) return back();
        //如果有控制模組更新需求change_mac
        //複製到新的裝置(開發版:app應用 & 命令 & setting,
        //模組:node & node_scripts & setting)
        $change_mac = $request->input('change_mac', null);
        if($change_mac) {
            $newDevice = $this->deviceService->findBy('macAddr', $change_mac);
            //更換Node & rules 到新的裝置
            $this->nodeService->checkAndCopyNode($device->macAddr, $newDevice);
            $this->nodeScriptService->checkAndCopyScript($device->macAddr, $newDevice);
        }
        //移除模組的node
        $this->nodeService->destroyOfDevice($device);
        //移除裝置的應用(服務會做刪除判斷)
        $this->appService->destroyOfDevice($device);
        //移除裝置的設定
        $this->settingService->destroyOfDevice($device);

        //移除任務，群組&裝置
        $this->memberRepository->removeGroupDevice($id);

        $device->delete();

        return back();
    }
}
