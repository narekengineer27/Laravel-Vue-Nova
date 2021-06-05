<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessCollectionResource;
use App\Http\Resources\BusinessResource;
use App\Models\Business;
use App\Models\User;
use App\Rules\Uuid;
use App\Services\Api\BusinessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBusinessesController extends Controller
{
    /**
     * @var BusinessService
     */
    private $businessService;

    /**
     * UserBusinessesController constructor.
     * @param BusinessService $businessService
     */
    public function __construct(BusinessService $businessService)
    {
        $this->businessService = $businessService;
    }

    /**
     * @OA\Get(
     *      path="/api/v1/user-owned-businesses",
     *      summary="Get user owned businesses",
     *      @OA\Response(response="200", description="Businesses owned by authed user"),
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $businesses = $this->businessService->getUserOwnedBusinesses($this->perPage());
        return $this->sendResponse(
            new BusinessCollectionResource($businesses)
        );
    }
}
