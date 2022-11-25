<?php

namespace App\Services;

use Yish\Generators\Foundation\Service\Service;
use App\Repositories\AppRepository;

class AppService extends Service
{
    protected $repository;

    public function __construct(AppRepository $repository) {
        $this->repository = $repository;
    }

    public function checkAndCopyPublicApps($publicDevice = null, $device, $userId) {
        //取得公共裝置所有應用
        if($publicDevice !=null) {
            $apps = $this->repository->getBy('device_id', $publicDevice->id);
            if($apps) {
                foreach ($apps as $old) {
                    //從舊的複製
                    $arr = $old->toArray();
                    unset($arr['id']);
                    $app = $this->repository->create($arr);

                    $this->repository->update($app->id, [
                        'api_key'   => getAPIkey($app->id, $userId),
                        'device_id' => $device->id,
                        'macAddr'   => $device->macAddr
                    ]);
                }
            }
        }
    }

    public function destroyOfDevice($device) {
        if($device->type_id>101 && $device->type_id<200 ){
            $apps = $this->repository->getBy('device_id', $device->id);
            foreach ($apps as $app) {
                $app->delete();
            }
        }
    }

    public function changeMacToApps($device) {
        $apps = $this->repository->getBy('device_id', $device->id);
        if($apps) {
            foreach ($apps as $app) {
                $this->repository->update($app->id, [
                    'macAddr'   => $device->macAddr
                ]);
            }
        }

    }
}

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
