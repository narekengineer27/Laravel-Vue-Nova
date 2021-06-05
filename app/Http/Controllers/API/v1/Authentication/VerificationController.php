<?php

namespace App\Http\Controllers\API\v1\Authentication;

use Illuminate\Http\Request;
use App\Events\EmailVerified;
use App\Events\SmsCodeVerified;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;

class VerificationController extends Controller
{
    public function __construct()
    {
        $this->middleware('signed')->only('verify');
        $this->middleware('auth:api');
    }

    /**
     * @OA\Get(
     *     path="/api/v1/email/verify/{id}",
     *     summary="Post to email verification MUST contain the authorization bearer token in Headers. Simply, the User must be logged in to verify their identity.",
     *     @OA\Response(
     *      response="200",
     *      description="'verification': true"
     *  )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request)
    {
        if ($request->route('id') != $request->user()->getKey()) {
            throw new AuthorizationException;
        }

        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'verification' => 'Email has been already verified.'
            ]);
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new EmailVerified($request->user()));
        }

        $request->user()->update([
            'verified' => true
        ]);

        return response()->json([
            'verification' => true
        ]);
    }

    /**
     * Resend the email verification notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json('User already have verified email.', 422);
        }

        $request->user()->sendVerification();

        return response()->json('Email has been reseneded.');
    }

    /**
     * @OA\Post(
     *     path="/api/v1/sms/verify",
     *     summary="Post a 5 digit verification PIN, Headers MUST contain the authorization bearer token.",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="verification_code",
     *                     type="integer"
     *                 ),
     *                 example={"verification_code": 24465}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *      response="200",
     *      description="'verification': true",
     *   )
     *  )
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verifySms(Request $request)
    {
        if ($request->route('id') != $request->user()->getKey()) {
            throw new AuthorizationException;
        }

        if ($request->user()->hasVerifiedCode()) {
            return response()->json([
                'verification' => 'Verification code has been already verified.'
            ]);
        }

        if ($request->verification_code != $request->user()->verification_code) {
            return response()->json(['verification' => 'Wrong verification code.']);
        }

        event(new SmsCodeVerified($request->user()));

        $request->user()->update(['verified' => true]);
    
        return response()->json(['verification' => true]);
    }

    public function resendSms(Request $request)
    {
        if ($request->user()->hasVerifiedCode()) {
            return response()->json('User has already verified sms.', 422);
        }

        $request->user()->sendVerification();

        return response()->json('Sms has been reseneded.');
    }
}
