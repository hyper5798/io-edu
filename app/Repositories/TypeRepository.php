<?php

namespace App\Repositories;

use App\Models\Type;
use Yish\Generators\Foundation\Repository\Repository;

class TypeRepository extends Repository
{
    protected $model;

    public function __construct(Type $model) {
        $this->model = $model;
    }

    public function getByCategory($category) {
        return $this->model->category($category);
    }
}
