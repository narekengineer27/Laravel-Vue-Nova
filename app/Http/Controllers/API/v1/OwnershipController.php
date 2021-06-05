<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BusinessOwnership\StoreOwnershipRequest;
use App\Http\Resources\OwnershipRequestResource;
use App\Models\Business;
use App\Models\OwnershipRequest;
use App\Services\Api\BusinessOwnershipService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OwnershipController extends Controller
{

    private $businessOwnershipService;

    /**
     * OwnershipController constructor.
     * @param BusinessOwnershipService $businessOwnershipService
     */
    public function __construct(BusinessOwnershipService $businessOwnershipService)
    {
        $this->businessOwnershipService = $businessOwnershipService;
    }



    /**
     * @OA\Post(
     *     path="/api/v1/ownership-requests/{business}",
     *     summary="Submit business ownership request.",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="business uuid",
     *                     type="string"
     *                 ),
     *             ),
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="method",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Ownership requests status - boolean",
     *     )
     * )
     * @param StoreOwnershipRequest $request
     * @param Business $business
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function store(Business $business, StoreOwnershipRequest $request)
    {
        $businessOwnershipService = $this->businessOwnershipService->create($business, $request->validated());
        return $this->sendResponse(new OwnershipRequestResource($businessOwnershipService), 201);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/ownership-requests/{ownershipRequest}/verify",
     *     summary="Submit business ownership verification request.",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="token",
     *                     type="string"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Ownership requests status - boolean",
     *     )
     * )
     * @param OwnershipRequest $ownershipRequest
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function verify(OwnershipRequest $ownershipRequest, Request $request)
    {
        $verificationResult = $this->businessOwnershipService->verify($ownershipRequest, $request);
        return $this->sendResponse($verificationResult, 201);
    }
}
