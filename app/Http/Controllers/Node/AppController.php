<?php

namespace App\Http\Controllers\Node;

use App\Constant\AppConstant;
use App\Models\App;
use App\Models\Cp;
use App\Models\Device;
use App\Models\Report;
use App\Models\Type;
use App\Models\Setting;
use App\Models\User;
use App\Services\DeviceService;
use App\Services\SettingService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use App\Repositories\ReportRepository;
use App\Repositories\SettingRepository;

class AppController extends Common4Controller
{
    private $reportRepository, $settingRepository,$deviceService, $settingService;

    public function __construct(
        ReportRepository $reportRepository,
        SettingRepository $settingRepository,
        DeviceService $deviceService,
        SettingService $settingService
    )
    {
        $this->reportRepository = $reportRepository;
        $this->settingRepository = $settingRepository;
        $this->deviceService = $deviceService;
        $this->settingService = $settingService;
    }
    /**
     * Display a listing of the app.
     *
     * @param Request $request
     * @return View
     */
    public function admin(Request $request)
    {
        $device_id = $request['device_id'];
        if($device_id == null) {
            return redirect('node/myDevices');
        }
        $user = session('user');
        $myIntro = $request['myIntro'];
        if($myIntro != null) {
            $user->myIntro = (int)$myIntro;
            session(['user'=>$user]);
        }

        $types = null;

        $devices = Device::where('user_id', $user['id'])
            ->where('type_id', 11)
            ->get();

        $device_id = (int)$device_id;
        $device = Device::find($device_id);

        $target = $device;//Jason add for show device mac and name

        $apps = App::where('macAddr', $device->macAddr)->get();
        $data = [
            'nextLabel' => trans('intro.nextLabel'),
            'prevLabel' => trans('intro.prevLabel'),
            'skipLabel'=>trans('intro.skipLabel'),
            'doneLabel'=>trans('intro.doneLabel'),
            'command_step2' => trans('intro.command_step2'),
            'command_step3' => trans('intro.command_step3'),
            'back_step' => trans('intro.back_step'),
            'command_page' => trans('intro.command_page'),
        ];

        return view('nodes.admin', compact(['user', 'devices', 'apps', 'device_id', 'target', 'data']));
    }
    /**
     * Display a listing of the app.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $link = $request['link'];
        if($link == null) {
            $link = 'develop';
            session(['link' => $link]);
        }
        $app_limit = AppConstant::APP_MAX;
        $target = session('target');
        $target['url'] = url()->full();
        $user = session('user');
        $input = $request->all();
        $user_id = $target['user_id'];
        if(array_key_exists('user_id', $input)) {
            $user_id = (int)$input['user_id'];
            $target['user_id'] = $user_id;
        }
        $target['user_id'] = $user_id;
        $devices = null;
        $types = null;
        $device = null;
        $app_id = isset($request['app_id']) ? (int)$request['app_id'] : 0;
        $device_id = $request['device_id'];
        $mac = $request['mac'];;

        $devices = Device::where('user_id', $user_id)
            ->where('type_id','>' ,100)
            ->get();

        //if($app_id == null && $device_id == null) {
        if($app_id == null && $mac == null && $device_id == null) {
            return redirect('node/index');
        } else if( $mac != null) {//From
            $device = Device::where('macAddr', $mac)->first();
            $device_id = $device->id;
            $target['mac'] = $mac;

        } else if( $device_id != null) {//From http command management
            $device_id = (int)$device_id;
            $device = Device::find($device_id);
            $target['mac'] = $device->macAddre;
        } else if($device_id == null){//From edit command channel

            $mac = App::find($app_id)->macAddr;
            $device = Device::where('macAddr',$mac)->first();
            $device_id = $device->id;
            $target['mac'] = $mac;
        }
        session(['target' => $target]);
        $myTarget = $device;//Jason add for show device mac and name

        $apps = App::where('macAddr', $device->macAddr)
            ->orderBy('sequence', 'ASC')
            ->get();
        $data = [
            'nextLabel' => trans('intro.nextLabel'),
            'prevLabel' => trans('intro.prevLabel'),
            'skipLabel'=>trans('intro.skipLabel'),
            'doneLabel'=>trans('intro.doneLabel'),
            'field_required' => trans('app.field_required'),
            'name_required' => trans('app.name_required'),
            'add_app_page' => trans('intro.add_app_page'),
            'device_name' => $device->device_name,
            'type_id' => $device->type_id,
        ];
        $type_id = $device->type_id;
        return view('nodes.apps', compact(['type_id','user', 'user_id' ,'devices', 'apps', 'device_id', 'myTarget', 'app_id', 'data', 'link', 'app_limit']));
    }

    public function channel(Request $request)
    {
        $input = $request->all();

        $user = session('user');
        $target = session('target');
        $target['url'] = url()->full();
        $cp_id =  (int)$request->input('cp_id', $user->cp_id);
        $user_id = 0;
        $cps = null;
        //Super admin

        //Add user_id for super admin to selected.
        if ($user->role_id >= 7) {//Group user
            //Get rooms of group user
            $user_id = $user['id'];

        } else {//Admin user
            //Get rooms of the company
            if(array_key_exists('user_id', $input)) {
                //更新選的user Id 到 $target
                $user_id = (int)$input['user_id'];
            } else if($target != null && array_key_exists('user_id', $target)){
                //
                $user_id = $target['user_id'];
            } else {
                //未指定過$target & 選擇用戶 ，取第一個用戶
                $user_id = $users->first()->id;
            }
        }

        session(['target' => $target]);
        $devices = null;
        $types = null;
        $device = null;
        $app_id = isset($request['app_id']) ? (int)$request['app_id'] : 0;
        $device_id = $request['device_id'];
        $mac = $request['mac'];
        if($mac == null && isset($user['mac'])) {
            $mac = $user['mac'];
        }

        $devices = Device::where('user_id', $user['id'])
            ->where('type_id','>' ,100)
            ->get();

        $app = App::find($app_id);

        $device = Device::where('macAddr',$app->macAddr)->first();
        $device_id = $device->id;
        $type_id = $device->type_id;
        $target = $device;//Jason add for show device mac and name
        $apps = App::where('macAddr', $device->macAddr)->get();

        $test = $this->settingService->getDeviceSetting($device_id, AppConstant::CONTROL_SETTING_KEY, $app_id);

        $data = [
            'data_title' =>AppConstant:: DATA_TITLE,
            'control_setting_title' =>AppConstant:: CONTROL_SETTING_TITLE,
            'control_setting_max' => AppConstant:: CONTROL_SETTING_MAX,
            'control_setting_key' => AppConstant::CONTROL_SETTING_KEY,
            'field_required' => trans('app.field_required'),
            'name_required' => trans('app.name_required'),
            'add_app_page' => trans('intro.add_app_page'),
            'device_name' => $device->device_name,
            'triggers' => $this->settingRepository->getDeviceSetting($device_id, 'sensor_trigger', $app_id),
            'emptyTrigger' => $this->settingRepository->getTrigger(),
            'report_settings' => $this->settingRepository->getDeviceSetting($device_id, 'report', $app_id),
            'control_setting' => $this->settingService->getDeviceSetting($device_id, AppConstant::CONTROL_SETTING_KEY, $app_id),
            'api_key' => $app->api_key
        ];

        return view('nodes.channel', compact(['app','type_id','user', 'device_id', 'target', 'app_id', 'data', 'device']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @return View
     */
    public function update(Request $request)
    {
        $user = session('user');
        $input = $request->all();
        $device_id = null;
        $mac = null;

        if(array_key_exists('device_id', $input)) {
            $device_id = (int)$input['device_id'];
            $device = Device::find($device_id);
            $mac = $device->macAddr;
        }
        if(array_key_exists('mac', $input)) {
            $mac = $input['mac'];
            $device = Device::where('macAddr', $mac)->first();
            $device_id = $device->id;
        }


        if($input['id'] == 0) {
            $app = new App();
            $app->macAddr = $mac;
            $app->device_id =$device_id;
            $app->api_key = '';
            $app->sequence = (int)$input['sequence'];
        } else {
            $id = $input['id'];
            $app= App::find($id);
            $app->updated_at = now();
        }
        $app->name = $input['name'];
        $app->key_label = json_decode($input['label']);
        $parse = $input['parse'];
        if($parse == null || $parse == '{}')
            $app->key_parse = null;
        else
            $app->key_parse = json_decode($input['parse']);

        $app->save();
        //For new app
        if($app->api_key == '') {
            $code = getAPIkey($app->id, $user['id']);
            $app->api_key = $code;
            $app->save();
        }

        return back();
    }

    /**
     * Display a listing of the app.
     *
     * @param Request $request
     * @return View
     */
    public function reports(Request $request)
    {
        $app_id = $request['app_id'];
        $macAddr = $request['macAddr'];

        if($app_id == null) {
            return redirect('node/myDevices');
        } else {
            $app_id = (int)$app_id;
        }
        $start = $request['start'];
        $end = $request['end'];

        if( $end == null ){
            $end = date("Y/m/d");
            $date = new DateTime($end);
            $date->modify('+1 day');
            $end = $date->format('Y-m-d');
        }

        if( $start == null ){
            $start = date("Y/m/d");
            $date = new DateTime($end);
            $date->modify('-30 day');
            $start = $date->format('Y-m-d');
        }

        $page = $request['page'];
        $tab = $request['tab'];
        $app = App::where('id',$app_id)->first();


        if($macAddr == null) {
            $macAddr = $app->macAddr;
        }

        $query = Report::where('macAddr', $macAddr)
            ->whereBetween('recv', [$start, $end])
            ->where('app_id', $app->id);
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
        $user = session('user');
        $target = session('target');
        $target['url'] = url()->full();
        $cp_id = array_key_exists('cp_id', $target) ? $target['cp_id'] : $user['cp_id'];
        $target['cp_id'] = $cp_id;
        session(['target' => $target]);

        $types =  Type::all();

        $settings = Setting::where('app_id', $app_id)->get();
        $labels = [];
        $dataKeys = [];
        $label = [];

        if($app != null && gettype($app->key_label) == 'array') {


            $keys = array_keys($app->key_label);
            foreach($keys as $key) {
                $value = $app->key_label[$key];
                if($value != '') {
                    $label[$key] =  $app->key_label[$key];
                }
            }

            list($dataKeys, $labels) = Arr::divide($label);

        }

        //Select macAddr, recv and keys
        $arr1 = array(0=>'id', 1 => "macAddr", 2=> "recv");
        $result = array_merge($arr1, $dataKeys);

        $device = Device::where('macAddr',$macAddr)->first();
        /*$reports = $query->offset($offset)->limit($limit)
            ->select($result)
            ->orderBy('recv', 'ASC')
            ->get(['id','recv']);*/
        $reports = $query->select($result)
            ->orderBy('recv', 'ASC')
            ->get();
        $data = [
            'page' => $page,
            'limit' => $limit,
            'types'=>$types,
            'myApp' => $app,
            'tab' => $tab,
            'device' => $device,
            'count' => $count,
            'nextLabel' => trans('intro.nextLabel'),
            'prevLabel' => trans('intro.prevLabel'),
            'skipLabel'=>trans('intro.skipLabel'),
            'doneLabel'=>trans('intro.doneLabel'),
        ];
        $labelObj = json_encode($label);

        return view('nodes.reports', compact(['user','labelObj','label','dataKeys', 'labels','reports', 'settings', 'data', 'start', 'end', 'app_id']));
    }

    /**
     * Display a listing of the app.
     *
     * @param Request $request
     * @return View
     */
    public function APIkey(Request $request)
    {
        $app_id = $request['app_id'];
        if($app_id == null) {
            return redirect('node/myDevices');
        }
        $user = session('user');
        $tab = session('tab');
        if($tab == null) {
            $tab = 1;
        }
        //Reset tab to default
        session(['tab'=>null]);
        $target = session('target');
        $target['url'] = url()->full();
        session(['target' => $target]);

        $app_id = (int)$app_id;
        $app = App::where('id',$app_id)->first();

        $labels = $app->key_label;

        $device = Device::where('macAddr',$app->macAddr)->first();
        $data = [
            'nextLabel' => trans('intro.nextLabel'),
            'prevLabel' => trans('intro.prevLabel'),
            'skipLabel'=>trans('intro.skipLabel'),
            'doneLabel'=>trans('intro.doneLabel'),
        ];

        return view('nodes.apikey', compact(['user', 'app','labels', 'device', 'tab', 'data']));
    }

    /**
     * Update  a setting of the report gauge.
     *
     * @param Request $request
     * @return View
     */
    public function gaugeSetting(Request $request)
    {
        $input = $request->all();
        $set = json_decode($input['set']);
        if($input['id'] == 0) {//New setting
            $setting = new Setting();
            $setting->app_id = (int)$input['app_id'];
            $setting->field = $input['field'];
        } else {
            $setting = Setting::find($input['id']);
        }

        $setting->set = $set;
        $setting->save();

        return back();
    }

    /**
     * Display a listing of the app.
     *
     * @param Request $request
     * @return View
     */
    public function change(Request $request)
    {

        $app_id = $request['app_id'];
        $user = session('user');
        $app_id = (int)$app_id;
        $app= App::find($app_id );
        $app->updated_at = now();
        $app->updated_at = now();
        $code =  getAPIkey($app->id, $user['id']);
        $app->api_key = $code;
        $app->save();

        session(['tab' => 3]);

        return back()->withErrot('ChangeTab3');
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
        $id = (int)$input['id'];
        App::where('id', $id)->delete();
        Setting::where('app_id', $id)->delete();
        Report::where('app_id', $id)->delete();
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
        $id = (int)$input['id'];
        Report::where('app_id', $id)->delete();
        return back();
    }

    public function delReport(Request $request)
    {
        $input = $request->all();
        $id = (int)$input['id'];
        Report::find($id)->delete();
        return back();
    }

    public function lineChart(Request $request)
    {
        $input = $request->all();
        $start = $request->input('start', null);
        $end = $request->input('end', null);

        if( $end == null ){
            $end = date("Y/m/d");
            $date = new DateTime($end);
            $date->modify('+1 day');
            $end = $date->format('Y-m-d');
        }

        if( $start == null ){
            $start = date("Y/m/d");
            $date = new DateTime($end);
            $date->modify('-30 day');
            $start = $date->format('Y-m-d');
        }
        $app_id = (int)$request->input('app_id', 0);
        $app = App::where('id',$app_id)->first();
        $macAddr = $app->macAddr;
        $query = Report::where('app_id', $app->id)
            ->whereBetween('recv', [$start, $end]);


        $title = $app->name;
        $keys = array_keys($app->key_label);
        $labels = [];
        $dataKeys = [];
        $label = [];
        foreach($keys as $key) {
            $value = $app->key_label[$key];
            if($value != '') {
                $label[$key] =  $app->key_label[$key];
            }
        }

        list($dataKeys, $labels) = Arr::divide($label);
        $arr1 = array(0=>'id', 1 => "macAddr", 2=> "recv");
        $result = array_merge($arr1, $dataKeys);
        $reports = $query->select($result)
            ->orderBy('recv', 'ASC')
            ->get();

        return view('nodes.lineChart', compact([ 'title','dataKeys', 'labels', 'app_id', 'reports', 'macAddr']));
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
