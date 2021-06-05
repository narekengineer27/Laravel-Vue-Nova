<?php

namespace App\Services\Api;

use App\Models\Business;
use App\Models\OwnershipRequest;
use App\Notifications\VerifyOwnershipRequestNotificiation;
use App\Utils\GeoLocation;
use App\Utils\ImageUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BusinessOwnershipService
{
    /**
     * @var OwnershipRequest
     */
    private $model;

    /**
     * @var ImageUploadService
     */
    private $imageUploadService;


    /**
     * BusinessService constructor.
     * @param OwnershipRequest $model
     * @param GeoLocation $geoLocation
     * @param ImageUploadService $imageUploadService
     * @param CategoryService $categoryService
     */
    public function __construct(OwnershipRequest $model, ImageUploadService $imageUploadService)
    {
        $this->model = $model;
        $this->imageUploadService = $imageUploadService;
    }

    /**
     * @param $validatedOwnershipRequest
     * @return OwnershipRequest
     * @throws \App\Exceptions\GeneralException
     */
    public function create(Business $business, array $validOwnershipRequest)
    {
        DB::beginTransaction();

        $ownershipRequest = request()->user()->businessOwnershipRequests()->create(
            array_merge($validOwnershipRequest, ['business_id' => $business->uuid])
        );

        $pin = \HelperServiceProvider::generatePIN(5);

        if($ownershipRequest->method == 'email')
        {
            $ownershipRequest->user->notify(new VerifyOwnershipRequestNotificiation($pin, $business, $ownershipRequest));
        }

        if($ownershipRequest->method == 'phone' && !is_null($ownershipRequest->user->phone_number))
        {
            $application = config('app.name');
            \Twilio::message($ownershipRequest->user->phone_number, "Thanks for requesting ownership to {$business->name}. Your ownership verification pin is {$pin}. Please sign in to verify your ownership.");
        }

        $ownershipRequest->update(
            [
                'token' => Hash::make($pin)
            ]
        );

        DB::commit();

        return $ownershipRequest;
    }

    public function verify(OwnershipRequest $ownershipRequest, Request $request)
    {
        if (Hash::check($request->token, $ownershipRequest->token)) {
            $ownershipRequest->update([
                'confirmed_at' => Carbon::now(),
            ]);

            $ownershipRequest->business()->update(
              [ 'user_id' => $ownershipRequest->user_id ]
            );

            return ['verification-status' => true];
        }
        return ['verification-status' => false];
    }

    /**
     * @param $data
     * @param $key
     * @return string|null
     * @throws \App\Exceptions\GeneralException
     */
    private function processImage($data, $key)
    {
        if (empty($data[$key])) {
            return null;
        }
        if ($data[$key] instanceof UploadedFile) {
            return $this->imageUploadService->saveImage($data[$key], 'images');
        }
        return null;
    }

}
