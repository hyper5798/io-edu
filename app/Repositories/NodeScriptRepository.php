<?php

namespace App\Repositories;

use App\Models\NodeScripts;
use Yish\Generators\Foundation\Repository\Repository;

class NodeScriptRepository extends Repository
{
    protected $model;

    public function __construct(NodeScripts $model) {
        $this->model = $model;
    }
}
