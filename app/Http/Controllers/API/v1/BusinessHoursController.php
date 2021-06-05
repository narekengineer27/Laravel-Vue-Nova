<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessHoursResource;

class BusinessHoursController extends Controller
{
	/**
	 *
	 * @OA\Put(
	 *     path="/api/v1/business-hours/{business}",
     *     summary="Update business hours",
     *  @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="open_period_mins",
     *                     description="Start time",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="close_period_mins",
     *                     description="End time",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="business_id",
     *                     description="ID of business",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="wd_0",
     *                     description="Sunday",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="wd_1",
     *                     description="Monday",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="wd_2",
     *                     description="Tuesday",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="wd_3",
     *                     description="Wednesday",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="wd_4",
     *                     description="Thursday",
     *                     type="string"
     *                 ),
     *                  @OA\Property(
     *                     property="wd_5",
     *                     description="Friday",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="wd_6",
     *                     description="Saturday",
     *                     type="string"
     *                 ),
     *             )
     *         ),
     *     ),
	 *   @OA\Response(response="200", description="BusinessHoursResource")
	 * )
	 *
	 * Update the specified resource in storage.
	 */
    public function update(Request $request, Business $business)
    {
    	$this->validate($request, [
    	    'open_period_mins' => ['required'],
    	    'close_period_mins'  => ['required'],
    	    'business_id' => ['required']
    	]);

    	$transformedBusinessHour = new BusinessHoursResource($businessHourHandler->update($id, $request));

    	return response($transformedBusinessHour, 200);

    }
}
