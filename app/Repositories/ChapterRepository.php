<?php

namespace App\Repositories;

use App\Models\Chapter;
use Yish\Generators\Foundation\Repository\Repository;

class ChapterRepository extends Repository
{
    protected $model;

    public function __construct(Chapter $model) {
        $this->model = $model;
    }
}
