<?php

namespace App\Http\Requests\Api\BusinessPosts;

use App\Rules\Uuid;
use App\Services\Api\BusinessPostService;
use App\Services\Api\BusinessService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBusinessPostRequest extends FormRequest
{
    /**§
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        /** @var $businessPostService BusinessPostService */
        $businessPostService = app()->make(BusinessPostService::class);
        $id = current(($this->route()->parameters));
        $businessPost = $businessPostService->get($id);
        if ($businessPost->user_id == null) {
            return true;
        }
        if ($businessPost->user_id == request()->user()->id) {
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
            'photo'       => 'nullable|image',
            'title'       => 'sometimes|string',
            'text'        => 'sometimes|string',
            'expire_date' => 'sometimes|string'
        ];
    }
}
