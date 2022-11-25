<?php

namespace App\Repositories;

use App\Models\Product;
use Yish\Generators\Foundation\Repository\Repository;

class ProductRepository extends Repository
{
    protected $model;

    public function __construct(Product $model) {
        $this->model = $model;
    }
}
