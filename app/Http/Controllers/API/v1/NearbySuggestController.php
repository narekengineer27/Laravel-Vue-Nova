<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\NearbySuggest\NearbySuggestRequest;
use App\Http\Resources\BusinessCollectionResource;
use App\Services\Api\BusinessService;

class NearbySuggestController extends Controller
{
    /**
     * @var BusinessService
     */
    private $businessService;

    /**
     * NearbySuggestController constructor.
     * @param BusinessService $businessService
     */
    public function __construct(BusinessService $businessService)
    {
        $this->businessService = $businessService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/nearby-suggest",
     *     summary="Get business close to lat/lon",
     *     @OA\Parameter(
     *         name="lat",
     *         in="query",
     *         description="Lat of location",
     *         required=true,
     *         @OA\Schema(
     *             type="float"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="lon",
     *         in="query",
     *         description="Lng of location",
     *         required=true,
     *         @OA\Schema(
     *             type="float"
     *         )
     *     ),
     *   @OA\Response(response="200", description="List of BusinessResource")
     *  )
     * @param NearbySuggestRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(NearbySuggestRequest $request)
    {
        $businesses = $this->businessService->getNearby(
            $request->get('lat'),
            $request->get('lon')
        );
        return $this->sendResponse(
            new BusinessCollectionResource($businesses)
        );
    }
}
