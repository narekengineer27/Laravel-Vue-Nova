<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Users\GetUserReviewsRequest;
use App\Http\Resources\BusinessReviewCollectionResource;
use App\Http\Resources\StickerResource;
use App\Models\BusinessAttribute;
use App\Models\Category;
use App\Models\Sticker;
use App\Services\Api\UserService;
use Illuminate\Http\Request;
use Cache;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class UserReviewsController extends Controller
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * UserReviewsController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     *  @OA\Get(
     *     path="/api/v1/user-business-reviews/{userId}",
     *     summary="Get business reviews made by a user",
     *     @OA\Response(response="200", description="Common items")
     *  )
     * @param $userId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($userId) {
        $perPage = request()->get('perPage') ?? 10;
        $data = $this->userService->getUserReviews($userId, $perPage);

        return $this->sendResponse(new BusinessReviewCollectionResource($data));
    }
}
