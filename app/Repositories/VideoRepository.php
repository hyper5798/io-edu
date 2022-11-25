<?php

namespace App\Repositories;

use App\Models\Video;
use Yish\Generators\Foundation\Repository\Repository;

class VideoRepository extends Repository
{
    protected $model;

    public function __construct(Video $model) {
        $this->model = $model;
    }
}
