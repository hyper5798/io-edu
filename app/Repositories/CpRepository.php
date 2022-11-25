<?php

namespace App\Repositories;

use App\Models\Cp;
use Yish\Generators\Foundation\Repository\Repository;

class CpRepository extends Repository
{
    protected $model;

    public function __construct(Cp $model) {
        $this->model = $model;
    }
}
