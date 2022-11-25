<?php

namespace App\Services;

use App\Repositories\NodeScriptRepository;
use Yish\Generators\Foundation\Service\Service;

class NodeScriptService extends  Service
{
    protected $repository;

    public function __construct(NodeScriptRepository $repository) {
        $this->repository = $repository;
    }

    public function checkAndCopyScript($oldMac, $newDevice) {
        if($newDevice->type_id === 99) {//控制模組
            //刪除已有的node 腳本設定
            $newDeviceScript = $this->repository->findBy('node_mac', $newDevice->macAddr);
            if($newDeviceScript) $newDeviceScript->delete();

            $nodeScript = $this->repository->findBy('node_mac', $oldMac);
            if($nodeScript!=null ) {
                //Node::where('node_mac', $input['change_mac'])->delete();
                //change_mac:更換控制模組的註冊碼
                $this->repository->update($nodeScript->id, [
                    'node_mac' => $newDevice->macAddr,
                ]);
            }
        }
    }

    public function destroyOfDevice($device) {
        if($device->type_id === 99) {//控制模組
            $this->repository->findBy('node_mac', $device->macAddr)->delete();
        }
    }
}
