<?php

namespace App\Services\Api;

use App\Models\StickerCategory;

class StickerCategoryService
{
    /**
     * @var BusinessPost
     */
    private $model;


    /**
     * BusinessPostService constructor.
     * @param StickerCategory $model
     */
    public function __construct(StickerCategory $model)
    {
        $this->model = $model;
    }

    /**
     * @param $categoryId
     * @param $tags
     * @return mixed
     */
    public function getAll()
    {
        return $this->model->get();
    }

}
