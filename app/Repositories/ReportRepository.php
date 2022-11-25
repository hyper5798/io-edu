<?php
namespace App\Repositories;

use App\Models\App;
use App\Models\Device;
use App\Models\Report;
use App\Models\Setting;
use DateTime;

class ReportRepository
{
    public function getAppsByMac($mac) {
        return App::where('macAddr', $mac)->get();
    }

    public function getLastReportByAppId($app_id, $start , $mac)
    {
        $reports = null;
        if($start == null) {
            //desc由大到小
            $reports = Report::where('app_id', $app_id)
                ->where('macAddr', $mac)
                ->orderBy('recv', 'desc')
                ->first();
        } else {
            $end = date("Y/m/d");
            $date = new DateTime($end);
            $date->modify('+1 day');
            $date = $date->format('Y-m-d');
            if(isset($mac)) {
                $reports = Report::where('app_id', $app_id)
                    ->where('macAddr', $mac)
                    ->whereBetween('recv', [$start, $date])
                    ->orderBy('recv', 'desc')
                    ->first();
            } else {
                $reports = Report::where('app_id', $app_id)
                    ->whereBetween('recv', [$start, $date])
                    ->orderBy('recv', 'desc')
                    ->first();
            }
        }


        return $reports;
    }

    public function getTodayReportsByAppId($app_id, $start , $devices , $order,$skip,$limit)
    {
        $mac = null;
        if($devices->count()==1) {
            $mac = $devices->first()->macAddr;
        } else {
            //Device macAddr array
            $mac = array_column($devices->toArray(), 'macAddr');
        }

        $date = new DateTime($start);
        $date->modify('+1 day');
        $date = $date->format('Y-m-d');
        if($devices->count()==1) {
            $reports = Report::where('app_id', $app_id)
                ->where('macAddr', $mac)
                ->whereBetween('recv', [$start, $date])
                ->orderBy('recv', $order)
                ->skip($skip)
                ->take($limit)
                ->get();
        } else {
            $reports = Report::where('app_id', $app_id)
                ->whereIn('macAddr',$mac)
                ->whereBetween('recv', [$start, $date])
                ->orderBy('recv', $order)
                ->skip($skip)
                ->take($limit)
                ->get();
        }


        return $reports;
    }

    public function getSettingByAppIdKey($id, $key) {
        $setting = Setting::where('app_id',$id )
            ->where('field', $key)
            ->get();
        if($setting != null) {
            $setting = $setting->first();
        }
        return $setting;
    }

    public function getSettingByDevice($id, $key) {
        $setting = Setting::where('device_id',$id )
            ->where('field', $key)
            ->get();
        if($setting != null) {
            $setting = $setting->first();
        }
        return $setting;
    }

    public function getSetListByRoom($id, $key) {
        $setting = Setting::where('room_id',$id )
            ->where('field', $key)
            ->get();

        return $setting;
    }
    //客製化上報設定(for cp)
    public function getAppSettingByCp($app_id, $cp_id, $key) {
        $setting = Setting::where('app_id',$app_id)
            ->where('cp_id',$cp_id )
            ->where('field', $key)
            ->get();
        if($setting != null) {
            $setting = $setting->first();
        }
        return $setting;
    }
    //客製化上報設定(for user)
    public function getAppSettingByUser($app_id, $user_id, $key) {
        $setting = Setting::where('app_id',$app_id)
            ->where('user_id',$user_id )
            ->where('field', $key)
            ->first();
        return $setting;
    }

    //客製化上報設定(for user)
    public function getSettingByUser($user_id, $key) {
        $setting = Setting::where('user_id',$user_id )
            ->where('field', $key)
            ->first();

        return $setting;
    }

    public function getAllSettingByAppIdKey($id) {
        $setting = Setting::where('app_id',$id )
            ->whereNotIn('field', ['key9', 'video'])
            ->get();
        return $setting;
    }

    public function getMapDevicesByUser($user) {

        $type_id = 101;

        if($user->role_id<3) {
            $devices = Device::where('type_id',102)
                ->get();
        } else {
            $devices = Device::where('user_id', $user['id'])
                ->where('type_id',102)
                ->get();
        }
        return $devices;
    }
}
