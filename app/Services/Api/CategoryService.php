<?php

namespace App\Services\Api;

use App\Models\Category;

class CategoryService
{
    private $model;

    /**
     * BusinessService constructor.
     * @param Category $model
     */
    public function __construct(Category $model)
    {
        $this->model = $model;
    }

    /**
     * @param $uuid
     * @return mixed
     */
    public function get($uuid)
    {
        return $this->model->uuid($uuid);
    }

}
