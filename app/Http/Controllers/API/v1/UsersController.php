<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Resources\BookmarkCollectionResource;
use App\Http\Resources\BookmarkResource;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\Api\Users\StoreUserRequest;
use App\Http\Requests\Api\Users\UpdateUserRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\Api\UserService;
use Illuminate\Support\Facades\Log;

class UsersController extends Controller
{

    /**
     * @var UserService
     */
    private $userService;

    /**
     * UsersController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Return current logged in user
     * @OA\Get(
     *     path="/api/v1/users/",
     *
     *   @OA\Response(response="200", description="List of UserResource")
     * )
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $user = $this->userService->getCurrent();
        return $this->sendResponse(
            new UserResource($user)
        );
    }

    /**
     * Return current user's bookmarks
     * @OA\Get(
     *     path="/api/v1/users/bookmarks",
     *
     *   @OA\Response(response="200", description="List of BookmarkResource")
     * )
     * @return \Illuminate\Http\JsonResponse
     */
    public function bookmarks()
    {
        $bookmarks = $this->userService->getBookmarks(auth()->id());
        return $this->sendResponse(
            new BookmarkCollectionResource($bookmarks)
        );
    }

    /**
     * Update current logged in user
     * @OA\Put(
     *     path="/api/v1/users",
     *   @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="name",
     *                     description="Name",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     description="Email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="phone_number",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="age_group",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="gender",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="cover_photo",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="avatar_photo",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="bio",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="allow_location_tracking",
     *                     type="boolean"
     *                 ),
     *                 @OA\Property(
     *                     property="post_publicly",
     *                     type="boolean"
     *                 ),
     *                 @OA\Property(
     *                     property="t_c_agreed",
     *                     type="boolean"
     *                 ),
     *                 @OA\Property(
     *                     property="profile_visible",
     *                     type="boolean"
     *                 ),
     *             )
     *         ),
     *     ),
     *  @OA\Response(response="200", description="UserResource")
     * )
     *
     * @param UpdateUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \App\Exceptions\GeneralException
     */
    public function update(UpdateUserRequest $request)
    {
        return $this->sendResponse(
            new UserResource($this->userService->update(auth()->id(), $request->validated()))
        );
    }

    /**
     * Delete current logged in user
     * @OA\Delete(
     *     path="/api/v1/users",
     *
     *   @OA\Response(response="200", description="bool")
     * )
     * @return \Illuminate\Http\JsonResponse
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy()
    {
        return $this->sendResponse([
            'result' => $this->userService->delete(auth()->id())
        ]);
    }
}
