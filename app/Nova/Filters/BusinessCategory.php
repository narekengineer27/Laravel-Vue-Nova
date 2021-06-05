<?php

namespace App\Nova\Filters;

use App\Models\Category;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class BusinessCategory extends Filter
{
    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        return $query
            ->select('businesses.*')
            ->join('business_category', 'businesses.id', '=', 'business_category.business_id')
            ->where('business_category.category_id', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function options(Request $request)
    {
        $categories = [];

        if ($businesses = cache('business_builder'.auth()->id())) {
            // a
            // a-a
            // a-a-a
            $businesses->flatMap(function ($business) {
                return $business['_source']['categories'];
            })->unique()
            ->sortBy('name')
            ->filter(function ($category) use (&$categories) {
                return strpos($category['name'], '-') !== false ? true : ($categories[$category['name']] = $category['id']) && false;
            })->filter(function ($category) use (&$categories) {
                return substr_count($category['name'], '-') > 1 ? true : ($categories[$category['name']] = $category['id']) && false;
            })->filter(function ($category) use (&$categories) {
                return substr_count($category['name'], '-') > 2 ? true : ($categories[$category['name']] = $category['id']) && false;
            })->filter(function ($category) use (&$categories) {
                return substr_count($category['name'], '-') > 3 ? true : ($categories[$category['name']] = $category['id']) && false;
            })->filter(function ($category) use (&$categories) {
                return substr_count($category['name'], '-') > 4 ? true : ($categories[$category['name']] = $category['id']) && false;
            });
        }

        return $categories;
    }
}
