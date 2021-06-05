<?php

namespace App\Services\Api;

use App\Models\BusinessPost;
use App\Models\Sticker;
use App\Utils\ImageUploadService;
use Carbon\Carbon;

class StickerService
{
    /**
     * @var BusinessPost
     */
    private $model;


    /**
     * BusinessPostService constructor.
     * @param Sticker $model
     */
    public function __construct(Sticker $model)
    {
        $this->model = $model;
    }

    /**
     * @param $categoryId
     * @param $tags
     * @return mixed
     */
    public function getAll($categoryId, $tags)
    {
        $stickers   = $this->model->with('categories');

        if (null !== $categoryId) {
            $stickers->whereHas('categories', function ($query) use ($categoryId) {
                $query->whereStickerCategoryId($categoryId);
            });
        }

        if (null !== $tags) {
            $tags = explode(",", $tags);
            $stickers->where(function($query) use ($tags) {
                foreach ($tags as $tag) {
                    $query->orWhereRaw("FIND_IN_SET('$tag', tags)");
                }
            });
        }

        return $stickers->get();
    }

}
