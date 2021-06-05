<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\MapPresets\GetMapPresetRequest;
use App\Http\Resources\BusinessCollectionResource;
use App\Http\Resources\BusinessResource;
use App\Http\Resources\MapPresetCollectionResource;
use App\Http\Resources\MapPresetResource;
use App\Services\Api\MapPresetService;

class MapPresetsController extends Controller
{

    /**
     * @var MapPresetService
     */
    private $mapPresetService;

    /**
     * MapPresetsController constructor.
     * @param MapPresetService $mapPresetService
     */
    public function __construct(MapPresetService $mapPresetService)
    {
        $this->mapPresetService = $mapPresetService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/map-presets",
     *     summary="Get All Map Presets",
     *   @OA\Response(response="200", description="List of MapPresetResource")
     * )
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $results = $this->mapPresetService->getAll();
        return $this->sendResponse(
            new MapPresetCollectionResource($results)
        );
    }

    /**
     * @OA\Get(
     *     path="/api/v1/map-presets/search",
     *     summary="Search Map Preset",
     *     @OA\Parameter(
     *         name="map_preset_uuid",
     *         in="query",
     *         description="Map Preset UUID",
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
     *         name="lon",
     *         in="query",
     *         description="Longitude",
     *         @OA\Schema(
     *             type="float"
     *         )
     *     ),
     *   @OA\Response(response="200", description="List of BusinessResource")
     * )
     * @param GetMapPresetRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(GetMapPresetRequest $request)
    {
        $results = $this->mapPresetService->search($request->all());
        return $this->sendResponse(
            new BusinessCollectionResource($results)
        );
    }
}
