<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\BusinessOptionalAttributeResource;
use App\Models\Business;
use App\Models\OptionalAttribute;
use App\Rules\Uuid;
use Illuminate\Http\Request;

class UserOptionalAttributesController extends Controller
{

    /**
     * @OA\Get(
     *   path="/api/v1/user-businesses/optional-attributes",
     *   summary="Get optional attributes",
     *        @OA\Parameter(
     *         name="Tags",
     *         in="query",
     *         description="tags",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *   @OA\Response(response="200", description="Businesses optional attirbutes"),
     * )
     * @return mixed
     */
    public function index()
    {
        return BusinessOptionalAttributeResource::collection(OptionalAttribute::all());
    }

    /**
     *  @OA\Post(
     *   path="/api/v1/user-businesses/optional-attributes",
     *   summary="Save optional attribute",
     *  @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="business_id",
     *                     description="Business UUID",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="optional_attribute_id",
     *                     description="Optional Attribute UUID",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     description="Description",
     *                     type="string"
     *                 ),
     *             )
     *         ),
     *     ),
     *   @OA\Response(response="200", description="Created business optional attirbutes"),
     * )
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'business_id'     => ['required', 'exists:businesses,uuid', new Uuid],
            'optional_attribute_id' => ['required', 'exists:optional_attributes,uuid'],
            'description' => ['nullable', 'string', 'max:255']
        ]);

        $optionalAttribute = OptionalAttribute::uuid($request->get('optional_attribute_id'));
        $business = Business::uuid($request->get('business_id'));

        if ((int)$business->user_id === (int)$request->user()->id) {
            $business->optionalAttributes()->attach($optionalAttribute, ['description' => $request->input('description')]);
        } else {
            return response()->json([], 403);
        }

        return BusinessOptionalAttributeResource::collection($business->optionalAttributes);
    }

    /**
     *  @OA\Put(
     *   path="/api/v1/user-businesses/optional-attributes",
     *     summary="Update optional attribute",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="business_id",
     *                     description="Business UUID",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="optional_attribute_id",
     *                     description="Optional Attribute UUID",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     description="Description",
     *                     type="string"
     *                 ),
     *             )
     *         ),
     *     ),
     *   @OA\Response(response="200", description="Updated business optional attirbutes"),
     * )
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'business_id' => ['required', 'exists:businesses,uuid', new Uuid],
            'optional_attribute_id' => ['required', 'exists:optional_attributes,uuid', new UUid],
            'description' => ['nullable', 'string', 'max:255']
        ]);

        $business = Business
            ::where('uuid', $request->get('business_id'))
            ->whereHas('optionalAttributes', function ($query) use ($request) {
                $query->where('optional_attributes.uuid', $request->get('optional_attribute_id'));
        })->firstOrFail();

        if ((int)$business->user_id === (int)$request->user()->id) {
            $optionalAttribute = OptionalAttribute::uuid($request->get('optional_attribute_id'));
            $business->optionalAttributes()->updateExistingPivot($optionalAttribute, ['description' => $request->input('description')]);
        } else {
            return response()->json([], 403);
        }

        return BusinessOptionalAttributeResource::collection($business->optionalAttributes);
    }

    /**
     *  @OA\Delete(
     *   path="/api/v1/user-businesses/optional-attributes",
     *     summary="Delete optional attribute",
     *  @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="business_id",
     *                     description="Business UUID",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="optional_attribute_id",
     *                     description="Optional Attribute UUID",
     *                     type="string"
     *                 ),
     *             )
     *         ),
     *     ),
     *   @OA\Response(response="200", description="Deleted business optional attirbutes"),
     * )
     * @param Request $request
     * @return mixed
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete(Request $request)
    {
        $this->validate($request, [
            'business_id' => ['required', 'exists:businesses,uuid', new Uuid],
            'optional_attribute_id' => ['required', 'exists:optional_attributes,uuid'],
        ]);

        $optionalAttribute = OptionalAttribute::uuid($request->get('optional_attribute_id'));
        $business = Business::uuid($request->get('business_id'));

        if ((int)$business->user_id === (int)$request->user()->id) {
            $business->optionalAttributes()->detach($optionalAttribute);
        } else {
            return response()->json([], 403);
        }

        return BusinessOptionalAttributeResource::collection($business->optionalAttributes);
    }
}
