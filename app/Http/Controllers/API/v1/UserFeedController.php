<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserFeed\UserFeedRequest;
use App\Http\Resources\BusinessPostCollectionResource;
use App\Http\Resources\UserFeedResource;
use App\Services\FeedService;
use Illuminate\Http\Request;

class UserFeedController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/user-feed",
     *     summary="Get user feed",
     *   @OA\Response(response="200", description="List of UserFeedResource")
     * )
     * @param FeedService $feedService
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     * @throws \Exception
     */
    public function index(FeedService $feedService)
    {
        $feed = $feedService->forUser();

        return UserFeedResource::collection($feed);
    }

    /**
     * @OA\GET(
     *     path="/api/v1/user-home-feed",
     *     summary="Get user home feed",
     *   @OA\Response(response="200", description="List of user's home feed")
     * )
     * @param FeedService $feedService
     * @param UserFeedRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function homeFeed(FeedService $feedService, UserFeedRequest $request)
    {
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        $distance = $request->input('distance');
        $feed = $feedService->forHomeFeed($lat, $lng, $distance);
        return $this->sendResponse(
            new BusinessPostCollectionResource($feed)
        );
    }
}
