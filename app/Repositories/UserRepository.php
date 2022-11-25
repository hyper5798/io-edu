<?php

namespace App\Repositories;

use App\Models\User;
use Yish\Generators\Foundation\Repository\Repository;

class UserRepository extends Repository
{
    protected $model;

    public function __construct(User $model) {
        $this->model = $model;
    }
}
