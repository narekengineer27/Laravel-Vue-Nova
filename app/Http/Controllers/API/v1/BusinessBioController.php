<?php

namespace App\Http\Controllers\API\v1;

use App\Models\Business;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\BusinessBioGeneratorService;

class BusinessBioController extends Controller
{
    /**
     * Display the specified resource.
     * @OA\GET(
     *     path="/api/v1/bookmark/{id}",
     *      @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="Business uuid",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Business bio"),
     *
     * )
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $bio = Business::uuid($id)->bio;

        return response()->json([
            'bio' => $bio ?? null,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @OA\Put(
     *     path="/api/v1/bookmark",
     *     summary="Updates a business bio",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="id",
     *                     description="Business ID",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="bio",
     *                     description="Business bio",
     *                     type="string"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Success"),
     *
     * )
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id  = $request->id;
        $bio = $request->bio;

        $business = Business::uuid($id);

        if(empty($bio)){
            $bio = BusinessBioGeneratorService::generateBio($business);
        }

        $business->bio = $bio;

        $business->save();

        return response('Success', 200);
    }
}
