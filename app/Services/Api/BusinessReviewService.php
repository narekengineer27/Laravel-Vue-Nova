<?php

namespace App\Services\Api;

use App\Models\Business;
use App\Models\BusinessReview;
use App\Utils\GeoLocation;
use App\Utils\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class BusinessReviewService
{
    /**
     * @var Business
     */
    private $model;


    /**
     * @var ImageUploadService
     */
    private $imageUploadService;
    /**
     * @var BusinessService
     */
    private $businessService;


    /**
     * BusinessService constructor.
     * @param BusinessReview $model
     * @param ImageUploadService $imageUploadService
     * @param BusinessService $businessService
     */
    public function __construct(BusinessReview $model, ImageUploadService $imageUploadService, BusinessService $businessService)
    {
        $this->model = $model;
        $this->imageUploadService = $imageUploadService;
        $this->businessService = $businessService;
    }

    /**
     * Update a Business cover
     * @param $business
     * @param array $data
     * @return Business
     * @throws \App\Exceptions\GeneralException
     */
    public function updateCover(Business $business, $validatedRequest)
    {
        if (!empty($validatedRequest['cover_photo'])) {
            $validatedRequest['cover_photo'] = $this->processImage($validatedRequest, 'cover_photo');
        }

        $business->fill($validatedRequest)->save();
        return $business;
    }


    public function create(Business $business, $validatedRequest)
    {
        $review = $business->reviews()->create([
           'user_id' => request()->user()->id,
           'score'   => $validatedRequest['score'],
            'comment' => $validatedRequest['comment']
        ]);


        if (!empty($validatedRequest['review_photo'])) {
            $validatedRequest['review_photo'] = $this->processImage($validatedRequest, 'review_photo');

            $review->update([
                'review_photo' =>  $validatedRequest['review_photo']
            ]);
        }

        return $review;
    }

    /**
     * @param $data
     * @param $key
     * @return string|null
     * @throws \App\Exceptions\GeneralException
     */
    private function processImage($data, $key)
    {
        if (empty($data[$key])) {
            return null;
        }
        if ($data[$key] instanceof UploadedFile) {
            return $this->imageUploadService->saveImage($data[$key], 'images');
        }

        return null;
    }

    public function getForBusiness($businessUuid)
    {
        $business = $this->businessService->get($businessUuid);
        return $this->model->where('business_id', $business->id)->latest()->paginate();
    }
}
