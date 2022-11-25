<?php

namespace App\Services;

use App\Constant\DeviceConstant;
use App\Repositories\AppRepository;
use App\Repositories\DeviceRepository;
use App\Repositories\NodeRepository;
use App\Repositories\NodeScriptRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Validator;
use Yish\Generators\Foundation\Service\Service;

class DeviceService extends Service
{
    protected $repository;
    protected $nodeRepository;
    protected $productRepository;
    protected $appRepository;
    protected $nodeScriptRepository;

    public function __construct(
        DeviceRepository     $repository,
        NodeRepository       $nodeRepository,
        NodeScriptRepository $nodeScriptRepository,
        ProductRepository    $productRepository,
        AppRepository        $appRepository
    )
    {
        $this->repository           = $repository;
        $this->nodeRepository       = $nodeRepository;
        $this->nodeScriptRepository = $nodeScriptRepository;
        $this->productRepository    = $productRepository;
        $this->appRepository        = $appRepository;
    }

    //取得用戶的所有開發裝置
    public function getUserDevelops($user_id, $device_name=null) {
        if($device_name != null) {
            return $this->repository->getUserDevelopsByName($user_id, $device_name);
        } else {
            return $this->repository->getUserDevelops($user_id);
        }
    }
    //取得用戶的所有控制模組
    public function getUserModules($user_id, $device_name=null) {
        if($device_name != null) {
            return $this->repository->getUserModulesByName($user_id, $device_name);
        } else {
            return $this->repository->getUserModules($user_id);
        }
    }
    //取得用戶(控制模組)的所有輸入裝置
    public function getUserInputs($user_id, $device_name=null) {
        return $this->repository->getUserInputs($user_id);
    }
    //取得用戶(控制模組)的所有輸出裝置
    public function getUserOutputs($user_id, $device_name=null) {
        return $this->repository->getUserOutputs($user_id);
    }

    //檢查控制模組的輸出入裝置設定
    public function checkUserNodes($controllers, $user_id) {
        foreach ($controllers as $controller) {

            //控制模組 support:2
            if($controller->type_id == DeviceConstant::MODULE_TYPE || $controller->type_id >= DeviceConstant::ALL_TYPE) {
                if($controller->support != 2) {
                    $node = $this->nodeRepository->findBy('node_mac', $controller->macAddr);
                    if($node == null) {
                        $tmp = [
                            'node_name' => $controller->macAddr,
                            'node_mac'  => $controller->macAddr,
                            'user_id'   => $user_id,
                        ];
                        if($controller->type_id >= DeviceConstant::ALL_TYPE) {
                            $tmp['inputs'] = array("self");
                            $tmp['outputs'] = array("self");
                        }
                        $this->nodeRepository->create($tmp);
                    }
                    $controller->support = 2;
                    $controller->save();
                }
            }

        }
    }

    //取得控制模組的輸出入裝置設定
    public function getUserNodes($user_id) {
        $nodes = $this->nodeRepository->getBy('user_id', $user_id);
        foreach ($nodes as $node) {
            if(isset($node->inputs)) {
                $node->inputs = getRealMacList($node->inputs, $node->node_mac);
            }
            if(isset($node->outputs)) {
                $node->outputs = getRealMacList($node->outputs, $node->node_mac);
            }
        }
        return $nodes;
    }
    //
    public function getDeviceValidator($input)
    {
        $id = (int)$input['id'];
        $rules = [
            'device_name' => 'required',
            'type_id' => 'required',
            //'network_id' => 'required',
            //'user_id' => 'required',
        ];
        if($id == 0) {
            $rules['macAddr'] = 'required|between:12,12';
        }
        $msg = [
            'device_name.required' => trans('device.device_name_required'),
            'macAddr.required' => trans('device.device_mac_required'),
            'macAddr.between' => trans('device.device_mac_length'),
            'type_id.required' => trans('device.type_id_required'),
            //'network_id.required' => trans('device.network_id_required'),
        ];

        return Validator::make($input, $rules, $msg);
    }
    // 新增或更新裝置 ， 若id=0為新增
    public function createOrUpdate($input, $cp_id)
    {
        $id = (int)$input['id'];

        //Default network
        if(!array_key_exists('network_id',$input)) {
            $input['network_id'] = 1;//wifi
        }
        //backend 綁定場域
        if(array_key_exists('setting_id',$input)) {
            $room_id = (int)$input['room_id'];
            if($room_id==0) {
                //未選場域
                $input['setting_id'] = DeviceConstant::SET_NONE;//0:未綁定場域
            } else {
                //選擇場域
                $input['setting_id'] = DeviceConstant::SET_ROOM;//1:已綁定場域
            }
        }

        $device = null;
        if($id==0) {
            $input['status'] = DeviceConstant::ACTIVE_STATUS;
            $input['cp_id'] = $cp_id;
            //product_id:用於產品可取得裝置綁定人
            if(!array_key_exists('product_id', $input)) {
                $input['product_id'] = $this->productRepository->findBy('macAddr', $input['macAddr'])->id;
            }
            if((int)$input['type_id'] == DeviceConstant::USV_TYPE) {//無人船
                $input['support'] = DeviceConstant::UAV_SUPPORT;//支援無人機
            }

            $device = $this->repository->create($input);
        } else {
            unset($input['product_id']);
            unset($input['id']);
            if(array_key_exists('changeMac',$input) && $input['changeMac']) {
                $input['macAddr'] = $input['changeMac'];
                $input['product_id'] = $this->productRepository->findBy('macAddr', $input['macAddr'])->id;
            }
            $this->repository->update($id, $input);
            $device = $this->repository->find($id);
        }

        return $device;
    }
    //檢查控制模組裝置輸出入設定node是否存在
    public function checkForModuleController($device) {
        if($device->type_id == DeviceConstant::MODULE_TYPE || $device->type_id>=DeviceConstant::ALL_TYPE) {
            $node = $this->nodeRepository->findBy('node_mac', $device->macAddr);
            if ($node == null) {
                $this->nodeRepository->create(
                    [
                        'node_name' => $device->device_name,
                        'node_mac' => $device->macAddr,
                        'user_id' => $device->user_id
                    ]
                );
            }
            return true;
        } else {
            return false;
        }
    }
    //檢查控制模組裝置輸出入設定node是否存在
    public function checkForDevelopController($device, $publicDevice=null) {
        if($publicDevice==null) return false;
        if($device->type_id > DeviceConstant::MODULE_TYPE && $device->type_id<DeviceConstant::ALL_TYPE) {
            $apps = $this->appRepository->getBy('device_id', $device->id);
            if($apps->count()==0) {
                return true;
            }
        }
        return false;
    }

    //檢查裝置類型再處理複製或更新
    public function checkAndUpdate($device, $newDevice) {
        if($device->type_id == DeviceConstant::MODULE_TYPE){//控制模組
            $this->checkAndUpdateForModuleController($device, $newDevice);
        }
    }
    //模組型裝置更新node & node_rule
    public function checkAndUpdateForModuleController($device, $newDevice=null) {
            if($newDevice != null) {
                //變更node & rules 到新的裝置
                $this->updateNode($device, $newDevice);
                $this->updateNodeScripts($device, $newDevice);
            } else {
                //不須更新直接刪除node & node_rule
                $node = $this->nodeRepository->findBy('node_mac', $device->macAddr)->delete();
                $nodeRules = $this->nodeScriptRepository->getBy('node_mac', $device->macAddr)->delete();
            }
    }
    //模組型裝置更新node
    public function updateNode($device, $newDevice) {
        $node = $this->nodeRepository->findBy('node_mac', $device->macAddr);
        if($node) {
            $this->nodeRepository->update($node->id,
                [
                    'node_mac'  => $newDevice->macAddr,
                    'node_name' => $newDevice->device_name
                ]
            );
        }
    }
    //模組型裝置更新node_rule
    public function updateNodeScripts($device, $newDevice)
    {
        $nodeScripts = $this->nodeScriptRepository->getBy('node_mac', $device->macAddr);
        foreach ($nodeScripts as $script)
        {
            $this->nodeRepository->update($script->id,
                [
                    'node_mac' => $newDevice->macAddr
                ]
            );
        }
    }
    //取得用戶的所有開發裝置
    public function getDevices($cpId,$typeId, $deviceMac=null) {
        if($deviceMac != null) {
            return $this->getDevicesByMac($deviceMac);
        } else {
            return $this->getDevicesByCPType($cpId, $typeId);
        }
    }
    //取得公司指定類型的所有裝置
    public function getDevicesByCPType($cpId, $typeId) {
        return $this->repository->getCpTypeDevices($cpId, $typeId);
    }
    //取得指定註冊碼的所有裝置
    public function getDevicesByMac($mac=null) {
        return $this->repository->getBy('macAddr', $mac);
    }
    //取得指定註冊碼的所有裝置
    public function findPublicDeviceByTypeId($type_id) {
        return $this->repository->findTypePublicDevices($type_id);
    }
}

//For all in one device
function getRealMacList(Array $list, $mac) {

    for($i=0; $i<count($list); $i++) {
        if($list[$i] == 'self') {
            $list[$i] = $mac;
        }
    }
    return $list;
}
