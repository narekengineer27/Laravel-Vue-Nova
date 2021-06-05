<?php

namespace App\Services\Api;

use App\Models\Business;
use App\Models\Category;
use App\Utils\GeoLocation;
use App\Utils\ImageUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Spatie\OpeningHours\OpeningHours;

class BusinessService
{
    /**
     * @var Business
     */
    private $model;

    /**
     * @var GeoLocation
     */
    private $geoLocation;


    /**
     * @var ImageUploadService
     */
    private $imageUploadService;

    /**
     * @var CategoryService
     */
    private $categoryService;

    /**
     * @var BookmarkService
     */
    private $bookmarkService;


    /**
     * BusinessService constructor.
     * @param Business $model
     * @param GeoLocation $geoLocation
     * @param ImageUploadService $imageUploadService
     * @param CategoryService $categoryService
     * @param BookmarkService $bookmarkService
     */
    public function __construct(Business $model,
                                GeoLocation $geoLocation,
                                ImageUploadService $imageUploadService,
                                CategoryService $categoryService,
                                BookmarkService $bookmarkService)
    {
        $this->model = $model;
        $this->geoLocation = $geoLocation;
        $this->imageUploadService = $imageUploadService;
        $this->categoryService = $categoryService;
        $this->bookmarkService = $bookmarkService;
    }

    /**
     * @param $businessUuid
     * @return mixed
     */
    public function get($businessUuid)
    {
        return $this->model->uuid($businessUuid);
    }

    /**
     * @param $lat
     * @param $lon
     * @param $radius
     * @param $keyword
     * @param $categoryId
     * @param $howMany
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function search($lat, $lon, $radius, $keyword, $categoryId, $howMany)
    {
        $boundingBox  = $this->geoLocation->getElasticSearchBounding($lat, $lon, $radius);
        $res =  $this->model->search($keyword)->whereGeoBoundingBox('location', $boundingBox)->take($howMany)->paginate();
        if (count($res) == 0) {
            return $this->model->search('*')->take($howMany)->paginate();
        }
        return $res;
    }

    /**
     * @param $lat
     * @param $lon
     * @param int $howMany
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getNearby($lat, $lon, $howMany = 10)
    {
        $radius = 1;
        $count = 0;
        $result = null;
        while ($count < 10 || $radius <= 10) {
            $boundingBox  = $this->geoLocation->getElasticSearchBounding($lat, $lon, $radius);
            $result = $this->model->search('*')->whereGeoBoundingBox('location', $boundingBox)->take($howMany)->get();
            $count = count($result);
            $radius += 1;
        }
        return $result;
    }

    /**
     * @param int $howMany
     * @return mixed
     */
    public function getUserOwnedBusinesses($howMany = 50)
    {
        return $this->model->where('user_id', auth()->id())->paginate($howMany);
    }

    /**
     * @param $validatedBusiness
     * @return Business
     * @throws \App\Exceptions\GeneralException
     */
    public function create(array $validatedBusiness)
    {
        DB::beginTransaction();

        if (!empty($validatedBusiness['cover_photo'])) {
            $validatedBusiness['cover_photo'] = $this->processImage($validatedBusiness, 'cover_photo');
        }

        if (!empty($validatedBusiness['avatar'])) {
            $validatedBusiness['avatar'] = $this->processImage($validatedBusiness, 'avatar');
        }

        unset($validatedBusiness['category_id']);
        $validatedBusiness['verified'] = true;
        $business = request()->user()->businesses()->create(
            $validatedBusiness
        );

        if(request()->filled('category_id')) {
            $categoryId = Category::find(request('category_id'));
            $business->categories()->attach($categoryId, [
                'relevance' => 100
            ]);
        }

        DB::commit();

        return $business;
    }

    public function update(Business $business, array $validatedBusiness)
    {
        DB::beginTransaction();

        if (!empty($validatedBusiness['cover_photo'])) {
            $validatedBusiness['cover_photo'] = $this->processImage($validatedBusiness, 'cover_photo');
        }

        if (!empty($validatedBusiness['avatar'])) {
            $validatedBusiness['avatar'] = $this->processImage($validatedBusiness, 'avatar');
        }

        unset($validatedBusiness['category_id']);

        $business->update(
            $validatedBusiness
        );

        if(request()->filled('category_id')) {
            $business->categories()->attach(request('category_id'), [
                'relevance' => 100
            ]);
        }

        DB::commit();

        return $business;
    }


    /**
     * Update a Business cover
     * @param Business $business
     * @param $validatedRequest
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

    /**
     * @param $uuid
     * @return bool
     */
    public function bookmark($uuid)
    {
        $business = $this->get($uuid);

        $bookmark = $this->bookmarkService->getBookmarkForBusiness($business->id);

        if ($bookmark){
            $bookmark->delete();
            return false;
        }
        $this->bookmarkService->create($business->id);
        return true;
    }

    /**
     * @param $business
     * @throws \Exception
     */
    public function calculateOpenStatus($business)
    {
        $hoursArray = $business->opening_hours_info;
        if (!OpeningHours::isValid($hoursArray)) {
            throw new \Exception('Badly formatted opening hours supplied to calculateOpenStatus.');
        }
        $openHour = OpeningHours::createAndMergeOverlappingRanges($hoursArray);
        $this->update($business, [
            'is_open' => $openHour->isOpen(),
            'open_description' => $this->getOpenDescription($openHour)
        ]);
    }

    /**
     * @param OpeningHours $openHour
     * @return string
     * @throws \Exception
     */
    private function getOpenDescription(OpeningHours $openHour)
    {
        if ($openHour->isOpen()) {
            $nextClose = $openHour->nextClose(new \DateTime());
            if (($nextClose->getTimestamp() - time()) < 60*30) {
                return 'Closing soon';
            }
            return 'Open';
        }
        $nextOpen = $openHour->nextOpen(new \DateTime());
        if (($nextOpen->getTimestamp() - time()) < 60*30) {
            return 'Opening soon';
        }
        return 'Closed';
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

}
