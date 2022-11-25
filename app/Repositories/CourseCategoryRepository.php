<?php

namespace App\Repositories;

use App\Models\CourseCategory;

use Illuminate\Database\Eloquent\Builder;
use Yish\Generators\Foundation\Repository\Repository;

class CourseCategoryRepository extends Repository
{
    protected $model;

    public function __construct(CourseCategory $model) {
        $this->model = $model;
    }

    public function getWithCourseCount() {
        return  $this->model->withCount(
            ['courses' => function (Builder $query)
                {
                    $query->where('isShow', 1);
                }
            ])->get();
    }
}
