<?php

namespace App\Services\Api;

use App\Models\Bookmark;

class BookmarkService
{
    private $model;

    /**
     * BusinessService constructor.
     * @param Bookmark $model
     */
    public function __construct(Bookmark $model)
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

    public function create($businessId)
    {
        $bookmark = new $this->model;
        $bookmark->user_id = auth()->id();
        $bookmark->business_id = $businessId;
        $bookmark->save();
    }


    /**
     * @param $businessId
     * @return mixed
     */
    public function getBookmarkForBusiness($businessId)
    {
        return $this->model->where('business_id', $businessId)->where('user_id', auth()->id())->first();
    }


}
