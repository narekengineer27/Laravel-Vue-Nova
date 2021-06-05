<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Requests\Api\Businesses\SearchBusinessesRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessCollectionResource;
use App\Services\Api\BusinessService;

class BusinessSearchController extends Controller
{
    /**
     * @var BusinessService
     */
    private $businessService;

    /**
     * BusinessSearchController constructor.
     * @param BusinessService $businessService
     */
    public function __construct(BusinessService $businessService)
    {
        $this->businessService = $businessService;
    }

    /**
     *
     * @OA\Get(
     *     path="/api/v1/business-search",
     *     summary="search business by query",
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         description="Query",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="lat",
     *         in="query",
     *         description="Latitude",
     *         @OA\Schema(
     *             type="float"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="lng",
     *         in="query",
     *         description="Longitude",
     *         @OA\Schema(
     *             type="float"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="radius",
     *         in="query",
     *         description="Radius in KM",
     *         @OA\Schema(
     *             type="float"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Radius in KM",
     *         @OA\Schema(
     *             type="float"
     *         )
     *     ),
     *   @OA\Response(response="200", description="List businesses"),
     *  )
     * @param SearchBusinessesRequest $request
     * @param BusinessService $businessService
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(SearchBusinessesRequest $request)
    {
        $businesses = $this->businessService->search(
            $request->get('lat'),
            $request->get('lng'),
            $request->get('radius'),
            $request->get('keyword', '*'),
            $request->get('category_id'),
            $this->perPage()
        );
        return $this->sendResponse(
            new BusinessCollectionResource($businesses)
        );
    }
}
