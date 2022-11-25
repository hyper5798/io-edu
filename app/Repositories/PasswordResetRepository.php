<?php

namespace App\Repositories;

use App\Models\PasswordReset;
use Yish\Generators\Foundation\Repository\Repository;

class PasswordResetRepository extends Repository
{
    protected $model;

    public function __construct(PasswordReset $model) {
        $this->model = $model;
    }
}
