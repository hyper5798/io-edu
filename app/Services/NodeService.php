<?php

namespace App\Services;

use App\Repositories\NodeRepository;
use Yish\Generators\Foundation\Service\Service;

class NodeService extends Service
{
    protected $repository;

    public function __construct(NodeRepository $repository) {
        $this->repository = $repository;
    }

    public function checkAndCopyNode($oldMac, $newDevice) {
        if($newDevice->type_id === 99) {//控制模組
            //刪除已有的node設定
            $newDeviceNode = $this->repository->findBy('node_mac', $newDevice->macAddr);
            if($newDeviceNode) $newDeviceNode->delete();

            $node = $this->repository->findBy('node_mac', $oldMac);
            if($node!=null ) {
                //Node::where('node_mac', $input['change_mac'])->delete();
                //change_mac:更換控制模組的註冊碼
                $this->repository->update($node->id, [
                    'node_mac' => $newDevice->macAddr,
                    'node_name'=>  $newDevice->device_name
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
