<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\OptionalAttributeResource;
use App\Http\Resources\StickerResource;
use App\Models\BusinessAttribute;
use App\Models\Category;
use App\Models\OptionalAttribute;
use App\Models\Sticker;
use Illuminate\Http\Request;
use Cache;

class CommonItemsController extends Controller
{
    /**
     *  @OA\Get(
     *     path="/api/v1/common",
     *     summary="Get commonly used data, cached",
     *     @OA\Response(response="200", description="Common items")
     *  )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $data = Cache::remember('common_items', 60, function() {
            return [
                'categories' => CategoryResource::collection(Category::get()->unique('name')),
                'business_attrs' => OptionalAttributeResource::collection(OptionalAttribute::get()->unique('name'))
            ];
        });
        return $this->sendResponse($data);
    }
}
