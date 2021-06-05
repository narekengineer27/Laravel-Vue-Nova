<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Rules\Uuid;
use App\Models\Business;
use App\Models\BusinessCategory;

class CategoriesController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/categories",
     *     summary="Returns all categories.",
     *     @OA\Response(response="200", description="List of all categories")
     *
     * )
     * @param CategoryService $CategoryService
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(CategoryService $CategoryService)
    {
        $results = $CategoryService->getActive();
        foreach ($results as $key => $result) {
        	if(trim($result->icon) !== '') {
	        	$result->icon = asset('storage/' . $result->icon);
	        }
        }
        return CategoryResource::collection($results);
    }

    public function businessStats(CategoryService $CategoryService, Request $request)
    {

        $categories = BusinessCategory::leftJoin('categories', 'business_category.category_id', '=', 'categories.id');
        if($request->search)  $categories = $categories->where('categories.name', 'like', '%'.$request->search.'%');
         $categories = $categories->groupBy('categories.name')
            ->orderBy(\DB::raw('count(business_category.id)'), 'desc')
            ->select("categories.name", \DB::raw('count(business_category.id) as businessCount'))
            ->limit(20)
            ->get();
        return $this->sendResponse($categories, 200);
    }
}
