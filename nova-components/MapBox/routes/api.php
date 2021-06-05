<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Card API Routes
|--------------------------------------------------------------------------
|
| Here is where you may register API routes for your card. These routes
| are loaded by the ServiceProvider of your card. You're free to add
| as many additional routes to this file as your card may require.
|
*/

Route::get('/places', function (Request $request) {
    return response()->json(url('/api/v1/places/geo-json'));
});

Route::get('/business-totals', function (Request $request) {
    return response()->json([
        'totalBusinesses' => cache('business_count'.auth()->id()),
        'totalReviews' => cache('review_count'.auth()->id()),
        'totalImages' => cache('post_count'.auth()->id()),
    ]);
});
