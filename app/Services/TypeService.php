<?php

namespace App\Services;

use App\Repositories\TypeRepository;
use Yish\Generators\Foundation\Service\Service;

class TypeService extends Service
{
    protected $repository;

    public function __construct(TypeRepository $repository) {
        $this->repository = $repository;
    }
    //檢查是否有輸入類型Id，若無(id=0) 輸入就用第一個類型
    public function checkTypeId($type_id=0, $types=null) {
       //如果type_id =0,request表示沒有type_id ,取得
        //$types = $this->repository->getByCategory($category)->get();
        if($types != null && $types->count() > 0 && $type_id ==0 ){
            $type_id = $types[0]->type_id ;
        }
        return $type_id;
    }

    public function getTypesByCategory($category) {
        return $this->repository->getByCategory($category)->orderBy('id','ASC')->get();
    }
}
