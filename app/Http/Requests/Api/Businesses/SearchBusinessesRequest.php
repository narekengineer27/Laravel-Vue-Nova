<?php

namespace App\Http\Requests\Api\Businesses;

use App\Rules\Lat;
use App\Rules\Lng;
use Illuminate\Foundation\Http\FormRequest;

class SearchBusinessesRequest extends FormRequest
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
            'lat'         => ['required', 'numeric', new Lat()],
            'lng'         => ['required', 'numeric', new Lng()],
            'radius'      => ['required', 'numeric'],
            'category_id' => ['nullable', 'min:0;exists:category'],
            'keyword'     => ['nullable', 'string'],
        ];
    }
}


