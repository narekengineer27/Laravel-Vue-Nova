<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Sticker\StickerRequest;
use App\Http\Resources\StickerCategoryResource;
use App\Http\Resources\StickerCollectionResource;
use App\Http\Resources\StickerResource;
use App\Models\Sticker;
use App\Services\Api\StickerCategoryService;
use App\Services\Api\StickerService;
use Illuminate\Http\Request;

class StickersController extends Controller
{

    /**
     * @var StickerService
     */
    private $stickerService;

    /**
     * @var StickerCategoryService
     */
    private $stickerCategoryService;

    /**
     * StickersController constructor.
     * @param StickerService $stickerService
     * @param StickerCategoryService $stickerCategoryService
     */
    public function __construct(StickerService $stickerService, StickerCategoryService $stickerCategoryService)
    {
        $this->stickerService = $stickerService;
        $this->stickerCategoryService = $stickerCategoryService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/stickers",
     *     summary="Get all stickers for category_id",
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Category ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="Tags",
     *         in="query",
     *         description="tags",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *
     *     @OA\Response(response="200", description="List of StickerResource")
     *  )
     * @param StickerRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(StickerRequest $request) {
        $stickers = $this->stickerService->getAll($request->get('category_id'), $request->get('tags'));
        $stickerCats = $this->stickerCategoryService->getAll();
        return $this->sendResponse([
            'stickers' =>  (new StickerCollectionResource($stickers)),
            'categories' => (new StickerCategoryResource($stickerCats))
        ]);
    }
}
