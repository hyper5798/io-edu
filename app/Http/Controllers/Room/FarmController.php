<?php

namespace App\Http\Controllers\Room;

use App\Http\Controllers\Admin\CommonController;
use App\Models\Command;
use App\Models\Device;
use App\Models\Plant;
use App\Models\Setting;
use App\Repositories\ReportRepository;
use App\Repositories\SettingRepository;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FarmController extends CommonController
{
    private $reportRepository, $settingRepository;

    public function __construct(ReportRepository $reportRepository, SettingRepository $settingRepository)
    {
        $this->reportRepository = $reportRepository;
        $this->settingRepository = $settingRepository;
    }

    /**
     * Display a listing of the resource.
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $input = $request->all();
        $id = $input['device_id'];
        if($id != null) {
            $id = (int)$id;//
        }
        $link = session('link');

        $user = session('user');
        $target = session('target');
        $room = $target['room'];
        $missions = $target['missions'];
        //Jason add for record current url on 2022/5/20
        $target['url'] = url()->full();
        session(['target' => $target]);
        $arr = array_column($missions->toArray(), 'macAddr');
        $devices = Device::whereIn('macAddr', $arr)->get();
        $set = null;

        if(array_key_exists('device_id', $input)) {
            $device_id = (int)$input['device_id'];
        } else {
            $device_id = $id;
        }

        $device = Device::find($device_id);

        $status = array();
        $start = date("Y/m/d");

        $setting = [
            'farm_size_set' => $this->settingRepository->getFarmSetting($device_id, 'farm_bot'),
            'farm_home_set' => $this->settingRepository->getFarmSetting($device_id, 'farm_home'),
            'farm_plate_set' => $this->settingRepository->getFarmSetting($device_id, 'farm_plate'),
            'farm_script_set' => $this->settingRepository->getFarmSetting($device_id, 'farm_script'),
            'farm_script_empty' => $this->settingRepository->getFarmScriptEmpty(),
            'farm_commands_set' => $this->settingRepository->getFarmSetting($device_id, 'farm_commands'),
            'trigger_set' => $this->settingRepository->getDeviceSetting($device_id, 'sensor_trigger')
        ];
        $plant_kinds = Setting::where('device_id', $device_id)
            ->where('field', 'plant_kinds')->get();
        $plant_kinds_set = null;
        if(count($plant_kinds) > 0) {
            $plant_kinds_set = $plant_kinds->first()->set;
        }
        $home_setting = null;
        $parameter = null;

        $farmObject = array();
        $allPlants = Plant::where('device_id', $device->id)
            ->orderBy('sort', 'asc')
            ->get();

        $apps = $this->reportRepository->getAppsByMac($device->macAddr);
        //dd($apps);
        foreach ($allPlants as $plant) {
            $farmObject[$plant->plant_key] = $plant;
        }

        //上報資料
        foreach ($apps as $app) {
            $report = $this->reportRepository->getLastReportByAppId($app->id, $start, $device->macAddr);
            if ($report != null) {
                $status[$app->sequence] = $report->toArray();
            } else {
                $status[$app->sequence] = null;
            }
        }
        $token = $user->remember_token;
        //dd($setting);


        return view('room.farm.index', compact(['user', 'room', 'device_id', 'apps', 'status', 'setting', 'plant_kinds_set', 'devices', 'device', 'token', 'farmObject']));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @return mixed
     */
    public function editFarmSetting(Request $request)
    {
        $input = $request->all();
        $set = null;
        $device_id = null;
        $field = 'farm_bot';
        if(isset($input['set'])) {
            $set = json_decode($input['set']);
        }
        if(array_key_exists('device_id', $input)) {
             $device_id =(int)$input['device_id'];
        }
        if(array_key_exists('field', $input)) {
            $field = $input['field'];
        }

        $setting = null;
        $id = 0;

        if($field != 'farm_script') {
            $setting = Setting::where('device_id', $device_id)
                ->where('field', $field)
                ->first();
            if($setting == null) {
                $setting = new Setting;
                $setting->device_id = $device_id;
                $setting->field = $field;
            }
        } else {
            //Edit farm script
            if(array_key_exists('id', $input)) {
                $id =(int)$input['id'];
            }
            if($id == 0) {
                $setting = new Setting;
                $setting->device_id = $device_id;
                $setting->field = $field;
            } else {
                $setting = Setting::find($id);
            }
        }

        $setting->set = $set;
        $setting->save();

        return back();
    }

    public function deleteFarmSetting(Request $request)
    {
        $input = $request->all();
        if(array_key_exists('id', $input)) {
            $id =(int)$input['id'];
        }

        $setting = Setting::find($id);
        if($setting != null) {
            $setting->delete();
        }

        return back();
    }

    public function webrtc(Request $request, $id) {
        $input = $request->all();
        if($id != null) {
            $id = (int)$id;//
        }
        $size = 'small';
        if(array_key_exists('size', $input)) {
            $size = $input['size'];
        }
        $device = Device::find($id);
        $user = session('user');
        return view('room.farm.webrtc', compact([ 'device', 'size', 'user']));
    }
}
