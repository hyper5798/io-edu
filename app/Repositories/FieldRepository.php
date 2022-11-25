<?php

namespace App\Repositories;

use App\Models\Field;
use Yish\Generators\Foundation\Repository\Repository;

class FieldRepository extends Repository
{
    protected $model;

    public function __construct(Field $model) {
        $this->model = $model;
    }
}
