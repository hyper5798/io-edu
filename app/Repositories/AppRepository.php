<?php

namespace App\Repositories;

use App\Models\App;
use Yish\Generators\Foundation\Repository\Repository;

class AppRepository extends Repository
{
    protected $model;

    public function __construct(App $model) {
        $this->model = $model;
    }
}
