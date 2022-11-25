<?php

namespace App\Http\Controllers\Node;

use App\Models\App;
use App\Models\Command;
use App\Models\Device;
use App\Models\Report;
use App\Models\Setting;
use DateTime;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Repositories\ReportRepository;

class MapController extends Common4Controller
{
    private $reportRepository;

    public function __construct(ReportRepository $reportRepository)
    {
        $this->reportRepository = $reportRepository;
    }
    /**
     * Display a listing of the resource.
     * * @param Request $request
     *
     * @return View
     */
    public function index(Request $request)
    {
        $link = $request['link'];
        if($link == null) {
            $link = 'develop';
        }
        $input = $request->all();
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
        $mac = $input['mac'];


        $arr = array_column($missions->toArray(), 'macAddr');
        $devices = Device::whereIn('macAddr', $arr)->get();

        $device = Device::where('macAddr', $mac)->first();
        $device_id = $device->id;
        $target['mac'] = $device->macAddr;
        session(['target' => $target]);

        $setting = $this->reportRepository->getSettingByDevice($device_id, 'center');
        $home_setting = $this->reportRepository->getSettingByDevice($device_id, 'home');
        $parameter = $this->reportRepository->getSettingByDevice($device_id, 'parameter');
        if($setting == null) {
            dd($setting);
        }

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

        foreach ($apps as $app) {
            $report = $this->reportRepository->getLastReportByAppId($app->id, $start, $device->macAddr);
            if ($report != null) {
                $status[$app->sequence] = $report->toArray();
            } else {
                $status[$app->sequence] = null;
            }
        }

        //dd($setting);
        $myTarget = $device;

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

        return view('nodes.map', compact(['status','user', 'devices', 'apps', 'device_id', 'myTarget', 'link', 'setting', 'center_index', 'home_setting', 'parameter', 'mechanism']));
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
        if($input['id'] == 0) {//New setting
            $setting = new Setting();
            if(array_key_exists('app_id', $input)) {
                $setting->app_id = (int)$input['app_id'];
            }
            if(array_key_exists('device_id', $input)) {
                $setting->device_id = (int)$input['device_id'];
            }
            $setting->field = $input['field'];
        } else {
            $setting = Setting::find($input['id']);
        }

        $setting->set = $set;
        $setting->save();

        return back();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
        $setting = Setting::find($id)->delete();
        /*if(isset($request['usv_tab']) ) {
            $user = session('user');
            $user->usv_tab = $request['usv_tab'];
            session(['user' => $user]);
        }*/
        return back();
    }

    /**
     * Display the specified resource.
     * @param Request $request
     *
     * @return RedirectResponse | View
     */
    public function viewControl(Request $request)
    {
        $link = $request['link'];
        if($link == null) {
            $link = 'develop';
        }
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

        return view('nodes.viewControl', compact(['status','user', 'devices', 'apps', 'device_id', 'target', 'link', 'btn_setting', 'video_setting', 'commands', 'data', 'center']));
    }

    /**
     * Display the specified resource.
     * @param Request $request
     *
     * @return RedirectResponse | View
     */
    public function paramTest(Request $request)
    {
        $link = $request['link'];
        if($link == null) {
            $link = 'develop';
        }
        $user = session('user');
        $center = ($user['center'] == null) ?  null : $user['center'];
        $input = $request->all();

        $devices = null;
        $device = null;
        $app_id = (int)$request['app_id'];
        $device_id = $request['device_id'];
        $mac = $request['mac'];
        if($mac == null && isset($user->mac)) {
            $mac = $user->mac;
        }
        $devices = null;
        $type_id = 102;//Develop device
        if($user->role_id<3) {
            $devices = Device::where('type_id', $type_id)
                ->get();
        } else {
            return redirect('/node?mac='.$mac);
        }
        //if($app_id == null && $device_id == null) {
        if($mac == null && $device_id == null) {
            return redirect('node/index');
        } else if( $mac != null) {//From /node/index
            $device = Device::where('macAddr', $mac)->first();
            $device_id = $device->id;
            $user->mac = $mac;
            session(['user' => $user]);
        } else if( $device_id != null) {//From http command management
            $device_id = (int)$device_id;
            $device = Device::find($device_id);
            $user->mac = $device>macAddr;
            session(['user' => $user]);
        }
        $target = $device;//Jason add for show device mac and name

        $apps = $this->reportRepository->getAppsByMac($device->macAddr);
        //For get report by app sequence and app_id
        $status = array();
        $start = date("Y/m/d");
        $btn_setting = null;
        $video_setting = null;

        foreach ($apps as $app) {
            $report = $this->reportRepository->getLastReportByAppId($app->id, $start, $device->macAddr);
            if($report != null) {
                $status[$app->sequence] = $report->toArray();
            }
            if($app->sequence == 1) {
                $btn_setting = $this->reportRepository->getSettingByAppIdKey($app->id, 'btn');
                $video_setting = $this->reportRepository->getSettingByAppIdKey($app->id, 'video');
            }
        }
        $data = [
            'token' => $user['remember_token'],
        ];

        $commands = Command::where('device_id', $device_id)->get();

        return view('nodes.paramTest', compact(['status','user', 'devices', 'apps', 'device_id', 'target', 'link', 'btn_setting', 'video_setting', 'commands', 'data', 'center']));
    }
}
