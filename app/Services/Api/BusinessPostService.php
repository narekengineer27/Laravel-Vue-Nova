<?php

namespace App\Services\Api;

use App\Models\BusinessPost;
use App\Utils\ImageUploadService;
use Carbon\Carbon;

class BusinessPostService
{
    /**
     * @var BusinessPost
     */
    private $model;

    /**
     * @var BusinessService
     */
    private $businessService;

    /**
     * @var ImageUploadService
     */
    private $imageUploadService;

    /**
     * BusinessPostService constructor.
     * @param BusinessPost $businessPost
     * @param BusinessService $businessService
     * @param ImageUploadService $imageUploadService
     */
    public function __construct(BusinessPost $businessPost,
                                BusinessService $businessService,
                                ImageUploadService $imageUploadService)
    {
        $this->model = $businessPost;
        $this->businessService = $businessService;
        $this->imageUploadService = $imageUploadService;
    }

    /**
     * @param $perPage
     * @return mixed
     */
    public function getAll($perPage)
    {
        return $this->model->latest()->paginate($perPage);
    }

    /**
     * @param $businessPostId
     * @return mixed
     */
    public function get($businessPostId)
    {
        return $this->model->uuid($businessPostId);
    }

    /**
     * @param $businessUuid
     * @param $perPage
     * @return mixed
     */
    public function getAllPostsForBusiness($businessUuid, $perPage)
    {
        $business = $this->businessService->get($businessUuid);
        return $business->posts()->latest()->paginate($perPage);
    }

    /**
     * @param $data
     * @return mixed
     * @throws \App\Exceptions\GeneralException
     */
    public function create($data)
    {
        $data = $this->prepareData($data);
        $data['business_id'] = $this->businessService->get($data['business_id'])->id;

        /** @var BusinessPost $businessPost */
        $businessPost = $this->model->create($data);
        if ($data['imagePath']) {
            $businessPost->images()->create(['path' => $data['imagePath']]);
        }
        return $businessPost;
    }

    /**
     * @param $data
     * @return BusinessPost
     * @throws \App\Exceptions\GeneralException
     */
    public function update($id, $data)
    {
        // We don't want to do anything with image when updating, only when creating.
        unset($data['photo']);
        $data = $this->prepareData($data);
        $data['business_id'] = $this->businessService->get($data['business_id'])->id;

        /** @var BusinessPost $businessPost */
        $businessPost = $this->get($id)->fill($data);
        $businessPost->update($data);

        return $businessPost;
    }

    /**
     * @param $businessPostUuid
     * @return bool
     */
    public function delete($businessPostUuid)
    {
        $post = $this->model->uuid($businessPostUuid);
        return $post->delete();
    }

    /**
     * @param $data
     * @return mixed
     * @throws \App\Exceptions\GeneralException
     */
    private function prepareData($data)
    {
        $data['user_id'] = auth()->id();
        $imagePath = null;
        if (isset($data['photo'])) {
            $imagePath = $this->imageUploadService->saveImage($data['photo'], 'images');
            unset($data['photo']);
            $data['imagePath'] = $imagePath;
        }
        if (isset($data['expire_date'])) {
            $data['expire_date'] = Carbon::parse($data['expire_date']);
        }
        return $data;
    }

}
