<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\BusinessPosts\DeleteBusinessPostRequest;
use App\Http\Requests\Api\BusinessPosts\StoreBusinessPostRequest;
use App\Http\Requests\Api\BusinessPosts\UpdateBusinessPostRequest;
use App\Http\Resources\BusinessPostResource;
use App\Services\Api\BusinessPostService;

class BusinessPostsController extends Controller
{
    /**
     * @var BusinessPostService
     */
    private $businessPostService;

    /**
     * BusinessPostsController constructor.
     * @param BusinessPostService $businessPostService
     */
    public function __construct(BusinessPostService $businessPostService)
    {
        $this->businessPostService = $businessPostService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/business-posts",
     *     summary="Create business post",
     *  @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="business_id",
     *                     description="UUID of business",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="text",
     *                     description="Post text",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="photo",
     *                     description="photo file",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="expire_date",
     *                     type="string"
     *                  )
     *             )
     *         ),
     *     ),
     *     @OA\Response(response="201", description="BusinessPostResource"),
     *   )
     * )
     * @param StoreBusinessPostRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\GeneralException
     */
    public function store(StoreBusinessPostRequest $request)
    {
        return $this->sendResponse(
            new BusinessPostResource($this->businessPostService->create($request->all()))
        , 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/business-posts",
     *     summary="Update business post by ID",
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="id",
     *                     description="id of business post",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="business_id",
     *                     description="id of business",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="text",
     *                     description="id of user",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="photo",
     *                     description="base64 encoded image",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="expire_date",
     *                     type="string"
     *                  )
     *             )
     *         ),
     *     ),
     *     @OA\Response(response="200", description="Business post updated"),
     *     @OA\Response(response="400", description="Invalid given data"),
     *     @OA\Response(response="404", description="Business post not found"),
     *   )
     * )
     * @param UpdateBusinessPostRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\GeneralException
     */
    public function update(UpdateBusinessPostRequest $request, $id)
    {
        $businessPost = $this->businessPostService->update($id, $request->all());
        return $this->sendResponse(
            new BusinessPostResource($businessPost)
        , 200);
    }

    /**
     *
     * @OA\Delete(
     *     path="/api/v1/business-posts",
     *     summary="Delete a business post",
     *  @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="id",
     *                     description="id of business post",
     *                     type="string"
     *                 ),
     *         ),
     *     ),
     * ),
     *   @OA\Response(response="200", description="Business Post Resource")
     *  )
     *)
     * @param DeleteBusinessPostRequest $request
     * @param $businessPostUuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(DeleteBusinessPostRequest $request, $businessPostUuid)
    {
        return $this->sendResponse([
            'success' => $this->businessPostService->delete($businessPostUuid)
        ]);
    }

    /**
     *
     * @OA\Get(
     *     path="/api/v1/business-posts/business/{$businessUuid}",
     *     summary="Get all business posts for business",
     *     @OA\Parameter(
     *         name="business_id",
     *         in="query",
     *         description="ID of business",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *
     *   @OA\Response(response="200", description="List of Business Post")
     *  )
     *
     * @param $businessUuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function forBusiness($businessUuid) {
        return $this->sendResponse(
            BusinessPostResource::collection(
                $this->businessPostService->getAllPostsForBusiness($businessUuid, $this->perPage())
            )
        );
    }


    /**
     * @OA\Get(
     *     path="/api/v1/business-posts/{businessPostUuid}",
     *      summary="Get individual business post",
     *
     *   @OA\Response(response="200", description="Businesse Post")
     * )
     * @param $businessPostUuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($businessPostUuid) {
        return $this->sendResponse(
            new BusinessPostResource(
                $this->businessPostService->get($businessPostUuid)
            )
        );
    }
}
