<?php

namespace App\Http\Controllers\API\v1\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Authentication\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Lcobucci\JWT\Parser;

class LoginController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *      summary="Login to the system",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="phone_number",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="remember_me",
     *                     type="boolean"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *      response="200",
     *      description="Access token information",
     *   )
     * )
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(LoginRequest $request)
    {

        $credentials = request(['email', 'password']);

        if($request->filled('email')) {
            if (!\Auth::attempt($credentials)) {
                return $this->sendResponse([
                    'message' => 'Unauthorized',
                ], 401);
            }
        }

        $user = $request->user();

        if($request->filled('phone_number')) {
            $user = User::findByPhoneNumber($request->phone_number);
            if(!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'message' => 'Unauthorized',
                ], 401);
            }
        }

        $tokenResult = $user->createToken('Personal Access Token');

        $token = $tokenResult->token;

        if ($request->remember_me) {
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();

        return $this->sendResponse([
            'access_token' => $tokenResult->accessToken,
            'token_type'   => 'Bearer',
            'expires_at'   => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString(),
            'user'         => new UserResource($user),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/logout",
     *     summary="Log out from the system / invalidate token",
     *     @OA\Response(response="200", description="Logout successfully"),
     *
     * )
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request) {

        /**
         * Currently destroys all sessions where a user
         */
        $request->user()->tokens()->delete();

        return $this->sendResponse('You have been successfully logged out', 200);
    }
}
