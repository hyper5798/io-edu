<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Admin\CommonController;
use App\Models\Device;
use App\Models\Location;
use App\Models\Mission;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Repositories\ReportRepository;
use App\Models\Command;

class UsvController extends CommonController
{
    private $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $room = session('room');
        $input = $request->all();
        $user = session('user');
        $target = session('target');
        $center_index = 0;
        $isChange = false;

        if(isset($request['center_index']) ) {
            $center_index = (int)$request['center_index'];
            $isChange = true;
        }
        //判斷是否為加入場域裝置
        $devices = null;
        if($room) {
            $missions = array_key_exists('missions', $target) ? $target['missions']: null;
            $devices = Device::whereIn('macAddr', array_column($missions->toArray(), 'macAddr'))->get();
        } else {
            $devices = Device::where('user_id', $user['id'])->get();
        }

        $device_id = (int)$input['device_id'];
        $device = Device::find($device_id);
        $target['mac'] = $device->macAddr;
        $target['url'] = url()->full();
        session(['target' => $target]);

        $report_setting = array();

        //for customer

        $setting = $this->reportRepository->getSettingByDevice($device_id, 'center');
        $home_setting = $this->reportRepository->getSettingByDevice($device_id, 'home');
        $parameter = $this->reportRepository->getSettingByDevice($device_id, 'parameter');

        $apps = $this->reportRepository->getAppsByMac($device->macAddr);
        //dd($apps);

        $status = array();
        $start = date("Y/m/d");


        if($parameter == null) {
            $parameter = new Setting();
            $parameter->device_id = $device_id;
            $a=array('interval' => 1,'trigger1' => null,'trigger2' => null);
            $parameter->set = $a;
            $parameter->field = 'parameter';
            $parameter->save();
        }

        $isShowReport = false;

        foreach ($apps as $app) {
            $report = $this->reportRepository->getLastReportByAppId($app->id, $start, $device->macAddr);
            if ($report != null) {
                $status[$app->sequence] = $report->toArray();
            } else {
                $status[$app->sequence] = null;
            }
            if($app->sequence > 5) {
                //取得公司或個人上報設定
                $tem_report_setting = $this->reportRepository->getAppSettingByCp($app->id, $target['cp_id'], 'report');
                if($tem_report_setting == null) {
                    $tem_report_setting = $this->reportRepository->getAppSettingByUser($app->id, $target['user_id'], 'report');
                }
                if($tem_report_setting != null) {
                    $report_setting[$app->id] =  $tem_report_setting;
                    if($tem_report_setting->set!=null && count($tem_report_setting->set)>0) {
                        $isShowReport = true;
                    }
                } else {
                    $report_setting[$app->id] = null;
                }
            }
        }

        //dd($setting);

        if($setting != null && $isChange) {
            $setting->set_index = $center_index;
            $setting->save();
        } else if($setting != null && $setting->set_index != null) {
            $center_index = $setting->set_index;
        }
        //機溝控制除汙裝置
        $mechanism = Command::where('device_id', $device_id)
            ->where('type_id', 102)
            ->where('cmd_name', '機構控制')
            ->first();

        $locations = Location::where('macAddr', $device->macAddr)
            ->orderBy('recv', 'asc')
            ->get();
        foreach ($locations as $location) {
            $location->image_url = Storage::url($location->image_url);
        }

        return view('room.usv.index', compact(['user', 'device_id', 'apps', 'center_index', 'isShowReport',
            'status', 'setting', 'home_setting', 'parameter', 'devices', 'device', 'mechanism', 'locations','report_setting']));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @return mixed
     */
    public function editSetting(Request $request)
    {
        $input = $request->all();
        $set = '[]';
        if(isset($input['set'])) {
            $set = json_decode($input['set']);
        }
        if(isset($input['setString'])) {
            $set = json_decode($input['setString']);
        }
        if($input['id'] == 0) {//New setting
            $setting = new Setting();
            if(array_key_exists('app_id', $input)) {
                $setting->app_id = (int)$input['app_id'];
            }
            if(array_key_exists('device_id', $input)) {
                $setting->device_id = (int)$input['device_id'];
            }
            if(array_key_exists('room_id', $input)) {
                $setting->room_id = (int)$input['room_id'];
            }
            $setting->field = $input['field'];
        } else {
            $setting = Setting::find($input['id']);
        }
        if(array_key_exists('name', $input)) {
            $setting->name =$input['name'];
        }
        $setting->set = $set;
        $setting->save();

        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function delSetting(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        Setting::find($id)->delete();
        return back();
    }

    /**
     * Display the specified resource.(多channel view from media server 棄用)
     * @param Request $request
     *
     * @return RedirectResponse | View
     */
    public function viewControl(Request $request)
    {
        $input = $request->all();
        $user = session('user');
        $target = session('target');

        $center = ($user['center'] == null) ? null : $user['center'];

        $room = $target['room'];
        $missions = $target['missions'];
        $devices = $target['devices'];
        $mac = $target['mac'];

        $device = Device::where('macAddr', $mac)->first();
        $device_id = $device->id;
        $setting = $this->reportRepository->getSettingByDevice($device_id, 'key9');
        $btn_setting = $this->reportRepository->getSettingByDevice($device_id, 'btn');
        $video_setting = $this->reportRepository->getSettingByDevice($device_id, 'video');

        $apps = $this->reportRepository->getAppsByMac($device->macAddr);
        //dd($apps);

        $status = array();
        $start = date("Y/m/d");

        foreach ($apps as $app) {
            $report = $this->reportRepository->getLastReportByAppId($app->id, $start, $device->macAddr);
            if ($report != null) {
                $status[$app->sequence] = $report->toArray();
            }  else {
                $status[$app->sequence] = null;
            }
        }

        $data = [
            'token' => $user['remember_token'],
        ];

        $commands = Command::where('device_id', $device_id)->get();

        return view('room.usv.viewControl', compact(['status', 'user', 'apps', 'btn_setting', 'video_setting', 'commands', 'data', 'center', 'room', 'device']));
    }

    /**
     * Display a history of the GPS track.
     * @param Request $request
     * @return View
     */
    public function history(Request $request, $device_id)
    {
        $input = $request->all();
        $user = session('user');
        $target = session('target');
        $center_index = 0;
        $isChange = false;
        $devices = null;
        if(isset( $target['devices'])){
            $devices = $target['devices'];
        }

        if(isset($request['center_index']) ) {
            $center_index = (int)$request['center_index'];
            $isChange = true;
        }

        $url = $target['url'] ? $target['url'] : null;
        $devices = null;
        $missions = array_key_exists('missions', $target) ? $target['missions']: null;

        if($missions) {
            $devices = Device::whereIn('macAddr', array_column($missions->toArray(), 'macAddr'))->get();
        } else {
            $devices = Device::where('user_id', $user['id'])->get();
        }
        $device_id = (int)$device_id;
        $device = Device::find($device_id);

        //公版裝置
        $publicDevice = Device::where('type_id', 104)
            ->where('isPublic',1)->first();
        if($publicDevice == null) {
            $publicDevice = Device::where('type_id', 104)->first();
        }

        $apps = $this->reportRepository->getAppsByMac($publicDevice->macAddr);
        $room = array_key_exists('room', $target) ? $target['room'] : null;
        if($room) {
            $dangers = $this->reportRepository->getSetListByRoom($room->id, 'danger');
        } else {
            $dangers = $this->reportRepository->getSettingByUser($user['id'], 'danger');
        }

        //dd($apps);

        $status = array();
        $start = date("Y/m/d");
        $setting = null;
        $home_setting = null;
        $parameter = '';

        foreach ($apps as $app) {
            $report = $this->reportRepository->getLastReportByAppId($app->id, $start, $device->macAddr);
            if ($report != null) {
                $status[$app->sequence] = $report->toArray();
            } else {
                $status[$app->sequence] = null;
            }
            if ($app->sequence == 1) {
                $setting = $this->reportRepository->getSettingByAppIdKey($app->id, 'center');
            }
        }

        //dd($setting);

        if($setting != null && $isChange) {
            $setting->set_index = $center_index;
            $setting->save();
        } else if($setting != null && $setting->set_index != null) {
            $center_index = $setting->set_index;
        }

        return view('room.usv.history', compact(['user', 'room', 'device_id', 'apps', 'center_index', 'status', 'setting', 'devices', 'device' , 'devices', 'dangers', 'url']));

    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return View
     */
    public function thruster(Request $request)
    {
        $input = $request->all();
        $order = 'desc';
        $user = session('user');
        $target = session('target');
        $center_index = 0;
        $isChange = false;

        if(isset($request['center_index']) ) {
            $center_index = (int)$request['center_index'];
            $isChange = true;
        }
        $room = $target['room'];
        $missions = $target['missions'];
        $devices = $target['devices'];
        $arr = array_column($missions->toArray(), 'macAddr');


        $device_id = (int)$input['device_id'];
        $device = Device::find($device_id);

        $target['mac'] = $device->macAddr;
        $target['url'] = url()->full();
        session(['target' => $target]);
        //公版裝置
        $publicDevice = Device::where('type_id', 104)
            ->where('isPublic',1)->first();
        if($publicDevice == null) {
            $publicDevice = Device::where('type_id', 104)->first();
        }
        //取得公版裝置所有 app
        $apps = $this->reportRepository->getAppsByMac($publicDevice->macAddr);
        //dd($apps);

        $status = array();
        $start = date("Y/m/d");
        $setting = null;
        $parameter = null;
        $setting = $this->reportRepository->getSettingByDevice($device_id, 'center');
        $dangers = $this->reportRepository->getSetListByRoom($room->id, 'danger');

        foreach ($apps as $app) {
            //指定裝置 Id及 公版 app Id 取得當天reports
            if($app->sequence >1) {
                $report = $this->reportRepository->getLastReportByAppId($app->id, $start, $device->macAddr);
            } else {
                $report = $this->reportRepository->getTodayReportsByAppId($app->id, $start, $devices, $order, 0, 500);
            }

            //float經過json_encode會

            if ($report != null) {
                $status[$app->sequence] = $report->toArray();
            } else {
                $status[$app->sequence] = null;
            }
        }

        //dd($setting);

        if($setting != null && $isChange) {
            $setting->set_index = $center_index;
            $setting->save();
        } else if($setting != null && $setting->set_index != null) {
            $center_index = $setting->set_index;
        }

        return view('room.usv.thruster', compact(['order','user', 'room', 'device_id', 'apps', 'center_index', 'status', 'setting', 'devices', 'device', 'dangers']));

    }
}
