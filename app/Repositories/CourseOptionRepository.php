<?php

namespace App\Repositories;

use App\Models\CourseOption;
use Yish\Generators\Foundation\Repository\Repository;

class CourseOptionRepository extends Repository
{
    protected $model;

    public function __construct(CourseOption $model) {
        $this->model = $model;
    }
}
