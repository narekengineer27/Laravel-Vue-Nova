<?php

namespace App\Nova;

use Acme\MapBox\MapBox;
use Acme\MapField\MapField;
use Laravel\Nova\Fields\ID;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use App\Nova\BusinessOpeningHours;
use App\Nova\Filters\BusinessCategory;
use Laravel\Nova\Fields\BelongsToMany;
use App\Models\Business as ModelBusiness;
use Laravel\Nova\Http\Requests\NovaRequest;

class Business extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = 'App\\Models\\Business';

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * @var bool
     */
    protected static $searchable = true;

    /**
     * @var array
     */
    public static $with = ['attributes', 'totalEmailAttributes'];

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name',
    ];

    /**
     * @var array
     */
    public static $indexDefaultOrder = [
        'internal_score' => 'desc'
    ];

    /**
     * {@inheritdoc }
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        if (cache('business_count'.auth()->id()) == 0) {
            return $query->where('id', 0); // no result
        }
        
        if ($ids = cache('business_ids'.auth()->id())) {
            $query->whereIn('businesses.id', json_decode($ids, true));
        }

        if (empty($request->get('orderBy'))) {
            $query->getQuery()->orders = [];
            $query->orderBy(key(static::$indexDefaultOrder), reset(static::$indexDefaultOrder));
        }

        return $query;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            MapField::make('location')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->hideFromIndex(),
            ID::make('id')
                ->hideFromIndex()
                ->hideFromDetail(),
            Boolean::make('Verified', 'verified'),
            Text::make('name')
                ->asHtml()
                ->displayUsing(function ($name) {
                    return view('partials.business-name', [
                        'name' => $name,
                        'id'   => $this->id,
                    ])->render();
                })
                ->sortable(),
            Text::make('Business Summary', 'name')
                ->asHtml()
                ->displayUsing(function ($name) {
                    return view('partials.business-summary', [
                        'name' => $name,
                        'id'   => $this->id,
                    ])->render();
                })
                ->hideWhenCreating()
                ->hideWhenUpdating(),
            Text::make('uuid')
                ->onlyOnDetail(),
            Text::make('bio')
                ->hideWhenCreating()
                ->hideFromIndex(),
            Avatar::make('Cover', 'cover_path')
                ->disk('s3')
                ->hideWhenCreating()
                ->hideWhenUpdating()
                ->hideFromDetail(),
            Text::make('#reviews', 'total_reviews')
                ->hideWhenUpdating()
                ->hideWhenCreating()
                ->hideFromDetail(),
            Text::make('#posts', 'total_posts')
                ->hideWhenUpdating()
                ->hideWhenCreating()
                ->hideFromDetail(),
            Text::make('#attributes', 'total_attributes')
                ->hideWhenUpdating()
                ->hideWhenCreating()
                ->hideFromDetail(),
            Text::make('#email', 'total_email_attributes')
                ->hideWhenUpdating()
                ->hideWhenCreating()
                ->hideFromDetail(),
            Text::make('lat')
                ->hideFromIndex()
                ->hideFromDetail(),
            Text::make('lng')
                ->hideFromIndex()
                ->hideFromDetail(),
            Image::make('Image', 'avatar')->disk('s3')
                ->creationRules('required', 'image', 'mimes:jpg,jpeg,png,gif'),
            BelongsToMany::make('Categories', 'categories', Category::class),
            HasMany::make('DataAI Keywords', 'keywords', BusinessKeyword::class),
//            new Panel('Posts', [
//                ImageBox::make('postImages')
//                    ->hideFromIndex()
//                    ->hideWhenCreating()
//                    ->hideWhenUpdating(),
//            ]),
            HasMany::make('Open Hours', 'openHours', BusinessOpeningHours::class),
            HasMany::make('Reviews', 'reviews', BusinessReview::class),
            BelongsToMany::make('Business Attributes', 'optionalAttributes', OptionalAttribute::class)
                ->fields(function () {
                    return [
                        Text::make('Description', 'description')
                    ];
                }),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [
            new MapBox,
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new BusinessCategory,
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            //new GenerateBusinessBioAction()
        ];
    }
}
