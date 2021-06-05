<?php

namespace App\Http\Requests\Api\BusinessPosts;

use App\Rules\Uuid;
use App\Services\Api\BusinessService;
use Illuminate\Foundation\Http\FormRequest;

class StoreBusinessPostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var BusinessService $businessService */
        $businessService = app()->make(BusinessService::class);
        $owner = $businessService->get(request()->get('business_id'))->user;
        if ($owner == null) {
            return true;
        }
        if ($owner->id  == request()->user()->id) {
           return true;
        }
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'business_id' => ['required', new Uuid],
            'photo'       => 'required',
            'title'       => 'sometimes|string',
            'text'        => 'sometimes|string',
            'expire_date' => 'sometimes|string'
        ];
    }
}
