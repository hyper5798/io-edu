<?php

namespace App\Repositories;

use App\Models\Announce;
use Yish\Generators\Foundation\Repository\Repository;

class AnnounceRepository extends Repository
{
    protected $model;

    public function __construct(Announce $model) {
        $this->model = $model;
    }
}
