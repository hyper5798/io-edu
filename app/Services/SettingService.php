<?php

namespace App\Services;

use App\Constant\AppConstant;
use App\Constant\UserConstant;
use App\Repositories\SettingRepository;
use Yish\Generators\Foundation\Service\Service;

class SettingService extends Service
{
    protected $repository;

    public function __construct(SettingRepository $repository) {
        $this->repository = $repository;
    }

    public function copyPublicSetting($publicDevice=null, $device_id) {

        if($publicDevice != null) {
            //$sets = Setting::where("device_id", $publicDeviceId)->get();
            $sets = $this->repository->getBy("device_id", $publicDevice->id);
            if($sets) {
                foreach ($sets as $set) {
                    $arr = $set->toArray();
                    unset($arr['id']);
                    $arr['device_id'] = $device_id;
                    $this->repository->create($arr);
                }
            }
        }
    }

    public function destroyOfDevice($device) {
        $settings = $this->repository->getBy('device_id', $device->id);
        foreach ($settings as $item) {
            $item->delete();
        }
    }

    /*
     * @param $device_id : 裝置ID
     * @param $field :  設定欄位key
     * @param $app_id :  APP ID
     * @return array
     * */
    public function getDeviceSetting($device_id, $field, $app_id) {
        $set = $this->repository-> getDeviceSetting($device_id, $field, $app_id);
        if(count($set) == 0&& $field == AppConstant::CONTROL_SETTING_KEY) {
            $set = $this->repository->getEmptyAppControlSetting();
        }
        return $set;
    }

    /*
     * @param $user_id : 帳戶ID
     * @return array
     * */
    public function saveMailSetting($user_id) {
        $setting = $this->repository-> getUserSetting($user_id, UserConstant::MAIL_COUNT);
        $count = 1;
        if($setting) {
            $count = $setting->set['count'];
            $count = $count + 1;
            $this->repository->update($setting->id, ['set'=>['count'=> $count]]);
        } else {
            $this->repository->create(['user_id'=>$user_id, 'field'=>UserConstant::MAIL_COUNT, 'set'=>['count'=> $count]]);
        }

        return $count;
    }

    /*
     * @param $user_id : 帳戶ID
     * @return int
     * */
    public function destroyMailSetting($user_id) {
        $setting = $this->repository-> getUserSetting($user_id, UserConstant::MAIL_COUNT)->delete();

        return $setting;
    }
}
