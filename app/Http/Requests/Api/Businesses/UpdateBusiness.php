<?php

namespace App\Http\Requests\Api\Businesses;

use App\Rules\Lat;
use App\Rules\Lng;
use App\Rules\Uuid;
use Illuminate\Foundation\Http\FormRequest;

class UpdateBusiness extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'        => 'sometimes|required|string|max:191',
            'lat'         => ['sometimes', 'required', 'numeric', new Lat()],
            'lng'         => ['sometimes', 'required', 'numeric', new Lng()],
            'category_id' => 'sometimes|required|string',
            'bio'         => 'sometimes|string',
            'avatar'      => 'sometimes|file|image',
            'cover_photo' => 'sometimes|file|image',
        ];
    }
}
