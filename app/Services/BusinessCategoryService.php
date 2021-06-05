<?php

namespace App\Services;

use App\Models\Business;
use App\Models\Category;
use App\Models\MapPreset;
use App\Repositories\BusinessesRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\BusinessService;

class BusinessCategoryService
{
	private $bizRepo;
	private $catRepo;
	private $bizServices;

	public function __construct(BusinessService $bizServices)
	{
		$this->bizServices = $bizServices;
	}

	/**
	 * @param null|string $keyword
	 * @return array
	 * @throws \Exception
	 */
	public function search(?string $keyword=''): array
	{
		if ('' == $keyword) {
		    throw new \Exception("The required text option is missing");
		}

		$businessesSuggestions = $this->bizServices->suggestWithCategories($keyword);

		$categories = $this->getCategoriesFromBusinesses($businessesSuggestions['suggestions']);

		$topCategories = [];
		if (!empty($categories))
		{
			usort($categories, function($a, $b) {
			    return $b['score'] <=> $a['score'];
            });

			$topCategories = array_slice($categories, 0, 3);
		}

		return ['categories' => $topCategories, 'keyword' => $keyword];

	}

	private function getCategoriesFromBusinesses($businessesSuggestions = [])
	{
		$categories = [];
		$keepTrack = [];
		foreach ($businessesSuggestions as $biz)
		{
			foreach ($biz['categories'] as $cate)
			{
				if (!in_array($cate['id'], $keepTrack)) {
					$cat = ['category_id' => $cate['pivot']['category_id'], 'name' => $cate['name'], 'score' => 1];
					$categories[$cate['id']] = $cat;
					array_push($keepTrack, $cate['id']);
				}
				$categories[$cate['id']]['score'] += 1;
			}
		}
		return $categories;
	}
}
