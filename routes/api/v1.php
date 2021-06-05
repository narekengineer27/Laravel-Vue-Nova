<?php
/*
|--------------------------------------------------------------------------
| V1 API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for V1.
|
 */
Route::get('/manifest', function () {
    return [
        'servers' => [
            [
                'label' => 'v0.1',
                'url'   => 'http://104.248.44.122/api/v1',
            ],
            [
                'label' => 'v0.2',
                'url'   => 'http://167.99.193.195/api/v1',
            ],
            [
                'label' => 'Internal Testing 1',
                'url'   => 'http://104.248.253.106/api/v1',
            ],
            [
                'label' => 'Internal Testing 2',
                'url'   => 'http://167.99.93.110/api/v1',
            ],
        ],
    ];
});

Route::put('put-test', function (\Illuminate\Support\Facades\Request $request) {
    return $request;
});

Route::post('/login', 'Authentication\LoginController@store');
Route::post('/register', 'Authentication\RegistrationController@create');

Route::get('/email/verify/{id}', 'Authentication\VerificationController@verify')->name('verification.email');
Route::get('/email/resend', 'Authentication\VerificationController@resend')->name('verification.email.resend');

Route::post('/sms/verify/{id}', 'Authentication\VerificationController@verifySms')->name('verification.sms');
Route::get('/sms/resend', 'Authentication\VerificationController@resendSms')->name('verification.sms.resend');
    

/**
 *  Business
 */
//Route::group(['middleware' => ['auth:api', 'role:admin']], function () {
    Route::get('/businesses/geo-json', 'BusinessesController@geoJson');
    Route::get('/businesses/geo-json/{business_id}', 'BusinessesController@geoJsonByBisinessID');
    
    Route::get('/categories/business-stats', 'CategoriesController@businessStats');
    Route::get('/reviews-datatable/{business_id}', 'BusinessesController@getReviewsDatatable');
    Route::get('/post-images-datatable/{business_id}', 'BusinessesController@getPostImagesDatatable');
//});

Route::post('/businesses/{business}/business-cover', 'BusinessCoverController@store');

Route::group(['middleware' => ['auth:api', 'verified']], function () {
    Route::get('/logout', 'Authentication\LoginController@logout');


    Route::get('test', function () {
        return ['key' => 'value'];
    });

    /**
     * User
     */
    Route::get('users', 'UsersController@index');
    Route::put('users', 'UsersController@update');
    Route::get('users/bookmarks', 'UsersController@bookmarks');
    Route::delete('users', 'UsersController@destroy');

    //Common items
    Route::get('/common', 'CommonItemsController@index');



    /**
     * User categories
     */
    Route::get('/user-categories', 'UserCategoriesController@index');
    Route::post('/user-categories', 'UserCategoriesController@store');

    /**
     * User-owned-businesses
     */
    Route::get('/user-owned-businesses', 'UserBusinessesController@index');

    /**
     * User businesses optional_attributes
     */
    Route::group(['prefix' => '/user-businesses/optional-attributes'], function () {
        Route::get('/', 'UserOptionalAttributesController@index');
        Route::post('/', 'UserOptionalAttributesController@store');
        Route::put('/', 'UserOptionalAttributesController@update');
        Route::delete('/', 'UserOptionalAttributesController@delete');
    });

    /**
     * Business
     */
    Route::post('/businesses', 'BusinessesController@store');
    Route::put('/businesses/{business}', 'BusinessesController@update');
    Route::delete('/businesses/{business}', 'BusinessesController@delete');
    Route::get('/businesses/{business}', 'BusinessesController@show');
    Route::post('/businesses/bookmark', 'BusinessesController@toggleBookmark');
    Route::get('/business-search', 'BusinessSearchController@index');
    Route::get('/top-categories', 'TopCategoriesSearchController@search');
    Route::get('/nearby-suggest', 'NearbySuggestController@index');


    /**
     * Business Bio
     */
    Route::get('/business-bio/{id}', 'BusinessBioController@show');
    Route::put('/business-bio', 'BusinessBioController@update');

    /**
     * Business Bio
     */
    Route::get('/business-bio/{id}', 'BusinessBioController@show');
    Route::patch('/business-bio', 'BusinessBioController@update');

    /**
     * Business Posts
     */
    Route::resource('/business-posts', 'BusinessPostsController')->only([
        'show', 'store', 'update', 'destroy'
    ]);

    Route::get('/business-posts/business/{id}', 'BusinessPostsController@forBusiness');

    Route::get('/active-business-posts', 'ActiveBusinessPostsController@index');

    Route::put('/business-hours/{business}', 'BusinessHoursController@update');
    Route::delete('/business-hours/{business}', 'BusinessHoursController@delete');

    /**
     * Business Reviews
     */
    Route::post('/business-reviews/{business}', 'BusinessReviewsController@store');
    Route::get('/business-reviews/business/{business}', 'BusinessReviewsController@show');
    Route::get('/user-business-reviews/{userId}', 'UserReviewsController@index');

    /**
     * Business Feed
     */
    Route::get('/business-feed/{businessId}', 'BusinessFeedController@index');

    /**
     * User feed
     */
    Route::get('/user-feed', 'UserFeedController@index');

    /**
     * User feed
     */
    Route::get('/user-home-feed', 'UserFeedController@homeFeed');

    /**
     * Images
     */
    Route::any('/face-detection', 'FaceDetectionController@index');

    /**
     * Explore
     */
    Route::get('/explore', 'ExploreController@index');

    /**
     *  Discover
     */
    Route::get('/discover', 'DiscoverController@index');

    /**
     *  Map Presets
     */
    Route::get('/map-presets', 'MapPresetsController@index');
    Route::get('/map-presets/search', 'MapPresetsController@search');

    /**
     * Stickers
     */
    Route::get('/stickers', 'StickersController@index');

    /**
     * Categories
     */
    Route::get('/categories', 'CategoriesController@index');

    /**
     * Ownership
     */
    Route::post('/ownership-requests/{business}', 'OwnershipController@store');
    Route::post('/ownership-requests/{ownershipRequest}/verify', 'OwnershipController@verify')->name('business.verification');

    /**
     * Feed
     */
    Route::get('/feed', 'FeedController@index');
});
