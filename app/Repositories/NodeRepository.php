<?php

namespace App\Repositories;

use App\Models\Node;
use Yish\Generators\Foundation\Repository\Repository;

class NodeRepository extends Repository
{
    protected $model;

    public function __construct(Node $model) {
        $this->model = $model;
    }
}
