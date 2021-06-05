<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Businesses\StoreBusinessCoverPhoto;
use App\Http\Resources\BusinessResource;
use App\Models\Business;
use App\Models\BusinessPost;
use App\Models\BusinessPostImage;
use App\Services\Api\BusinessService;
use Illuminate\Http\Request;

class BusinessCoverController extends Controller
{
    /**
     * @var BusinessService
     */
    private $businessService;

    /**
     * UsersController constructor.
     * @param UserService $userService
     */
    public function __construct(BusinessService $businessService)
    {
        $this->businessService = $businessService;
    }

    /**
     *  @OA\Post(
     *     path="/api/v1/business-cover",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="cover_image",
     *                     description="File for business cover",
     *                     type="file"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Status OK"),
     *  )
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Business $business, StoreBusinessCoverPhoto $request) {
            return $this->sendResponse(
                new BusinessResource($this->businessService->updateCover($business, $request->validated()))
            );
    }
}
