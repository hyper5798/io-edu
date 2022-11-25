<?php

namespace App\Repositories;

use App\Models\Device;
use Yish\Generators\Foundation\Repository\Repository;

class DeviceRepository extends Repository
{
    protected $model;

    public function __construct(Device $model) {
        $this->model = $model;
    }
    //查詢用戶透過裝置名稱開發版
    public function getUserDevelopsByName($user_id, $device_name) {
        return $this->model::where('device_name', 'like', '%' . $device_name . '%')
            ->whereBetween('type_id',array(101, 199))
            ->where('user_id', $user_id)
            ->get();
    }
    //查詢用戶所有開發版
    public function getUserDevelops($user_id) {
        return $this->model::where('user_id', $user_id)
            ->whereBetween('type_id',array(101, 199))
            ->get();
    }
    //查詢用戶透過裝置名稱控制模組
    public function getUserModulesByName($user_id, $device_name) {
        return  $this->model::where('device_name', 'like', '%' . $device_name . '%')
            ->where('user_id', $user_id)
            ->whereBetween('type_id',array(99, 255))
            ->whereNotBetween('type_id', array(100, 199))
            ->get();
    }
    //查詢用戶所有控制模組
    public function getUserModules($user_id) {
        return  $this->model::where('user_id', $user_id)
            ->whereBetween('type_id',array(99, 255))
            ->whereNotBetween('type_id', array(100, 199))
            ->get();
    }
    //查詢用戶所有輸入模組
    //1~20 outputs, 21~99 inputs, 99: module controller, 100~199: develop controller, 200: all in one
    public function getUserInputs($user_id) {
        return  $this->model::where('user_id', $user_id)
            ->whereBetween('type_id',array(21, 255))
            ->whereNotBetween('type_id', array(99, 199))
            ->get();
    }
    //查詢用戶所有輸出模組
    public function getUserOutputs($user_id) {
        return  $this->model::where('user_id', $user_id)
            ->whereBetween('type_id',array(1, 255))
            ->whereNotBetween('type_id', array(21, 199))
            ->get();
    }

    //查詢所屬公司指定類型所有控制器
    public function getCpTypeDevices($cpId, $typeId) {
        return  $this->model->ofCp($cpId)->type($typeId)->get();
    }

    public function findTypePublicDevices($typeId) {
        return  $this->model->public()->type($typeId)->first();
    }

}
